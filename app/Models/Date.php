<?php

namespace App\Models;

use App\Enums\DateType;
use App\Events\DateSaved;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string getDurationStartEnd
 */

class Date extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($date) {
            event(new DateSaved($date));
        });
    }

    protected $fillable = [
        'title',
        'description',
        'location',
        'type',
        'start',
        'end'
    ];

    protected $casts = [
        'title' => 'string',
        'type' => DateType::class,
        'start' => 'datetime:d-m-Y H:i',
        'end' => 'datetime:d-m-Y H:i'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getDurationStartEnd(): Attribute
    {
        return Attribute::make(
            get: function () {
                $start = Carbon::parse($this->start);
                $end = Carbon::parse($this->end);

                // calculate total minutes
                $totalMinutes = $start->diffInMinutes($end);

                // calculate duration
                $days = floor($totalMinutes / 1440); // 1440 minutes in a day
                $remainingMinutes = $totalMinutes % 1440; // remaining minutes
                $hours = floor($remainingMinutes / 60); // 60 minutes in an hour
                $minutes = $remainingMinutes % 60; // remaining minutes

                // generate duration string
                $durationString = '';
                if ($days > 0) {
                    $durationString .= "{$days} " . ($days > 1 ? __("Days") : __('Day')) . '</br>';
                }
                if ($hours > 0) {
                    $durationString .= "{$hours} " . __("Hour") . '</br>';
                }
                if ($minutes > 0 || ($days == 0 && $hours == 0)) {
                    $durationString .= "{$minutes} " . ($minutes > 1 ? __("Minutes") : __('Minute'));
                }

                return Str::lower($durationString);
            }
        );
    }
}
