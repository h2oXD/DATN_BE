<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.components.notification', function ($view) { // Thay 'layouts.header' bằng tên view header của bạn
            if (Auth::check()) {
                $notifications = Auth::user()->notifications;
                $view->with('notifications', $notifications);
            } else {
                $view->with('notifications', collect()); // Truyền một collection rỗng nếu người dùng chưa đăng nhập
            }

        });
    }
}
