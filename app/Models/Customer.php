<?php

namespace App\Models;

use App\Enums\DateType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type_contact',
        'type',
        'names',
        'emails',
        'phones',
        'address',
        'description',
        'is_date_set',
        'date',
    ];

    protected $casts = [
        'is_date_set' => 'boolean',
        'date' => 'date:=d-m-Y',
        'names' => 'array',
        'emails' => 'array',
        'phones' => 'array',
        'address' => 'array',
        'type' => DateType::class,
        'type_contact' => 'string',

    ];

    public function dates(): HasMany
    {
        return $this->hasMany(Date::class);
    }

    public function fair(): HasMany
    {
        return $this->hasMany(Fair::class);
    }

    public function photographers(): BelongsToMany
    {
        return $this->belongsToMany(Photographer::class);
    }
}
