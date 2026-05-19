<?php

namespace App\Models;

class ProducerSetting extends BaseModel
{
    protected $fillable = [
        'farm_name',
        'description',
        'certifications',
        'address',
        'city',
        'state',
        'website',
        'delivery_info',
        'accepts_new_subscribers',
        'visible_in_search',
        'email_notifications',
        'sms_notifications',
        'new_order_alert',
        'weekly_report',
        'producer_id',
    ];

}
