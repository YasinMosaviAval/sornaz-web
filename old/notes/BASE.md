# BaseController و BaseModel چی هستن؟

```
هر دو یه کلاس پایه (abstract) هستن که بقیه کلاس‌ها ازشون extend می‌کنن. یعنی همه Controller‌ها و Model‌های پروژه، ابزارهای اون‌ها رو به ارث می‌برن بدون اینکه دوباره بنویسنشون.
php// وقتی می‌نویسی:
class UserModel extends BaseModel { ... }

// UserModel به طور خودکار همه متدهای BaseModel رو داره:
$model->find(5);
$model->all();
$model->create([...]);
// ...

```

# چرا بعضی متدها هم‌نام هستن؟

```
این اصلی‌ترین سوالته. سه حالت مختلف داره:

حالت ۱ — Override کردن (بازنویسی)
وقتی در Model جدید، متدی با همون اسم می‌نویسی، نسخه جدید جایگزین نسخه BaseModel میشه.
مثال واقعی: SessionModel::create()
php// BaseModel::create() — generic، array می‌گیره و insert می‌کنه
// و int (last insert id) برمی‌گردونه
public function create(array $data): int {
  // INSERT INTO {table} ...
}

// SessionModel::create() — بازنویسی شده
// همون array می‌گیره ولی string (token) برمی‌گردونه
public function create(array $data): string {
  $token = bin2hex(random_bytes(32));
  $this->db->insert("INSERT INTO user_sessions ...", [...]);
  return $token; // ← رفتار کاملاً متفاوت
}
وقتی بنویسی $sessionModel->create([...]) — نسخه SessionModel اجرا میشه، نه BaseModel.

```

```
حالت ۲ — استفاده از متد پدر با parent::
وقتی می‌خوای هم کار BaseModel رو بکنی هم چیزی اضافه کنی:
phpclass UserModel extends BaseModel {

  public function create(array $data): int {
    // اول validation اضافه می‌کنیم
    if (empty($data['email'])) {
      throw new InvalidArgumentException('ایمیل الزامی است');
    }

    // بعد متد پدر رو صدا می‌زنیم
    return parent::create($data);
    //     ↑ اینجا BaseModel::create() اجرا میشه
  }

}

```

```
حالت ۳ — اشتباه طراحی (که در کدهای ما هست)
بعضی جاها متدهایی در Controller نوشتم که اسمشون با BaseController یکیه ولی منظور متفاوتیه — مثل delete() یا show(). این می‌تونه گیج‌کننده باشه. بذار دقیق توضیح بدم:
php// BaseController داره:
protected function redirect(string $url): never { ... }
protected function json(mixed $data): never { ... }
protected function view(string $path): void { ... }

// UserController داره:
public function show(int $id): void { ... }   // ← این متد در BaseController نیست
public function delete(int $id): void { ... } // ← این هم نیست

// ولی داخل UserController::show() از متدهای BaseController استفاده می‌کنیم:
public function show(int $id): void {
  $user = (new UserModel())->findById($id);
  $this->view('/user/show', 'پروفایل', ['user' => $user]);
  //   ↑ این view() از BaseController میاد
}

پس تداخل واقعی نیست — فقط ظاهرشون شبیه هم به نظر میرسه.

```

# ساختار کامل BaseModel

```
BaseModel
│
├── $db          ← instance از Db (prepared statements)
├── $table       ← نام جدول — هر Model خودش set می‌کنه
├── $pk          ← کلید اصلی (پیش‌فرض: 'id')
│
├── find($id)              ← SELECT با id
├── all($orderBy)          ← SELECT همه ردیف‌ها
├── where($conditions)     ← SELECT با شرط
├── firstWhere($conditions)← اولین نتیجه با شرط
├── create($data)          ← INSERT
├── update($id, $data)     ← UPDATE با id
├── delete($id)            ← DELETE با id (hard delete)
├── exists($id)            ← وجود داشتن با id
├── count($conditions)     ← تعداد ردیف‌ها
└── paginate(...)          ← صفحه‌بندی با فیلتر
کِی باید override کنی؟
وضعیتکار درستجدول soft delete داره (deleted_at)override کن — شرط AND deleted_at IS NULL اضافه کننیاز به join با جدول دیگه داریمتد جدید بنویس، مثل findWithTranslation()رفتار insert باید فرق کنه (مثل token برگردوندن)override کنquery ساده‌ای داری که BaseModel پوشش میدهoverride نکن — همون رو استفاده کن

```


