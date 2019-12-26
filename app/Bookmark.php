<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends APIModel
{
  protected $table = 'bookmarks';
  protected $fillable = ['account_id', 'request_id'];

  public function getRequestIdAttribute($value){
    return intval($value);
  }

  public function getAccountIdAttribute($value){
    return intval($value);
  }
}
