<?php

namespace App\Http\Requests\Profile;

use App\Enum\ProfileRole;
use App\Http\Requests\BaseRequest;
use App\Support\IdentityValidator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProfileRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('cpf') && ! $this->filled('document')) {
            $this->merge([
                'document' => $this->input('cpf'),
                'document_type' => $this->input('document_type', 'cpf'),
            ]);
        }
        if ($this->filled('cnpj') && ! $this->filled('document')) {
            $this->merge([
                'document' => $this->input('cnpj'),
                'document_type' => 'cnpj',
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'role' => ['sometimes', 'integer', Rule::in([ProfileRole::CLIENT->value, ProfileRole::PRODUCER->value])],
            'email' => ['sometimes', 'string', 'email'],
            'phone' => ['sometimes', 'string', 'nullable'],
            'cpf' => ['sometimes', 'string', 'nullable'],
            'cnpj' => ['sometimes', 'string', 'nullable'],
            'document_type' => ['sometimes', 'in:cpf,cnpj'],
            'document' => ['sometimes', 'string', 'max:18'],
            'birth_date' => ['sometimes', 'date', 'before_or_equal:today'],
            'address' => ['sometimes', 'string', 'nullable'],
            'address_number' => ['sometimes', 'string', 'nullable', 'max:20'],
            'city' => ['sometimes', 'string', 'nullable'],
            'state' => ['sometimes', 'string', 'nullable'],
            'zip_code' => ['sometimes', 'string', 'nullable'],
            'complement' => ['sometimes', 'string', 'nullable'],
            'avatar_url' => ['sometimes', 'string', 'nullable'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('document') && ! $this->filled('birth_date') && ! $this->filled('cpf') && ! $this->filled('cnpj')) {
                return;
            }
            $profile = $this->user()?->profile;
            IdentityValidator::after($validator, [
                'role' => $this->input('role', $profile?->role ?? ProfileRole::CLIENT->value),
                'document_type' => $this->input('document_type', $profile?->document_type ?? 'cpf'),
                'document' => $this->input('document', $this->input('cpf', $this->input('cnpj'))),
                'birth_date' => $this->input('birth_date', optional($profile?->birth_date)->toDateString()),
            ], (int) ($profile?->role ?? ProfileRole::CLIENT->value));
        });
    }
}
