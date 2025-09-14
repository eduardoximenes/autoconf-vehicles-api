<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Autoconf Vehicles API",
 *     version="1.0.0",
 *     description="API para gerenciamento de veículos - Desafio Técnico",
 *     @OA\Contact(
 *         email="contato@autoconf.com"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Servidor de Desenvolvimento"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token de autenticação Sanctum"
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="João Silva"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@email.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="is_admin", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Vehicle",
 *     required={"id", "license_plate", "chassis", "brand", "model", "sale_price"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="license_plate", type="string", pattern="^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$", example="ABC1D23"),
 *     @OA\Property(property="chassis", type="string", minLength=17, maxLength=17, example="9BWZZZ377VT004251"),
 *     @OA\Property(property="brand", type="string", example="Toyota"),
 *     @OA\Property(property="model", type="string", example="Corolla"),
 *     @OA\Property(property="version", type="string", example="XEI 2.0"),
 *     @OA\Property(property="sale_price", type="number", format="decimal", minimum=0.01, example=85000.00),
 *     @OA\Property(property="color", type="string", example="Prata"),
 *     @OA\Property(property="km", type="integer", minimum=0, example=15000),
 *     @OA\Property(
 *         property="transmission",
 *         type="string",
 *         enum={"manual", "automatic"},
 *         example="automatic"
 *     ),
 *     @OA\Property(
 *         property="fuel_type",
 *         type="string",
 *         enum={"gasoline", "ethanol", "flex", "diesel", "hybrid", "electric"},
 *         example="flex"
 *     ),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/VehicleImage")
 *     ),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="created_by", ref="#/components/schemas/User", nullable=true),
 *     @OA\Property(property="updated_by", ref="#/components/schemas/User", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="VehicleImage",
 *     required={"id", "vehicle_id", "path", "is_cover"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="vehicle_id", type="integer", example=1),
 *     @OA\Property(property="path", type="string", example="vehicles/1/image_001.jpg"),
 *     @OA\Property(property="is_cover", type="boolean", example=true),
 *     @OA\Property(property="url", type="string", example="http://localhost:8000/storage/vehicles/1/image_001.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="VehicleStoreRequest",
 *     required={"license_plate", "chassis", "brand", "model", "sale_price", "transmission", "fuel_type"},
 *     @OA\Property(property="license_plate", type="string", pattern="^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$", example="ABC1D23"),
 *     @OA\Property(property="chassis", type="string", minLength=17, maxLength=17, example="9BWZZZ377VT004251"),
 *     @OA\Property(property="brand", type="string", example="Toyota"),
 *     @OA\Property(property="model", type="string", example="Corolla"),
 *     @OA\Property(property="version", type="string", example="XEI 2.0"),
 *     @OA\Property(property="sale_price", type="number", format="decimal", minimum=0.01, example=85000.00),
 *     @OA\Property(property="color", type="string", example="Prata"),
 *     @OA\Property(property="km", type="integer", minimum=0, example=15000),
 *     @OA\Property(property="transmission", type="string", enum={"manual", "automatic"}, example="automatic"),
 *     @OA\Property(property="fuel_type", type="string", enum={"gasoline", "ethanol", "flex", "diesel", "hybrid", "electric"}, example="flex")
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedVehicleResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Veículos listados com sucesso"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="data",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Vehicle")
 *         ),
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=5),
 *         @OA\Property(property="per_page", type="integer", example=10),
 *         @OA\Property(property="total", type="integer", example=50),
 *         @OA\Property(property="from", type="integer", example=1),
 *         @OA\Property(property="to", type="integer", example=10)
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(
 *             property="license_plate",
 *             type="array",
 *             @OA\Items(type="string", example="O campo license_plate é obrigatório.")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UnauthorizedError",
 *     @OA\Property(property="message", type="string", example="Unauthenticated.")
 * )
 *
 * @OA\Schema(
 *     schema="ForbiddenError",
 *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
 * )
 *
 * @OA\Schema(
 *     schema="NotFoundError",
 *     @OA\Property(property="message", type="string", example="No query results for model [Vehicle] 1")
 * )
 *
 * @OA\Schema(
 *     schema="StandardSuccessResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operação realizada com sucesso"),
 *     @OA\Property(property="data", type="object")
 * )
 *
 * @OA\Schema(
 *     schema="VehicleSuccessResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Veículo encontrado com sucesso"),
 *     @OA\Property(property="data", ref="#/components/schemas/Vehicle")
 * )
 */
abstract class BaseController extends Controller
{
    use ApiResponse;
}
