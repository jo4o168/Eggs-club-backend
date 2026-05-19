<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HttpResponse;
use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\Cart\CartItemResource;
use App\Http\Services\Cart\ClearCartService;
use App\Http\Services\Cart\DeleteCartItemService;
use App\Http\Services\Cart\ListCartItemsService;
use App\Http\Services\Cart\UpdateCartItemService;
use App\Http\Services\Cart\UpsertCartItemService;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerCartController extends Controller
{
    public function __construct(
        private readonly ListCartItemsService $listCart,
        private readonly UpsertCartItemService $upsertCartItem,
        private readonly UpdateCartItemService $updateCartItem,
        private readonly DeleteCartItemService $deleteCartItem,
        private readonly ClearCartService $clearCart,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->denyUnlessCustomer($request);

        $items = $this->listCart->run($request->user());

        return HttpResponse::ok(CartItemResource::collection($items));
    }

    public function store(StoreCartItemRequest $request): JsonResponse
    {
        $item = $this->upsertCartItem->run($request->validated(), $request->user());

        return HttpResponse::ok(new CartItemResource($item));
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $item = $this->updateCartItem->run($cartItem, $request->validated(), $request->user());

        return HttpResponse::ok(new CartItemResource($item));
    }

    public function destroyItem(Request $request, CartItem $cartItem): Response
    {
        $this->denyUnlessCustomer($request);
        $this->deleteCartItem->run($cartItem, $request->user());

        return HttpResponse::noContent();
    }

    public function destroy(Request $request): Response
    {
        $this->denyUnlessCustomer($request);
        $this->clearCart->run($request->user());

        return HttpResponse::noContent();
    }

    private function denyUnlessCustomer(Request $request): void
    {
        $roles = $request->user()->roles;
        $role = is_array($roles) ? (int) ($roles[0] ?? 0) : (int) $roles;
        abort_unless($role === 0, 403);
    }
}
