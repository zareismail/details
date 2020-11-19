<?php

namespace Zareismail\Details\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model as LaravelModel, SoftDeletes};  
use Armincms\Targomaan\Concerns\InteractsWithTargomaan; 

class Model extends LaravelModel 
{ 
    use HasFactory, SoftDeletes, InteractsWithTargomaan;  

    /**
     * Get the config value with the given key.
     * 
     * @param  string $key    
     * @param  mixed $default
     * @return mixed         
     */
    public function config(string $key, $default = null)
    {
    	return data_get($this->config, $key, $default);
    }
}
