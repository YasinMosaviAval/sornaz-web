<?php

namespace Modules\System\Providers;

use Modules\System\Events\UserCreated;
use Modules\System\Listeners\SendWelcomeEmail;
use Modules\System\Listeners\CreateTranslationRecord;
use Modules\System\Listeners\WriteAuditLog;
use Modules\System\Models\User;
use Modules\System\Observers\UserObserver;
use Core\Providers\ServiceProvider;

class EventServiceProvider extends ServiceProvider {


    /**
     * Event => Listeners
     */
    protected array $listen = [
        UserCreated::class => [
            SendWelcomeEmail::class,
            CreateTranslationRecord::class,
            WriteAuditLog::class
        ],
    ];


    /**
     * Model => Observer
     */
    protected array $observers = [
        User::class => UserObserver::class,
        //     Role::class => RoleObserver::class,
        //     Post::class => PostObserver::class,
    ];


    /**
     * Event Subscribers
     */
    protected array $subscribers = [
        // UserSubscriber::class,
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
        User::observe(UserObserver::class);
    }

}