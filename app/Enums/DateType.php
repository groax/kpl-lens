<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum DateType: string
{
    case MEETING = 'meeting';
    case APPOINTMENT = 'appointment';
    case CALL = 'call';
    case EMAIL = 'email';
    case ONLINE = 'online';
    case MEETING_WITHOUT_CALENDAR = 'meeting-without-calendar';
    case IN_PERSON = 'in-person';
    case TALK = 'talk';
    case WEDDING = 'wedding';
    case OTHER = 'other';

    public static function getTypes(): array
    {
        return collect(DateType::cases())->mapWithKeys(fn($type) => [$type->value => Str::ucfirst($type->value)])->toArray();
    }
}
