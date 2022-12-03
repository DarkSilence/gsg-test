<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unique_code',
        'amount',
        'expired_dt',
        'used_dt',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expired_dt' => 'datetime',
        'used_dt' => 'datetime',
    ];

    /**
     * Returns true if voucher is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expired_dt <= new DateTime();
    }

    /**
     * Returns true if voucher has been used
     *
     * @return bool
     */
    public function isUsed(): bool
    {
        return $this->used_dt !== null;
    }

    /**
     * Get the order associated with the voucher.
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
