<?php

namespace App\Http\Controllers;
use App\Models\File;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles()
    {
        return Inertia::render('MyFiles');
    }
}
