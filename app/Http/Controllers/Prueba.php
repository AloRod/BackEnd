<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Prueba extends Controller
{
    public function prueba(){
        return response()->json(
            ["message" => "PRUEBA EXITOSA"]
        );

    }
}
