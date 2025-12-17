<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Responses\LoginResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\RegisterResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;




use App\Models\User;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        // 会員登録処理
        Fortify::createUsersUsing(CreateNewUser::class);

        // 会員登録画面
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログイン画面
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログインのレート制限
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->email . $request->ip());
        });

        // LoginRequestにすり替え
        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);

        // ログインエラーメッセージ
        Fortify::authenticateUsing(function ($request) {

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'login_error' => ['ログイン情報が登録されていません'],
                ]);
            }

            return $user;
        });

        // ログイン後の遷移先をカスタム
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        // 会員登録後の遷移先をカスタム
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
    }
}
