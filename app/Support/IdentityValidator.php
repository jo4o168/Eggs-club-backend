<?php

namespace App\Support;

use App\Enum\ProfileRole;
use Illuminate\Validation\Validator;

class IdentityValidator
{
    public static function after(Validator $validator, array $data, int $defaultRole = ProfileRole::CLIENT->value): void
    {
        $role = (int) ($data['role'] ?? $defaultRole);
        $type = strtolower((string) ($data['document_type'] ?? 'cpf'));
        $document = BrazilianDocument::digits($data['document'] ?? $data['cpf'] ?? $data['cnpj'] ?? '');

        if ($role === ProfileRole::CLIENT->value && $type !== 'cpf') {
            $validator->errors()->add('document_type', 'Cliente deve cadastrar um CPF.');
        }

        if ($type === 'cnpj') {
            if (! BrazilianDocument::isValidCnpj($document)) {
                $validator->errors()->add('document', 'Informe um CNPJ válido.');
            }
        } elseif (! BrazilianDocument::isValidCpf($document)) {
            $validator->errors()->add('document', 'Informe um CPF válido.');
        }

        $birthDate = $data['birth_date'] ?? null;
        if (! BrazilianDocument::isAdult(is_string($birthDate) ? $birthDate : null)) {
            $validator->errors()->add('birth_date', 'É necessário ter 18 anos ou mais.');
        }
    }

    /**
     * @return array{document_type:string,cpf:?string,cnpj:?string,birth_date:string}
     */
    public static function persistable(array $data): array
    {
        $type = strtolower((string) ($data['document_type'] ?? 'cpf'));
        $document = BrazilianDocument::digits($data['document'] ?? '');

        $birthDate = $data['birth_date'] ?? null;

        return [
            'document_type' => $type,
            'cpf' => $type === 'cpf' ? $document : null,
            'cnpj' => $type === 'cnpj' ? $document : null,
            'birth_date' => $birthDate instanceof \DateTimeInterface
                ? $birthDate->format('Y-m-d')
                : $birthDate,
        ];
    }
}
