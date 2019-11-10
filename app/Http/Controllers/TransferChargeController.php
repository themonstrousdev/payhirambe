<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransferCharge;
class TransferChargeController extends APIController
{
  function __construct(){
    $this->model = new Report();
  }
}
