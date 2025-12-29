<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'role' => 'editor',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Alice Brown',
                'email' => 'alice@example.com',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Charlie Davis',
                'email' => 'charlie@example.com',
                'role' => 'moderator',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Emma Johnson',
                'email' => 'emma@example.com',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Michael Lee',
                'email' => 'michael@example.com',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Sarah Miller',
                'email' => 'sarah@example.com',
                'role' => 'editor',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'David Garcia',
                'email' => 'david@example.com',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa@example.com',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'James Taylor',
                'email' => 'james@example.com',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Emily White',
                'email' => 'emily@example.com',
                'role' => 'user',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
