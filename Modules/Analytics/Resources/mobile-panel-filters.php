<?php
// Generated from active web controls by scripts/generate_mobile_panel_filters.php.
return json_decode(<<<'JSON'
{
    "members": [
        {
            "key": "filterStaffType",
            "webId": "filterStaffType",
            "label": "همه نوع قرارداد",
            "type": "select",
            "rowPath": "type",
            "options": {
                "teacher": "استاد",
                "receptionist": "پذیرش",
                "manager": "مدیر",
                "other": "سایر"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterStaffStatus",
            "webId": "filterStaffStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "در انتظار تأیید": "در انتظار تأیید",
                "فعال": "فعال",
                "غیرفعال": "غیرفعال",
                "حذف شده": "حذف شده",
                "رد شده": "رد شده"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterStaffCurrency",
            "webId": "filterStaffCurrency",
            "label": "همه انواع پول",
            "type": "select",
            "rowPath": "currency",
            "options": {
                "تومان": "تومان",
                "دلار": "دلار",
                "یورو": "یورو"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterStaffLesson",
            "webId": "filterStaffLesson",
            "label": "همه درس‌های شعبه‌ها",
            "type": "select",
            "rowPath": "lessonId",
            "options": {},
            "optionSource": {
                "source": "staff_catalog.lessons",
                "id": "id",
                "label": "name"
            },
            "distinct": true
        }
    ],
    "students": [
        {
            "key": "filterStudentAgeGroup",
            "webId": "filterStudentAgeGroup",
            "label": "همه رده‌های سنی",
            "type": "select",
            "rowPath": "_ageGroup",
            "options": {
                "child": "زیر ۱۸ سال",
                "adult": "۱۸ سال و بالاتر"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterStudentLevel",
            "webId": "filterStudentLevel",
            "label": "همه سطوح",
            "type": "select",
            "rowPath": "level",
            "options": {
                "مبتدی": "مبتدی",
                "متوسط": "متوسط",
                "پیشرفته": "پیشرفته"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterStudentInstrument",
            "webId": "filterStudentInstrument",
            "label": "همه سازها",
            "type": "select",
            "rowPath": "instrument",
            "options": {
                "پیانو": "پیانو",
                "گیتار": "گیتار",
                "ویولن": "ویولن",
                "آواز": "آواز",
                "درام": "درام",
                "سنتور": "سنتور",
                "کمانچه": "کمانچه"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "lessons": [
        {
            "key": "filterLessonStatus",
            "webId": "filterLessonStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "active": "فعال",
                "inactive": "غیرفعال",
                "pending": "در انتظار تأیید"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterLessonLevel",
            "webId": "filterLessonLevel",
            "label": "همه سطوح",
            "type": "select",
            "rowPath": "level_id",
            "options": {},
            "optionSource": {
                "source": "levels",
                "id": "level_id"
            },
            "distinct": true
        }
    ],
    "courses": [
        {
            "key": "filterCourseStatus",
            "webId": "filterCourseStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "pending": "در انتظار تأیید",
                "open": "باز",
                "ongoing": "در حال برگزاری",
                "finished": "پایان یافته"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterCourseInstrument",
            "webId": "filterCourseInstrument",
            "label": "همه درس‌ها",
            "type": "select",
            "rowPath": "instrument",
            "options": {},
            "optionSource": null,
            "distinct": true
        },
        {
            "key": "filterCourseLevel",
            "webId": "filterCourseLevel",
            "label": "همه سطح‌ها",
            "type": "select",
            "rowPath": "level_id",
            "options": {},
            "optionSource": {
                "source": "levels"
            },
            "distinct": true
        }
    ],
    "terms": [
        {
            "key": "filterTermStatus",
            "webId": "filterTermStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "در حال برگزاری": "در حال برگزاری",
                "در انتظار تأیید": "در انتظار تأیید",
                "پایان یافته": "پایان یافته",
                "باز": "باز"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterTermCourse",
            "webId": "filterTermCourse",
            "label": "همه دوره‌ها",
            "type": "select",
            "rowPath": "courseId",
            "options": {},
            "optionSource": {
                "source": "courses"
            },
            "distinct": true
        },
        {
            "key": "filterTermCurrency",
            "webId": "filterTermCurrency",
            "label": "همه نوع پول",
            "type": "select",
            "rowPath": "currencyId",
            "options": {},
            "optionSource": {
                "source": "currencies"
            },
            "distinct": true
        },
        {
            "key": "filterTermDiscount",
            "webId": "filterTermDiscount",
            "label": "همه تخفیف‌ها",
            "type": "select",
            "rowPath": "discountId",
            "options": {},
            "optionSource": {
                "source": "discounts"
            },
            "distinct": true
        },
        {
            "key": "filterTermClassroom",
            "webId": "filterTermClassroom",
            "label": "همه کلاس‌ها",
            "type": "select",
            "rowPath": "classroomId",
            "options": {},
            "optionSource": {
                "source": "classrooms"
            },
            "distinct": true
        },
        {
            "key": "filterTermInstallmentCount",
            "webId": "filterTermInstallmentCount",
            "label": "همه تعداد اقساط",
            "type": "select",
            "rowPath": "installmentCount",
            "options": {},
            "optionSource": null,
            "distinct": true
        },
        {
            "key": "filterTermWeekday",
            "webId": "filterTermWeekday",
            "label": "همه روزهای هفته",
            "type": "select",
            "rowPath": "_weekday",
            "options": {
                "6": "شنبه",
                "0": "یکشنبه",
                "1": "دوشنبه",
                "2": "سه‌شنبه",
                "3": "چهارشنبه",
                "4": "پنجشنبه",
                "5": "جمعه"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "classrooms": [
        {
            "key": "filterClassroomStatus",
            "webId": "filterClassroomStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "در انتظار تأیید": "در انتظار تأیید",
                "در دسترس": "در دسترس",
                "خارج از دسترس": "خارج از دسترس"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterClassroomType",
            "webId": "filterClassroomType",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "typeLabel",
            "options": {},
            "optionSource": null,
            "distinct": true
        },
        {
            "key": "filterClassroomCapacity",
            "webId": "filterClassroomCapacity",
            "label": "همه ظرفیت‌ها",
            "type": "select",
            "rowPath": "capacity",
            "options": {},
            "optionSource": null,
            "distinct": true
        },
        {
            "key": "filterClassroomEquipment",
            "webId": "filterClassroomEquipment",
            "label": "همه تجهیزات",
            "type": "select",
            "rowPath": "equipment",
            "options": {},
            "optionSource": null,
            "distinct": true
        }
    ],
    "classroom-types": [
        {
            "key": "classroomTypeStatusFilter",
            "webId": "classroomTypeStatusFilter",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "approved": "تأیید شده",
                "pending": "در انتظار تأیید",
                "rejected": "رد شده"
            },
            "optionSource": null,
            "distinct": false,
            "permission": "showStatus"
        },
        {
            "key": "classroomTypeCategoryFilter",
            "webId": "classroomTypeCategoryFilter",
            "label": "همه دسته‌ها",
            "type": "select",
            "rowPath": "type",
            "options": {},
            "optionSource": {
                "source": "typeOptions",
                "id": "value"
            },
            "distinct": true,
            "permission": "showCategory"
        }
    ],
    "availabilities": [
        {
            "key": "filterBranchDay",
            "webId": "filterBranchDay",
            "label": "همه روزها",
            "type": "select",
            "rowPath": "day",
            "options": {
                "شنبه": "شنبه",
                "یکشنبه": "یکشنبه",
                "دوشنبه": "دوشنبه",
                "سه‌شنبه": "سه‌شنبه",
                "چهارشنبه": "چهارشنبه",
                "پنجشنبه": "پنجشنبه",
                "جمعه": "جمعه"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterBranchRepeat",
            "webId": "filterBranchRepeat",
            "label": "همه دوره‌های تکرار",
            "type": "select",
            "rowPath": "repeatPeriod",
            "options": {
                "هفتگی": "هفتگی",
                "دو هفته": "دو هفته",
                "سه هفته": "سه هفته",
                "چهار هفته": "چهار هفته",
                "ماهانه": "ماهانه",
                "سالانه": "سالانه",
                "بی‌تکرار": "بی‌تکرار"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterBranchTimezone",
            "webId": "filterBranchTimezone",
            "label": "همه مناطق زمانی",
            "type": "select",
            "rowPath": "timezone",
            "options": {
                "Asia\/Tehran": "تهران",
                "Asia\/Dubai": "دبی",
                "Asia\/Istanbul": "استانبول",
                "Europe\/London": "لندن",
                "Europe\/Paris": "پاریس",
                "Europe\/Berlin": "برلین",
                "Europe\/Rome": "رم",
                "Europe\/Amsterdam": "آمستردام",
                "America\/New_York": "نیویورک",
                "America\/Chicago": "شیکاگو",
                "America\/Los_Angeles": "لس‌آنجلس",
                "America\/Toronto": "تورنتو",
                "UTC": "UTC"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterBranchStatus",
            "webId": "filterBranchStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "فعال": "فعال",
                "غیرفعال": "غیرفعال",
                "در انتظار تأیید": "در انتظار تأیید"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "member-schedules": [
        {
            "key": "filterMemberRole",
            "webId": "filterMemberRole",
            "label": "همه نقش‌ها",
            "type": "select",
            "rowPath": "role",
            "options": {
                "استاد": "استاد",
                "منشی": "منشی",
                "مدیر": "مدیر",
                "پرسنل": "پرسنل"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterMemberDay",
            "webId": "filterMemberDay",
            "label": "همه روزها",
            "type": "select",
            "rowPath": "day",
            "options": {
                "شنبه": "شنبه",
                "یکشنبه": "یکشنبه",
                "دوشنبه": "دوشنبه",
                "سه‌شنبه": "سه‌شنبه",
                "چهارشنبه": "چهارشنبه",
                "پنجشنبه": "پنجشنبه",
                "جمعه": "جمعه"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterMemberRepeat",
            "webId": "filterMemberRepeat",
            "label": "همه دوره‌های تکرار",
            "type": "select",
            "rowPath": "repeatPeriod",
            "options": {
                "هفتگی": "هفتگی",
                "دو هفته": "دو هفته",
                "سه هفته": "سه هفته",
                "چهار هفته": "چهار هفته",
                "ماهانه": "ماهانه",
                "سالانه": "سالانه",
                "بی‌تکرار": "بی‌تکرار"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterMemberTimezone",
            "webId": "filterMemberTimezone",
            "label": "همه مناطق زمانی",
            "type": "select",
            "rowPath": "timezone",
            "options": {
                "Asia\/Tehran": "تهران",
                "Asia\/Dubai": "دبی",
                "Asia\/Istanbul": "استانبول",
                "Europe\/London": "لندن",
                "Europe\/Paris": "پاریس",
                "Europe\/Berlin": "برلین",
                "Europe\/Rome": "رم",
                "Europe\/Amsterdam": "آمستردام",
                "America\/New_York": "نیویورک",
                "America\/Chicago": "شیکاگو",
                "America\/Los_Angeles": "لس‌آنجلس",
                "America\/Toronto": "تورنتو",
                "UTC": "UTC"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterMemberStatus",
            "webId": "filterMemberStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "فعال": "فعال",
                "غیرفعال": "غیرفعال",
                "پر شده": "پر شده",
                "در انتظار تأیید": "در انتظار تأیید"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "availability-exceptions": [
        {
            "key": "filterHolidayLeaveTimezone",
            "webId": "filterHolidayLeaveTimezone",
            "label": "همه مناطق زمانی",
            "type": "select",
            "rowPath": "timezone",
            "options": {
                "Asia\/Tehran": "تهران",
                "Asia\/Dubai": "دبی",
                "Asia\/Istanbul": "استانبول",
                "Europe\/London": "لندن",
                "Europe\/Paris": "پاریس",
                "Europe\/Berlin": "برلین",
                "Europe\/Rome": "رم",
                "Europe\/Amsterdam": "آمستردام",
                "America\/New_York": "نیویورک",
                "America\/Chicago": "شیکاگو",
                "America\/Los_Angeles": "لس‌آنجلس",
                "America\/Toronto": "تورنتو",
                "UTC": "UTC"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterHolidayLeaveStatus",
            "webId": "filterHolidayLeaveStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "فعال": "فعال",
                "غیرفعال": "غیرفعال",
                "در انتظار تأیید": "در انتظار تأیید"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterHolidayLeaveType",
            "webId": "filterHolidayLeaveType",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "type",
            "options": {
                "holiday": "تعطیل رسمی",
                "closed": "تعطیل",
                "unavailable": "عدم دسترسی",
                "busy": "مشغول",
                "vacation": "مرخصی",
                "blocked": "مسدود"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "scheduling-rules": [
        {
            "key": "filterRuleStatus",
            "webId": "filterRuleStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "در انتظار تأیید": "در انتظار تأیید",
                "فعال": "فعال",
                "غیرفعال": "غیرفعال",
                "حذف‌شده": "حذف‌شده"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterRuleType",
            "webId": "filterRuleType",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "type",
            "options": {},
            "optionSource": null,
            "distinct": true
        }
    ],
    "schedules": [
        {
            "key": "filterScheduleDay",
            "webId": "filterScheduleDay",
            "label": "همه روزها",
            "type": "select",
            "rowPath": "day",
            "options": {
                "5": "شنبه",
                "6": "یکشنبه",
                "0": "دوشنبه",
                "1": "سه‌شنبه",
                "2": "چهارشنبه",
                "3": "پنجشنبه",
                "4": "جمعه"
            },
            "optionSource": null,
            "distinct": false,
            "queryKey": "day"
        },
        {
            "key": "filterScheduleType",
            "webId": "filterScheduleType",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "type",
            "options": {
                "in_person": "حضوری",
                "online": "آنلاین"
            },
            "optionSource": null,
            "distinct": false,
            "queryKey": "mode"
        },
        {
            "key": "filterScheduleStatus",
            "webId": "filterScheduleStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "pending": "در انتظار تأیید",
                "approved": "تأیید شده",
                "rejected": "رد شده",
                "completed": "پایان یافته",
                "canceled": "لغو شده",
                "rescheduled": "زمان‌بندی مجدد",
                "held": "برگزار شده",
                "postponed": "به تعویق افتاده"
            },
            "optionSource": null,
            "distinct": false,
            "queryKey": "status"
        },
        {
            "key": "filterScheduleInstrument",
            "webId": "filterScheduleInstrument",
            "label": "همه درس‌ها",
            "type": "select",
            "rowPath": "lesson",
            "options": {},
            "optionSource": {
                "source": "lessons"
            },
            "distinct": true,
            "queryKey": "lesson"
        },
        {
            "key": "filterScheduleClassroom",
            "webId": "filterScheduleClassroom",
            "label": "همه کلاس‌ها",
            "type": "select",
            "rowPath": "classroom",
            "options": {},
            "optionSource": {
                "source": "classrooms"
            },
            "distinct": true,
            "queryKey": "classroom"
        },
        {
            "key": "filterScheduleTimeFrom",
            "webId": "filterScheduleTimeFrom",
            "label": "همه ساعت‌ها",
            "type": "select",
            "rowPath": "timeFrom",
            "options": {},
            "optionSource": null,
            "distinct": true,
            "queryKey": "timeFrom"
        },
        {
            "key": "filterScheduleTimeTo",
            "webId": "filterScheduleTimeTo",
            "label": "همه ساعت‌ها",
            "type": "select",
            "rowPath": "timeTo",
            "options": {},
            "optionSource": null,
            "distinct": true,
            "queryKey": "timeTo"
        }
    ],
    "finance": [
        {
            "key": "filterFinanceStatus",
            "webId": "filterFinanceStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "پیش‌نویس": "پیش‌نویس",
                "صادرشده": "صادرشده",
                "پرداخت جزئی": "پرداخت جزئی",
                "پرداخت‌شده": "پرداخت‌شده",
                "ماه اول رایگان": "ماه اول رایگان",
                "در انتظار پرداخت": "در انتظار پرداخت",
                "منقضی‌شده": "منقضی‌شده",
                "لغوشده": "لغوشده"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterFinanceType",
            "webId": "filterFinanceType",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "type",
            "options": {
                "فاکتور ترم": "فاکتور ترم",
                "اشتراک سامانه": "اشتراک سامانه"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterFinanceRange",
            "webId": "filterFinanceRange",
            "label": "همه زمان‌ها",
            "type": "select",
            "rowPath": "range",
            "options": {
                "weekly": "هفتگی",
                "monthly": "ماهانه",
                "yearly": "سالانه",
                "custom": "انتخابی"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterFinanceFrom",
            "webId": "filterFinanceFrom",
            "label": "از تاریخ",
            "type": "date",
            "rowPath": "_dateFrom",
            "visibleWhen": {
                "_form.filterFinanceRange": "custom"
            }
        },
        {
            "key": "filterFinanceTo",
            "webId": "filterFinanceTo",
            "label": "تا تاریخ",
            "type": "date",
            "rowPath": "_dateTo",
            "visibleWhen": {
                "_form.filterFinanceRange": "custom"
            }
        }
    ],
    "points": [
        {
            "key": "filterPointType",
            "webId": "filterPointType",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "type",
            "options": {
                "general": "عمومی",
                "professional": "حرفه‌ای"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterPointCategory",
            "webId": "filterPointCategory",
            "label": "همه دسته‌ها",
            "type": "select",
            "rowPath": "category",
            "options": {
                "attendance": "حضور",
                "academic": "آموزشی",
                "event": "رویداد",
                "social": "اجتماعی",
                "financial": "مالی",
                "profile": "پروفایل",
                "achievement": "دستاورد",
                "engagement": "تعامل",
                "management": "مدیریت"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterPointStatus",
            "webId": "filterPointStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "فعال": "فعال",
                "غیرفعال": "غیرفعال"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "messages": [
        {
            "key": "filterMessageStatus",
            "webId": "filterMessageStatus",
            "label": "همه وضعیت‌های مطالعه",
            "type": "select",
            "rowPath": "status",
            "options": {
                "خوانده‌نشده": "خوانده‌نشده",
                "خوانده‌شده": "خوانده‌شده"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "notifications": [
        {
            "key": "filterNotificationActivity",
            "webId": "filterNotificationActivity",
            "label": "همه عنوان‌های فعالیت",
            "type": "select",
            "rowPath": "activityTitle",
            "options": {},
            "optionSource": null,
            "distinct": true
        },
        {
            "key": "filterNotificationStatus",
            "webId": "filterNotificationStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "در انتظار": "در انتظار",
                "منتشر شده": "منتشر شده",
                "پیش‌نویس": "پیش‌نویس",
                "خصوصی": "خصوصی",
                "زباله‌دان": "زباله‌دان"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterNotificationPriority",
            "webId": "filterNotificationPriority",
            "label": "همه اولویت‌ها",
            "type": "select",
            "rowPath": "priority",
            "options": {
                "بالا": "بالا",
                "متوسط": "متوسط",
                "کم": "کم"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterNotificationAudience",
            "webId": "filterNotificationAudience",
            "label": "همه مخاطبان",
            "type": "select",
            "rowPath": "audience",
            "options": {
                "همه": "همه",
                "هنرجویان": "هنرجویان",
                "اساتید": "اساتید",
                "والدین": "والدین",
                "پرسنل": "پرسنل"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "posts": [
        {
            "key": "postTypeFilter",
            "webId": "postTypeFilter",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "type",
            "options": {
                "post": "نوشته",
                "product": "محصول",
                "music_theory": "تئوری موسیقی"
            },
            "optionSource": null,
            "distinct": false,
            "queryKey": "type"
        },
        {
            "key": "postVisibilityFilter",
            "webId": "postVisibilityFilter",
            "label": "همه سطوح دسترسی",
            "type": "select",
            "rowPath": "visibility",
            "options": {
                "public": "عمومی",
                "private": "خصوصی",
                "followers": "دنبال‌کنندگان",
                "premium": "ویژه"
            },
            "optionSource": null,
            "distinct": false,
            "queryKey": "visibility"
        },
        {
            "key": "status",
            "label": "وضعیت",
            "type": "select",
            "rowPath": "status",
            "queryKey": "status",
            "options": {
                "published": "منتشرشده",
                "draft": "پیش‌نویس",
                "pending": "در انتظار بررسی",
                "private": "خصوصی",
                "trash": "زباله‌دان",
                "future": "زمان‌بندی‌شده"
            }
        }
    ],
    "comments": [
        {
            "key": "commentPostFilter",
            "webId": "commentPostFilter",
            "label": "همه نوشته‌ها",
            "type": "select",
            "rowPath": "postId",
            "options": {},
            "optionSource": {
                "source": "posts"
            },
            "distinct": true,
            "queryKey": "post"
        },
        {
            "key": "status",
            "label": "وضعیت",
            "type": "select",
            "rowPath": "status",
            "queryKey": "status",
            "options": {
                "approved": "تأیید شده",
                "pending": "در انتظار",
                "rejected": "رد شده",
                "trash": "زباله‌دان"
            }
        }
    ],
    "media": [
        {
            "key": "mediaType",
            "webId": "mediaType",
            "label": "همه رسانه‌ها",
            "type": "select",
            "rowPath": "type",
            "options": {
                "image": "تصویر",
                "video": "ویدیو",
                "audio": "صوت",
                "document": "سند",
                "archive": "فایل فشرده"
            },
            "optionSource": null,
            "distinct": false,
            "queryKey": "type"
        },
        {
            "key": "mediaMonth",
            "webId": "mediaMonth",
            "label": "همه تاریخ‌ها",
            "type": "select",
            "rowPath": "month",
            "options": {},
            "optionSource": null,
            "distinct": true,
            "queryKey": "month"
        }
    ],
    "roles": [
        {
            "key": "accessRoleScope",
            "webId": "accessRoleScope",
            "label": "همه دامنه‌ها",
            "type": "select",
            "rowPath": "scope",
            "options": {
                "platform": "پلتفرم",
                "website": "سایت",
                "academy": "آموزشگاه",
                "branch": "شعبه",
                "self": "شخصی"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "accessRoleLevel",
            "webId": "accessRoleLevel",
            "label": "همه سطوح",
            "type": "select",
            "rowPath": "level",
            "options": {
                "1": "درجه ۱",
                "2": "درجه ۲",
                "3": "درجه ۳"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "permissions": [
        {
            "key": "accessPermissionScope",
            "webId": "accessPermissionScope",
            "label": "همه دامنه‌ها",
            "type": "select",
            "rowPath": "scope",
            "options": {
                "platform": "پلتفرم",
                "website": "سایت",
                "academy": "آموزشگاه",
                "branch": "شعبه",
                "self": "شخصی"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "accessPermissionRisk",
            "webId": "accessPermissionRisk",
            "label": "همه سطوح خطر",
            "type": "select",
            "rowPath": "risk",
            "options": {
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "users": [
        {
            "key": "accessUserStatus",
            "webId": "accessUserStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "pending": "در انتظار",
                "approved": "تأییدشده",
                "rejected": "ردشده",
                "inactive": "غیرفعال",
                "banned": "مسدود"
            },
            "optionSource": null,
            "distinct": false,
            "queryKey": "status"
        }
    ],
    "my-points": [
        {
            "key": "type",
            "label": "نوع امتیاز",
            "type": "select",
            "rowPath": "type",
            "distinct": true,
            "options": {}
        },
        {
            "key": "action",
            "label": "عملیات",
            "type": "select",
            "rowPath": "action",
            "distinct": true,
            "options": {}
        }
    ],
    "organization-holidays": [
        {
            "key": "filterHolidayLeaveTimezone",
            "webId": "filterHolidayLeaveTimezone",
            "label": "همه مناطق زمانی",
            "type": "select",
            "rowPath": "timezone",
            "options": {
                "Asia\/Tehran": "تهران",
                "Asia\/Dubai": "دبی",
                "Asia\/Istanbul": "استانبول",
                "Europe\/London": "لندن",
                "Europe\/Paris": "پاریس",
                "Europe\/Berlin": "برلین",
                "Europe\/Rome": "رم",
                "Europe\/Amsterdam": "آمستردام",
                "America\/New_York": "نیویورک",
                "America\/Chicago": "شیکاگو",
                "America\/Los_Angeles": "لس‌آنجلس",
                "America\/Toronto": "تورنتو",
                "UTC": "UTC"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterHolidayLeaveStatus",
            "webId": "filterHolidayLeaveStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "فعال": "فعال",
                "غیرفعال": "غیرفعال",
                "در انتظار تأیید": "در انتظار تأیید"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterHolidayLeaveType",
            "webId": "filterHolidayLeaveType",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "type",
            "options": {
                "holiday": "تعطیل رسمی",
                "closed": "تعطیل",
                "unavailable": "عدم دسترسی",
                "busy": "مشغول",
                "blocked": "مسدود"
            },
            "optionSource": null,
            "distinct": false
        }
    ],
    "employee-leaves": [
        {
            "key": "filterHolidayLeaveTimezone",
            "webId": "filterHolidayLeaveTimezone",
            "label": "همه مناطق زمانی",
            "type": "select",
            "rowPath": "timezone",
            "options": {
                "Asia\/Tehran": "تهران",
                "Asia\/Dubai": "دبی",
                "Asia\/Istanbul": "استانبول",
                "Europe\/London": "لندن",
                "Europe\/Paris": "پاریس",
                "Europe\/Berlin": "برلین",
                "Europe\/Rome": "رم",
                "Europe\/Amsterdam": "آمستردام",
                "America\/New_York": "نیویورک",
                "America\/Chicago": "شیکاگو",
                "America\/Los_Angeles": "لس‌آنجلس",
                "America\/Toronto": "تورنتو",
                "UTC": "UTC"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterHolidayLeaveStatus",
            "webId": "filterHolidayLeaveStatus",
            "label": "همه وضعیت‌ها",
            "type": "select",
            "rowPath": "status",
            "options": {
                "فعال": "فعال",
                "غیرفعال": "غیرفعال",
                "در انتظار تأیید": "در انتظار تأیید"
            },
            "optionSource": null,
            "distinct": false
        },
        {
            "key": "filterHolidayLeaveType",
            "webId": "filterHolidayLeaveType",
            "label": "همه انواع",
            "type": "select",
            "rowPath": "type",
            "options": {
                "unavailable": "عدم دسترسی",
                "busy": "مشغول",
                "vacation": "مرخصی",
                "blocked": "مسدود"
            },
            "optionSource": null,
            "distinct": false
        }
    ]
}
JSON, true, 512, JSON_THROW_ON_ERROR);
