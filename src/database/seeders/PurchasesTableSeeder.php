<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchases')->insert([
            [
                'user_id' => '1',
                'item_id' => '2',
                'payment_method' => 'convenience',
                'postal_code'  => '222-3333',
                'address'      => '神奈川県',
                'building'     => '',
            ],
        ]);
    }
}
