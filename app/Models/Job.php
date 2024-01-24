<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'profession',
        'experience_count',
        'education_id'
    ];

    /**
     * Define the relationship to fetch educations
     * @return HasMany
     */
    public function experience(): HasMany
    {
        return $this->hasMany(Experience::class, 'job_id', 'id');
    }
}
