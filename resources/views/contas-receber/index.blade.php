@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800">Contas a Receber</h1>
        <a href="{{ route('contas-receber.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Nova Conta
        </a>

        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($contas as $conta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $conta->descricao ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $conta->cliente_nome ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">R$ {{ number_format($conta->valor, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @php
        $data = $conta->data_vencimento ?? null;
        if ($data && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $data = \Carbon\Carbon::createFromFormat('Y-m-d', $data)->format('d/m/Y');
        }
                                @endphp
                                {{ $data ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($conta->status === 'recebido')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Recebido</span>
                                @elseif($conta->status === 'cancelado')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Cancelado</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium flex space-x-3">
                                <a href="{{ route('contas-receber.show', $conta->id_creceber) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('contas-receber.edit', $conta->id_creceber) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('contas-receber.destroy', $conta->id_creceber) }}" method="POST" onsubmit="return confirm('Excluir esta conta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Nenhuma conta encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
