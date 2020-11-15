<?php

namespace Zareismail\Details\Nova; 

use Illuminate\Http\Request; 
use Laravel\Nova\Fields\{ID, Text, Number, Select, BooleanGroup, BelongsTo};
use Armincms\Fields\{Targomaan, Chain};
use Superlatif\NovaTagInput\Tags;
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

            $this->mergeWhen(static::moreDetailedResources($request)->isNotEmpty(), function() {
                $resources = static::moreDetailedResources(app('request'))->pluck('label', 'name');

                return [ 
                    BooleanGroup::make(__('Except On The'), 'config->except')
                        ->options($resources)
                        ->help(__('The user never sees this on the selected pages.')),

                    BooleanGroup::make(__('Only On The'), 'config->only')
                        ->options($resources)
                        ->help(__('The user sees this just in the selected pages.')),

                    BooleanGroup::make(__('Required On The'), 'config->required')
                        ->options($resources)
                        ->help(__('The user forces to fill this on the selected pages.')),
                ];
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
    	];
    } 

    /**
     * Get the avaialble feilds.
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function fieldOptions()
    {
        return collect(static::newModel()::fields())->mapWithKeys(function($field) {
            return [
                $field => __(class_basename($field))
            ];
        });
    } 

    /**
     * Return Nova's resources that need details.
     *
     * @param  \use Illuminate\Http\Request $request
     * @return \Laravel\Nova\ResourceCollection
     */
    public static function moreDetailedResources(Request $request)
    { 
        return Details::moreDetailedResources($request)->map(function($resource) {
            return [
                'label' => $resource::label(),
                'name' => $resource::uriKey(), 
            ];
        });
    }
}