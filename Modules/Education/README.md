# Education Module

---

## اطلاعات ماژول

| مورد | مقدار |
|------|--------|
| Module | Education |
| Table | educations |
| Primary Key | education_id |
| Status | 🚧 Under Development |

---

# هدف ماژول

توضیح مختصری درباره وظیفه این ماژول.

مثال:

این ماژول مسئول مدیریت آموزشگاه‌ها است.

---

# ساختار

```
Education/
│
├── Controllers
│
├── DTO
│
├── Events
│
├── Listeners
│
├── Models
│
├── Policies
│
├── Providers
│
├── Repositories
│
├── Requests
│
├── Resources
│   ├── Assets
│   └── Views
│
├── Routes
│
├── Services
│
├── config.php
│
├── helpers.php
│
└── module.php
```

---

# Route ها

## Web

```
GET
POST
PUT
DELETE
```

## API

```
GET
POST
PUT
DELETE
```

---

# Controller

```
EducationController
```

---

# Service

```
EducationService
```

---

# Repository

```
EducationRepository
```

---

# Model

```
EducationModel
```

---

# Policy

```
EducationPolicy
```

---

# Request ها

```
EducationStoreRequest

EducationUpdateRequest
```

---

# DTO

```
EducationDTO
```

---

# Translation

این ماژول از سیستم Translation استفاده می‌کند.

در صورت وجود فیلدهای متنی، اطلاعات باید داخل جدول translations ذخیره شوند.

---

# Events

```
None
```

---

# Listeners

```
None
```

---

# Config

```
config.php
```

---

# Helpers

```
helpers.php
```

---

# وابستگی‌ها

```
Core\Database

Core\Http

Core\Validation

Core\View

Core\Auth

Core\Translation
```

---

# TODO

- [ ] Create Controller

- [ ] Create Views

- [ ] Create Validation

- [ ] Create Tests

- [ ] Create Policies

- [ ] Complete Documentation

---

# Version

```
1.0.0
```

---

# Author

Sornaz Framework
