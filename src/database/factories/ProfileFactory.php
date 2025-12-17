<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username'      => $this->faker->name,
            'profile_image' => '',
            'postal_code'   => $this->faker->numerify('###-####'),
            'address'       => $this->faker->prefecture . $this->faker->city . $this->faker->streetAddress,
            'building'      => $this->faker->randomElement([
                'テストマンション101',
                'サンプルハイツ202',
                'コーポ山田303',
                '',
            ]),
        ];
    }

    // プローフィール未設定者の挙動確認用
    public function incomplete()
    {
        return $this->state(function () {
            return [
                'postal_code' => '',
                'address'     => '',
                'building'    => '',
            ];
        });
    }
}
