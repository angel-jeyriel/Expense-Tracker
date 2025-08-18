<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'Food',
            'Transport',
            'Utility',
            'Entertainment',
        ];

        foreach ($defaults as $name) {
            Category::firstOrCreate([
                'name' => $name,
                'user_id' => null,
            ]);
        }
    }
}