# ساختار کامل BaseController

```
BaseController
│
├── $db               ← instance از Db
│
├── ── View ──
├── view($path, $title, $data)   ← رندر صفحه HTML
├── partial($path, $data)        ← رندر بخشی از صفحه
│
├── ── JSON/API ──
├── json($data, $status)         ← خروجی JSON خام
├── success($data, $message)     ← JSON موفق: {success:true, data:...}
└── error($message, $status)     ← JSON خطا: {success:false, message:...}
│
├── ── Auth ──
├── isLoggedIn()                 ← آیا کاربر لاگینه؟
├── currentUser()                ← آرایه اطلاعات کاربر از session
├── requireLogin($redirect)      ← اگه لاگین نبود redirect کن (Web)
├── requireAuth()                ← اگه لاگین نبود 401 JSON برگردون (API)
└── requireRole($roles)          ← اگه نقش نداشت 403 برگردون
│
├── ── Request ──
├── input($key, $default)        ← از POST یا GET
├── post($key, $default)         ← فقط از POST
├── get($key, $default)          ← فقط از GET
├── body()                       ← JSON body برای API
└── isApiRequest()               ← آیا درخواست به /api/ هست؟
│
└── ── Redirect ──
    redirect($url)               ← header Location و exit

```

# یه مثال کامل که همه چیز رو نشون میده

```
phpclass CourseController extends BaseController {

  // ── این متد در BaseController نیست — مال خود CourseController هست ──
  public function list(): void {

    // requireLogin() از BaseController میاد
    $this->requireLogin();

    // $this->db از BaseController میاد
    $courses = $this->db->query("SELECT * FROM courses WHERE active = 1");

    // view() از BaseController میاد
    $this->view('/course/list', 'دوره‌ها', ['courses' => $courses]);
  }


  // ── این هم در BaseController نیست ──
  public function delete(int $id): void {
    $this->requireRole(['admin', 'superadmin']); // از BaseController

    $model = new CourseModel();
    $course = $model->find($id); // find() از BaseModel میاد

    if (!$course) {
      // اگه API بود JSON برگردون، اگه Web بود redirect کن
      if ($this->isApiRequest()) {       // از BaseController
        $this->error('یافت نشد', 404);  // از BaseController
      } else {
        $this->redirect('/courses');     // از BaseController
      }
      return;
    }

    $model->delete($id); // delete() از BaseModel میاد — ولی CourseModel می‌تونه override کنه
    $this->redirect('/courses?deleted=1');
  }

}


class CourseModel extends BaseModel {

  protected string $table = 'courses';

  // ── این find() را override می‌کنه چون جدول soft delete داره ──
  public function find(int $id): ?array {
    return $this->db->first(
      "SELECT * FROM courses WHERE id = :id AND deleted_at IS NULL",
      ['id' => $id]
    );
    // BaseModel::find() این شرط را نداره
  }

  // ── این delete() را override می‌کنه — soft delete بجای hard delete ──
  public function delete(int $id): int {
    return $this->db->modify(
      "UPDATE courses SET deleted_at = NOW() WHERE id = :id",
      ['id' => $id]
    );
    // BaseModel::delete() یه DELETE واقعی می‌زنه
  }

  // ── این متد در BaseModel نیست — اضافه می‌کنیم ──
  public function findWithTeachers(int $id): ?array {
    return $this->db->first(
      "SELECT c.*, u.username as teacher_name
       FROM courses c
       LEFT JOIN users u ON u.id = c.teacher_id
       WHERE c.id = :id AND c.deleted_at IS NULL",
      ['id' => $id]
    );
  }

}

```

# خلاصه قانون طلایی


```
در BaseModel override کن وقتی:

* جدول deleted_at داره → همه متدهای read باید AND deleted_at IS NULL داشته باشن
* create() باید چیزی غیر از int برگردونه (مثل token)
* query نیاز به join داره

در BaseController override نکن — متدهایی مثل view(), redirect(), json() رو مستقیم با $this-> صدا بزن. متدهای عمومی مثل list(), show(), delete() رو در خود Controller بنویس — اینا اصلاً در BaseController نیستن و هم‌نامی وجود نداره.

```