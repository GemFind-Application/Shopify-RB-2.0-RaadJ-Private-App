<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppProxyController extends Controller
{
    public function index(){
      return response()->view('ringbuilder.ringbuilder')->header('Content-Type','application/liquid');
    }
}
