<?php

namespace Skywalker\Location\Traits;

use Skywalker\Location\Facades\Location as LocationFacade;
use Skywalker\Location\Models\Location as LocationModel;

trait UsesLocation
{
    /**
     * The location relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function location()
    {
        return $this->morphOne(LocationModel::class, 'authenticatable');
    }

    /**
     * Detect and save the model's location.
     *
     * @param  string|null  $ip
     * @return \Skywalker\Location\Models\Location|null
     */
    public function detectLocation($ip = null)
    {
        if ($position = LocationFacade::get($ip)) {
            return $this->location()->updateOrCreate([], $position->toArray());
        }

        return null;
    }
}

