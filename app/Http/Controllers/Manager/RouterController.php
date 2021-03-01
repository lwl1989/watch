<?php

namespace App\Http\Controllers\Manager;


use App\Http\Controllers\Controller;
use App\Library\Constant\StaticRoute;

class RouterController extends Controller
{

        public function getRouter()
        {
            return ['router' =>  StaticRoute::getAllPath()];
        }
        
}