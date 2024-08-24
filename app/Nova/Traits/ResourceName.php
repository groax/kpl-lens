<?php

namespace App\Nova\Traits;

trait ResourceName
{
    public static function label()
    {
        return __(class_basename(self::class) . 's');
    }

    public static function singularLabel()
    {
        return __(class_basename(self::class));
    }

}
