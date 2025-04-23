<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'device_number',
        'user_id',
        'customer_id',
        'status',
        'total_valves',
        'total_flow_sensors',
        'total_water_temp_sensors',
        'in_repair',
        'is_blocked',
    ];

    // Relationship (each device may belong to one user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Generate device number like SSNM2025ESP000001
    public static function generateDeviceNumber(): string
    {
        $year = date('Y');
        $prefix = 'SSNM' . $year . 'ESP';

        $latest = self::withTrashed()
            ->where('device_number', 'like', "$prefix%")
            ->orderByDesc('device_number')
            ->first();

        $lastNumber = 0;

        if ($latest) {
            $lastSixDigits = (int) substr($latest->device_number, -6);
            $lastNumber = $lastSixDigits;
        }

        $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }
}
