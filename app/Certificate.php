<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificate extends APIModel
{
  protected $table = 'certificates';
  protected $fillable = ['account_id', 'payload', 'payload_value', 'url'];
}
