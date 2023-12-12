<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProBusiness extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'user_id',
        'inn',
        'name_of_business',
        'form_of_business',
        'oked',
        'address',
    ];

    /**
     * Define the relationship to fetch banks data
     * @return HasMany
     */
    public function banks(): HasMany
    {
        return $this->hasMany(BanksData::class, 'business_id', 'id');
    }

    /**
     * Define the relationship to fetch banks data
     * @return HasMany
     */
    public function boxOffices(): HasMany
    {
        return $this->hasMany(BoxOffice::class, 'business_id', 'id');
    }
}
