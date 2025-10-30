@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Contas a Receber</h1>

            <a href="{{ route('contas-receber.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center w-full sm:w-auto gap-2">
                <i class="fas fa-plus"></i> Nova Conta
            </a>
        </div>

        {{-- ✅ Filtros --}}
        <form method="GET" action="{{ route('contas-receber.index') }}" class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-4">
            <!-- Status -->
            <div>
                <label class="text-sm block mb-1 text-gray-700">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 p-2">
                    <option value="">Todos</option>
                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="recebido" {{ request('status') == 'recebido' ? 'selected' : '' }}>Recebido</option>
                    <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <!-- Período -->
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="text-sm block mb-1 text-gray-700">De</label>
                    <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
                        class="w-full rounded-lg border border-gray-300 p-2">
                </div>
                <div class="flex-1">
                    <label class="text-sm block mb-1 text-gray-700">Até</label>
                    <input type="date" name="data_fim" value="{{ request('data_fim') }}"
                        class="w-full rounded-lg border border-gray-300 p-2">
                </div>
            </div>

            <!-- Busca -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">Buscar</label>
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Ex: cliente, descrição, #ID"
                    class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Botões -->
            <div class="flex items-end space-x-2">
                <button
                    class="h-10 w-full md:w-auto px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
                @if(request()->query())
                    <a href="{{ route('contas-receber.index') }}"
                        class="h-10 w-full md:w-auto px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                        <i class="fas fa-rotate-left mr-2"></i>Limpar
                    </a>
                @endif
            </div>
        </form>

        {{-- ✅ Mensagens --}}
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

        {{-- ✅ Tabela --}}
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
                                {{ \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($conta->status === 'recebido')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Recebido</span>
                                @elseif($conta->status === 'cancelado')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Cancelado</span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium flex space-x-3">
                                <a href="{{ route('contas-receber.show', $conta->id_creceber) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('contas-receber.edit', $conta->id_creceber) }}"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('contas-receber.destroy', $conta->id_creceber) }}" method="POST"
                                    onsubmit="return confirm('Excluir esta conta?')">
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