@extends('layouts.app')
@section('title', 'Contas a Pagar')

@section('content')
<div class="p-6">
    {{-- ✅ Mensagem de sucesso --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- ✅ Cabeçalho --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Contas a Pagar</h1>
        <a href="{{ route('contas-pagar.create') }}"
           class="px-4 py-2 rounded-lg bg-blue-700 text-white hover:bg-gray-600 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Nova Conta
        </a>
    </div>

    {{-- ✅ Filtros --}}
    <form method="GET" class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-3">
        <div>
            <label class="text-sm block mb-1 text-gray-700">Status</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 p-2">
                <option value="">Todos</option>
                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
            </select>
        </div>

        <div>
            <label for="busca_compra" class="block text-sm font-medium mb-1 text-gray-700">
                Buscar Compra
            </label>
            <input type="text" id="busca_compra" name="busca_compra"
                   value="{{ request('busca_compra') }}"
                   placeholder="Ex: #12 ou 'equipamentos'"
                   class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
        </div>

        <div class="flex items-end">
            <button
                class="h-10 w-full md:w-auto px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition">
                Filtrar
            </button>
        </div>
    </form>

    {{-- ✅ Tabela --}}
    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-left font-semibold text-gray-700">
                <tr>
                    <th class="px-4 py-3">Compra</th>
                    <th class="px-4 py-3">Valor</th>
                    <th class="px-4 py-3">Nota Fiscal</th> {{-- ✅ Nova coluna adicionada --}}
                    <th class="px-4 py-3">Vencimento</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($contas as $conta)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Compra --}}
                        <td class="px-4 py-3">
                            @if($conta->compra)
                                <span class="font-semibold text-gray-900">Compra #{{ $conta->compra->id_compra }}</span>
                                <div class="text-sm text-gray-500">{{ $conta->compra->descricao ?? '' }}</div>
                            @else
                                <span class="text-gray-400 italic">Sem compra vinculada</span>
                            @endif
                        </td>

                        {{-- Valor --}}
                        <td class="px-4 py-3 font-medium">
                            R$ {{ number_format($conta->valor, 2, ',', '.') }}
                        </td>

                        {{-- ✅ Nota Fiscal --}}
                        <td class="px-4 py-3 text-gray-700">
                            @if($conta->nota_fiscal)
                                {{ $conta->nota_fiscal }}
                            @else
                                <span class="text-gray-400 italic">—</span>
                            @endif
                        </td>

                        {{-- Vencimento --}}
                        <td class="px-4 py-3">
                            @php
                                try {
                                    $data = \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y');
                                } catch (\Exception $e) {
                                    $data = $conta->data_vencimento;
                                }
                            @endphp
                            {{ $data }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                {{ $conta->status === 'pago'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($conta->status) }}
                            </span>
                        </td>

                        {{-- Ações --}}
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('contas-pagar.show', $conta->id_cpagar) }}"
                                   class="px-3 py-1 rounded-md border border-gray-300 hover:bg-gray-100 transition">
                                    Ver
                                </a>

                                <a href="{{ route('contas-pagar.edit', $conta->id_cpagar) }}"
                                   class="px-3 py-1 rounded-md border border-blue-300 text-blue-700 hover:bg-blue-50 transition">
                                    Editar
                                </a>

                                <form method="POST" action="{{ route('contas-pagar.toggle-status', $conta->id_cpagar) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-3 py-1 rounded-md border transition
                                        {{ $conta->status === 'pago'
                                            ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50'
                                            : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                        {{ $conta->status === 'pago' ? 'Marcar Pendente' : 'Marcar Pago' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('contas-pagar.destroy', $conta->id_cpagar) }}"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta conta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 rounded-md border border-red-300 text-red-700 hover:bg-red-50 transition">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Nenhuma conta encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Paginação --}}
    <div class="mt-6 flex justify-center">
        {{ $contas->links() }}
    </div>
</div>
@endsection
