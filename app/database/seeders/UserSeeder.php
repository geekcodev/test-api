<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->warn('Начинаю создавать пользователей...');
        User::factory(10)->create();
        $this->command->info('✓ Пользователи успешно созданы');
    }
}
