<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guarantor extends APIModel
{
  protected $table = 'guarantors';
  protected $fillable = ['code', 'sender', 'receiver','email','status'];

  public function getReceiverAttribute($value){
    return intval($value);
  }

  public function getSenderAttribute($value){
    return intval($value);
  }
}
