<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $type
 * @property mixed $access_token
 * @property mixed $refresh_token
 * @property mixed $expires_in
 * @property mixed $scope
 * @property mixed $token_type
 * @property mixed $created
 */

class GoogleToken extends Model
{
    protected $fillable = [
        'type',
        'access_token',
        'refresh_token',
        'expires_in',
        'scope',
        'token_type',
        'created',
    ];

    protected $casts = [
        'created' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->created->addSeconds($this->expires_in)->isPast();
    }
}
