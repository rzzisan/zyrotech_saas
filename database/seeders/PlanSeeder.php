<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
use Illuminate\Support\Facades\Schema; // <-- এই লাইনটি যোগ করা হয়েছে

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // *** মূল পরিবর্তনটি এখানে ***
        Schema::disableForeignKeyConstraints(); // Foreign key পরীক্ষা বন্ধ করা হলো
        Plan::truncate(); // এখন টেবিলটি সফলভাবে truncate হবে
        Schema::enableForeignKeyConstraints();  // Foreign key পরীক্ষা আবার চালু করা হলো

        Plan::create([
            'name' => 'Free',
            'price' => 0.00,
            'daily_courier_limit' => 2000,
            'monthly_courier_limit' => 500,
            'monthly_incomplete_order_limit' => 100,
        ]);

        Plan::create([
            'name' => 'Startup',
            'price' => 0.00,
            'daily_courier_limit' => 100,
            'monthly_courier_limit' => 3000,
            'monthly_incomplete_order_limit' => 500,
        ]);
    }
}