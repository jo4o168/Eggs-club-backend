<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;

class UpdateOrderStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,confirmed,preparing,shipped,delivered,cancelled'],
        ];
    }
}
