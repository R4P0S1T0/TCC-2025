<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FluxoCaixaController extends Controller
{
    public function index()
{
    $inicioSemana = \Carbon\Carbon::now()->startOfWeek();
    $fimSemana = \Carbon\Carbon::now()->endOfWeek();

    // Agrupar por data: entradas
    $entradasPorDia = \DB::table('contas_receber')
        ->selectRaw('DATE(data_vencimento) as data, SUM(valor) as total')
        ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
        ->where('status', 'recebido')
        ->groupBy('data')
        ->pluck('total', 'data');

    // Agrupar por data: saídas
    $saidasPorDia = \DB::table('contas_pagar')
        ->selectRaw('DATE(data_vencimento) as data, SUM(valor) as total')
        ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
        ->where('status', 'pago')
        ->groupBy('data')
        ->pluck('total', 'data');

    // Combinar e calcular saldo por dia
    $datas = collect($entradasPorDia->keys())
        ->merge($saidasPorDia->keys())
        ->unique()
        ->sort()
        ->values();

    $transacoes = $datas->map(function ($data) use ($entradasPorDia, $saidasPorDia) {
        $entradas = $entradasPorDia[$data] ?? 0;
        $saidas = $saidasPorDia[$data] ?? 0;
        $saldo = $entradas - $saidas;

        return (object) [
            'data' => $data,
            'entradas' => $entradas,
            'saidas' => $saidas,
            'saldo' => $saldo,
        ];
    });

    $entradasTotal = $entradasPorDia->sum();
    $saidasTotal = $saidasPorDia->sum();
    $saldoTotal = $entradasTotal - $saidasTotal;

    return view('fluxo-caixa.index', [
        'transacoes' => $transacoes,
        'entradas' => $entradasTotal,
        'saidas' => $saidasTotal,
        'saldo' => $saldoTotal,
    ]);
}


    public function exportPdf()
    {
        $inicioSemana = \Carbon\Carbon::now()->startOfWeek();
        $fimSemana = \Carbon\Carbon::now()->endOfWeek();

        $entradas = \DB::table('contas_receber')
            ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
            ->where('status', 'recebido')
            ->sum('valor');

        $saidas = \DB::table('contas_pagar')
            ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
            ->where('status', 'pago')
            ->sum('valor');

        $saldo = $entradas - $saidas;

        $recebimentos = \DB::table('contas_receber')
            ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
            ->where('status', 'recebido')
            ->select('descricao', 'valor', 'data_vencimento as data')
            ->addSelect(\DB::raw("'entrada' as tipo"));

        $pagamentos = \DB::table('contas_pagar')
            ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
            ->where('status', 'pago')
            ->select(\DB::raw("CONCAT('NF: ', IFNULL(nota_fiscal, 'Sem nota')) as descricao"), 'valor', 'data_vencimento as data')
            ->addSelect(\DB::raw("'saida' as tipo"));

        $transacoes = $recebimentos->unionAll($pagamentos)->orderBy('data', 'desc')->get();

        $pdf = Pdf::loadView('fluxo-caixa.pdf', [
            'transacoes' => $transacoes,
            'entradas' => $entradas,
            'saidas' => $saidas,
            'saldo' => $saldo,
            'inicioSemana' => $inicioSemana,
            'fimSemana' => $fimSemana
        ]);

        return $pdf->download('fluxo_caixa_' . now()->format('d_m_Y') . '.pdf');
    }

    public function exportCsv()
{
    $inicioSemana = \Carbon\Carbon::now()->startOfWeek();
    $fimSemana = \Carbon\Carbon::now()->endOfWeek();

    // Buscar entradas (contas a receber)
    $entradas = \DB::table('contas_receber')
        ->select(
            \DB::raw("'Entrada' as tipo"),
            'descricao',
            'valor',
            'data_vencimento as data',
            'status'
        )
        ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
        ->where('status', 'recebido');

    // Buscar saídas (contas a pagar)
    $saidas = \DB::table('contas_pagar')
        ->select(
            \DB::raw("'Saída' as tipo"),
            \DB::raw("CONCAT('NF: ', IFNULL(nota_fiscal, 'Sem nota')) as descricao"),
            'valor',
            'data_vencimento as data',
            'status'
        )
        ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
        ->where('status', 'pago');

    // Unir e ordenar
    $movimentos = $entradas->unionAll($saidas)
        ->orderBy('data', 'asc')
        ->get();

    // Calcular saldo acumulado
    $saldo = 0;
    $dados = $movimentos->map(function ($m) use (&$saldo) {
        if ($m->tipo === 'Entrada') {
            $saldo += $m->valor;
        } else {
            $saldo -= $m->valor;
        }

        return [
            'Data' => \Carbon\Carbon::parse($m->data)->format('d/m/Y'),
            'Tipo' => $m->tipo,
            'Descrição' => $m->descricao,
            'Valor (R$)' => number_format($m->valor, 2, ',', '.'),
            'Status' => ucfirst($m->status),
            'Saldo Acumulado (R$)' => number_format($saldo, 2, ',', '.'),
        ];
    });

    // Gerar CSV
    $csv = \League\Csv\Writer::createFromString('');
    $csv->insertOne(array_keys($dados->first() ?? []));
    $csv->insertAll($dados->toArray());

    $nomeArquivo = 'extrato_fluxo_caixa_' . now()->format('d_m_Y') . '.csv';

    return response((string) $csv)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', "attachment; filename=\"$nomeArquivo\"");
}



}
