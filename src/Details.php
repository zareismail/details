<?php

namespace Zareismail\Details; 

use Illuminate\Http\Request;
use Laravel\Nova\Nova;  

class Details 
{   
    /**
     * Return Nova's resources that need details.
     *
     * @param  \
     use Illuminate\Http\Request $request
     * @return \Laravel\Nova\ResourceCollection
     */
    public static function moreDetailedResources(Request $request)
    {
        return Nova::authorizedResources($request)->filter(function($resource) { 
            return in_array(Contracts\MoreDetails::class, class_implements($resource)) ;
        });
    }
}
