<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InvestorLocation;
class InvestorLocationController extends APIController
{
  function __construct(){
    $this->model = new InvestorLocation();
  }

  public function retrieve(Request $request){
    $data = $request->all();
    $this->model = new InvestorLocation();
    $this->retrieveDB($data);
    $result = $this->response['data'];
    if(sizeof($result) > 0){
      $i = 0;

      foreach ($result as $key) {
        $this->response['data'][$i]['account'] = $this->retrieveAccountDetails($result[$i]['account_id']);
        $i++;
      }
    }
    return $this->response();
  }

  public function getByParams($column, $value){
    $result = InvestorLocation::where($column, $value)->get();
    //echo json_encode($result);
    $response = null;
    if(sizeof($result) > 0){
      $i = 0;
      $locality = array();
      foreach ($result as $key) {
        $locality[$i] = $result[$i]['locality'];
        $i++;
      }
      $response = array(
        'region' => $result[0]['region'],
        'country' => $result[0]['country'],
        'locality' => $locality,
        'result' => $result
      );
    }
    return $response;
  }
}
