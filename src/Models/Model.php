<?php

namespace Zareismail\Details\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model as LaravelModel, SoftDeletes};  
use Armincms\Targomaan\Concerns\InteractsWithTargomaan; 
use Zareismail\NovaContracts\Concerns\InteractsWithConfigs; 

class Model extends LaravelModel 
{ 
    use HasFactory, SoftDeletes, InteractsWithTargomaan, InteractsWithConfigs;  
}
