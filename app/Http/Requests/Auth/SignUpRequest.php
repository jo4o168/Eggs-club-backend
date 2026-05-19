<?php

namespace App\Http\Requests\Auth;

use App\Enum\ProfileRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignUpRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('roles') && ! $this->has('role')) {
            $this->merge(['role' => $this->input('roles')]);
        }

        $email = $this->input('email');
        if (is_string($email) && $email !== '') {
            $this->merge(['username' => $email]);
        }
    }

    public function rules(): array
    {
        return [
            "username" => ["required", "string", "max:255", "unique:users,username"],
            "email" => ["required", "email", "unique:users"],
            "name" => ["required", "string"],
            'role' => ['required', 'integer', Rule::in([ProfileRole::CLIENT->value, ProfileRole::PRODUCER->value])],
            "farm_name" => ["sometimes", "nullable", "string", "max:255"],
            "location" => ["sometimes", "nullable", "string", "max:255"],
            "password" => [
                "required",
                'confirmed',
                "min:8",
                "max:64",
                "regex:/[a-z]/",
                "regex:/[A-Z]/",
                "regex:/[0-9]/",
                "regex:/[@$!%*#?&]/",
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'e-mail',
            'username' => 'identificador da conta',
            'name' => 'nome',
            'password' => 'senha',
            'password_confirmation' => 'confirmação de senha',
            'role' => 'tipo de conta',
            'farm_name' => 'nome da propriedade',
            'location' => 'localização',
        ];
    }
}
