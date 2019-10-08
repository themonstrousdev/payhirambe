<?php

namespace App\Http\Controllers;

use Mail;
use App\Mail\ResetPassword;
use App\Mail\Verification;
use App\Mail\ChangedPassword;
use App\Mail\Referral;
use App\Mail\LoginEmail;
use App\Mail\OtpEmail;
use App\Mail\NotifyReferrer;
use App\Mail\Receipt;
use App\Mail\NewMessage;
use App\Mail\Ledger;
use Illuminate\Http\Request;

class EmailController extends APIController
{
    function __construct(){  
    }

    public function resetPassword($id){
    	$user = $this->retrieveAccountDetails($id);
    	if($user != null){
    		Mail::to($user['email'])->send(new ResetPassword($user));
    		return true;
    	}
    	return false;
    }

    public function verification($id){
        $user = $this->retrieveAccountDetails($id);
        if($user != null){
            Mail::to($user['email'])->send(new Verification($user));
            return true;
        }
        return false;
    }

    public function changedPassword($id){
        $user = $this->retrieveAccountDetails($id);
        if($user != null){
            Mail::to($user['email'])->send(new ChangedPassword($user));
            return true;
        }
        return false;
    }

    public function loginEmail($id){
        $user = $this->retrieveAccountDetails($id);
        if($user != null){
            Mail::to($user['email'])->send(new LoginEmail($user));
            return true;
        }
        return false;
    }

    public function notifyReferrer($id){
        $user = $this->retrieveAccountDetails($id);
        if($user != null){
            Mail::to($user['email'])->send(new NotifyReferrer($user));
            return true;
        }
        return false;
    }

    public function otpEmail($id, $otpCode){
        $user = $this->retrieveAccountDetails($id);
        $text = "to continue login to ".env('APP_NAME').". Enjoy!";
        if($user != null){
            Mail::to($user['email'])->send(new OtpEmail($user, $otpCode, $text));
            return true;
        }
        return false;
    }

    public function otpEmailFundTransfer($id, $otpCode){
        $user = $this->retrieveAccountDetails($id);
        $text = "to continue for money transfer from your account.";
        if($user != null){
            Mail::to($user['email'])->send(new OtpEmail($user, $otpCode, $text));
            return true;
        }
        return false;
    }

    public function referral(Request $request){
        $data = $request->all();
        $user = $this->retrieveAccountDetails($data['account_id']);
        if($user != null){
            Mail::to($data['to_email'])->send(new Referral($user, $data['content'], $data['to_email']));
            $this->response['data'] = true;
        }
        return $this->response();
    }

    public function receipt($accountId, $data){
        $user = $this->retrieveAccountDetails($accountId);
        if($user != null && sizeof($data) > 0){
            Mail::to($user['email'])->send(new Receipt($user, $data[0]));
            return true;
        }
        return false;
    }

    public function ledger($accountId, $details, $subject){
        $user = $this->retrieveAccountDetails($accountId);
        if($user != null){
            Mail::to($user['email'])->send(new Ledger($user, $details, $subject));
            return true;
        }
        return false;
    }

    public function newMessage($accountId){
        $online = app('Increment\Account\Http\AccountOnlineController')->getStatus($accountId);
        $user = $this->retrieveAccountDetails($accountId);
        if($user != null && $online == false){
            Mail::to($user['email'])->send(new NewMessage($user));
            return true;
        }
        return false;
    }

    public function trial(Request $request){
        $data = $request->all();
        $user = $this->retrieveAccountDetails($data['account_id']);
        if($user != null){
            Mail::to($user['email'])->send(new LoginEmail($user));
            $this->response['data'] = true;
        }
        return $this->response();
    }


    public function investment($accountId, $details, $subject){
        $user = $this->retrieveAccountDetails($accountId);
        if($user != null){
            Mail::to($user['email'])->send(new Ledger($user, $details, $subject));
            return true;
        }
        return false;
    }
}