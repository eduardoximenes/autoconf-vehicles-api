<?php

namespace App\Http\Requests\Api\V1\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vehicleId = $this->route('vehicle');

        return [
            'license_plate' => 'sometimes|string|max:8|unique:vehicles,license_plate,' . $vehicleId . '|regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/i',
            'chassis' => 'sometimes|string|size:17|unique:vehicles,chassis,' . $vehicleId . '|regex:/^[A-HJ-NPR-Z0-9]{17}$/i',
            'brand' => 'sometimes|string|max:100',
            'model' => 'sometimes|string|max:100',
            'version' => 'sometimes|string|max:100',
            'sale_price' => 'sometimes|numeric|min:0.01',
            'color' => 'sometimes|string|max:50',
            'km' => 'sometimes|integer|min:0',
            'transmission' => 'sometimes|string|in:manual,automatic',
            'fuel_type' => 'sometimes|string|in:gasoline,ethanol,flex,diesel,hybrid,electric',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'license_plate.max' => 'A placa não pode ter mais de 8 caracteres.',
            'license_plate.unique' => 'Esta placa já está cadastrada.',
            'license_plate.regex' => 'A placa deve seguir o formato brasileiro (ex: ABC1D23).',
            'chassis.size' => 'O chassi deve ter exatamente 17 caracteres.',
            'chassis.unique' => 'Este chassi já está cadastrado.',
            'chassis.regex' => 'O chassi deve conter apenas letras e números válidos (exceto I, O, Q).',
            'brand.max' => 'A marca não pode ter mais de 100 caracteres.',
            'model.max' => 'O modelo não pode ter mais de 100 caracteres.',
            'version.max' => 'A versão não pode ter mais de 100 caracteres.',
            'sale_price.numeric' => 'O preço de venda deve ser um número.',
            'sale_price.min' => 'O preço de venda deve ser maior que zero.',
            'color.max' => 'A cor não pode ter mais de 50 caracteres.',
            'km.integer' => 'A quilometragem deve ser um número inteiro.',
            'km.min' => 'A quilometragem deve ser maior ou igual a zero.',
            'transmission.in' => 'A transmissão deve ser manual ou automatic.',
            'fuel_type.in' => 'O combustível deve ser: gasoline, ethanol, flex, diesel, hybrid ou electric.',
        ];
    }
}
