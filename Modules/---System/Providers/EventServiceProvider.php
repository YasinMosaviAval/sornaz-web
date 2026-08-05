<?php

namespace Modules\System\Providers;

use Modules\System\Events\UserCreated;
use Modules\System\Listeners\SendWelcomeEmail;
use Modules\System\Listeners\CreateTranslationRecord;
use Modules\System\Listeners\WriteAuditLog;

use Modules\System\Observers\UserObserver;
use Core\providers\ServiceProvider;
use Modules\System\Models\UserModel;

class EventServiceProvider extends ServiceProvider {


    protected array $listen = [
        UserCreated::class => [
            SendWelcomeEmail::class,
            CreateTranslationRecord::class,
            WriteAuditLog::class
        ],
    ];


    protected array $observers = [
        UserModel::class => UserObserver::class,
    ];


    protected array $subscribers = [
    ];


    public function register(): void {
        $this->registerListeners();
        $this->registerObservers();
        $this->registerSubscribers();
    }


    protected function registerListeners(): void {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                events()->listen($event, $listener);
            }
        }
    }


    protected function registerObservers(): void {
        foreach ($this->observers as $model => $observer) {
            $model::observe($observer);
        }
    }


    protected function registerSubscribers(): void {
        foreach ($this->subscribers as $subscriber) {
            if (method_exists($subscriber, 'subscribe')) {
                (new $subscriber)->subscribe(events());
            }
        }
    }


    public function boot(): void {
        UserModel::observe(UserObserver::class);
    }

}