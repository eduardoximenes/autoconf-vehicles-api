<?php

namespace App\Http\Requests\Api\V1\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'q' => 'sometimes|string|max:255',
            'brand' => 'sometimes|string|max:100',
            'model' => 'sometimes|string|max:100',
            'license_plate' => 'sometimes|string|max:8',
            'sort' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
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
            'q.max' => 'A busca não pode ter mais de 255 caracteres.',
            'brand.max' => 'A marca não pode ter mais de 100 caracteres.',
            'model.max' => 'O modelo não pode ter mais de 100 caracteres.',
            'license_plate.max' => 'A placa não pode ter mais de 8 caracteres.',
            'per_page.integer' => 'Items por página deve ser um número inteiro.',
            'per_page.min' => 'Items por página deve ser pelo menos 1.',
            'per_page.max' => 'Items por página não pode ser maior que 100.',
            'page.integer' => 'Página deve ser um número inteiro.',
            'page.min' => 'Página deve ser pelo menos 1.',
        ];
    }
}
