<?php

declare(strict_types=1);

namespace Tipoff\Addresses\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Tipoff\Support\Nova\BaseResource;

class Phone extends BaseResource
{
    public static $model = \Tipoff\Addresses\Models\Phone::class;

    public static $title = 'id';

    public static $search = [
        'full_number',
    ];

    public static $group = 'Resources';

    public function fieldsForIndex(NovaRequest $request)
    {
        return array_filter([
            Text::make('Country Calling Code', 'country_callingcode.id', function () {
                return $this->country_callingcode->code;
            })->sortable(),
            Text::make('Full Number')->sortable(),

            Text::make('Phone Area', 'phone_area.code', function () {
                return $this->phone_area->code;
            })->sortable(),
            Text::make('Exchange Code')->sortable(),
            Text::make('Line Number')->sortable(),
        ]);
    }

    public function fields(Request $request)
    {
        return array_filter([
            nova('country_callingcode') ? BelongsTo::make('Country Calling Code', 'country_callingcode', nova('country_callingcode'))->searchable() : null,
            Text::make('Full Number'),

            nova('phone_area') ? BelongsTo::make('Phone Area', 'phone_area', nova('phone_area'))->searchable() : null,
            Text::make('Line Number')->nullable(),
            Text::make('Exchange Code')->nullable(),
        ]);
    }

    protected function dataFields(): array
    {
        return array_merge(
            parent::dataFields(),
            $this->creatorDataFields(),
            $this->updaterDataFields(),
        );
    }
}