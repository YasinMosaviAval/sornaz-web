# Course screens and data mapping

The mobile implementation follows the supplied light/dark course screenshots
(093905 through 094124) and keeps private lesson content in the existing posts
and creator_course_lessons storage.

| Feature | Data / implementation |
| --- | --- |
| Catalog cards, search, rating/duration/category filters | CourseBrowse and CourseCard; localized course metadata and aggregate review scores |
| Latest Home courses | Last 10 published creator courses ordered by updated_at and id |
| Recent instructors/authors | Last publication across public courses, social posts and public articles; 10 distinct active users |
| Course tabs and curriculum | CourseExperience; existing purchase and password checks remain authoritative |
| Preview video | Separately uploaded preview_id; files belonging to private lessons cannot act as public previews |
| Reviews and recommendations | creator_course_reviews; one review per enrolled user/course, locale-specific display |
| Questions and replies | creator_course_questions; parent must belong to the same course and locale |
| Likes/dislikes | creator_course_reactions; one current reaction per account/course |
| Report an issue | creator_course_reports with pending status |
| Learning schedule | creator_course_schedules; account-specific future date/time |
| English title/description and category, summary, duration, original price | creator_course_details metadata, editable from course studio |
| Resources and PDF/DOCX files | Owner-managed metadata, protected course media, account-separated app cache, Android FileProvider viewer |
| Video controls | Play/pause, seeking, volume, playback speed, full-screen view and local account-specific lesson notes |
| Related courses, rating distribution, previous/next | Derived from published catalog and review records |
| Interface translations | AppStrings learningEn/learningFa via existing translate extension and AppText |

## Deployment

Apply `course-experience-schema.sql` to the same database before publishing the
updated API files. The script is additive and idempotent; the course installer
also includes it. Deploy CourseExperienceService, CourseService, LearningService,
SocialController and the Social API routes together.

The local schema has been applied. No production deployment or publication of
existing draft courses was performed. A public catalog intentionally omits draft
courses. External resource links are entered by the course owner; private lesson
files keep their purchase/password requirements.

## Verification

37 isolated backend checks passed, including access control, locale separation,
review replacement, question parent validation, schedules, the ten-course limit
and recent authors without courses. Temporary fixtures used connection-local
SQL tables rather than application data. Flutter regression and interaction
checks cover startup restoration, catalog filters, Home carousel limits, course
tabs and review submission. Android native file opening still requires a device
with an appropriate document viewer.