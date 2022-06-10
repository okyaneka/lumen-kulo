<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilestoreController extends Controller
{
    //
    public function public($path)
    {
        return response($path);
        $file = Storage::get($path);
        return $file;
    }
}
