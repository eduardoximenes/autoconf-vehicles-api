<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar usuários existentes
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('❌ Nenhum usuário encontrado. Execute UserSeeder primeiro.');
            return;
        }

        // Dados específicos para veículos de exemplo
        $vehicleData = [
            [
                'license_plate' => 'ABC1D23',
                'chassis' => '1HGBH41JXMN109186',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'version' => 'XEI 2.0 Flex',
                'sale_price' => 85000.00,
                'color' => 'Prata',
                'km' => 15000,
                'transmission' => 'automatic',
                'fuel_type' => 'flex'
            ],
            [
                'license_plate' => 'DEF2E34',
                'chassis' => '2HGBH41JXMN109187',
                'brand' => 'Honda',
                'model' => 'Civic',
                'version' => 'LX CVT',
                'sale_price' => 95000.00,
                'color' => 'Branco',
                'km' => 8500,
                'transmission' => 'automatic',
                'fuel_type' => 'gasoline'
            ],
            [
                'license_plate' => 'GHI3F45',
                'chassis' => '3HGBH41JXMN109188',
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'version' => 'TSI Highline',
                'sale_price' => 75000.00,
                'color' => 'Preto',
                'km' => 22000,
                'transmission' => 'manual',
                'fuel_type' => 'gasoline'
            ],
            [
                'license_plate' => 'JKL4G56',
                'chassis' => '4HGBH41JXMN109189',
                'brand' => 'Ford',
                'model' => 'Focus',
                'version' => 'SE Plus',
                'sale_price' => 65000.00,
                'color' => 'Azul',
                'km' => 35000,
                'transmission' => 'automatic',
                'fuel_type' => 'flex'
            ],
            [
                'license_plate' => 'MNO5H67',
                'chassis' => '5HGBH41JXMN109190',
                'brand' => 'Chevrolet',
                'model' => 'Onix',
                'version' => 'Premier Turbo',
                'sale_price' => 55000.00,
                'color' => 'Vermelho',
                'km' => 12000,
                'transmission' => 'automatic',
                'fuel_type' => 'flex'
            ],
            [
                'license_plate' => 'PQR6I78',
                'chassis' => '6HGBH41JXMN109191',
                'brand' => 'Hyundai',
                'model' => 'HB20',
                'version' => 'Diamond Plus',
                'sale_price' => 48000.00,
                'color' => 'Cinza',
                'km' => 28000,
                'transmission' => 'manual',
                'fuel_type' => 'flex'
            ],
            [
                'license_plate' => 'STU7J89',
                'chassis' => '7HGBH41JXMN109192',
                'brand' => 'Nissan',
                'model' => 'Sentra',
                'version' => 'Exclusive CVT',
                'sale_price' => 78000.00,
                'color' => 'Branco Perolizado',
                'km' => 18500,
                'transmission' => 'automatic',
                'fuel_type' => 'flex'
            ],
            [
                'license_plate' => 'VWX8K90',
                'chassis' => '8HGBH41JXMN109193',
                'brand' => 'Fiat',
                'model' => 'Argo',
                'version' => 'HGT 1.8',
                'sale_price' => 52000.00,
                'color' => 'Laranja',
                'km' => 41000,
                'transmission' => 'manual',
                'fuel_type' => 'flex'
            ],
            [
                'license_plate' => 'YZA9L01',
                'chassis' => '9HGBH41JXMN109194',
                'brand' => 'Renault',
                'model' => 'Sandero',
                'version' => 'RS 2.0',
                'sale_price' => 46000.00,
                'color' => 'Azul Metálico',
                'km' => 33500,
                'transmission' => 'manual',
                'fuel_type' => 'flex'
            ],
            [
                'license_plate' => 'BCD0M12',
                'chassis' => 'AHGBH41JXMN109195',
                'brand' => 'Jeep',
                'model' => 'Renegade',
                'version' => 'Sport 1.8',
                'sale_price' => 89000.00,
                'color' => 'Verde',
                'km' => 25600,
                'transmission' => 'automatic',
                'fuel_type' => 'flex'
            ],
        ];

        foreach ($vehicleData as $index => $data) {
            // Distribuir veículos entre os usuários
            $user = $users[$index % $users->count()];

            Vehicle::create([
                'license_plate' => $data['license_plate'],
                'chassis' => $data['chassis'],
                'brand' => $data['brand'],
                'model' => $data['model'],
                'version' => $data['version'],
                'sale_price' => $data['sale_price'],
                'color' => $data['color'],
                'km' => $data['km'],
                'transmission' => $data['transmission'],
                'fuel_type' => $data['fuel_type'],
                'user_id' => $user->id,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        $this->command->info('✅ 10 veículos criados com sucesso!');
        $this->command->info('🚗 Veículos distribuídos entre os usuários cadastrados');
    }
}
