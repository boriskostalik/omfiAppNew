<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Institution;
use App\Models\Author;
use App\Models\Publication;
use App\Models\Topic;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
            Institution::factory()->count(5)->create();
            Author::factory()->count(10)->create();
            Publication::factory()->count(20)->create();
            Topic::factory()->count(10)->create();
            User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('testtest'),
        ]);
    }
}
