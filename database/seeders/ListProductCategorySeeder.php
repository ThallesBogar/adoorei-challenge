<?php

namespace Database\Seeders;

use App\Models\ListProductCategory;
use Illuminate\Database\Seeder;

class ListProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() : void
    {
        ListProductCategory::create([
            'id' => ListProductCategory::CELLPHONE,
            'name' => 'Cellphones',
        ]);

        ListProductCategory::create([
            'id' => ListProductCategory::SOME_OTHER_CATEGORY,
            'name' => 'Some other category',
        ]);
    }
}
