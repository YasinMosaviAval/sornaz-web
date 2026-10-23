<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use Core\security\ContentSafety;
use RuntimeException;

class SitePageContentService
{
    public function panelGuides(): array
    {
        if (!(int) auth()->id()) throw new RuntimeException('ورود به حساب کاربری الزامی است.', 403);
        $pages = DB::table('f_settings')->where('table_name', 'site_page_content')->where('page', 'LIKE', 'panel-%')->whereNull('deleted_at')->get();
        $out = [];
        foreach (array_unique(array_column($pages, 'page')) as $page) {
            foreach ($this->content($page, locale()) as $item) {
                $field = substr($item['key'], strrpos($item['key'], '.') + 1);
                if (in_array($field, ['title', 'body'], true)) $out[substr($page, 6)][$field] = ['fa' => $item['fa'] ?? '', 'en' => $item['en'] ?? ''];
            }
        }
        $guideFile = dirname(__DIR__) . '/Resources/panel-page-guidance.php';
        $defaults = is_file($guideFile) ? require $guideFile : [];
        foreach ($out as $page => $guide) {
            $texts = [...array_values($guide['title'] ?? []), ...array_values($guide['body'] ?? [])];
            if (array_filter($texts, static fn ($text) => trim((string) $text) !== '')) $defaults[$page] = $guide;
        }
        return $defaults;
    }

    private function requireGuideEditor(int $actor): void
    {
        if ($actor !== 1 || (int) auth()->id() !== 1) throw new RuntimeException('ویرایش توضیحات فقط برای مدیر اصلی سایت مجاز است.', 403);
    }

    public function savePanelGuide(int $actor, array $data): void
    {
        $this->requireGuideEditor($actor);
        $page = $this->validPage((string) ($data['page'] ?? ''));
        if (!str_starts_with($page, 'panel-')) throw new RuntimeException('صفحهٔ پنل معتبر نیست.', 422);
        $items = [];
        foreach (['title', 'body'] as $field) {
            $item = ['page' => $page, 'kind' => 'text', 'key' => 'site.page.' . $page . '.text.' . $field];
            foreach (['fa', 'en'] as $language) {
                if (!is_array($data[$language] ?? null)) throw new RuntimeException('ترجمهٔ توضیحات معتبر نیست.', 422);
                $item[$language] = ContentSafety::text($data[$language][$field] ?? '', $field === 'title' ? 200 : 20000, true);
            }
            $items[] = $this->validatedContent($item);
        }
        transaction(function () use ($actor, $items) {
            foreach ($items as $item) $this->persistContent($actor, $item);
        });
    }

    private const PAGES = [
        ['id' => 'home', 'title' => 'خانه', 'route' => '/'], ['id' => 'articles', 'title' => 'مقاله‌های آموزشی', 'route' => '/analytics/articles'], ['id' => 'article-details', 'title' => 'نمایش مقاله', 'route' => '/analytics/article-details'], ['id' => 'academies', 'title' => 'آموزشگاه‌ها', 'route' => '/academy/academies'], ['id' => 'users', 'title' => 'کاربران', 'route' => '/users'], ['id' => 'about-us', 'title' => 'درباره ما', 'route' => '/page/about-us'], ['id' => 'contact-us', 'title' => 'تماس با ما', 'route' => '/page/contact-us'], ['id' => 'login', 'title' => 'ورود', 'route' => '/system/login'], ['id' => 'register', 'title' => 'ثبت‌نام', 'route' => '/system/register'], ['id' => 'forgot-password', 'title' => 'فراموشی رمز عبور', 'route' => '/system/forgot-password']];

    public function content(string $page, string $locale): array
    {
        $page = $this->validPage($page);
        if (str_starts_with($page, 'panel-') && !(int) auth()->id()) throw new RuntimeException('ورود به حساب کاربری الزامی است.', 403);
        $locale = in_array($locale, ['fa', 'en'], true) ? $locale : 'fa';
        $out = [];
        foreach (DB::table('f_settings')->where('table_name', 'site_page_content')->where('page', $page)->whereNull('deleted_at')->get() as $r) {
            $item = ['key' => $r['variable_name'], 'kind' => $r['status'], 'value' => $r['status'] === 'image' ? ($r['source'] ?? '') : ($r['status'] === 'icon' ? ($r['icon'] ?? '') : ($r['value'] ?? ''))];
            if ($r['status'] === 'image') {
                $item['value'] = ContentSafety::url((string) $item['value']);
            }
            if ($r['status'] === 'text') {
                $values = [];
                foreach (DB::table('f_translations')->where('table_name', 'f_settings')->where('table_id', (int) $r['setting_id'])->where('field', 'value')->whereNull('deleted_at')->get() as $tr) {
                    $values[$tr['locale']] = $tr['value'];
                }
                $item['fa'] = $values['fa'] ?? '';
                $item['en'] = $values['en'] ?? '';
                $item['value'] = $values[$locale] ?? $values['fa'] ?? '';
            } $out[] = $item;
        }return $out;
    }

