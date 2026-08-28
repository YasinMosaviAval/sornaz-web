<?php

namespace Modules\Page\Services;

use Modules\Page\Repositories\PageRepository;

class PageService {


    public function __construct(protected PageRepository $repository) {
    }


    public function getByPage(string $page): array {
        return $this->repository->findByPage($page);
    }

    public function homeStatistics(): array {
        $pdo = db();

        return [
            'articles' => (int) $pdo->query("SELECT COUNT(*) FROM posts WHERE type = 'post' AND status = 'published' AND visibility = 'public' AND deleted_at IS NULL")->fetchColumn(),
            'teachers' => (int) $pdo->query("SELECT COUNT(DISTINCT members.user_id)
                FROM academy_branch_member_contracts contracts
                INNER JOIN academy_branch_members members ON members.member_id = contracts.member_id
                INNER JOIN users ON users.user_id = members.user_id
                WHERE contracts.type = 'teacher'
                  AND contracts.deleted_at IS NULL
                  AND members.deleted_at IS NULL
                  AND members.status IN ('active', 'approved')
                  AND users.deleted_at IS NULL
                  AND users.status IN ('active', 'approved')")->fetchColumn(),
            'students' => (int) $pdo->query("SELECT COUNT(DISTINCT members.user_id)
                FROM academy_branch_course_term_enrollments enrollments
                INNER JOIN academy_branch_members members ON members.member_id = enrollments.member_id
                INNER JOIN users ON users.user_id = members.user_id
                INNER JOIN academy_branch_course_terms terms ON terms.term_id = enrollments.term_id
                WHERE enrollments.type = 'student'
                  AND enrollments.status = 'active'
                  AND enrollments.deleted_at IS NULL
                  AND members.deleted_at IS NULL
                  AND members.status IN ('active', 'approved')
                  AND users.deleted_at IS NULL
                  AND users.status IN ('active', 'approved')
                  AND terms.deleted_at IS NULL")->fetchColumn(),
            'courses' => (int) $pdo->query("SELECT COUNT(*)
                FROM academy_branch_courses courses
                INNER JOIN academy_branches branches ON branches.branch_id = courses.branch_id
                INNER JOIN users ON users.user_id = branches.user_id
                WHERE courses.status IN ('open', 'ongoing')
                  AND courses.deleted_at IS NULL
                  AND branches.deleted_at IS NULL
                  AND users.deleted_at IS NULL
                  AND users.status IN ('active', 'approved')")->fetchColumn(),
        ];
    }

    public function activityOverviewHtml(string $locale): string {
        $locale = $locale === 'en' ? 'en' : 'fa';
        $statement = db()->prepare("SELECT translations.value
            FROM f_settings settings
            INNER JOIN f_translations translations
                ON translations.table_name = 'f_settings'
               AND translations.table_id = settings.setting_id
               AND translations.field = 'value'
               AND translations.locale = ?
               AND translations.deleted_at IS NULL
            WHERE settings.variable_name = 'home_activity_overview_html'
              AND settings.deleted_at IS NULL
            LIMIT 1");
        $statement->execute([$locale]);

        return (string) ($statement->fetchColumn() ?: '');
    }

    public function homeSearchSelectLabels(string $locale): array {
        $locale = $locale === 'en' ? 'en' : 'fa';
        $statement = db()->prepare("SELECT settings.variable_name, translations.value
            FROM settings
            INNER JOIN translations
                ON translations.table_name = 'settings'
               AND translations.table_id = settings.setting_id
               AND translations.field = 'value'
               AND translations.locale = ?
               AND translations.deleted_at IS NULL
            WHERE settings.variable_name IN ('home_search_instrument_label','home_search_city_label')
              AND settings.deleted_at IS NULL");
        $statement->execute([$locale]);
        $values = [];
        foreach ($statement->fetchAll() as $row) $values[(string)$row['variable_name']] = (string)$row['value'];

        return [
            'instrument' => $values['home_search_instrument_label'] ?? ($locale === 'en' ? 'Instrument' : 'ساز'),
            'city' => $values['home_search_city_label'] ?? ($locale === 'en' ? 'City' : 'شهر'),
        ];
    }

    public function homeLearningPath(string $locale): array {
        $locale = $locale === 'en' ? 'en' : 'fa';
        $keys = [
            'heading'=>'home_learning_path_heading',
            'basic_title'=>'home_learning_basic_title','basic_description'=>'home_learning_basic_description',
            'iranian_title'=>'home_learning_iranian_title','iranian_description'=>'home_learning_iranian_description',
            'forms_title'=>'home_learning_forms_title','forms_description'=>'home_learning_forms_description',
            'instruments_title'=>'home_learning_instruments_title','instruments_description'=>'home_learning_instruments_description',
        ];
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $statement = db()->prepare("SELECT settings.variable_name, translations.value FROM settings
            INNER JOIN translations ON translations.table_name='settings' AND translations.table_id=settings.setting_id
                AND translations.field='value' AND translations.locale=? AND translations.deleted_at IS NULL
            WHERE settings.variable_name IN ($placeholders) AND settings.deleted_at IS NULL");
        $statement->execute(array_merge([$locale], array_values($keys)));
        $stored=[];foreach($statement->fetchAll() as$row)$stored[(string)$row['variable_name']]=(string)$row['value'];
        $fallbacks = $locale === 'en' ? [
            'heading'=>'Learning Path','basic_title'=>'Basic Theory','basic_description'=>'Rhythm, melody, harmony, intervals, and scales',
            'iranian_title'=>'Iranian Music','iranian_description'=>'Dangs, dastgahs, avazes, and radif',
            'forms_title'=>'Forms and Styles','forms_description'=>'Iranian and international forms and stylistics',
            'instruments_title'=>'Instruments and Performance','instruments_description'=>'Choosing an instrument, practicing, and entering the professional world',
        ] : [
            'heading'=>'مسیر یادگیری','basic_title'=>'تئوری پایه','basic_description'=>'ریتم، ملودی، هارمونی، فاصله و گام',
            'iranian_title'=>'موسیقی ایرانی','iranian_description'=>'دانگ‌ها، دستگاه‌ها، آوازها و ردیف',
            'forms_title'=>'فرم و سبک','forms_description'=>'فرم‌های ایرانی و جهانی، سبک‌شناسی',
            'instruments_title'=>'ساز و نوازندگی','instruments_description'=>'انتخاب ساز، تمرین و ورود حرفه‌ای',
        ];
        $result=[];foreach($keys as$name=>$key)$result[$name]=$stored[$key]??$fallbacks[$name];return$result;
    }


}
