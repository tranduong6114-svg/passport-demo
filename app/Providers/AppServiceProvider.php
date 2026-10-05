<?php

namespace App\Providers;

use App\Bridge\AccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Contracts\AuthorizationViewResponse;
use Laravel\Passport\Http\Responses\SimpleViewResponse;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Passport::useAccessTokenEntity(AccessToken::class);

        $this->app->singleton(AuthorizationViewResponse::class, function () {
            return new SimpleViewResponse(fn($params) => response('<h1>OAuth2 Authorization</h1>'));
        });

        Passport::enablePasswordGrant();

        Passport::tokensExpireIn(now()->addMinutes(1));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addDays(15));

        Passport::tokensCan([
            'read-tasks'  => 'Đọc danh sách tasks',
            'write-tasks' => 'Tạo, sửa, xóa tasks',
            'admin'       => 'Quyền quản trị',
        ]);

        Passport::setDefaultScope(['read-tasks']);
    }
}