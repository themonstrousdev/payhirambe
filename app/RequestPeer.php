<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestPeer extends APIModel
{
  protected $table = 'request_peers';
  protected $fillable = ['request_id', 'request_id', 'charge', 'currency', 'status'];
}
