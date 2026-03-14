<?php

namespace App\Http\Requests\Auth;

use App\Enum\ProfileRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignUpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "username" => ["required", "unique:users"],
            "email" => ["required", "email", "unique:users"],
            "name" => ["required", "string"],
            "role" => ["required", "integer", Rule::enum(ProfileRole::class)],
            "password" => [
                "required",
                'confirmed',
                "min:8",
                "max:64",
                "regex:/[a-z]/",
                "regex:/[A-Z]/",
                "regex:/[0-9]/",
                "regex:/[@$!%*#?&]/",
            ]
        ];
    }
}
