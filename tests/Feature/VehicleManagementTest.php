<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VehicleManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $admin;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->otherUser = User::factory()->create(['is_admin' => false]);
    }

    #[Test]
    public function authenticated_user_can_create_vehicle_with_valid_data()
    {
        Sanctum::actingAs($this->user);

        $vehicleData = [
            'license_plate' => 'ABC1D23',
            'chassis' => '1HGBH41JXMN109186',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => 'XEI 2.0',
            'sale_price' => 45000.50,
            'color' => 'Prata',
            'km' => 25000,
            'transmission' => 'automatic',
            'fuel_type' => 'flex'
        ];

        $response = $this->postJson('/api/v1/vehicles', $vehicleData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Veículo criado com sucesso'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'license_plate',
                    'chassis',
                    'brand',
                    'model',
                    'version',
                    'sale_price',
                    'color',
                    'km',
                    'transmission',
                    'fuel_type',
                    'user_id',
                    'created_by',
                    'updated_by',
                    'created_at',
                    'updated_at'
                ]
            ]);

        // Verificar se os dados foram salvos corretamente no banco
        $this->assertDatabaseHas('vehicles', [
            'license_plate' => 'ABC1D23',
            'chassis' => '1HGBH41JXMN109186',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => 'XEI 2.0',
            'sale_price' => 45000.50,
            'color' => 'Prata',
            'km' => 25000,
            'transmission' => 'automatic',
            'fuel_type' => 'flex',
            'user_id' => $this->user->id,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_create_vehicle()
    {
        $vehicleData = [
            'license_plate' => 'ABC1D23',
            'chassis' => '1HGBH41JXMN109186',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => 'XEI 2.0',
            'sale_price' => 45000.50,
            'color' => 'Prata',
            'km' => 25000,
            'transmission' => 'automatic',
            'fuel_type' => 'flex'
        ];

        $response = $this->postJson('/api/v1/vehicles', $vehicleData);

        $response->assertStatus(401);
        $this->assertDatabaseCount('vehicles', 0);
    }

    #[Test]
    public function license_plate_must_follow_brazilian_format()
    {
        Sanctum::actingAs($this->user);

        $invalidPlates = [
            'ABC123',      // Muito curta
            'ABCD1234',    // Muito longa
            '1234567',     // Só números
            'ABCDEFG',     // Só letras
            'AB1C234',     // Formato inválido
            'ABC12345',    // Muito longa
            'abc1d23',     // Minúscula (deve aceitar e converter)
        ];

        foreach ($invalidPlates as $plate) {
            if ($plate === 'abc1d23') {
                // Passa e converte pra maiúsculo
                continue;
            }

            $response = $this->postJson('/api/v1/vehicles', [
                'license_plate' => $plate,
                'chassis' => '1HGBH41JXMN109186',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'version' => 'XEI 2.0',
                'sale_price' => 45000.50,
                'color' => 'Prata',
                'km' => 25000,
                'transmission' => 'automatic',
                'fuel_type' => 'flex'
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['license_plate']);
        }

        // Testa que minúsculas são aceitas e convertidas
        $response = $this->postJson('/api/v1/vehicles', [
            'license_plate' => 'abc1d23',
            'chassis' => '1hgbh41jxmn109186',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => 'XEI 2.0',
            'sale_price' => 45000.50,
            'color' => 'Prata',
            'km' => 25000,
            'transmission' => 'automatic',
            'fuel_type' => 'flex'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('vehicles', [
            'license_plate' => 'ABC1D23',
            'chassis' => '1HGBH41JXMN109186',
        ]);
    }

    #[Test]
    public function chassis_must_be_valid_vin()
    {
        Sanctum::actingAs($this->user);

        $invalidChassis = [
            '1234567890123456',    // 16 caracteres (deve ter 17)
            '123456789012345678',  // 18 caracteres (deve ter 17)
            '1HGBH41JXMN10918I',   // Contém 'I' (inválido no VIN)
            '1HGBH41JXMN10918O',   // Contém 'O' (inválido no VIN)
            '1HGBH41JXMN10918Q',   // Contém 'Q' (inválido no VIN)
            '1HGBH41JXMN109-86',   // Contém hífen (inválido)
        ];

        foreach ($invalidChassis as $chassis) {
            $response = $this->postJson('/api/v1/vehicles', [
                'license_plate' => 'ABC1D23',
                'chassis' => $chassis,
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'version' => 'XEI 2.0',
                'sale_price' => 45000.50,
                'color' => 'Prata',
                'km' => 25000,
                'transmission' => 'automatic',
                'fuel_type' => 'flex'
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['chassis']);
        }
    }

    #[Test]
    public function license_plate_must_be_unique()
    {
        Sanctum::actingAs($this->user);

        // Cria primeiro veículo
        Vehicle::factory()->create([
            'license_plate' => 'ABC1D23',
            'user_id' => $this->user->id
        ]);

        // Tenta criar segundo veículo com mesma placa
        $response = $this->postJson('/api/v1/vehicles', [
            'license_plate' => 'ABC1D23',
            'chassis' => '1HGBH41JXMN109187',
            'brand' => 'Honda',
            'model' => 'Civic',
            'version' => 'LX',
            'sale_price' => 50000.00,
            'color' => 'Preto',
            'km' => 15000,
            'transmission' => 'manual',
            'fuel_type' => 'gasoline'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['license_plate']);
    }

    #[Test]
    public function chassis_must_be_unique()
    {
        Sanctum::actingAs($this->user);

        // Cria primeiro veículo
        Vehicle::factory()->create([
            'chassis' => '1HGBH41JXMN109186',
            'user_id' => $this->user->id
        ]);

        // Tenta criar segundo veículo com mesmo chassis
        $response = $this->postJson('/api/v1/vehicles', [
            'license_plate' => 'DEF2E34',
            'chassis' => '1HGBH41JXMN109186',
            'brand' => 'Honda',
            'model' => 'Civic',
            'version' => 'LX',
            'sale_price' => 50000.00,
            'color' => 'Preto',
            'km' => 15000,
            'transmission' => 'manual',
            'fuel_type' => 'gasoline'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['chassis']);
    }

    #[Test]
    public function only_vehicle_owner_or_admin_can_update_vehicle()
    {
        $vehicle = Vehicle::factory()->forUser($this->user)->create();

        $updateData = ['brand' => 'Honda Updated'];

        // Proprietário pode atualizar
        Sanctum::actingAs($this->user);
        $response = $this->putJson("/api/v1/vehicles/{$vehicle->id}", $updateData);
        $response->assertStatus(200);

        // Admin pode atualizar
        Sanctum::actingAs($this->admin);
        $response = $this->putJson("/api/v1/vehicles/{$vehicle->id}", $updateData);
        $response->assertStatus(200);

        // Outro usuário não pode atualizar
        Sanctum::actingAs($this->otherUser);
        $response = $this->putJson("/api/v1/vehicles/{$vehicle->id}", $updateData);
        $response->assertStatus(403);
    }

    #[Test]
    public function only_vehicle_owner_or_admin_can_delete_vehicle()
    {
        $vehicle = Vehicle::factory()->forUser($this->user)->create();
        $vehicleId = $vehicle->id;

        // Outro usuário não pode deletar
        Sanctum::actingAs($this->otherUser);
        $response = $this->deleteJson("/api/v1/vehicles/{$vehicleId}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('vehicles', ['id' => $vehicleId]);

        // Proprietário pode deletar
        Sanctum::actingAs($this->user);
        $response = $this->deleteJson("/api/v1/vehicles/{$vehicleId}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicleId]);

        // Criar novo veículo para testar admin
        $vehicle2 = Vehicle::factory()->forUser($this->user)->create();
        $vehicle2Id = $vehicle2->id;

        // Admin pode deletar
        Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/v1/vehicles/{$vehicle2Id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle2Id]);
    }

    #[Test]
    public function vehicle_creation_validates_required_fields()
    {
        Sanctum::actingAs($this->user);

        $requiredFields = [
            'license_plate',
            'chassis',
            'brand',
            'model',
            'version',
            'sale_price',
            'color',
            'km',
            'transmission',
            'fuel_type'
        ];

        foreach ($requiredFields as $field) {
            $vehicleData = [
                'license_plate' => 'ABC1D23',
                'chassis' => '1HGBH41JXMN109186',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'version' => 'XEI 2.0',
                'sale_price' => 45000.50,
                'color' => 'Prata',
                'km' => 25000,
                'transmission' => 'automatic',
                'fuel_type' => 'flex'
            ];

            // Remove o campo obrigatório
            unset($vehicleData[$field]);

            $response = $this->postJson('/api/v1/vehicles', $vehicleData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors([$field]);
        }
    }

    #[Test]
    public function vehicle_creation_validates_enum_fields()
    {
        Sanctum::actingAs($this->user);

        // Testa transmission inválida
        $response = $this->postJson('/api/v1/vehicles', [
            'license_plate' => 'ABC1D23',
            'chassis' => '1HGBH41JXMN109186',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => 'XEI 2.0',
            'sale_price' => 45000.50,
            'color' => 'Prata',
            'km' => 25000,
            'transmission' => 'invalid_transmission',
            'fuel_type' => 'flex'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['transmission']);

        // Testa fuel_type inválido
        $response = $this->postJson('/api/v1/vehicles', [
            'license_plate' => 'ABC1D23',
            'chassis' => '1HGBH41JXMN109186',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => 'XEI 2.0',
            'sale_price' => 45000.50,
            'color' => 'Prata',
            'km' => 25000,
            'transmission' => 'automatic',
            'fuel_type' => 'invalid_fuel'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fuel_type']);
    }

    #[Test]
    public function authenticated_user_can_list_vehicles_with_pagination()
    {
        Sanctum::actingAs($this->user);

        // Cria alguns veículos
        Vehicle::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/vehicles?per_page=5&page=1');


        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'license_plate',
                        'brand',
                        'model',
                        'sale_price',
                        'created_at'
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page'
                ]
            ]);

        $responseData = $response->json();
        $this->assertEquals(5, count($responseData['data']));
        $this->assertEquals(1, $responseData['pagination']['current_page']);
        $this->assertEquals(5, $responseData['pagination']['per_page']);
        $this->assertEquals(15, $responseData['pagination']['total']);
    }
}
