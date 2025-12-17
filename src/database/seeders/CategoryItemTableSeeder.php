<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_item')->insert([
            // 腕時計：1 と 5
            ['item_id' => 1, 'category_id' => 1],
            ['item_id' => 1, 'category_id' => 5],

            // HDD
            ['item_id' => 2, 'category_id' => 2],

            // 玉ねぎ3束
            ['item_id' => 3, 'category_id' => 10],

            // 革靴
            ['item_id' => 4, 'category_id' => 1],

            // ノートPC：2 と 8
            ['item_id' => 5, 'category_id' => 2],
            ['item_id' => 5, 'category_id' => 8],

            // マイク
            ['item_id' => 6, 'category_id' => 2],

            // ショルダーバッグ
            ['item_id' => 7, 'category_id' => 1],

            // タンブラー
            ['item_id' => 8, 'category_id' => 10],

            // コーヒーミル：2 と 10
            ['item_id' => 9, 'category_id' => 2],
            ['item_id' => 9, 'category_id' => 10],

            // メイクセット
            ['item_id' => 10, 'category_id' => 6],
        ]);
    }
}
