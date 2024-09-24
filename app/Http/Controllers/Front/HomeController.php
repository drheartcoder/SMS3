<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct()  
    {   
        $this->arr_view_data = [];
    }

    public function index()
    {
        $this->arr_view_data['title'] = config('app.project.name');
       return view('site_offline',$this->arr_view_data);
    }
}