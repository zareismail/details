<?php

namespace Zareismail\Details\Models;  
     
use Illuminate\Database\Eloquent\Collection;
use Laravel\Nova\Resource;

class DetailCollection extends Collection
{  
    /**
     * Resolves the deails field.
     * 
     * @param  \Laravel\Nova\Resource $resource
     * @return $this            
     */
    public function fields(Resource $resource)
    { 
        return $this->forResource($resource)->map->resolveFieldForResource($resource);
    }

    /**
     * Filter the deails for the given resource.
     * 
     * @param  \Laravel\Nova\Resource $resource
     * @return $this            
     */
    public function forResource(Resource $resource)
    { 
        return $this->filter->isAvailableFor($resource);
    }
}
