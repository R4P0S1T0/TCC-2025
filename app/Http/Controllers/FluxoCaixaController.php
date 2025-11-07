<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FluxoCaixaController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->get('periodo', 'semanal');

        // 🔹 Define o intervalo de acordo com o período selecionado
        switch ($periodo) {
            case 'diario':
                $inicio = Carbon::today();
                $fim = Carbon::today();
                break;

            case 'mensal':
                $inicio = Carbon::now()->startOfMonth();
                $fim = Carbon::now()->endOfMonth();
                break;

            case 'anual':
                $inicio = Carbon::now()->startOfYear();
                $fim = Carbon::now()->endOfYear();
                break;

            default:
                $inicio = Carbon::now()->startOfWeek();
                $fim = Carbon::now()->endOfWeek();
                break;
        }

        // 🔹 Entradas (Contas a Receber)
        $entradasPorDia = DB::table('contas_receber')
            ->selectRaw('DATE(data_vencimento) as data, SUM(valor) as total')
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'recebido')
            ->groupBy('data')
            ->pluck('total', 'data');

        // 🔹 Saídas (Contas a Pagar)
        $saidasPorDia = DB::table('contas_pagar')
            ->selectRaw('DATE(data_vencimento) as data, SUM(valor) as total')
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'pago')
            ->groupBy('data')
            ->pluck('total', 'data');

        // 🔹 Combina as datas e gera extrato
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

        // 🔹 Totais do período
        $entradasTotal = $entradasPorDia->sum();
        $saidasTotal = $saidasPorDia->sum();
        $saldoTotal = $entradasTotal - $saidasTotal;

        // 🔹 Análise Mensal
        $mediaEntradasDiaria = $periodo === 'mensal'
            ? round($entradasTotal / max(1, $datas->count()), 2)
            : null;

        $mediaSaidasDiaria = $periodo === 'mensal'
            ? round($saidasTotal / max(1, $datas->count()), 2)
            : null;

        $diasPositivos = $periodo === 'mensal'
            ? $transacoes->where('saldo', '>=', 0)->count()
            : null;

        $diasNegativos = $periodo === 'mensal'
            ? $transacoes->where('saldo', '<', 0)->count()
            : null;

        $saldoMensal = $periodo === 'mensal' ? $saldoTotal : null;

        // 🔹 Análise Anual
        if ($periodo === 'anual') {
            // Agrupa entradas e saídas por mês
            $entradasPorMes = DB::table('contas_receber')
                ->selectRaw('MONTH(data_vencimento) as mes, SUM(valor) as total')
                ->whereBetween('data_vencimento', [$inicio, $fim])
                ->where('status', 'recebido')
                ->groupBy('mes')
                ->pluck('total', 'mes');

            $saidasPorMes = DB::table('contas_pagar')
                ->selectRaw('MONTH(data_vencimento) as mes, SUM(valor) as total')
                ->whereBetween('data_vencimento', [$inicio, $fim])
                ->where('status', 'pago')
                ->groupBy('mes')
                ->pluck('total', 'mes');

            $meses = collect(range(1, 12));

            $mesesPositivos = 0;
            $mesesNegativos = 0;

            $meses->each(function ($m) use ($entradasPorMes, $saidasPorMes, &$mesesPositivos, &$mesesNegativos) {
                $entrada = $entradasPorMes[$m] ?? 0;
                $saida = $saidasPorMes[$m] ?? 0;
                if (($entrada - $saida) >= 0) $mesesPositivos++;
                else $mesesNegativos++;
            });

            $mediaEntradasMensal = round($entradasPorMes->avg() ?? 0, 2);
            $mediaSaidasMensal = round($saidasPorMes->avg() ?? 0, 2);
            $saldoAnual = $saldoTotal;
        } else {
            $mediaEntradasMensal = null;
            $mediaSaidasMensal = null;
            $mesesPositivos = null;
            $mesesNegativos = null;
            $saldoAnual = null;
        }

        return view('fluxo-caixa.index', [
            'transacoes' => $transacoes,
            'entradas' => $entradasTotal,
            'saidas' => $saidasTotal,
            'saldo' => $saldoTotal,
            'periodo' => $periodo,
            'mediaEntradasDiaria' => $mediaEntradasDiaria,
            'mediaSaidasDiaria' => $mediaSaidasDiaria,
            'diasPositivos' => $diasPositivos,
            'diasNegativos' => $diasNegativos,
            'saldoMensal' => $saldoMensal,
            'mediaEntradasMensal' => $mediaEntradasMensal,
            'mediaSaidasMensal' => $mediaSaidasMensal,
            'mesesPositivos' => $mesesPositivos,
            'mesesNegativos' => $mesesNegativos,
            'saldoAnual' => $saldoAnual,
        ]);
    }

    // -------------------- EXPORTAR PDF --------------------
    public function exportPdf(Request $request)
    {
        $periodo = $request->get('periodo', 'semanal');

        // Mesmo intervalo de tempo do index
        switch ($periodo) {
            case 'diario':
                $inicio = Carbon::today();
                $fim = Carbon::today();
                break;
            case 'mensal':
                $inicio = Carbon::now()->startOfMonth();
                $fim = Carbon::now()->endOfMonth();
                break;
            case 'anual':
                $inicio = Carbon::now()->startOfYear();
                $fim = Carbon::now()->endOfYear();
                break;
            default:
                $inicio = Carbon::now()->startOfWeek();
                $fim = Carbon::now()->endOfWeek();
                break;
        }

        $entradas = DB::table('contas_receber')
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'recebido')
            ->sum('valor');

        $saidas = DB::table('contas_pagar')
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'pago')
            ->sum('valor');

        $saldo = $entradas - $saidas;

        $recebimentos = DB::table('contas_receber')
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'recebido')
            ->select('descricao', 'valor', 'data_vencimento as data')
            ->addSelect(DB::raw("'entrada' as tipo"));

        $pagamentos = DB::table('contas_pagar')
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'pago')
            ->select(DB::raw("CONCAT('NF: ', IFNULL(nota_fiscal, 'Sem nota')) as descricao"), 'valor', 'data_vencimento as data')
            ->addSelect(DB::raw("'saida' as tipo"));

        $movimentos = $recebimentos->get()->merge($pagamentos->get())->sortBy('data');

        $pdf = Pdf::loadView('fluxo-caixa.pdf', [
            'movimentos' => $movimentos,
            'entradas' => $entradas,
            'saidas' => $saidas,
            'saldo' => $saldo,
            'inicio' => $inicio,
            'fim' => $fim,
            'periodo' => $periodo,
        ]);

        return $pdf->download('fluxo_caixa_' . now()->format('d_m_Y') . '.pdf');
    }

    // -------------------- EXPORTAR CSV --------------------
    public function exportExcel(Request $request)
    {
        $periodo = $request->get('periodo', 'semanal');

        switch ($periodo) {
            case 'diario':
                $inicio = Carbon::today();
                $fim = Carbon::today();
                break;
            case 'mensal':
                $inicio = Carbon::now()->startOfMonth();
                $fim = Carbon::now()->endOfMonth();
                break;
            case 'anual':
                $inicio = Carbon::now()->startOfYear();
                $fim = Carbon::now()->endOfYear();
                break;
            default:
                $inicio = Carbon::now()->startOfWeek();
                $fim = Carbon::now()->endOfWeek();
                break;
        }

        $entradas = DB::table('contas_receber')
            ->select(DB::raw("'Entrada' as tipo"), 'descricao', 'valor', 'data_vencimento as data', 'status')
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'recebido');

        $saidas = DB::table('contas_pagar')
            ->select(
                DB::raw("'Saída' as tipo"),
                DB::raw("CONCAT('NF: ', IFNULL(nota_fiscal, 'Sem nota')) as descricao"),
                'valor',
                'data_vencimento as data',
                'status'
            )
            ->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'pago');

        $movimentos = $entradas->get()->merge($saidas->get())->sortBy('data');

        $saldo = 0;
        $dados = $movimentos->map(function ($m) use (&$saldo) {
            if ($m->tipo === 'Entrada') $saldo += $m->valor;
            else $saldo -= $m->valor;

            return [
                'Data' => Carbon::parse($m->data)->format('d/m/Y'),
                'Tipo' => $m->tipo,
                'Descrição' => $m->descricao,
                'Valor (R$)' => number_format($m->valor, 2, ',', ''),
                'Status' => ucfirst($m->status),
                'Saldo Acumulado (R$)' => number_format($saldo, 2, ',', ''),
            ];
        });

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($handle, array_keys($dados->first() ?? []), ',');
        foreach ($dados as $linha) fputcsv($handle, $linha, ',');

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        $fileName = 'fluxo_caixa_' . now()->format('d_m_Y_His') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"");
    }
}
