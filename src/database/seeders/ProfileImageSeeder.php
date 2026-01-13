<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProfileImageSeeder extends Seeder
{
    public function run(): void
    {
        $files = ['banana.png', 'grapes.png'];

        foreach ($files as $file) {
            $from = public_path('images/' . $file);
            $to   = 'profile_images/' . $file;
            if (!file_exists($from)) {
                continue;
            }
            if (Storage::disk('public')->exists($to)) {
                continue;
            }
            Storage::disk('public')->put($to, file_get_contents($from));
        }
    }
}
