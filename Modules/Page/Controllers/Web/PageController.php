<?php

namespace Modules\Page\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Page\Services\PageService;


class PageController {


    public function __construct(protected PageService $service) {
    }

    public function home() {
        return ResponseFactory::view(
            'Page::home',
            [
                'home' => $this->service->getByPage('home'),
                'header' => $this->service->getByPage('header'),
                'footer' => $this->service->getByPage('footer'),
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
