<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['name' => 'Mithun'],
            [
                'email' => 'admin@mithuncards.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        \App\Models\InventoryItem::firstOrCreate(
            ['item_name' => 'Premium Glossy Cardstock'],
            [
                'stock_quantity' => 500,
                'low_stock_threshold' => 100,
                'cost_per_unit' => 0.50,
            ]
        );

        \App\Models\Customer::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'phone' => '1234567890',
            ]
        );
    }
}
