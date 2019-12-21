<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestImage extends APIModel
{
  protected $table = 'request_images';
  protected $fillable = ['request_id', 'url'];
}
