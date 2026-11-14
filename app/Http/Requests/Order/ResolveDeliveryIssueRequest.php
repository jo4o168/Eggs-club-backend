<?php

namespace App\Http\Requests\Order;

use App\Enum\DeliveryIssueStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ResolveDeliveryIssueRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    DeliveryIssueStatus::REFUNDED->value,
                    DeliveryIssueStatus::REPLACED->value,
                    DeliveryIssueStatus::REJECTED->value,
                ]),
            ],
            'resolution_message' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'resolução',
            'resolution_message' => 'mensagem para o cliente',
        ];
    }
}
