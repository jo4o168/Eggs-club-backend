<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'producer_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('producer_id')->nullable()->after('customer_id')->constrained('profiles');
        });

        Order::query()->with(['items.product'])->whereNull('producer_id')->each(function (Order $order) {
            $producerId = $order->items->first()?->product?->producer_id;
            if ($producerId) {
                $order->update(['producer_id' => $producerId]);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('orders', 'producer_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('producer_id');
        });
    }
};
