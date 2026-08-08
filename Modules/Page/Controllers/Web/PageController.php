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
        ->title('سُرناز | خانه');
    }




    public function aboutUs() {
        return ResponseFactory::view(
            'Page::about-us',
            [
                'about_us' => $this->service->getByPage('about_us'),
                'header' => $this->service->getByPage('header'),
                'footer' => $this->service->getByPage('footer'),
            ]
        )
        ->layout('main')
        ->title('سُرناز | درباره ما');
    }




    public function contactUs() {
        return ResponseFactory::view(
            'Page::contact-us',
            [
                'contact_us' => $this->service->getByPage('contact_us'),
                'header' => $this->service->getByPage('header'),
                'footer' => $this->service->getByPage('footer'),
            ]
        )
        ->layout('main')
        ->title('سُرناز | تماس با ما');
    }


}