<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Certificate;


class CertificateController extends APIController
{
    function __construct(){
        $this->model = new Certificate();
    }

    
}
