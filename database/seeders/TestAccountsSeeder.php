<?php

namespace Database\Seeders;

use App\Enum\ProfileRole;
use App\Models\ProducerSetting;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    public const PASSWORD = '12345678';

    public function run(): void
    {
        $this->upsertAccount(
            name: 'Cliente Teste',
            email: 'cliente@teste.com',
            role: ProfileRole::CLIENT,
            documentType: 'cpf',
            document: '39053344705',
            birthDate: '1998-01-15',
        );

        $profile = $this->upsertAccount(
            name: 'Produtor Teste',
            email: 'produtor@teste.com',
            role: ProfileRole::PRODUCER,
            documentType: 'cnpj',
            document: '11444777000161',
            birthDate: '1985-06-20',
        );

        ProducerSetting::query()->updateOrCreate(
            ['producer_id' => $profile->id],
            [
                'farm_name' => 'Fazenda Teste',
                'address' => 'São Paulo, SP',
            ],
        );

        Product::query()
            ->where('producer_id', $profile->id)
            ->where('stock_quantity', '<=', 0)
            ->update(['stock_quantity' => 10]);
    }

    private function upsertAccount(
        string $name,
        string $email,
        ProfileRole $role,
        string $documentType,
        string $document,
        string $birthDate,
    ): Profile
    {
        $user = User::withTrashed()->where('email', $email)->first();

        $payload = [
            'name' => $name,
            'username' => $email,
            'password' => Hash::make(self::PASSWORD),
            'roles' => $role->value,
            'email_verified_at' => now(),
            'active' => true,
            'deleted_at' => null,
        ];

        if ($user) {
            $user->fill($payload)->save();
        } else {
            $user = User::query()->create([
                ...$payload,
                'email' => $email,
            ]);
        }

        $profile = Profile::withTrashed()->where('user_id', $user->id)->first();

        $profilePayload = [
            'name' => $name,
            'role' => $role->value,
            'email' => $email,
            'document_type' => $documentType,
            'cpf' => $documentType === 'cpf' ? $document : null,
            'cnpj' => $documentType === 'cnpj' ? $document : null,
            'birth_date' => $birthDate,
            'deleted_at' => null,
        ];

        if ($profile) {
            $profile->fill($profilePayload)->save();
        } else {
            $profile = Profile::query()->create([
                ...$profilePayload,
                'user_id' => $user->id,
            ]);
        }

        return $profile;
    }
}
