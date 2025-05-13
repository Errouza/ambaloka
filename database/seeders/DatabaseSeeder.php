<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create sample flights
        $flights = [
            [
                'airline' => 'Garuda Indonesia',
                'flight_number' => 'GA-123',
                'departure_city' => 'Jakarta (CGK)',
                'arrival_city' => 'Bali (DPS)',
                'departure_time' => '2025-05-14 08:00:00',
                'arrival_time' => '2025-05-14 10:30:00',
                'price' => 1200000,
            ],
            [
                'airline' => 'Lion Air',
                'flight_number' => 'JT-456',
                'departure_city' => 'Jakarta (CGK)',
                'arrival_city' => 'Surabaya (SUB)',
                'departure_time' => '2025-05-14 09:30:00',
                'arrival_time' => '2025-05-14 11:00:00',
                'price' => 800000,
            ],
            [
                'airline' => 'Air Asia',
                'flight_number' => 'QZ-789',
                'departure_city' => 'Jakarta (CGK)',
                'arrival_city' => 'Yogyakarta (JOG)',
                'departure_time' => '2025-05-14 10:15:00',
                'arrival_time' => '2025-05-14 11:30:00',
                'price' => 650000,
            ],
        ];

        foreach ($flights as $flight) {
            \App\Models\Flight::create($flight);
        }
    }
}
