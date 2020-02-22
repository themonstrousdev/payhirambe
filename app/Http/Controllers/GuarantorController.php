<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Guarantor as GuarantorModel;
use Increment\Account\Models\Account;
use App\Mail\Guarantor;
use Carbon\Carbon;

class GuarantorController extends APIController
{
  public $notificationClass = 'Increment\Common\Notification\Http\NotificationController';

  function __construct(){
    if($this->checkAuthenticatedUser() == false){
      return $this->response();
    }
    $this->model = new GuarantorModel();
  }

  public function create(Request $request){
    $data = $request->all();
    $guarantorExist = GuarantorModel::where('email', '=', $data['email'])->where('sender', '=', $data['account_id'])->get();
    $sender = $this->retrieveAccountDetails($data['account_id']);
    $receiver = Account::where('email', '=', $data['email'])->get();

    if(sizeof($guarantorExist) == 0){
      $code = $this->generateCode();
      $guarantor = new GuarantorModel();
      $guarantor->code = $code;
      $guarantor->sender = $data['account_id'];
      $guarantor->receiver = (sizeof($receiver) > 0) ? $receiver[0]->id : null;
      $guarantor->email = $data['email'];
      $guarantor->status = 'pending';
      $guarantor->created_at = Carbon::now();
      $guarantor->save();
      if($guarantor){
        if(sizeof($receiver) > 0){
          $parameter = array(
            'to' => $receiver[0]->id,
            'from' => $data['account_id'],
            'payload' => 'guarantor',
            'payload_value' => $guarantor->id,
            'route' => '/profile/guarantor/'.$code,
            'created_at' => Carbon::now()
          );
          app($this->notificationClass)->createByParams($parameter);
        }
        Mail::to($data['email'])->send(new Guarantor($sender, $data['email'], $code));
        $this->response['data'] = $guarantor->id;
        $this->response['error'] = null;
      }
    }else{
      $this->reponse['data'] = null;
      $this->response['error'] = 'Email address already existed!';
    }
    return $this->response();
  }

  public function retrieve(Request $request){
    $data = $request->all();
    $this->response['data'] = GuarantorModel::where('sender', '=', $data['account_id'])->orWhere('receiver', '=', $data['account_id'])->orderBy('created_at', '=', 'desc')->get();
    return $this->response();
  }

  public function generateCode(){
    $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);
    $codeExist = GuarantorModel::where('code', '=', $code)->get();
    if(sizeof($codeExist) > 0){
      $this->generateCode();
    }else{
      return $code;
    }
  }

  public function getByParams($column, $value){
    $result = GuarantorModel::where($column, '=', $value)->where('status', '=', 'approved')->get();
    if(sizeof($result) > 0){
      $i = 0;
      foreach ($result as $key) {
        $result[$i]['account'] = $this->retrieveAccountDetails($result[$i]['receiver']);
        $i++;
      }
    }
    return sizeof($result) > 0 ? $result : null;
  }

  public function update(Request $request){
    $data = $request->all();
    $this->model = new GuarantorModel();
    $parameter = array(
      'id'  => $data['id'],
      'status'  => $data['status'],
    );
    $this->updateDB($parameter);
    // if($this->response['data'] == true){
    //   $notifParameter = array(
    //     'to' => $receiver[0]->id,
    //     'from' => $data['account_id'],
    //     'payload' => 'guarantor',
    //     'payload_value' => $guarantor->id,
    //     'route' => '/profile/guarantor/'.$code,
    //     'created_at' => Carbon::now()
    //   );
    //   app($this->notificationClass)->createByParams($notifParameter);
    // }
    return $this->response();
  }
}