<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition()
    {
        return [
            'username'      => $this->faker->name,
            'profile_image' => $this->faker->randomElement([
                'profile_images/banana.png',
                'profile_images/grapes.png',
            ]),
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

    public function incomplete()
    {
        return $this->state(function () {
            return [
                'profile_image' => '',
                'postal_code' => '',
                'address'     => '',
                'building'    => '',
            ];
        });
    }
}
