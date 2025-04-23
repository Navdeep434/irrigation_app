<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        // you can include deleted_by if you want it mass-assignable,
        // though it's set programmatically, not via form input:
        'deleted_by',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Device number generator...
    public static function generateDeviceNumber(): string
    {
        $year   = date('Y');
        $prefix = 'SSNM' . $year . 'ESP';

        $latest = self::withTrashed()
            ->where('device_number', 'like', "$prefix%")
            ->orderByDesc('device_number')
            ->first();

        $lastNumber = $latest
            ? (int) substr($latest->device_number, -6)
            : 0;

        return $prefix . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Override the soft-delete behavior to stamp `deleted_by`.
     */
    protected function runSoftDelete()
    {
        $query = $this->newModelQuery()
            ->where($this->getKeyName(), $this->getKey());

        $time = $this->freshTimestampString();

        // Update deleted_at and deleted_by in one query
        $query->update([
            $this->getDeletedAtColumn() => $time,
            'deleted_by'                => Auth::id(),
        ]);

        // Sync the model's attributes so further code sees the change
        $this->{$this->getDeletedAtColumn()} = $time;
        $this->deleted_by = Auth::id();
    }
}
