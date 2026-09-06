<?php
namespace Modules\CourseMarket\Services;

use Modules\CourseMarket\Repositories\CourseRepository;
use RuntimeException;
use Modules\CourseMarket\Repositories\LessonRepository;

class CourseService
{
    public function __construct(private CourseRepository $repo) {}

    public function listing(int $actor, string $mode): array
    {
        $columns = 'c.id,c.title,c.description,c.price,c.status,c.cover_id,c.owner_id,c.updated_at';
        if ($mode === 'manage') return $this->repo->query("SELECT $columns FROM creator_courses c WHERE owner_id=? ORDER BY id DESC", [$actor]);
        if ($mode === 'library') return $this->repo->query("SELECT $columns FROM creator_courses c WHERE EXISTS (SELECT 1 FROM creator_course_orders o WHERE o.course_id=c.id AND o.buyer_id=? AND o.status='paid') ORDER BY c.id DESC", [$actor]);
        return $this->repo->query("SELECT $columns FROM creator_courses c WHERE status='published' ORDER BY id DESC LIMIT 100");
    }

    public function course(int $id): array
    {
        $course = $this->repo->one('SELECT * FROM creator_courses WHERE id=?', [$id]);
        if (!$course) throw new RuntimeException('دوره پیدا نشد.', 404);
        $course['curriculum'] = (new LessonRepository($this->repo))->hydrate($id, json_decode($course['curriculum'], true) ?: []);
        $course['files'] = $this->repo->query('SELECT id,mime,bytes FROM creator_course_media WHERE course_id=?', [$id]);
        return $course;
    }

    public function owned(int $actor, int $id): array
    {
        $course = $this->course($id);
        if ($actor < 1 || (int)$course['owner_id'] !== $actor) throw new RuntimeException('اجازه ویرایش این دوره را ندارید.', 403);
        return $course;
    }

    public function hasAccess(int $actor, array $course): bool
    {
        return $actor > 0 && ((int)$course['owner_id'] === $actor || (bool)$this->repo->one("SELECT id FROM creator_course_orders WHERE buyer_id=? AND course_id=? AND status='paid'", [$actor, $course['id']]));
    }

    public function detail(int $actor, int $id): array
    {
        $course = $this->course($id);
        $access = $this->hasAccess($actor, $course);
        if ($course['status'] !== 'published' && !$access) throw new RuntimeException('دوره در دسترس نیست.', 404);
        $course['access'] = $access;
        $allowed=[];$owner=$actor===(int)$course['owner_id'];
        foreach($course['curriculum'] as &$chapter)foreach($chapter['lessons'] as &$lesson){
            $locked=$access&&!$owner&&!empty($lesson['has_password'])&&!(new LessonRepository($this->repo))->unlocked($actor,(int)$lesson['post_id']);
            $lesson['locked']=$locked;
            if(!$access||$locked)unset($lesson['text'],$lesson['media']);else $allowed=array_merge($allowed,$lesson['media']);
        }
        unset($chapter,$lesson);
        if(!$access)unset($course['files']);
        elseif(!$owner)$course['files']=array_values(array_filter($course['files'],fn($f)=>in_array((int)$f['id'],$allowed,true)));
        return $course;
    }

    private function value(mixed $value, int $max, bool $required = false): string
    {
        if (!is_string($value)) throw new RuntimeException('متن واردشده معتبر نیست.', 422);
        $value = trim($value);
        if (($required && $value === '') || mb_strlen($value) > $max) throw new RuntimeException('عنوان یا توضیحات خالی یا بیش از حد طولانی است.', 422);
        return $value;
    }

