<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class PDFController extends Controller
{
    public function generatePDF()
    {
        $data = ['title' => 'Department of Social Welfare and Development, Field Office XI'];

        $pdf = PDF::loadView('reports.sample', $data)->setPaper('folio','landscape');

        return $pdf->stream('sample.pdf');

        // For downloading the PDF instead of viewing, use the line below:
        // return $pdf->download('sample.pdf');
    }
}
