Modules/
└── Academy/
    ├── Controllers/
    │   ├── Web/
    │   │   └── AcademyController.php
    │   └── Api/
    │       └── AcademyController.php
    ├── Models/
    │   └── AcademyModel.php
    ├── Services/
    │   └── AcademyService.php
    ├── Repositories/
    │   └── AcademyRepository.php
    ├── Validators/
    │   └── AcademyValidator.php
    ├── Policies/
    ├── Events/
    ├── Listeners/
    ├── Providers/
    │   └── AcademyServiceProvider.php
    ├── Routes/
    │   ├── web.php
    │   └── api.php
    ├── Resources/
    │   ├── Views/
    │   │   ├── index.php
    │   │   ├── create.php
    │   │   ├── edit.php
    │   │   └── show.php
    │   ├── Lang/
    │   └── Assets/
    ├── Database/
    ├── Tests/
    ├── GUIDE_ACADEMY.md
    └── module.json


# Academy Module Guide

---

# هدف ماژول

ماژول Academy مسئول مدیریت مراکز آموزشی در سامانه سرناز است.

در سرناز، Academy یک موجودیت حقوقی است که می‌تواند دارای چندین شعبه، کارمند، مدرس، هنرجو، دوره آموزشی، کلاس، رویداد و اطلاعات مالی باشد.

این ماژول باید به گونه‌ای طراحی شود که علاوه بر آموزشگاه موسیقی، برای هر نوع مرکز آموزشی نیز قابل استفاده باشد.

---

# مسئولیت‌های ماژول

این ماژول مسئول موارد زیر است:

- ایجاد آموزشگاه
- ویرایش اطلاعات آموزشگاه
- حذف یا غیرفعال کردن آموزشگاه
- مدیریت وضعیت آموزشگاه
- مدیریت تنظیمات آموزشگاه
- مدیریت اطلاعات تماس
- مدیریت ترجمه‌ها
- مدیریت تصاویر
- مدیریت شعب
- مدیریت مدیران آموزشگاه
- مدیریت دسترسی‌ها

---

# ساختار دیتابیس

Academy جدول اختصاصی ندارد.

تمام آموزشگاه‌ها در جدول:

users

ثبت می‌شوند.

---

## شرط تشخیص آموزشگاه

type = academy

---

## اطلاعات پایه

از جدول users

فیلدهای مهم:

- user_id
- username
- email
- mobile
- status
- locale
- timezone
- avatar_file_id
- created_at
- updated_at

---

## اطلاعات متنی

هیچ متن چندزبانه‌ای داخل users ذخیره نمی‌شود.

تمام موارد زیر در جدول translations قرار می‌گیرند.

- title
- slug
- summary
- description
- keywords
- meta_title
- meta_description

---

## اطلاعات تماس

از جدول phones

---

## آدرس‌ها

از جدول addresses

---

## فایل‌ها

از جدول files

---

## گالری تصاویر

از جدول attachments

---

## شبکه‌های اجتماعی

از جدول contacts

یا

social_links

(بسته به طراحی نهایی)

---

# روابط

Academy

hasMany Branch

hasMany Teacher

hasMany Employee

hasMany Student

hasMany Course

hasMany Classroom

hasMany Event

hasMany Invoice

hasMany Payment

belongsToMany Category

---

# وضعیت‌ها

status

فعال

غیرفعال

تعلیق شده

در انتظار تأیید

حذف شده

---

# مجوزها

هر آموزشگاه می‌تواند دارای:

- مجوز رسمی
- تاریخ صدور
- تاریخ انقضاء

باشد.

---

# ترجمه‌ها

تمام ترجمه‌ها توسط

HasTranslations

مدیریت می‌شوند.

مثال:

$academy->translate('title');

---

# فایل‌ها

تمام فایل‌ها توسط FileManager مدیریت می‌شوند.

مثال:

لوگو

کاور

تصاویر گالری

اسناد

---

# سیاست طراحی

AcademyModel فقط مسئول داده است.

AcademyRepository فقط مسئول دسترسی به داده است.

AcademyService مسئول Business Logic است.

Controller فقط درخواست را مدیریت می‌کند.

Validator فقط اعتبارسنجی می‌کند.

---

# قوانین

هیچ Query خام داخل Service نوشته نمی‌شود.

هیچ Business Logic داخل Repository قرار نمی‌گیرد.

هیچ Validation داخل Controller نوشته نمی‌شود.

هیچ Translation داخل users ذخیره نمی‌شود.

---

# هدف نهایی

این ماژول باید بتواند بدون تغییر ساختاری برای موارد زیر استفاده شود:

- آموزشگاه موسیقی
- آموزشگاه زبان
- آموزشگاه کامپیوتر
- آموزشگاه کنکور
- آموزشگاه هنر
- مدرسه
- دانشگاه
- مؤسسات فرهنگی

---

# Roadmap

✔ AcademyModel

⬜ AcademyRepository

⬜ AcademyService

⬜ AcademyValidator

⬜ Web Controller

⬜ API Controller

⬜ Routes

⬜ Views

⬜ Tests

---

نسخه سند

v1.0