    public function save(int $actor, int $id, array $data): array
    {
        if ($actor < 1) throw new RuntimeException('ابتدا وارد حساب کاربری شوید.', 401);
        $existing = $id ? $this->owned($actor, $id) : null;
        $title = $this->value($data['title'] ?? '', 180, true);
        $description = $this->value($data['description'] ?? '', 20000);
        $price = filter_var($data['price'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000000]]);
        if ($price === false) throw new RuntimeException('قیمت باید عدد صحیح و به تومان باشد.', 422);
        $status = $data['status'] ?? 'draft';
        if (!in_array($status, ['draft', 'published'], true)) throw new RuntimeException('وضعیت نامعتبر است.', 422);
        $chapters = $data['curriculum'] ?? [];
        if (!is_array($chapters) || !array_is_list($chapters) || count($chapters) > 100) throw new RuntimeException('حداکثر ۱۰۰ فصل مجاز است.', 422);
        $allowedMedia = $id ? $this->repo->query('SELECT id,mime FROM creator_course_media WHERE course_id=?', [$id]) : [];
        $mediaMap = array_column($allowedMedia, 'mime', 'id');
        $cover = (int)($data['cover_id'] ?? 0);
        if ($cover && !str_starts_with($mediaMap[$cover] ?? '', 'image/')) throw new RuntimeException('تصویر جلد معتبر نیست.', 422);
        $clean = []; $lessonCount = 0;
        foreach ($chapters as $chapter) {
            if (!is_array($chapter)) throw new RuntimeException('ساختار فصل معتبر نیست.', 422);
            $chapterTitle = $this->value($chapter['title'] ?? '', 180, true);
            $lessons = $chapter['lessons'] ?? [];
            if (!is_array($lessons) || !array_is_list($lessons) || count($lessons) > 100) throw new RuntimeException('هر فصل حداکثر ۱۰۰ درس دارد.', 422);
            if ($status === 'published' && !$lessons) throw new RuntimeException('برای هر فصل حداقل یک درس اضافه کنید.', 422);
            $cleanLessons = [];
            foreach ($lessons as $lesson) {
                if (!is_array($lesson) || ++$lessonCount > 1000) throw new RuntimeException('ساختار یا تعداد درس‌ها معتبر نیست.', 422);
                $lessonTitle = $this->value($lesson['title'] ?? '', 180, true);
                $text = $this->value($lesson['text'] ?? '', 100000);
                $media = $lesson['media'] ?? [];
                if (!is_array($media) || count($media) > 20) throw new RuntimeException('حداکثر ۲۰ فایل برای هر درس مجاز است.', 422);
                $media = array_values(array_unique(array_map('intval', $media)));
                foreach ($media as $mid) if (!isset($mediaMap[$mid])) throw new RuntimeException('فایل متعلق به این دوره نیست.', 422);
                if ($status === 'published' && $text === '' && !$media) throw new RuntimeException('هر درس باید متن، تصویر یا ویدیو داشته باشد.', 422);
                $password=$this->value($lesson['password']??'',72);
                if($password!==''&&(strlen($password)<8||strlen($password)>72))throw new RuntimeException('رمز درس بین ۸ تا ۷۲ بایت باشد.',422);
                $cleanLessons[]=['post_id'=>(int)($lesson['post_id']??0),'title'=>$lessonTitle,'text'=>$text,'media'=>$media,'password'=>$password,'clear_password'=>!empty($lesson['clear_password'])];
            }
            $clean[] = ['title' => $chapterTitle, 'lessons' => $cleanLessons];
        }
        if ($status === 'published' && (!$cover || $description === '' || !$lessonCount)) throw new RuntimeException('برای انتشار، تصویر جلد، توضیحات و حداقل یک درس لازم است.', 422);
        $json = json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (strlen($json) > 2000000) throw new RuntimeException('حجم متن دوره بیش از حد مجاز است.', 422);
        $id=$this->repo->transaction(function()use($existing,$id,$actor,$data,$title,$description,$price,$status,$cover,$clean){
            if($existing){
                $current=$this->repo->one('SELECT version FROM creator_courses WHERE id=? AND owner_id=? FOR UPDATE',[$id,$actor]);
                if(!$current||(int)$current['version']!==(int)($data['version']??0))throw new RuntimeException('دوره در پنجره دیگری تغییر کرده است. صفحه را تازه کنید.',409);
            }else $id=$this->repo->insert('creator_courses',['owner_id'=>$actor,'title'=>$title,'description'=>$description,'price'=>$price,'status'=>$status,'cover_id'=>$cover?:null,'curriculum'=>'[]']);
            $outline=(new LessonRepository($this->repo))->sync($id,$actor,$clean);
            $this->repo->query('UPDATE creator_courses SET title=?,description=?,price=?,status=?,cover_id=?,curriculum=?,version=version+?,updated_at=CURRENT_TIMESTAMP WHERE id=? AND owner_id=?',[$title,$description,$price,$status,$cover?:null,json_encode($outline,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$existing?1:0,$id,$actor]);
            return $id;
        });
        return $this->course($id);
    }

