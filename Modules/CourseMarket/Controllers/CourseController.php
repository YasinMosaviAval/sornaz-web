<?php
namespace Modules\CourseMarket\Controllers;

use Core\http\ResponseFactory;
use Modules\CourseMarket\Repositories\CourseRepository;
use Modules\CourseMarket\Services\CourseService;
use Modules\CourseMarket\Services\PaymentService;

class CourseController
{
    private CourseService $courses;
    private PaymentService $payments;
    public function __construct()
    {
        $repo = new CourseRepository(db());
        $this->courses = new CourseService($repo);
        $this->payments = new PaymentService($repo, $this->courses);
    }
    private function actor(): int { return (int)auth()->id(); }
    private function page(string $mode, array $data = [])
    {
        return ResponseFactory::view('CourseMarket::index', ['mode'=>$mode] + $data);
    }
    public function index() { return $this->page('catalog', ['items'=>$this->courses->listing($this->actor(), 'catalog')]); }
    public function manage() { return $this->page('manage', ['items'=>$this->courses->listing($this->actor(), 'manage'), 'sales'=>$this->courses->sales($this->actor())]); }
    public function library() { return $this->page('library', ['items'=>$this->courses->listing($this->actor(), 'library')]); }
    public function create() { return $this->page('edit', ['course'=>null]); }
    public function edit(int $id) { return $this->viewAction(fn()=>$this->page('edit', ['course'=>$this->courses->owned($this->actor(), $id)])); }
    public function show(int $id) { return $this->viewAction(fn()=>$this->page('show', ['course'=>$this->courses->detail($this->actor(), $id)])); }
    public function store() { return $this->save(0); }
    public function update(int $id) { return $this->save($id); }
    private function save(int $id)
    {
        return $this->jsonAction(function () use ($id) {
            $data = json_decode((string)($_POST['payload'] ?? ''), true);
            if (!is_array($data)) throw new \RuntimeException('اطلاعات ارسالی معتبر نیست.', 422);
            return $this->courses->save($this->actor(), $id, $data);
        });
    }
    public function upload(int $id) { return $this->jsonAction(fn()=>$this->courses->upload($this->actor(), $id, $_FILES['file'] ?? [])); }
    public function buy(int $id) { return $this->jsonAction(fn()=>$this->payments->start($this->actor(), $id)); }
    public function unlockLesson(int $id,int $postId) { return $this->jsonAction(fn()=>$this->courses->unlockLesson($this->actor(),$id,$postId,(string)($_POST['password']??''))); }
    public function callback()
    {
        return $this->viewAction(function () {
            $order = $this->payments->callback((string)($_GET['token'] ?? ''), (string)($_GET['Authority'] ?? ''), (string)($_GET['Status'] ?? ''));
            return $this->page('receipt', ['order'=>$order]);
        });
    }
    private function status(\Throwable $e): int { return in_array($e->getCode(), [401,403,404,409,422,500,502,503], true) ? $e->getCode() : 500; }
    private function message(\Throwable $e): string
    {
        if ($e instanceof \RuntimeException && !($e instanceof \PDOException)) return $e->getMessage();
        error_log('CourseMarket: '.$e->getMessage());
        return 'خطایی رخ داد. لطفاً دوباره تلاش کنید.';
    }
    private function jsonAction(callable $action)
    {
        try { return ResponseFactory::json(['success'=>true,'data'=>$action()]); }
        catch (\Throwable $e) { return ResponseFactory::json(['success'=>false,'message'=>$this->message($e)], $this->status($e)); }
    }
    private function viewAction(callable $action)
    {
        try { return $action(); }
        catch (\Throwable $e) { http_response_code($this->status($e)); return $this->page('error', ['message'=>$this->message($e)]); }
    }
    public function media(int $id)
    {
        try { $media = $this->courses->media($this->actor(), $id); }
        catch (\Throwable $e) { abort($this->status($e)); }
        $path = base_path('storage/course-media/'.$media['filename']);
        if (!is_file($path)) abort(404);
        $size = filesize($path); $start = 0; $end = $size - 1;
        header('Content-Type: '.$media['mime']);
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        header('Accept-Ranges: bytes');
        if (isset($_SERVER['HTTP_RANGE'])) {
            if (!preg_match('/^bytes=(\d*)-(\d*)$/D', $_SERVER['HTTP_RANGE'], $range) || ($range[1] === '' && $range[2] === '')) {
                header('Content-Range: bytes */'.$size); abort(416);
            }
            if ($range[1] === '') $start = max(0, $size - (int)$range[2]);
            else { $start = (int)$range[1]; if ($range[2] !== '') $end = min($end, (int)$range[2]); }
            if ($start > $end || $start >= $size) { header('Content-Range: bytes */'.$size); abort(416); }
            http_response_code(206);
            header("Content-Range: bytes $start-$end/$size");
        }
        header('Content-Length: '.($end - $start + 1));
        session_write_close();
        $file = fopen($path, 'rb'); fseek($file, $start); $remaining = $end - $start + 1;
        while ($remaining > 0 && !feof($file) && !connection_aborted()) {
            $chunk = fread($file, min(65536, $remaining));
            if ($chunk === false || $chunk === '') break;
            echo $chunk; $remaining -= strlen($chunk);
        }
        fclose($file); exit;
    }
}
