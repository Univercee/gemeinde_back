<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The services that belong to the location.
 */
class Location extends Model
{
    public function services(){
        return $this->belongsToMany(Service::class, 'location_services');
    }
}
