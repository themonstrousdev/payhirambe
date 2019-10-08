<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comaker extends APIModel
{
    protected $table = 'comakers';
    protected $fillable = ['account_id', 'request_id', 'comaker', 'status'];
}

