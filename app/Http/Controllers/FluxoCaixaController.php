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

    // Total de entradas (contas recebidas)
    $totalEntradas = \DB::table('contas_receber')
        ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
        ->where('status', 'recebido')
        ->sum('valor');

    // Total de saídas (contas pagas)
    $totalSaidas = \DB::table('contas_pagar')
        ->whereBetween('data_vencimento', [$inicioSemana, $fimSemana])
        ->where('status', 'pago')
        ->sum('valor');

    // Lucro (diferença entre entradas e saídas)
    $lucro = $totalEntradas - $totalSaidas;

    // Nome do arquivo
    $filename = 'resumo_fluxo_caixa_' . now()->format('d_m_Y') . '.csv';
    $handle = fopen('php://memory', 'r+');

    // Cabeçalhos
    fputcsv($handle, ['Data', 'Entradas (R$)', 'Saídas (R$)', 'Lucro (R$)'], ',');

    // Linha de dados
    fputcsv($handle, [
        now()->format('d/m/Y'),
        number_format($totalEntradas, 2, '.', ''),
        number_format($totalSaidas, 2, '.', ''),
        number_format($lucro, 2, '.', '')
    ], ',');

    rewind($handle);
    $contents = stream_get_contents($handle);
    fclose($handle);

    return response($contents)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}


}