    public function save(int $actor, array $d): void
    {
        if (str_starts_with((string) ($d['page'] ?? ''), 'panel-')) $this->requireGuideEditor($actor);
        $d = $this->validatedContent($d);
        transaction(fn () => $this->persistContent($actor, $d));
    }

    private function persistContent(int $actor, array $d): void
    {
        $page = $this->validPage($d['page']);
        $kind = $d['kind'];
        $key = trim((string) ($d['key'] ?? ''));
        if (!preg_match('/^site\.page\.[a-z0-9-]+\.(text|image|icon)\.[a-z0-9]+$/', $key)) {
            throw new RuntimeException('کلید محتوا معتبر نیست.');
        }
        $row = DB::table('f_settings')->where('variable_name', $key)->first();
        $base = ['page' => $page, 'table_name' => 'site_page_content', 'status' => $kind, 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
        if ($kind === 'image') {
            $base['source'] = trim((string) ($d['value'] ?? ''));
        } elseif ($kind === 'icon') {
            $base['icon'] = trim((string) ($d['value'] ?? ''));
        } else {
            $base['value'] = null;
        }
        if ($row) {
            $id = (int) $row['setting_id'];
            DB::table('f_settings')->where('setting_id', $id)->update($base);
        } else {
            $id = (int) DB::table('f_settings')->insertGetId(['variable_name' => $key, 'created_by' => $actor] + $base);
        }
        if ($kind === 'text') {
            foreach (['fa', 'en'] as $locale) {
                $value = trim((string) ($d[$locale] ?? ''));
                $tr = DB::table('f_translations')->where('table_name', 'f_settings')->where('table_id', $id)->where('field', 'value')->where('locale', $locale)->first();
                $v = ['value' => $value, 'version' => 1, 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
                if ($tr) {
                    DB::table('f_translations')->where('translation_id', (int) $tr['translation_id'])->update($v);
                } else {
                    DB::table('f_translations')->insert(['table_name' => 'f_settings', 'table_id' => $id, 'field' => 'value', 'locale' => $locale, 'created_by' => $actor] + $v);
                }
            }
        }
    }

    private function validatedContent(array $d): array
    {
        $d['page'] = $this->validPage(ContentSafety::text($d['page'] ?? '', 60, true));
        $kind = ContentSafety::text($d['kind'] ?? '', 10, true);
        $key = ContentSafety::text($d['key'] ?? '', 200, true);
        $prefix = 'site.page.' . $d['page'] . '.' . $kind . '.';
        if (str_starts_with($d['page'], 'panel-') && ($kind !== 'text' || !in_array($key, [$prefix . 'title', $prefix . 'body'], true))) throw new RuntimeException('کلید توضیحات معتبر نیست.', 422);
        if (!in_array($kind, ['text', 'image', 'icon'], true) || !str_starts_with($key, $prefix)) {
            throw new RuntimeException('Invalid content key.', 422);
        }
        if ($kind === 'text') {
            foreach (['fa', 'en'] as $locale) {
                $d[$locale] = ContentSafety::text($d[$locale] ?? '', 20000, true);
            }
        } else {
            $value = ContentSafety::text($d['value'] ?? '', 2048, true);
            if (($kind === 'image' && ContentSafety::url($value) === '') || ($kind === 'icon' && !preg_match('/^[a-zA-Z0-9_ -]+$/D', $value))) {
                throw new RuntimeException('Invalid content value.', 422);
            }
            $d['value'] = $value;
        }
        return $d;
    }

    private function validPage(string $page): string
    {
        if (preg_match('/^panel-[a-z][a-z0-9-]{0,44}$/D', $page)) return $page;
        foreach (self::PAGES as $p) {
            if ($p['id'] === $page) {
                return $page;
            }
        }
        throw new RuntimeException('برگه معتبر نیست.');
    }
}
