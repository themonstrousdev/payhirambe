<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NotificationSetting;
use Carbon\Carbon;
class NotificationSettingController extends APIController
{
  function __construct(){
    $this->model = new NotificationSetting();
    $this->notRequired = array('code');
  }

  public function manageNotification($id){
  	$result = NotificationSetting::where('account_id', '=', $id)->get();
  	if(sizeof($result) > 0){
  		$result = $result[0];

  		if(intval($result['email_login']) == 1){
  			app('App\Http\Controllers\EmailController')->loginEmail($id);
  		}
  		if(intval($result['email_otp']) == 1 || intval($result['email_pin']) == 1){
  			$code = $this->otpCodeGenerator();
        $subject = null;
        $text = null;
        if(intval($result['email_otp']) == 1){
          $subject = 'OTP Notification';
          $text = "to continue your activity to ".env('APP_NAME').". Enjoy!";
        }
        if(intval($result['email_pin']) == 1){
          $subject = 'PIN Notification';
          $text = "as PIN everytime you have money transfer transaction from ".env('APP_NAME').". This is an autogenerated code everytime there's a login so do not share this to anyone unless authorized.";
        }
  			app('App\Http\Controllers\EmailController')->otpEmail($id, $code, $subject, $text);
  			NotificationSetting::where('account_id', '=', $id)->update(array(
  				'code'	=> $code,
  				'updated_at' => Carbon::now()
  			));
  		}
  		return true;
  	}
  	return false;
  }

  public function generateOTPFundTransfer($accountId){
    $code = $this->otpCodeGenerator();
    NotificationSetting::where('account_id', '=', $accountId)->update(array(
      'code' => $code,
      'updated_at' => Carbon::now()
    ));

    app('App\Http\Controllers\EmailController')->otpEmailFundTransfer($accountId, $code);
    return $code;
  }

  public function generateOTP(Request $request){
    $data = $request->all();
    $error = null;
    $previous = NotificationSetting::where('account_id', '=', $data['account_id'])->get();
    if(sizeof($previous) > 0){
      if($previous[0]['code'] != 'BLOCKED'){
        $code = $this->otpCodeGenerator();
        NotificationSetting::where('account_id', '=', $data['account_id'])->update(array(
          'code' => $code,
          'updated_at' => Carbon::now()
        ));
        app('App\Http\Controllers\EmailController')->otpEmailFundTransfer($data['account_id'], $code);
      }else{
        // check difference in updated
        $currentDate = Carbon::now();
        $blockedDate = Carbon::createFromFormat('Y-m-d H:i:s', $previous[0]['updated_at']);
        $diff = $currentDate->diffInMinutes($blockedDate);
        if($diff < env('OTP_BLOCK_LIMIT')){
          $error = "Your account still blocked! Please wait for 30 minutes.";
        }else{
          $error = null;
          $code = $this->otpCodeGenerator();
          NotificationSetting::where('account_id', '=', $data['account_id'])->update(array(
            'code' => $code,
            'updated_at' => Carbon::now()
          ));
        }
      }
    }else{
      $code = $this->otpCodeGenerator();
      $insertData = array(
        'code'        => $code,
        'account_id'  => $data['account_id'],
        'email_login' => 0,
        'email_otp'   => 0,
        'sms_login'   => 0,
        'sms_otp'     => 0,
        'created_at'  => Carbon::now()
      );
      NotificationSetting::insert($insertData);
      app('App\Http\Controllers\EmailController')->otpEmailFundTransfer($data['account_id'], $code);
    }
    
    return response()->json(array(
      'error' => $error,
      'attempt' => 0,
      'timestamps' => Carbon::now()
    ));
    
  }

  public function otpCodeGenerator(){
    $code = substr(str_shuffle("123456789"), 0, 6);
    $codeExist = NotificationSetting::where('code', '=', $code)->get();
    if(sizeof($codeExist) > 0){
      $this->generateCode();
    }else{
      return $code;
    }
  }

  public function getNotificationSettings($accountId){
    $result = NotificationSetting::where('account_id', '=', $accountId)->get();
    return (sizeof($result) > 0) ? $result[0] : null;
  }

  public function blockedAccount(Request $request){
    $data = $request->all();
    NotificationSetting::where('account_id', '=', $data['account_id'])->update(array(
      'code' => 'BLOCKED',
      'updated_at' => Carbon::now()
    ));
    return response()->json(array(
      'blocked' => true,
      'timestamps' => Carbon::now()
    ));
  }
}
