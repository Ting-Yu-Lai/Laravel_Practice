<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'name'         => '美式咖啡',
                'description'  => '經典黑咖啡，口感純粹，適合喜歡原味的咖啡愛好者。',
                'price'        => 80.00,
                'stock'        => 50,
                'is_available' => true,
            ],
            [
                'name'         => '拿鐵咖啡',
                'description'  => '義式濃縮搭配綿密奶泡，香醇順口。',
                'price'        => 120.00,
                'stock'        => 40,
                'is_available' => true,
            ],
            [
                'name'         => '卡布奇諾',
                'description'  => '等比例濃縮、蒸奶與奶泡，層次分明。',
                'price'        => 120.00,
                'stock'        => 30,
                'is_available' => true,
            ],
            [
                'name'         => '摩卡咖啡',
                'description'  => '濃縮咖啡加入巧克力醬與牛奶，甜而不膩。',
                'price'        => 140.00,
                'stock'        => 25,
                'is_available' => true,
            ],
            [
                'name'         => '焦糖瑪奇朵',
                'description'  => '香草糖漿、牛奶與濃縮咖啡，頂層淋上焦糖。',
                'price'        => 150.00,
                'stock'        => 20,
                'is_available' => true,
            ],
            [
                'name'         => '抹茶拿鐵',
                'description'  => '日式有機抹茶粉與鮮奶完美融合，清新回甘。',
                'price'        => 130.00,
                'stock'        => 15,
                'is_available' => true,
            ],
            [
                'name'         => '季節限定冰淇淋咖啡',
                'description'  => '義式濃縮澆淋香草冰淇淋，僅夏季供應。',
                'price'        => 160.00,
                'stock'        => 0,
                'is_available' => false,
            ],
        ];

        DB::table('menus')->insert(
            array_map(fn($menu) => array_merge($menu, [
                'created_at' => now(),
                'updated_at' => now(),
            ]), $menus)
        );
    }
}
