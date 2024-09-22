<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscribeTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'booking_trx_id',
        'proof',
        'total_amount',
        'started_at',
        'ended_at',
        'duration',
        'subscribe_package_id',
        'is_paid',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date'
    ];

    public static function generateUniqueTrxId() {
        $prefix = 'FITCAMP';
        $datetime = date('Ymdhis');
        do {
            $randString = $prefix . $datetime . mt_rand(1000,9999);
        } while (self::where('booking_trx_id', $randString)->exists());

        return $randString;
    }

    public function subscribePackage(): BelongsTo {
        return $this->belongsTo(SubscribePackage::class, 'subscribe_package_id');
    }

}
