<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FluxoCaixaController extends Controller
{
    public function index()
    {
        return view('fluxo-caixa.index');
    }

    public function exportPdf()
    {
        // Lógica de exportação PDF
        return response()->json(['message' => 'PDF exportado com sucesso']);
    }

    public function exportExcel()
    {
        // Lógica de exportação Excel
        return response()->json(['message' => 'Excel exportado com sucesso']);
    }
}
