<?php

namespace App\Models;

use App\Enums\DateType;
use App\Events\AgendaSaved;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string getDurationStartEnd
 * @property mixed $event_id
 * @property mixed $recurring_event_id
 * @property mixed $ical_uid
 * @property mixed $html_link
 * @property mixed $summary
 * @property mixed $in_agenda
 * @property mixed $meet_link
 * @property mixed $description
 * @property mixed $location
 * @property mixed $type
 * @property mixed $start
 * @property mixed $end
 */

class Agenda extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($agenda) {
            event(new AgendaSaved($agenda));
        });
    }

    protected $fillable = [
        'event_id',
        'recurring_event_id',
        'ical_uid',
        'html_link',
        'summary',
        'in_agenda',
        'meet_link',
        'description',
        'location',
        'type',
        'start',
        'end',
    ];

    protected $casts = [
        'type' => DateType::class,
        'start' => 'datetime',
        'end' => 'datetime',
        'in_agenda' => 'boolean',
    ];

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

    public function files(): HasMany
    {
        return $this->hasMany(Files::class);
    }
}
