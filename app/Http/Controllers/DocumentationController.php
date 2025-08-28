<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentationController extends Controller
{
    /**
     * Display documentation index
     */
    public function index()
    {
        return view('documentation.index');
    }

    /**
     * Generate User Flow PDF
     */
    public function generateUserFlowPdf()
    {
        $pdf = Pdf::loadView('documentation.user-flow-pdf')
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 150,
                'enable_css_float' => true
            ]);

        $filename = 'Altezza_Complete_A-Z_User_Flow_Guide_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Preview User Flow PDF in browser
     */
    public function previewUserFlowPdf()
    {
        return view('documentation.user-flow-pdf');
    }
}
