<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uid',
        'uid_year',
        'uid_number',
        'first_name',
        'last_name',
        'email',
        'country_code',
        'contact_number',
        'gender',
        'dob',
        'status',
    ];

    /**
     * Generate a unique UID in the format SSNM-YYYY-ID-000001
     */
    public static function generateTechyUid(User $user)
    {
        $year = now()->year;

        // Get the last UID number for the year
        $last = self::where('uid_year', $year)->max('uid_number');
        $next = $last ? $last + 1 : 1;

        // Format: SSNM-2025-<user_id>-000001
        $uid = 'SSNM-' . $year . '-' . $user->id . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);

        // Save to customers table
        $customer = self::create([
            'user_id' => $user->id,
            'uid' => $uid,
            'uid_year' => $year,
            'uid_number' => $next,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'country_code' => $user->country_code,
            'contact_number' => $user->contact_number,
            'gender' => $user->gender,
            'dob' => $user->dob,
            'status' => 'active',
        ]);

        return $customer;
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

}
