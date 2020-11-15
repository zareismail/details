<?php

namespace Zareismail\Details\Models;  
     
use Laravel\Nova\Fields\{Text, Number, Select, Boolean, DateTime, Timezone, BooleanGroup};

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
    protected $namespace = '\\Laravel\\Nova\\Fields\\'; 

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

    public static function resolveField($field)
    {
        return static::$namespace.$field;
    }
}
