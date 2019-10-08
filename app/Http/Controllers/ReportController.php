<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Report;
class ReportController extends APIController
{
    function __construct(){
      $this->model = new Report();
    }
}
