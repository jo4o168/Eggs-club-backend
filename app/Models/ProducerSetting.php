<?php

namespace App\Models;

class ProducerSetting extends BaseModel
{
    protected $fillable = [
        'farm_name',
        'description',
        'address',
        'city',
        'state',
        'delivery_info',
        'accepts_new_subscribers',
        'visible_in_search',
        'producer_id',
    ];

}
