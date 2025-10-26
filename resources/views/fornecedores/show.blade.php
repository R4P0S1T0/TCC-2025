@extends('layouts.app')

@section('title', 'Detalhes do Fornecedor')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Detalhes do Fornecedor</h1>
        <div class="flex space-x-2">
            <a href="{{ route('fornecedores.edit', $fornecedor->id_fornecedor) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <a href="{{ route('fornecedores.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações Principais</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Nome:</span>
                        <p class="text-gray-800">{{ $fornecedor->nome }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">CNPJ:</span>
                        <p class="text-gray-800">{{ $fornecedor->cnpj }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Telefone:</span>
                        <p class="text-gray-800">{{ $fornecedor->telefone }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">E-mail:</span>
                        <p class="text-gray-800">{{ $fornecedor->email }}</p>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Endereço</h3>
                <div>
                    <span class="text-sm font-medium text-gray-600">Endereço Completo:</span>
                    <p class="text-gray-800 mt-1">{{ $fornecedor->endereco ?: 'Não informado' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
