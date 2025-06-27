<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HardwareStatus extends Model
{
    protected $fillable = [
        'device_number',
        'valves',
        'flow_sensors',
        'temperature_sensor',
    ];

    protected $casts = [
        'valves' => 'array',
        'flow_sensors' => 'array',
        'temperature_sensor' => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_number', 'device_number');
    }
}
