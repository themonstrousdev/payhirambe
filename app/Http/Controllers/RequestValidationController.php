<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestValidation;
class RequestValidationController extends APIController
{   
  function __construct(){
    $this->model = new RequestValidation();
  }

  public function getByParams($column, $value){
    $requirements = array(
      array(
        'payload' => 'id',
        'title'   => 'Receiver\'s ID',
        'validations' => null
      ),
      array(
        'payload' => 'photo',
        'title'   => 'Receiver\'s Photo',
        'validations' => null
      ),
      array(
        'payload' => 'signature',
        'title'   => 'Receiver\'s Signature',
        'validations' => null
      )
    );
    $i = 0;
    $flag = true;
    $transferStatus = 'approved';
    foreach ($requirements as $key) {
      $validations = RequestValidation::where($column, '=', $value)->where('payload', '=', $key['payload'])->get();
      $requirements[$i]['validations'] = sizeof($validations) > 0 ? $validations[0] : null;
      if($flag == true && sizeof($validations) == 0){
        $flag = false;
      }
      if($transferStatus == 'approved' && sizeof($validations) > 0 && $validations[0]['status'] != 'approved'){
        $transferStatus = $validations[0]['status'];
      }
      $i++;
    }
    
    return array(
      'complete_status' => $flag,
      'requirements' => $requirements,
      'transfer_status' => $transferStatus
    );
  }
}
