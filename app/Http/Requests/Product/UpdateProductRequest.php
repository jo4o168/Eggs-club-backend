<?php

namespace App\Http\Requests\Product;

use App\Enum\EggColor;
use App\Enum\EggSize;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'egg_size' => ['sometimes', Rule::enum(EggSize::class)],
            'egg_color' => ['sometimes', Rule::enum(EggColor::class)],
            'kit_quantity' => ['sometimes', 'integer', 'min:1'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric'],
            'subscription_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'one_time_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'allow_subscription' => ['sometimes', 'boolean'],
            'allow_one_time_purchase' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'image_url' => ['sometimes', 'nullable', 'string'],
            'image' => ['sometimes', 'file', 'max:5120', 'extensions:jpg,jpeg,png,webp,gif,bmp,svg,avif,jfif'],
            'producer_id' => ['sometimes', 'integer', 'exists:profiles,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome do kit',
            'egg_size' => 'tamanho do ovo',
            'egg_color' => 'cor do ovo',
            'kit_quantity' => 'quantidade por kit',
            'description' => 'descrição',
            'price' => 'preço',
            'subscription_price' => 'preço da assinatura',
            'one_time_price' => 'preço da compra única',
            'allow_subscription' => 'permitir assinatura',
            'allow_one_time_purchase' => 'permitir compra única',
            'is_active' => 'ativo',
            'image' => 'imagem',
            'image_url' => 'imagem',
            'producer_id' => 'produtor',
        ];
    }
}
