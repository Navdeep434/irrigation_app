<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceReading extends Model
{
    protected $fillable = [
        'device_number',
        'flow_rate1',
        'flow_rate2',
        'flow_rate3',
        'flow_rate4',
        'flow_rate5',
        'flow_rate6',
        'flow_rate7',
        'flow_rate8',
        'temperature',
    ];
}
