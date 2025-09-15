<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seed da base de dados...');
        $this->command->newLine();

        // Executar seeders na ordem correta
        $this->call([
            UserSeeder::class,
            VehicleSeeder::class,
            VehicleImageSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🎉 Seed concluído com sucesso!');
        $this->command->info('🔗 Acesse: http://localhost:8000/api/v1/vehicles');
        $this->command->info('📚 Documentação: http://localhost:8000/api/documentation');
    }
}
