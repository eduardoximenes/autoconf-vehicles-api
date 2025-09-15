<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'license_plate' => $this->generateBrazilianLicensePlate(),
            'chassis' => $this->generateValidVIN(),
            'brand' => fake()->randomElement(['Toyota', 'Honda', 'Volkswagen', 'Ford', 'Chevrolet', 'Hyundai', 'Nissan']),
            'model' => fake()->randomElement(['Corolla', 'Civic', 'Golf', 'Focus', 'Onix', 'HB20', 'Sentra']),
            'version' => fake()->randomElement(['LX', 'EX', 'LXS', 'EXL', 'Base', 'Premium', 'Sport']),
            'sale_price' => fake()->randomFloat(2, 15000, 150000),
            'color' => fake()->randomElement(['Branco', 'Preto', 'Prata', 'Vermelho', 'Azul', 'Cinza']),
            'km' => fake()->numberBetween(0, 200000),
            'transmission' => fake()->randomElement(['manual', 'automatic']),
            'fuel_type' => fake()->randomElement(['gasoline', 'ethanol', 'flex', 'diesel', 'hybrid', 'electric']),
            'user_id' => User::factory(),
            'created_by' => function (array $attributes) {
                return $attributes['user_id'];
            },
            'updated_by' => function (array $attributes) {
                return $attributes['user_id'];
            },
        ];
    }

    /**
     * Generate a valid Brazilian license plate
     */
    private function generateBrazilianLicensePlate(): string
    {
        // Formato brasileiro: ABC1D23 ou ABC1234
        $letters = fake()->lexify('???');
        $numbers = fake()->numerify('####');

        // 50% chance para formato antigo (ABC1234) ou novo (ABC1D23)
        if (fake()->boolean()) {
            // Formato novo: ABC1D23
            return strtoupper($letters . substr($numbers, 0, 1) . fake()->randomLetter() . substr($numbers, 1, 2));
        } else {
            // Formato antigo: ABC1234
            return strtoupper($letters . $numbers);
        }
    }

    /**
     * Generate a valid VIN (Vehicle Identification Number)
     */
    private function generateValidVIN(): string
    {
        // VIN válido tem 17 caracteres e não pode conter I, O, Q
        $validChars = '0123456789ABCDEFGHJKLMNPRSTUVWXYZ';
        $vin = '';

        for ($i = 0; $i < 17; $i++) {
            $vin .= $validChars[array_rand(str_split($validChars))];
        }

        return $vin;
    }

    /**
     * Indicate that the vehicle belongs to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

}
