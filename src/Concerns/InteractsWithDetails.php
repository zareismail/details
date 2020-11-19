<?php

namespace Zareismail\Details\Concerns; 

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zareismail\Details\Models\Detail;

trait InteractsWithDetails
{ 
	/**
	 * Query the related details.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function details(): BelongsToMany
	{
		return $this->morphToMany(Detail::class, 'detailsable', 'detailsable')->withPivot('value');
	}
} 