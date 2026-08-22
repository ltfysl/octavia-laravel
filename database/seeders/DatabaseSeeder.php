<?php

namespace Database\Seeders;

use App\Actions\CreateStarterContent;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Demo account for local development only. Never run against
     * production: it creates a known-password user.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('DatabaseSeeder skipped in production.');

            return;
        }

        $demo = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@octavia.local',
            'onboarded_at' => now(),
        ]);

        CreateStarterContent::for($demo);
    }
}
