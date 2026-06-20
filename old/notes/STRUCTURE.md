# ساختار فریمورک

```
project/
│
├── .env                        ← تنظیمات واقعی (در git نباشه)
├── env.example.txt             ← نمونه .env برای اشتراک‌گذاری
├── .htaccess                   ← routing به index.php
├── .gitignore
├── config.php                  ← خواندن .env و تعریف route‌ها
├── index.php                   ← نقطه ورود — فقط loader + Router
│
├── system/                     ← هسته فریمورک (دست نزن)
│   ├── loader.php              ← require همه فایل‌های system
│   ├── core.php                ← autoloader کلاس‌ها
│   ├── db.php                  ← کلاس Db با prepared statements
│   ├── router.php              ← کلاس Router
│   ├── base_controller.php     ← BaseController
│   ├── base_model.php          ← BaseModel با CRUD آماده
│   ├── access.php              ← مدیریت دسترسی / permission
│   ├── common.php              ← توابع کمکی عمومی
│   ├── view.php                ← سیستم رندر view
│   ├── notes.php               ← سیستم پیام / flash message
│   └── graphic.php             ← توابع تصویر
│
├── mvc/
│   ├── controller/
│   │   ├── page.php            ← PageController extends BaseController
│   │   ├── account.php         ← AccountController
│   │   └── api/                ← (اختیاری) sub-controller برای API
│   │       └── ...
│   │
│   ├── model/
│   │   ├── page.php            ← PageModel extends BaseModel
│   │   └── user.php            ← UserModel
│   │
│   └── view/
│       └── page/
│           └── home.php        ← فایل HTML/PHP برای نمایش
│
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
│
└── theme/
    ├── default.php
    ├── header.php
    └── footer.php
```

---

## قوانین نامگذاری

| نوع | نام فایل | نام کلاس |
|-----|----------|----------|
| Controller | `mvc/controller/account.php` | `AccountController` |
| Model | `mvc/model/user.php` | `UserModel` |
| View | `mvc/view/account/login.php` | — |

---

## نحوه استفاده از BaseController

```php
class AccountController extends BaseController {

  // GET /account/login
  public function login(): void {
    $this->view('account/login');
  }

  // POST /account/login  (فرم ارسال میشه)
  public function loginPost(): void {
    $user = $this->post('username');
    // ...
    $this->redirect('/dashboard');
  }

  // GET /api/account/profile  → متد apiGet صدا میشه
  public function profileGet(): void {
    $this->requireAuth();
    $this->success($this->currentUser());
  }
}
```

---

## نحوه استفاده از BaseModel

```php
class UserModel extends BaseModel {
  protected string $table = 'users';

  // CRUD آماده:
  // $model->find(5)
  // $model->all()
  // $model->where(['active' => 1])
  // $model->create(['name' => 'Ali', 'email' => '...'])
  // $model->update(5, ['name' => 'Reza'])
  // $model->delete(5)
  // $model->paginate(page: 2, perPage: 20)

  // متد اختصاصی:
  public function findByEmail(string $email): ?array {
    return $this->db->first(
      "SELECT * FROM users WHERE email = :email LIMIT 1",
      ['email' => $email]
    );
  }
}
```

---

## نحوه استفاده از Db مستقیم

```php
$db = Db::getInstance();

// SELECT
$users = $db->query("SELECT * FROM users WHERE active = :a", ['a' => 1]);
$user  = $db->first("SELECT * FROM users WHERE id = :id", ['id' => 5]);
$count = $db->value("SELECT COUNT(*) FROM users");

// INSERT
$newId = $db->insert("INSERT INTO users (name) VALUES (:name)", ['name' => 'Ali']);

// UPDATE / DELETE
$affected = $db->modify("UPDATE users SET active = 0 WHERE id = :id", ['id' => 3]);

// Transaction
$db->transaction(function($db) {
  $db->modify("UPDATE accounts SET balance = balance - 100 WHERE id = :id", ['id' => 1]);
  $db->modify("UPDATE accounts SET balance = balance + 100 WHERE id = :id", ['id' => 2]);
});
```

---

## تنظیمات .env

```
APP_ENV=local     ← در production بشه: production
```

وقتی `APP_ENV=production` باشه:
- خطاهای DB به کاربر نشون داده نمیشه
- فقط در error_log ذخیره میشه
