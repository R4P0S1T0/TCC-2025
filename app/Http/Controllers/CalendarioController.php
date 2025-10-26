<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agendamento;
use App\Models\Cliente;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    /**
     * Exibe o calendário com todos os agendamentos.
     */
    public function index()
    {
        $agendamentos = Agendamento::with('cliente')->get()->map(function ($ag) {
            return [
                'id' => $ag->id_agendamento,
                'title' => "{$ag->servico} - {$ag->cliente}",
                'start' => $ag->data_hora,
                'backgroundColor' => match($ag->servico) {
                    'Agendamento' => '#2563eb', // Azul
                    'Reunião' => '#16a34a',     // Verde
                    'Evento' => '#9333ea',      // Roxo
                    default => '#6b7280',
                },
                'textColor' => '#fff',
                'extendedProps' => [
                    'cliente' => $ag->cliente,
                    'status' => $ag->status,
                    'tipo' => $ag->servico,
                    'observacoes' => $ag->observacoes ?? '',
                    'data_hora' => Carbon::parse($ag->data_hora)->format('d/m/Y H:i'),
                ]
            ];
        });

        return view('calendario.index', compact('agendamentos'));
    }

    /**
     * Exibe o formulário de criação de um novo agendamento.
     */
    public function create()
    {
        $clientes = Cliente::where('status', 'ativo')
            ->orderBy('nome')
            ->get(['id_cliente', 'nome', 'telefone']);

        return view('calendario.create', compact('clientes'));
    }

    /**
     * Armazena um novo agendamento no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'servico' => 'required|in:Agendamento,Reunião,Evento',
            'data' => 'required|date',
            'hora' => 'required',
            'status' => 'required|in:pendente,confirmado,cancelado',
            'observacoes' => 'nullable|string',
        ]);

        $cliente = Cliente::findOrFail($request->id_cliente);

        Agendamento::create([
            'id_cliente' => $cliente->id_cliente,
            'cliente' => $cliente->nome,
            'servico' => $request->servico,
            'data_hora' => Carbon::parse("{$request->data} {$request->hora}"),
            'status' => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()
            ->route('calendario.index')
            ->with('success', 'Agendamento criado com sucesso!');
    }

    /**
     * Exibe o formulário de edição de um agendamento.
     */
    public function edit($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $clientes = Cliente::where('status', 'ativo')
            ->orderBy('nome')
            ->get(['id_cliente', 'nome']);

        return view('calendario.edit', compact('agendamento', 'clientes'));
    }

    /**
     * Atualiza um agendamento existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'servico' => 'required|string|in:Agendamento,Reunião,Evento',
            'data' => 'required|date',
            'hora' => 'required',
            'status' => 'required|string|in:pendente,confirmado,cancelado',
            'observacoes' => 'nullable|string',
        ]);

        $agendamento = Agendamento::findOrFail($id);
        $agendamento->servico = $request->servico;
        $agendamento->status = $request->status;
        $agendamento->data_hora = Carbon::parse("{$request->data} {$request->hora}");
        $agendamento->observacoes = $request->observacoes;
        $agendamento->save();

        return redirect()
            ->route('calendario.index')
            ->with('success', 'Agendamento atualizado com sucesso!');
    }

    /**
     * Exclui um agendamento.
     */
    public function destroy($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->delete();

        return redirect()
            ->route('calendario.index')
            ->with('success', 'Agendamento excluído com sucesso!');
    }
}
