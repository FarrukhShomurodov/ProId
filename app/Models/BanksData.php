<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BanksData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_of_banking_akkaunt',
        'business_id',
        'mfo',
        'payment_account',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
