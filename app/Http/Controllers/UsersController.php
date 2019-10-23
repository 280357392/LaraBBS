<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    //
    public function index(){
        dd(date('Y年m月d日 H时i分s秒'));
        return 'index';
    }
}
