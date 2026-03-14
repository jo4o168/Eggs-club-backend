<?php

namespace App\Http\Services\Subscription;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StoreSubscriptionService
{
    public function run(array $data): void
    {
        $data['start_date'] = Carbon::today();
        $data['customer_id'] = Auth::user()->profile->id;
        Subscription::create($data);
    }
}
