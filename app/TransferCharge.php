<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferCharge extends APIModel
{
  protected $table = 'transfer_charges';
  protected $fillable = ['type', 'min_amount', 'max_amount', 'charge'];
}
