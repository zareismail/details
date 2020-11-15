<?php

namespace Zareismail\Details\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model as LaravelModel, SoftDeletes};  
use Armincms\Targomaan\Concerns\InteractsWithTargomaan;
use Zareismail\NovaContracts\Auth\Authorization;
use Zareismail\NovaContracts\Auth\Authorizable;  

class Model extends LaravelModel/* implements Authorizable*/
{
	use InteractsWithTargomaan; 
    use HasFactory, SoftDeletes; 
    // use Authorization; 
}
