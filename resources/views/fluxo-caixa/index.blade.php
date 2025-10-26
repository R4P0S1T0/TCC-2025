@extends('layouts.app')

@section('title', 'Fluxo de Caixa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Fluxo de Caixa</h1>
        <div class="flex space-x-2">
            <a href="{{ route('fluxo-caixa.export.pdf') }}" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
            </a>
            <a href="{{ route('fluxo-caixa.export.excel') }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-excel mr-2"></i>Exportar Excel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="text-center py-8">
            <i class="fas fa-chart-line text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Módulo Fluxo de Caixa</h3>
            <p class="text-gray-500 mt-2">Relatórios e exportações serão implementados aqui</p>
        </div>
    </div>
</div>
@endsection
