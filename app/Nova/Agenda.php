<?php

namespace App\Nova;

use App\Enums\DateType;
use App\Nova\Traits\ResourceName;
use Illuminate\Http\Request;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Agenda extends Resource
{
    use ResourceName;

    public static $with = ['customer'];

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Agenda>
     */
    public static $model = \App\Models\Agenda::class;

    public static $searchable = false;

    public function title()
    {
        return "{$this->title} - {$this->start->format('d-m-Y H:i')} / {$this->end->format('d-m-Y H:i')}";
    }

    public function subtitle()
    {
        return "{$this->start->format('d-m-Y H:i')} - {$this->end->format('d-m-Y H:i')}";
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
        'location',
        'start',
        'end',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     * @throws HelperNotSupported
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->hide(),
            // BelongsTo::make(__('Customer'), 'customer', Customer::class)->withoutTrashed(),

            Text::make(__('Title'), 'title')
//                ->dependsOn('customer', function (Text $field, NovaRequest $request, FormData $formData) {
//                    $field->setValue(\App\Models\Customer::find($formData->get('customer'))->name ?? '');
//                })
                ->required()
                ->sortable(),

            Boolean::make(__('In Agenda'), 'in_agenda'),

            Textarea::make(__('Description'), 'description')
                ->sortable(),
            Text::make(__('Location'), 'location')
                ->sortable(),
            Select::make(__('Type'), 'type')
                ->options(DateType::getTypes())
                ->required()
                ->sortable(),

            DateTime::make(__('Start'), 'start')
                ->min(now())
//                ->default(now()->format('d-m-Y'))
                ->required()
                ->sortable(),

            DateTime::make(__('End'), 'end')
                ->dependsOn(
                'start', function (DateTime $field, NovaRequest $request, FormData $formData) {
                    $field->min($formData->get('start'));
                })
                ->required()
                ->sortable(),

            Text::make(__('Duration'), fn() => $this->getDurationStartEnd)
                ->exceptOnForms()
                ->asHtml(),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
