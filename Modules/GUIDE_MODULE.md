1. هدف ماژول
2. مسئولیت‌ها
3. ساختار دیتابیس
4. موجودیت‌ها
5. روابط
6. Workflow
7. قوانین
8. Business Rules
9. API
10. Roadmap
11. TODO
12. Changelog


# modules

1. System
2. Geo
3. Profile
4. Academy
5. Education
6. Enrollment
7. Finance
8. CMS
9. Communication
10. Community
11. Analytics


# architecture

    Request
    ↓
    Router
    ↓
    Middleware
    ↓
    Controller
    ↓
    Policy
    ↓
    Service
    ↓
    Repository
    ↓
    Model
    ↓
    Database


# Router

دریافت URL
تشخیص Controller
تشخیص Action
اعمال Middleware


# controllers

فقط هماهنگ‌کننده است.

وظیفه
Request → Service → Response



# services

مهم‌ترین بخش فریمورک شما

اینجا منطق کسب‌وکار قرار می‌گیرد.



# repositories

تمام SQL ها اینجا هستند.

Controller و Service نباید SQL بنویسند.

وظیفه: Database Access Layer



# models

نماینده جدول است.

CRUD

Relations

Scopes


# Middleware

قبل از Controller اجرا می‌شود.

کارهای عمومی:

    Authentication

    Authorization

    Rate Limiting

    CSRF

    Maintenance Mode

    Language Detection

    Logging



# Policy

مجوز دسترسی به یک رکورد خاص

تفاوت Policy و Middleware:

Middleware:

آیا کاربر لاگین است؟

Policy:

آیا این کاربر اجازه ویرایش این آموزشگاه را دارد؟


# Event

برای جدا کردن بخش‌ها از هم

مثال:

ثبت نام هنرجو

بدون Event

EnrollmentService

ثبت نام

ارسال پیامک

ارسال ایمیل

ثبت گزارش

ثبت امتیاز

ارسال اعلان

کلاس 3000 خطی می‌شود.

با Event

EnrollmentCreated

فقط:

event(
 new EnrollmentCreated(
   $enrollment
 )
);

سپس Listenerها:

SendSmsListener

SendEmailListener

CreateInvoiceListener

CreateNotificationListener

UpdateStatisticsListener

خودکار اجرا می‌شوند.



# Listener

شنونده Event



# Validator

اعتبارسنجی داده‌ها

    مثال:

    CreateAcademyValidator

    قوانین:

    name => required

    phone => required

    email => email

    national_code => unique



# DTO

برای پروژه شما شدیداً توصیه می‌شود.


# last architecture

ساختار نهایی هر ماژول

مثلاً ماژول Academy

MVC
└── Academy
    ├── Controllers
    │       AcademyController.php
    │       BranchController.php
    ├── Services
    │       AcademyService.php
    │       BranchService.php
    ├── Repositories
    │       AcademyRepository.php
    │       BranchRepository.php
    ├── Models
    │       AcademyModel.php
    │       BranchModel.php
    ├── DTOs
    │       CreateAcademyDTO.php
    │       UpdateAcademyDTO.php
    ├── Validators
    │       CreateAcademyValidator.php
    ├── Policies
    │       AcademyPolicy.php
    ├── Events
    │       AcademyCreated.php
    │       AcademyUpdated.php
    ├── Listeners
    │       CreateOwnerRoleListener.php
    │       CreateDefaultSettingsListener.php
    └── Routes
            web.php
            api.php
