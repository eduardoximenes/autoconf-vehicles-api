<?php

namespace App\Http\Requests\Api\V1\VehicleImage;

use Illuminate\Foundation\Http\FormRequest;

class UploadImagesRequest extends FormRequest
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
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2MB
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'files.required' => 'É necessário enviar pelo menos uma imagem',
            'files.array' => 'O campo files deve ser um array',
            'files.min' => 'É necessário enviar pelo menos uma imagem',
            'files.max' => 'Máximo de 10 imagens por vez',
            'files.*.required' => 'Cada arquivo é obrigatório',
            'files.*.image' => 'Cada arquivo deve ser uma imagem válida',
            'files.*.mimes' => 'Cada imagem deve ser do tipo: jpeg, jpg, png ou webp',
            'files.*.max' => 'Cada imagem deve ter no máximo 2MB',
        ];
    }
}
