<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verify.guide');
        }

        $profile = $user->profile;

        if (
            ! $profile ||
            empty($profile->username) ||
            empty($profile->postal_code) ||
            empty($profile->address)
        ) {
            return redirect()->route('mypage.profile.edit', ['from' => 'first']);
        }

        return redirect('/');
    }
}
