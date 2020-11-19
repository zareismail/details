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
            $model = $resource::newModel();

            return $model instanceof Contracts\MoreDetails; 
        });
    }
}
