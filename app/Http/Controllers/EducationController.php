<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Education;
use Carbon\Carbon;

class EducationController extends APIController
{
  function __construct(){
    $this->model = new Education();
    $this->notRequired = array(
      'year_started', 'year_ended', 'month_started', 'month_ended'
    );
  }

  public function getByParams($column, $value){
    $result = Education::where($column, '=', $value)->get();
    $i = 0;
    foreach ($result as $key) {
      $result[$i]['verified'] = true;
      $i++;
    }
    return (sizeof($result) > 0) ? $result : null;
  }
}
