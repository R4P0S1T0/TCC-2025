@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Detalhes do Usuário</h1>

    <div class="space-y-2">
        <p><strong>ID:</strong> {{ $usuario->id_usuario ?? $usuario->id }}</p>
        <p><strong>Nome:</strong> {{ $usuario->nome }}</p>
        <p><strong>Email:</strong> {{ $usuario->email }}</p>
        <p><strong>Tipo:</strong> {{ ucfirst($usuario->tipo ?? 'N/A') }}</p>
        <p><strong>Telefone:</strong> {{ $usuario->telefone ?? '-' }}</p>
        <p><strong>Endereço:</strong> {{ $usuario->endereco ?? '-' }}</p>
        <p><strong>Cidade:</strong> {{ $usuario->cidade ?? '-' }}</p>
        <p><strong>Estado:</strong> {{ $usuario->estado ?? '-' }}</p>
    </div>

    <div class="mt-6">
        <a href="{{ route('usuarios.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>
</div>
@endsection
