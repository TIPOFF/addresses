<?php

declare(strict_types=1);

namespace Tipoff\Addresses\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Tipoff\Support\Nova\BaseResource;

class PhoneArea extends BaseResource
{
    public static $model = \Tipoff\Addresses\Models\PhoneArea::class;

    public static $title = 'code';

    public static $search = [
        'code',
    ];

    public static $group = 'Resources';

    public function fieldsForIndex(NovaRequest $request)
    {
        return array_filter([
            Text::make('Code')->sortable(),
            Text::make('Note')->sortable(),
            Text::make('State', 'state.id', function () {
                return $this->state->title;
            })->sortable(),
        ]);
    }

    public function fields(Request $request)
    {
        return array_filter([
            Text::make('Code'),
            Text::make('Note')->nullable(),
            nova('state') ? BelongsTo::make('State', 'state', nova('state'))->searchable() : null,
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