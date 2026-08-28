<?php

namespace Modules\Page\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Page\Services\PageService;
use Modules\Academy\Services\AcademyRegistrationService;


class PageController {


    public function __construct(protected PageService $service, protected AcademyRegistrationService $academies) {
    }

    public function home() {
        return ResponseFactory::view(
            'Page::home',
            [
                'home' => $this->service->getByPage('home'),
                'header' => $this->service->getByPage('header'),
                'footer' => $this->service->getByPage('footer'),
                'academySearchOptions' => $this->academies->searchOptions(),
            ]
        )
        ->layout('main')
        ->title(trans('public.meta.home', 'سُرناز | خانه'));
    }




    public function aboutUs() {
        return ResponseFactory::view('Page::about-us')
        ->layout('main')
        ->title(trans('public.meta.about', 'سُرناز | درباره ما'));
    }




    public function contactUs() {
        return ResponseFactory::view('Page::contact-us')
        ->layout('main')
        ->title(trans('public.meta.contact', 'سُرناز | تماس با ما'));
    }


}
