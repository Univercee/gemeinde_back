<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The locations that belong to the service.
 */
class Service extends Model
{
    public function roles(){
        return $this->belongsToMany(Location::class, 'location_services');
    }
}
