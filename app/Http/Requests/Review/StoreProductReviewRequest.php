<?php

namespace App\Http\Requests\Review;

use App\Http\Requests\BaseRequest;

class StoreProductReviewRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'kit',
            'rating' => 'nota',
            'comment' => 'comentário',
        ];
    }
}
