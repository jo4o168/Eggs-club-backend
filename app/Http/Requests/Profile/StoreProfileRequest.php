<?php

namespace App\Http\Requests\Profile;

use App\Enum\ProfileRole;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreProfileRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'role' => ['sometimes', 'integer', Rule::in([ProfileRole::CLIENT->value, ProfileRole::PRODUCER->value])],
            'email' => ['required', 'string', 'email'],
            'phone' => ['sometimes', 'string', 'nullable'],
            'avatar_url' => ['sometimes', 'string', 'nullable'],
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:profiles,user_id'],
        ];
    }
}
