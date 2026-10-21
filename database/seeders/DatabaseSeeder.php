<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate([
            'code' => 'MAIN',
        ], [
            'name' => 'The Pump Bar - Main Branch',
            'address' => '123 Fitness Avenue, Makati',
            'phone' => '+63 912 345 6789',
            'status' => 'active',
        ]);

        $owner = User::firstOrCreate(
            ['email' => 'owner@thepumpbar.com'],
            [
                'name' => 'Owner Admin',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@thepumpbar.com'],
            [
                'name' => 'Branch Manager',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'cashier@thepumpbar.com'],
            [
                'name' => 'Cashier Staff',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );

        $ingredients = [
            ['name' => 'Protein Powder', 'unit' => 'grams', 'cost_per_unit' => 6.50],
            ['name' => 'Milk', 'unit' => 'ml', 'cost_per_unit' => 0.55],
            ['name' => 'Banana', 'unit' => 'pieces', 'cost_per_unit' => 2.00],
            ['name' => 'Peanut Butter', 'unit' => 'grams', 'cost_per_unit' => 4.20],
            ['name' => 'Cups', 'unit' => 'pieces', 'cost_per_unit' => 1.20],
            ['name' => 'Straws', 'unit' => 'pieces', 'cost_per_unit' => 0.20],
        ];

        foreach ($ingredients as $ingredient) {
            $item = Ingredient::firstOrCreate(
                ['name' => $ingredient['name']],
                $ingredient
            );

            Inventory::firstOrCreate(
                ['branch_id' => $branch->id, 'ingredient_id' => $item->id],
                [
                    'quantity' => 100,
                    'reorder_level' => 20,
                    'last_stocked_at' => now(),
                ]
            );
        }

        $products = [
            ['name' => 'Chocolate Power Shake', 'category' => 'Protein Shake', 'size' => '500ml', 'flavor' => 'Chocolate', 'price' => 180, 'description' => 'Best for post-workout recovery'],
            ['name' => 'Vanilla Lean Shake', 'category' => 'Protein Shake', 'size' => '500ml', 'flavor' => 'Vanilla', 'price' => 175, 'description' => 'Smooth and light'],
            ['name' => 'Strawberry Boost', 'category' => 'Protein Shake', 'size' => '500ml', 'flavor' => 'Strawberry', 'price' => 185, 'description' => 'Fruit-forward and refreshing'],
            ['name' => 'Banana Recovery Shake', 'category' => 'Protein Shake', 'size' => '500ml', 'flavor' => 'Banana', 'price' => 190, 'description' => 'High-energy banana blend'],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
