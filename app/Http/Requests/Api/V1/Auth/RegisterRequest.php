<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
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
            'name.required' => 'Nome é obrigatório.',
            'name.min' => 'Nome deve ter pelo menos 2 caracteres.',
            'email.required' => 'E-mail é obrigatório.',
            'email.email' => 'E-mail inválido.',
            'email.unique' => 'E-mail já está em uso.',
            'password.required' => 'Senha é obrigatória.',
            'password.min' => 'Senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'Confirmação de senha não confere.',
        ];
    }
}
