<?php

namespace Zareismail\Details\Models;


class DetailGroup extends Model
{  
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    	'name' => 'array' 
    ]; 

	/**
	 * Query the related details.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function details()
	{ 
		return $this->hasMany(Detail::class, 'group_id');
	} 
}
