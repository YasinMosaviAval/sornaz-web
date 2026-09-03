<?php

namespace Modules\Page\Controllers\Web;

use Core\database\DB;
use Core\http\RedirectResponse;

class LegacyRedirectController
{
    public function single(string $legacySlug): RedirectResponse
    {
        $slug = $this->decode($legacySlug);
        $static = require base_path('config/legacy_redirects.php');
        if (isset($static[$slug])) {
            return redirect($static[$slug], 301);
        }

        $post = DB::table('posts')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->first();

        if ($post && ($post['type'] ?? '') !== 'page') {
            return redirect('/analytics/article-details?id=' . (int) $post['post_id'], 301);
        }

        $legacyTitle = $this->legacyArticleTitle($slug);
        if ($legacyTitle !== null) {
            $translation = DB::table('translations')
                ->where('table_name', 'posts')
                ->where('locale', 'fa')
                ->where('field', 'title')
                ->where('value', $legacyTitle)
                ->whereNull('deleted_at')
                ->first();
            if ($translation) {
                $matchedPost = DB::table('posts')
                    ->where('post_id', (int) $translation['table_id'])
                    ->where('status', 'published')
                    ->whereNull('deleted_at')
                    ->first();
                if ($matchedPost && ($matchedPost['type'] ?? '') !== 'page') {
                    return redirect('/analytics/article-details?id=' . (int) $matchedPost['post_id'], 301);
                }
            }
            return redirect('/analytics/articles', 301);
        }

        abort(404);
    }

    public function paginated(string $legacySection, string $page): RedirectResponse
    {
        $section = $this->decode($legacySection);
        if ($section === 'مقاله-ها') {
            return redirect('/analytics/articles', 301);
        }
        abort(404);
    }

    public function category(string $legacyCategory): RedirectResponse
    {
        return redirect('/analytics/articles?category=' . rawurlencode($this->decode($legacyCategory)), 301);
    }

    public function categoryPage(string $legacyCategory, string $page): RedirectResponse
    {
        return $this->category($legacyCategory);
    }

    public function author(string $legacyAuthor): RedirectResponse
    {
        return redirect('/users', 301);
    }

    public function authorPage(string $legacyAuthor, string $page): RedirectResponse
    {
        return $this->author($legacyAuthor);
    }

    private function decode(string $value): string
    {
        return trim(rawurldecode($value), '/');
    }

    private function legacyArticleTitle(string $slug): ?string
    {
        static $slugs;
        if ($slugs === null) {
            $slugs = [];
            $path = base_path('docs/migration/legacy-sornaz-urls.csv');
            $handle = is_file($path) ? fopen($path, 'rb') : false;
            if ($handle !== false) {
                $headers = fgetcsv($handle);
                $headers[0] = trim(ltrim((string) $headers[0], "\xEF\xBB\xBF"), '"');
                while (($values = fgetcsv($handle)) !== false) {
                    $row = array_combine($headers, $values);
                    $source = $row['source'] ?? '';
                    $pathValue = (string) parse_url((string) ($row['old_url'] ?? ''), PHP_URL_PATH);
                    $isSingleDiscoveredPath = $source === 'discovered-link'
                        && substr_count(trim($pathValue, '/'), '/') === 0;
                    if ($source !== 'sitemap:post' && !$isSingleDiscoveredPath) {
                        continue;
                    }
                    $title = preg_replace('/\s+-\s+سُرناز\s*$/u', '', trim((string) ($row['title'] ?? '')));
                    $slugs[trim(rawurldecode($pathValue), '/')] = $title;
                }
                fclose($handle);
            }
        }
        return $slugs[$slug] ?? null;
    }
}
