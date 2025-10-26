<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaReceber;
use App\Models\ContaPagar;
use App\Models\Agendamento;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $aReceber = ContaReceber::where('status', 'recebido')->sum('valor');
        $aPagar = ContaPagar::where('status', 'pago')->sum('valor');

        $hoje = Carbon::today()->toDateString();
        $agendamentosHoje = Agendamento::whereDate('data_hora', $hoje)->count();

        $saldoAtual = $aReceber - $aPagar;

        // ✅ Corrigido o caminho da view
        return view('dashboard.index', compact('aReceber', 'aPagar', 'agendamentosHoje', 'saldoAtual'));
    }
}
