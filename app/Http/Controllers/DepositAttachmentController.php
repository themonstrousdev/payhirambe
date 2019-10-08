<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DepositAttachment;
use Carbon\Carbon;

class DepositAttachmentController extends APIController
{
    function __construct(){
      $this->model = new DepositAttachment();
    }
    public function update(Request $request){
      $response = array(
        'data'  => null,
        'error' => null,
        'timestamps' => Carbon::now()
      );
      $data = $request->all();
      echo json_encode($data);
      $deposit = new DepositAttachment();
      $deposit->deposit_id = $data['activeId'];
      $deposit->file = $data['file'];
      $deposit->created_at = Carbon::now();
      $deposit->save();
    }
}
