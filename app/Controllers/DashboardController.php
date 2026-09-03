<?php

namespace App\Controllers;

use App\Core\Render\Render;
use App\Models\User;
class DashboardController extends Controller{

    static function show(){
        echo "<pre>";
        print_r(User::where('users.id', 1, '>')->whereGroup(function($query){
            $query->where('type', 'admin')
                ->orWhere('token_form', '%-1%', 'LIKE');
        })->get());
        die;

        return render::view("Dashboard");
    }

    static function list(){


        return render::view("List");
    }
}