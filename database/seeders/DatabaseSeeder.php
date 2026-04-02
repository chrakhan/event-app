<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. FIRST create roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Create admin user
        $admin = User::factory()->create([
            'name'  => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        // 3. Create one organizer
        $organizer = User::factory()->create([
            'name'  => 'John Organizer',
            'email' => 'john@example.com',
        ]);
        $organizer->assignRole('organizer');

        // 4. Create 10 random users
        $users = User::factory()->count(10)->create();

        // 5. Give all of them organizer role
        foreach ($users as $user) {
            $user->assignRole('organizer');
        }

        // 6. Create events linked to organizer
        Event::factory()->count(10)->create([
            'user_id' => $organizer->id
        ]);
    }
}
