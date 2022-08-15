<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class GeneratePdfController extends Controller
{
    public function generate()
    {
        return view('report.pdf');
        // $pdf = PDF::loadView('report.pdf');
        // return $pdf->download('file.pdf');
    }

}