    public function upload(int $actor, int $id, array $file): array
    {
        $this->owned($actor, $id);
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'] ?? '')) throw new RuntimeException('آپلود انجام نشد. اندازه فایل و محدودیت سرور را بررسی کنید.', 422);
        $size = filesize($file['tmp_name']);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $types = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','video/mp4'=>'mp4','video/webm'=>'webm'];
        if (!isset($types[$mime]) || !$size || $size > (str_starts_with($mime, 'image/') ? 10*1024*1024 : 250*1024*1024)) throw new RuntimeException('فرمت مجاز: JPG، PNG، WebP تا ۱۰ مگابایت یا MP4 و WebM تا ۲۵۰ مگابایت.', 422);
        if (str_starts_with($mime, 'image/') && !getimagesize($file['tmp_name'])) throw new RuntimeException('تصویر معتبر نیست.', 422);
        $filename = bin2hex(random_bytes(24)).'.'.$types[$mime];
        $directory = base_path('storage/course-media');
        if (!is_dir($directory) || !is_file($directory.'/.htaccess')) throw new RuntimeException('فضای امن آپلود آماده نیست.', 503);
        $path = $directory.'/'.$filename;
        return $this->repo->transaction(function () use ($id, $size, $mime, $filename, $path, $file) {
            $this->repo->one('SELECT id FROM creator_courses WHERE id=? FOR UPDATE', [$id]);
            $total = $this->repo->one('SELECT COALESCE(SUM(bytes),0) AS total FROM creator_course_media WHERE course_id=?', [$id]);
            if ((int)$total['total'] + $size > 5*1024*1024*1024) throw new RuntimeException('حداکثر فضای هر دوره ۵ گیگابایت است.', 422);
            if (!move_uploaded_file($file['tmp_name'], $path)) throw new RuntimeException('ذخیره فایل ناموفق بود.', 500);
            try {
                $mid = $this->repo->insert('creator_course_media', ['course_id'=>$id,'filename'=>$filename,'mime'=>$mime,'bytes'=>$size]);
            } catch (\Throwable $e) { unlink($path); throw $e; }
            return ['id'=>$mid,'mime'=>$mime,'url'=>'/course-market/media/'.$mid];
        });
    }

    public function media(int $actor, int $id): array
    {
        $media = $this->repo->one('SELECT * FROM creator_course_media WHERE id=?', [$id]);
        if (!$media) throw new RuntimeException('فایل پیدا نشد.', 404);
        $course = $this->course((int)$media['course_id']);
        $publicCover = $course['status'] === 'published' && (int)$course['cover_id'] === $id && str_starts_with($media['mime'], 'image/');
        if (!$publicCover && !$this->hasAccess($actor, $course)) throw new RuntimeException('برای مشاهده محتوا ابتدا دوره را خریداری کنید.', 403);
        if(!$publicCover&&$actor!==(int)$course['owner_id']){
            $detail=$this->detail($actor,(int)$course['id']);
            if(!in_array($id,array_map(fn($f)=>(int)$f['id'],$detail['files']??[]),true))throw new RuntimeException('برای این فایل ابتدا رمز درس را وارد کنید.',403);
        }
        return $media;
    }

    public function unlockLesson(int $actor,int $course,int $post,string $password): array
    {
        $item=$this->course($course);
        if(!$this->hasAccess($actor,$item))throw new RuntimeException('ابتدا دوره را تهیه کنید.',403);
        (new LessonRepository($this->repo))->unlock($actor,$course,$post,$password);
        return $this->detail($actor,$course);
    }

    public function sales(int $actor): array
    {
        return $this->repo->query("SELECT c.title,o.amount,o.reference_id,o.paid_at FROM creator_course_orders o JOIN creator_courses c ON c.id=o.course_id WHERE o.seller_id=? AND o.status='paid' ORDER BY o.id DESC", [$actor]);
    }
}
