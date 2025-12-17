<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;


class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input)
    {
        app(RegisterRequest::class)->validateResolved();

        // users 作成
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // プロフィール自動作成
        $user->profile()->create([
            'username' => $input['name'],
            'profile_image' => '',
            'postal_code' => '',
            'address' => '',
            'building' => '',
        ]);

        return $user;
    }
}
