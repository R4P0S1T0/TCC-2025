@extends('layouts.app')

@section('title', 'Compras')

@section('content')
    <div class="space-y-6">
        <!-- Cabeçalho -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Compras</h1>
            <a href="{{ route('compras.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center w-full sm:w-auto">
                <i class="fas fa-plus mr-2"></i>Nova Compra
            </a>
        </div>

        {{-- ✅ Filtros Avançados --}}
        <form method="GET" action="{{ route('compras.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">
            {{-- Status --}}
            <div>
                <label class="text-sm block mb-1 text-gray-700">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 p-2">
                    <option value="">Todos</option>
                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="finalizada" {{ request('status') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            {{-- Tipo --}}
            <div>
                <label class="text-sm block mb-1 text-gray-700">Tipo</label>
                <select name="tipo" class="w-full rounded-lg border border-gray-300 p-2">
                    <option value="">Todos</option>
                    <option value="produto" {{ request('tipo') == 'produto' ? 'selected' : '' }}>Produto</option>
                    <option value="serviço" {{ request('tipo') == 'serviço' ? 'selected' : '' }}>Serviço</option>
                </select>
            </div>

            {{-- Fornecedor --}}
            <div>
                <label class="text-sm block mb-1 text-gray-700">Fornecedor</label>
                <select name="fornecedor" class="w-full rounded-lg border border-gray-300 p-2">
                    <option value="">Todos</option>
                    @foreach($fornecedores as $fornecedor)
                        <option value="{{ $fornecedor->id_fornecedor }}" {{ request('fornecedor') == $fornecedor->id_fornecedor ? 'selected' : '' }}>
                            {{ $fornecedor->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Data Inicial --}}
            <div>
                <label class="text-sm block mb-1 text-gray-700">De</label>
                <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
                    class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Data Final --}}
            <div>
                <label class="text-sm block mb-1 text-gray-700">Até</label>
                <input type="date" name="data_fim" value="{{ request('data_fim') }}"
                    class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Busca e Botões --}}
            <div class="md:col-span-5 flex flex-col sm:flex-row items-end gap-3 mt-2">
                <div class="flex-1">
                    <label for="busca" class="block text-sm font-medium mb-1 text-gray-700">Buscar Compra</label>
                    <input type="text" id="busca" name="busca" value="{{ request('busca') }}"
                        placeholder="Ex: #12 ou 'equipamento'"
                        class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                </div>

                <div class="flex space-x-2">
                    <button
                        class="h-10 w-full md:w-auto px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>

                    @if(request()->hasAny(['status', 'tipo', 'fornecedor', 'busca', 'data_inicio', 'data_fim']))
                        <a href="{{ route('compras.index') }}"
                            class="h-10 w-full md:w-auto px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                            <i class="fas fa-rotate-left mr-2"></i>Limpar
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Mensagens de sucesso/erro --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Sucesso!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Erro!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- ✅ Tabela de Compras -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Fornecedor</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($compras as $compra)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">#{{ $compra->id_compra }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $compra->descricao }}</div>
                                @if($compra->observacoes)
                                    <div class="text-gray-500 text-sm">{{ Str::limit($compra->observacoes, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($compra->tipo == 'produto')
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Produto</span>
                                @elseif($compra->tipo == 'serviço')
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Serviço</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $compra->fornecedor_nome ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                R$ {{ number_format($compra->valor_total, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                {{ \Carbon\Carbon::parse($compra->data_compra)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($compra->status == 'finalizada')
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Finalizada</span>
                                @elseif($compra->status == 'cancelada')
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">Cancelada</span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pendente</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-3">
                                    <a href="{{ route('compras.show', $compra->id_compra) }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('compras.edit', $compra->id_compra) }}"
                                        class="text-indigo-600 hover:text-indigo-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('compras.destroy', $compra->id_compra) }}" method="POST"
                                        onsubmit="return confirm('Excluir esta compra?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Nenhuma compra encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection