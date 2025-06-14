<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    protected $table = 'device_data';

    protected $fillable = [
        'device_id',
        'temperature',
        'valves',
        'flow_sensors',
    ];

    protected $casts = [
        'valves' => 'array',
        'flow_sensors' => 'array',
    ];
}
