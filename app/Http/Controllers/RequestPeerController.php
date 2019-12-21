<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestPeer;
class RequestPeerController extends Controller
{
  public $notificationClass = 'Increment\Common\Notification\Http\NotificationController';
  function __construct(){
    $this->model = new RequestPeer();
  }

  public function create(Request $request){
    $data = $request->all();
    $data['code'] = $this->generateCode();
    $this->model = new RequestPeer();
    $this->insertDB($data);
    if($this->response['data'] > 0){
      // notifications
      $parameter = array(
        'to' => $data['to'],
        'from' => $data['account_id'],
        'payload' => 'peer',
        'payload_value' => $this->response['data'],
        'route' => '/peers/'.$data['code'],
        'created_at' => Carbon::now()
      );
      app($this->notificationClass)->createByParams($parameter);
    }
    return $this->response();
  }
  
  public function generateCode(){
    $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);
    $codeExist = RequestPeer::where('code', '=', $code)->get();
    if(sizeof($codeExist) > 0){
      $this->generateCode();
    }else{
      return $code;
    }
  }
}
