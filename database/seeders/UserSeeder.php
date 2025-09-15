<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@autoconf.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Criar usuário comum
        User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@autoconf.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        User::factory()->create([
            'name' => 'Eduardo Ximenes',
            'email' => 'ximas@autoconf.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Criar mais alguns usuários comuns
        User::factory()->count(3)->create([
            'is_admin' => false,
        ]);

        $this->command->info('✅ Usuários criados com sucesso!');
        $this->command->info('📧 Admin: admin@autoconf.com | Senha: password');
        $this->command->info('👤 Usuário: joao@autoconf.com | Senha: password');
        $this->command->info('👤 Usuário: ximas@autoconf.com | Senha: password');
    }
}
