<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'phone_number',
        'email',
        'date_of_birth',
        'gender',
        'avatar'
    ];


    /**
     * Define the relationship to fetch businesses
     * @return HasMany
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    /**
     * Define the relationship to fetch address
     * @return HasMany
     */
    public function address(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Define the relationship to fetch educations
     * @return HasMany
     */
    public function education(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    /**
     * Define the relationship to fetch educations
     * @return HasMany
     */
    public function job(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
