<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxOffice extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var string[]
     */
    protected $fillable=[
        'business_id',
        'name',
        'service',
        'bank_data_id',
        'isActive'
    ];

    use HasFactory;
}
