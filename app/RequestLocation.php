<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestLocation extends APIModel
{
    protected $table = 'request_locations';
    protected $fillable = ['request_id', 'longitude', 'latitude', 'route', 'locality', 'region', 'country'];
}
