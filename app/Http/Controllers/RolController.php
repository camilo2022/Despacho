<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolController extends Controller
{
    public function showAll(){
        return response()->json(Rol::get());
    }
}