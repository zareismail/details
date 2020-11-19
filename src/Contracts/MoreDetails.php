<?php

namespace Zareismail\Details\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;


interface MoreDetails
{
	/**
	 * Query the related details.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
	 */
	public function details(): BelongsToMany;
} 