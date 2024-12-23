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
 * @property mixed $created_at
 * @property mixed $updated_at
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
    ];

    public function isExpired(): bool
    {
        return $this->updated_at->addSeconds($this->expires_in)->isPast();
    }
}
