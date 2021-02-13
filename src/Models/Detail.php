<?php

namespace Zareismail\Details\Models;  

use Illuminate\Support\Str;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\{File, Text, Number, Select, Boolean, Date, DateTime, Timezone, BooleanGroup}; 
use Zareismail\NovaContracts\Concerns\ShareableResource; 

class Detail extends Model
{     
    use ShareableResource;

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
            'File',
            'Number',
            'Select',
            'Boolean',
            'Date',
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

            if(method_exists($field, 'download')) {
                $field->download(function($request, $resource, $disk, $path) use ($field) { 
                    $detail = $resource->details->find($this);
                    
                    return \Storage::disk($disk)->download(data_get($detail, 'pivot.value')); 
                });
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
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    { 
        $resource = static::sharedResources(request())->first(function($resource) use ($method) {
            return Str::plural(Str::camel($resource::uriKey())) === $method;
        });

        if(! is_null($resource)) {
            return $this->morphedByMany($resource::$model, 'detailsable', 'detailsable');
        }

        return parent::__call($method, $parameters);
    } 

    /**
     * Get the sharing contracts interface.
     *  
     * @return string            
     */
    public static function sharingContract(): string
    {
        return \Zareismail\Details\Contracts\MoreDetails::class;
    } 

    /**
     * Determine share condition.
     * 
     * @param  \Laravel\Nova\Resource $resource
     * @param  string $condition 
     * @return bool            
     */
    public function sharedAs($resource, string $condition): bool
    {
        return boolval($this->getConfig($condition.'.'.$resource::uriKey()));
    } 
}
