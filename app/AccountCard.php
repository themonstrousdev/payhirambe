<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountCard extends APIModel
{
  protected $table = 'account_cards';
  protected $fillable = ['account_id', 'payload', 'payload_value'];
}
