<?php

namespace Zareismail\Details\Models;  

use Illuminate\Support\Str;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\{Text, Number, Select, Boolean, DateTime, Timezone, BooleanGroup};
use Zareismail\Details\Details;

class Detail extends Model
{     
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [ 
    	'name' => 'json',
    	'help' => 'json',
    	'options' => 'json',
    	'config' => 'json',
    ]; 

    /**
     * Fields using namespace.
     *
     * @var array
     */
    protected static $namespace = '\\Laravel\\Nova\\Fields\\'; 

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new DetailCollection($models);
    } 

    /**
     * Query the related group.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    { 
        return $this->belongsTo(DetailGroup::class);
    } 

    /**
     * Get the available fields.
     * 
     * @return array
     */
    public static function fields()
    {
        return [
            'Text',
            'Number',
            'Select',
            'Boolean',
            'DateTime',
            'Timezone',
            'BooleanGroup' 
        ];
    }

    /**
     * Set the field namespace.
     * 
     * @return array
     */
    public static function namespace(string $namespace)
    {
        static::$namespace = rtrim($namespace, '\\').'\\';

        return static::class;
    } 

    /**
     * Create the field instance.
     * 
     * @return \Laravel\Nova\Fields\Field
     */
    public  function resolveField()
    {
        $class = static::$namespace.$this->field;

        return tap($class::make($this->name, "detail->{$this->id}"), function($field) { 
            $field->help($this->help ?? $this->name); 

            if(method_exists($field, 'options')) {
                $field->options(array_combine($this->options, $this->options));
            }

            // if(method_exists($field, 'displayUsingLabels')) {
            //     $field->displayUsingLabels();
            // }
        });
    } 

    /**
     * Create the field instance for the given resource.
     * 
     * @return \Laravel\Nova\Fields\Field
     */
    public  function resolveFieldForResource(Resource $resource)
    {
        return tap($this->resolveField(), function($field) use ($resource) {
            if($this->isRequiredFor($resource)) {
                $field->required()->rules('required');
            } else {
                $field->nullable();
            }
        }); 
    }

    /**
     * Determine if the field is required for the given resources.
     * 
     * @param  \Laravel\Nova\Resource $resource 
     * @return boolean             
     */
    public function isAvailableFor(Resource $resource)
    {
        return  $this->isRequiredFor($resource) || 
                $this->isExclusiveFor($resource) || 
                $this->isNotExcludedFor($resource) && $this->isNotExclusive();
    } 

    /**
     * Determine if the field is required for the given resources.
     * 
     * @param  \Laravel\Nova\Resource $resource 
     * @return boolean             
     */
    public function isRequiredFor(Resource $resource)
    {
        return boolval($this->config('required.'. $resource::uriKey()));  
    }  

    /**
     * Determine if the field is required for the given resources.
     * 
     * @param  \Laravel\Nova\Resource $resource 
     * @return boolean             
     */
    public function isOptionalFor(Resource $resource)
    {
        return ! $this->isRequiredFor($required);
    } 

    /**
     * Determine if the field is only available for some resources.
     *  
     * @return boolean             
     */
    public function isExclusive()
    {
        return collect($this->config('only'))->filter()->isNotEmpty(); 
    } 

    /**
     * Determine if the field is available for every resources.
     *  
     * @return boolean             
     */
    public function isNotExclusive()
    {
        return ! $this->isExclusive(); 
    } 

    /**
     * Determine if the field is only available for the given resources.
     * 
     * @param  \Laravel\Nova\Resource $resource 
     * @return boolean             
     */
    public function isExclusiveFor(Resource $resource)
    {
        return boolval($this->config('only.'. $resource::uriKey())); 
    } 

    /**
     * Determine if the field is excluded for the given resources.
     * 
     * @param  \Laravel\Nova\Resource $resource 
     * @return boolean             
     */
    public function isExcludedFor(Resource $resource)
    {
        return boolval($this->config('except.'. $resource::uriKey()));
    }

    /**
     * Determine if the field is not excluded for the given resources.
     * 
     * @param  \Laravel\Nova\Resource $resource 
     * @return boolean             
     */
    public function isNotExcludedFor(Resource $resource)
    {
        return ! $this->isExcludedFor($resource);
    }  

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    { 
        $resource = Details::moreDetailedResources(request())->first(function($resource) use ($method) {
            return Str::plural(Str::camel($resource::uriKey())) === $method;
        });

        if(! is_null($resource)) {
            return $this->morphedByMany($resource::$model, 'detailsable', 'detailsable');
        }

        return parent::__call($method, $parameters);
    }
}
