<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Penalty;
class PenaltyController extends APIController
{
    function __construct(){
      $this->model = new Penalty();
    }

    public function getTotalPenalty($requestId, $accountId){
      $total = Penalty::where('request_id', '=', $requestId)->where('account_id', '=', $accountId)->where('status', '=', 0)->sum('amount');
      return $total;
    }
}
