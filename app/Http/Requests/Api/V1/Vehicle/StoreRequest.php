<?php

namespace App\Http\Requests\Api\V1\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            'license_plate' => 'required|string|max:8|unique:vehicles,license_plate|regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/i',
            'chassis' => 'required|string|size:17|unique:vehicles,chassis|regex:/^[A-HJ-NPR-Z0-9]{17}$/i',

            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'version' => 'required|string|max:100',

            'sale_price' => 'required|numeric|min:0.01',
            'color' => 'required|string|max:50',

            'km' => 'required|integer|min:0',

            'transmission' => 'required|string|in:manual,automatic',
            'fuel_type' => 'required|string|in:gasoline,ethanol,flex,diesel,hybrid,electric',
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
            'license_plate.required' => 'A placa é obrigatória.',
            'license_plate.max' => 'A placa não pode ter mais de 8 caracteres.',
            'license_plate.unique' => 'Esta placa já está cadastrada.',
            'license_plate.regex' => 'A placa deve seguir o formato brasileiro (ex: ABC1D23).',
            'chassis.required' => 'O chassi é obrigatório.',
            'chassis.size' => 'O chassi deve ter exatamente 17 caracteres.',
            'chassis.unique' => 'Este chassi já está cadastrado.',
            'chassis.regex' => 'O chassi deve conter apenas letras e números válidos (exceto I, O, Q).',
            'brand.required' => 'A marca é obrigatória.',
            'brand.max' => 'A marca não pode ter mais de 100 caracteres.',
            'model.required' => 'O modelo é obrigatório.',
            'model.max' => 'O modelo não pode ter mais de 100 caracteres.',
            'version.required' => 'A versão é obrigatória.',
            'version.max' => 'A versão não pode ter mais de 100 caracteres.',
            'sale_price.required' => 'O preço de venda é obrigatório.',
            'sale_price.numeric' => 'O preço de venda deve ser um número.',
            'sale_price.min' => 'O preço de venda deve ser maior que zero.',
            'color.required' => 'A cor é obrigatória.',
            'color.max' => 'A cor não pode ter mais de 50 caracteres.',
            'km.required' => 'A quilometragem é obrigatória.',
            'km.integer' => 'A quilometragem deve ser um número inteiro.',
            'km.min' => 'A quilometragem deve ser maior ou igual a zero.',
            'transmission.required' => 'O tipo de transmissão é obrigatório.',
            'transmission.in' => 'A transmissão deve ser manual ou automatic.',
            'fuel_type.required' => 'O tipo de combustível é obrigatório.',
            'fuel_type.in' => 'O combustível deve ser: gasoline, ethanol, flex, diesel, hybrid ou electric.',
        ];
    }
}
