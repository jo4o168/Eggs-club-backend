<?php

namespace App\Http\Requests\ProducerSetting;

use App\Http\Requests\BaseRequest;

class StoreProducerSettingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user || ! $user->profile) {
            return false;
        }

        $role = is_array($user->roles) ? (int) ($user->roles[0] ?? 0) : (int) $user->roles;
        if ($role !== 1) {
            return false;
        }

        return (int) $this->input('producer_id') === (int) $user->profile->id;
    }

    public function rules(): array
    {
        return [
            'farm_name' => ['required', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'certifications' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string'],
            'city' => ['sometimes', 'nullable', 'string'],
            'state' => ['sometimes', 'nullable', 'string'],
            'website' => ['sometimes', 'nullable', 'string'],
            'delivery_info' => ['sometimes', 'nullable', 'string'],
            'accepts_new_subscribers' => ['sometimes', 'boolean'],
            'visible_in_search' => ['sometimes', 'boolean'],
            'email_notifications' => ['sometimes', 'boolean'],
            'sms_notifications' => ['sometimes', 'boolean'],
            'new_order_alert' => ['sometimes', 'boolean'],
            'weekly_report' => ['sometimes', 'boolean'],
            'producer_id' => ['required', 'integer', 'exists:profiles,id', 'unique:producer_settings,producer_id'],
        ];
    }
}
