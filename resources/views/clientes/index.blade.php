@extends('layouts.app')
@section('title', 'Clientes')

@section('content')
<div class="p-8">
    {{-- Alerta de sucesso --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 text-green-800 border border-green-200 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-800">Clientes</h1>
         <a href="{{ route('clientes.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Novo Cliente
        </a>
    </div>

    {{-- Tabela --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm bg-white">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold border-b">
                <tr>
                    <th class="px-5 py-3 text-left">Nome</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Telefone</th>
                    <th class="px-5 py-3 text-left">Cidade</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clientes as $cliente)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $cliente->nome }}
                        </td>
                        <td class="px-5 py-3">{{ $cliente->email ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $cliente->telefone ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $cliente->cidade ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                {{ $cliente->status === 'ativo' 
                                    ? 'bg-green-100 text-green-700' 
                                    : 'bg-gray-200 text-gray-600' }}">
                                {{ ucfirst($cliente->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            {{-- 🔍 Ver Detalhes --}}
                            <a href="{{ route('clientes.show', $cliente->id_cliente) }}" 
                               class="text-gray-700 hover:text-gray-900 hover:underline">
                                Ver
                            </a>

                            {{-- ✏️ Editar --}}
                            <a href="{{ route('clientes.edit', $cliente->id_cliente) }}" 
                               class="text-blue-600 hover:text-blue-800 hover:underline">
                                Editar
                            </a>

                            {{-- 🗑️ Excluir --}}
                            <form action="{{ route('clientes.destroy', $cliente->id_cliente) }}" 
                                  method="POST" 
                                  class="inline-block"
                                  onsubmit="return confirm('Deseja realmente excluir este cliente?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 hover:underline">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-6 text-center text-gray-500">
                            Nenhum cliente cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if($clientes->hasPages())
        <div class="mt-6">
            {{ $clientes->links() }}
        </div>
    @endif
</div>
@endsection
