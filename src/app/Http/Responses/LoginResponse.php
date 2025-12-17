<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        // プロフィール未設定なら強制遷移
        if (
            !$profile ||
            empty($profile->username) ||
            empty($profile->postal_code) ||
            empty($profile->address)
        ) {
            return redirect('/mypage/profile');
        }

        // 正常時の遷移先
        return redirect('/');
    }
}
