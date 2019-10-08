<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Work;


class WorkController extends APIController
{
    function __construct(){
        $this->model = new Work();
        $this->notRequired = array('month_ended','year_ended');
    }

    public function retrieve(Request $request){
      $data = $request->all();
      $this->retrieveDB($data);
      $result = $this->response['data'];
      if(sizeof($result) > 0){
        $i = 0;
        foreach($result as $key){
          $this->response['data'][$i]['flag'] = false;
          $i++;
        }
      }
      return $this->response();
    }

    public function getByParams($column, $value){
      $result = Work::where($column, '=', $value)->get();
      $i = 0;
      foreach ($result as $key) {
        $result[$i]['verified'] = true;
        $i++;
      }
      return (sizeof($result) > 0) ? $result : null;
    }
}
