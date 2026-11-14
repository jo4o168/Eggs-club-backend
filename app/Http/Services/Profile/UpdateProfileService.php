<?php

namespace App\Http\Services\Profile;

use App\Models\Profile;
use App\Support\IdentityValidator;

class UpdateProfileService
{
    public function run(array $data, Profile $profile): void
    {
        if (! empty($data['document']) || ! empty($data['document_type'])) {
            $data = array_merge($data, IdentityValidator::persistable([
                'document_type' => $data['document_type'] ?? $profile->document_type ?? 'cpf',
                'document' => $data['document'] ?? $data['cpf'] ?? $data['cnpj'] ?? '',
                'birth_date' => $data['birth_date'] ?? optional($profile->birth_date)->toDateString(),
                'cpf' => $data['cpf'] ?? $profile->cpf,
                'cnpj' => $data['cnpj'] ?? $profile->cnpj,
            ]));
            unset($data['document']);
        }

        $profile->fill($data);
        $profile->save();
    }
}
