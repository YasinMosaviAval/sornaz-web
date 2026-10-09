<?php

namespace Modules\Page\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyRegistrationService;
use Modules\Analytics\Services\PublicPostService;
use Modules\Page\Services\PageService;

class PageController
{

    public function __construct(private PageService $pages, private AcademyRegistrationService $academies, private PublicPostService $posts) {}

    /**
     * GET /api/pages
     */
    public function index()
    {
        return $this->home();
    }

    /**
     * GET /api/pages/{id}
     */
    public function show(string $id)
    {
        return match ($id) {
            'home' => $this->home(),
            'about-us' => $this->about(),
            'contact-us' => $this->contact(),
            default => ResponseFactory::json(['message' => 'Page not found.'], 404),
        };
    }

    public function home()
    {
        $locale = locale();
        return ResponseFactory::json(['data' => [
            'home' => $this->pages->getByPage('home'),
            'header' => $this->pages->getByPage('header'),
            'footer' => $this->pages->getByPage('footer'),
            'academySearchOptions' => $this->academies->searchOptions(),
            'homeStatistics' => $this->pages->homeStatistics(),
            'activityOverviewHtml' => $this->pages->activityOverviewHtml($locale),
            'homeSearchSelectLabels' => $this->pages->homeSearchSelectLabels($locale),
            'latestArticles' => $this->posts->latest($locale, 3),
            'homeLearningPath' => $this->pages->homeLearningPath($locale),
        ]]);
    }

    public function about()
    {
        $sections = [];
        for ($item = 1; $item <= 8; $item++) {
            $sections[] = ['title' => trans('public.about.section_' . $item . '.title'), 'description' => trans('public.about.section_' . $item . '.description')];
        }
        return ResponseFactory::json(['data' => ['title' => trans('public.about.title', 'درباره برنامه موسیقی سُرناز'), 'description' => trans('public.about.description'), 'sections' => $sections]]);
    }

    public function contact()
    {
        return ResponseFactory::json(['data' => ['title' => trans('public.contact.title', 'ارتباط با ما'), 'note' => trans('public.contact.note'), 'fields' => ['name', 'email', 'subject', 'message'], 'submit' => '/contact']]);
    }

    /**
     * POST /api/pages
     */
    public function store()
    {
        return $this->unsupported();
    }

    /**
     * PUT /api/pages/{id}
     */
    public function update(string $id)
    {
        return $this->unsupported();
    }

    /**
     * DELETE /api/pages/{id}
     */
    public function destroy(string $id)
    {
        return $this->unsupported();
    }

    private function unsupported()
    {
        return ResponseFactory::json(['message' => 'Method not allowed.'], 405);
    }
}
