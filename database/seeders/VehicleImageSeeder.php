<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VehicleImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = Vehicle::all();

        if ($vehicles->isEmpty()) {
            $this->command->error('❌ Nenhum veículo encontrado. Execute VehicleSeeder primeiro.');
            return;
        }

        // URLs de imagens placeholder de carros (usando picsum.photos)
        $placeholderImages = [
            'https://picsum.photos/800/600?random=1',
            'https://picsum.photos/800/600?random=2',
            'https://picsum.photos/800/600?random=3',
            'https://picsum.photos/800/600?random=4',
            'https://picsum.photos/800/600?random=5',
        ];

        foreach ($vehicles as $index => $vehicle) {
            // Criar diretório para o veículo se não existir
            $vehicleDir = "vehicles/{$vehicle->id}";
            Storage::disk('public')->makeDirectory($vehicleDir);

            // Número aleatório de imagens por veículo (2-4 imagens)
            $numImages = rand(2, 4);

            for ($i = 0; $i < $numImages; $i++) {
                // Gerar nome único para a imagem
                $filename = time() . '_' . uniqid() . '_' . ($i + 1) . '.jpg';
                $path = "{$vehicleDir}/{$filename}";

                try {
                    // Baixar imagem placeholder e salvar
                    $imageUrl = $placeholderImages[array_rand($placeholderImages)];
                    $imageContent = @file_get_contents($imageUrl);

                    if ($imageContent !== false) {
                        Storage::disk('public')->put($path, $imageContent);

                        // Criar registro no banco
                        VehicleImage::create([
                            'vehicle_id' => $vehicle->id,
                            'path' => $path,
                            'is_cover' => $i === 0, // Primeira imagem é sempre capa
                        ]);

                        $this->command->info("📸 Imagem " . ($i + 1) . " criada para {$vehicle->brand} {$vehicle->model}");
                    } else {
                        // Fallback: criar arquivo placeholder local
                        $this->createPlaceholderFile($path, $vehicle);

                        VehicleImage::create([
                            'vehicle_id' => $vehicle->id,
                            'path' => $path,
                            'is_cover' => $i === 0,
                        ]);

                        $this->command->info("📷 Placeholder " . ($i + 1) . " criado para {$vehicle->brand} {$vehicle->model}");
                    }
                } catch (\Exception $e) {
                    // Em caso de erro, criar placeholder local
                    $this->createPlaceholderFile($path, $vehicle);

                    VehicleImage::create([
                        'vehicle_id' => $vehicle->id,
                        'path' => $path,
                        'is_cover' => $i === 0,
                    ]);

                    $this->command->info("🖼️  Placeholder local " . ($i + 1) . " criado para {$vehicle->brand} {$vehicle->model}");
                }

                // Pequena pausa para evitar rate limiting
                usleep(200000); // 0.2 segundos
            }
        }

        $this->command->info('✅ Imagens placeholder criadas para todos os veículos!');
        $this->command->info('🎨 Cada veículo tem entre 2-4 imagens com uma definida como capa');
    }

    /**
     * Criar arquivo placeholder local quando não conseguir baixar imagem
     */
    private function createPlaceholderFile(string $path, Vehicle $vehicle): void
    {
        // Criar um SVG simples como placeholder
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="800" height="600" xmlns="http://www.w3.org/2000/svg">
  <rect width="800" height="600" fill="#f0f0f0"/>
  <rect x="50" y="50" width="700" height="500" fill="#e0e0e0" stroke="#ccc" stroke-width="2"/>
  <text x="400" y="280" text-anchor="middle" font-family="Arial, sans-serif" font-size="24" fill="#666">
    ' . $vehicle->brand . ' ' . $vehicle->model . '
  </text>
  <text x="400" y="320" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" fill="#888">
    Imagem Placeholder
  </text>
  <text x="400" y="350" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" fill="#aaa">
    ' . $vehicle->license_plate . '
  </text>
</svg>';

        Storage::disk('public')->put($path, $svg);
    }
}
