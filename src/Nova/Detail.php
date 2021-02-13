<?php

namespace Zareismail\Details\Nova; 

use Illuminate\Http\Request; 
use Laravel\Nova\Fields\{ID, Text, Number, Select, BooleanGroup, BelongsTo};
use Armincms\Fields\{Targomaan, Chain};
use Superlatif\NovaTagInput\Tags;
use Zareismail\NovaContracts\Nova\Fields\SharedResources;
use Zareismail\Details\Details;

class Detail extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Details\Models\Detail::class;

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

            BelongsTo::make(__('Detail Group'), 'group', DetailGroup::class)
                ->withoutTrashed()
                ->showCreateRelationButton()
                ->inverse('details')
                ->help(__('Groups the fields to user better experience.')),

            Chain::as('detail-field', function() {
                return [ 
                    Select::make(__('Entry Data'), 'field')
                            ->options(static::fieldOptions())
                            ->required()
                            ->rules('required')
                            ->help(__('Determine the type of entry data.')),
                ];
            }),

            Targomaan::make([ 
                Text::make(__('Name'), 'name')
                    ->required()
                    ->rules('required')
                    ->help(__('Determine the name of the field.')),

                Text::make(__('Help'), 'help')
                    ->help(__('Help users to fill the field.')),
            ]),

            Number::make(__('Display Order'), 'config->order') 
                ->required()
                ->rules('required')
                ->default(static::newModel()->count() + 1),

            Chain::with('detail-field', function($request) {   
                if(in_array($request->get('field'), ['Select', 'BooleanGroup'])) {
                    return $this->filter([ 
                        Tags::make(__('Available Options'), 'options') 
                                ->required()
                                ->rules('required') 
                                ->help(__('Press ENTER to add option'))
                                ->placeholder(__('Define the available options.'))
                                ->allowEditTags(true)
                                ->autocompleteItems(static::newModel()->whereNotNull('options')->get()->flatMap->options->all()), 
                    ]);
                }  

                if($request->get('field') === 'Number') {
                    return [
                        Number::make(__('Minimum Value'), 'config->rules->min')  
                            ->help(__('Determine the minimum of this detail.')),

                        Number::make(__('Maximum Value'), 'config->rules->max')  
                            ->help(__('Determine the maximum of this detail.')),
                    ];
                }   
            }),

            $this->mergeWhen($this->field === 'Number', function() {
                return [ 
                    Number::make(__('Minimum Value'), 'config->rules->min')
                        ->onlyOnDetail(),

                    Number::make(__('Maximum Value'), 'config->rules->max')
                        ->onlyOnDetail(), 
                ];
            }),

            $this->mergeWhen(in_array($this->field, ['Select', 'BooleanGroup']), function() {
                return [ 
                    Tags::make(__('Available Options'), 'options')
                        ->onlyOnDetail(), 
                ];
            }),

            new SharedResources($request, $this),
    	];
    } 

    /**
     * Get the avaialble feilds.
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function fieldOptions()
    {
        return collect(forward_static_call([static::$model, 'fields']))->mapWithKeys(function($field) {
            return [
                $field => __(class_basename($field))
            ];
        });
    }  
}