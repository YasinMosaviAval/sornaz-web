# Page Module

---

## اطلاعات ماژول

| مورد | مقدار |
|------|--------|
| Module | Page |
| Table | pages |
| Primary Key | page_id |
| Status | 🚧 Under Development |

---

# هدف ماژول

توضیح مختصری درباره وظیفه این ماژول.

مثال:

این ماژول مسئول مدیریت آموزشگاه‌ها است.

---

# ساختار

```
Page/
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
PageController
```

---

# Service

```
PageService
```

---

# Repository

```
PageRepository
```

---

# Model

```
PageModel
```

---

# Policy

```
PagePolicy
```

---

# Request ها

```
PageStoreRequest

PageUpdateRequest
```

---

# DTO

```
PageDTO
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
