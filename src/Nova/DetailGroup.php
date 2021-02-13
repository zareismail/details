<?php

namespace Zareismail\Details\Nova; 

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text, Number, HasMany};
use Armincms\Fields\Targomaan;

class DetailGroup extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Details\Models\DetailGroup::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
    	return [
    		ID::make(), 

            Targomaan::make([
                Text::make(__('Group Name'), 'name')
                    ->help(__('Display name of the group.')),
            ]),

            Number::make(__('Display Order'), 'order')
                ->required()
                ->rules('required')
                ->default(static::newModel()->count() + 1),

            HasMany::make(__('Details'), 'details', Detail::class),
    	];
    }
}