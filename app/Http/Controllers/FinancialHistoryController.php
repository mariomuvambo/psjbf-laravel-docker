<?php

namespace App\Http\Controllers;

// use App\Models\FinancialHistory;
use App\Models\Doacao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinancialHistoryController extends Controller
{
    //
    public function index()
{
     return Doacao::orderBy('data_doacao', 'desc')->get();
}

public function exportPdf()
{
    $histories = Doacao::orderBy('data_doacao', 'desc')->get();
    $pdf = Pdf::loadView('pdf.financeiro', compact('histories'));
    return $pdf->download('historico-financeiro.pdf');
}
}
