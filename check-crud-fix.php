<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔍 VERIFICAÇÃO DAS CORREÇÕES DE CRUD\n";
echo "====================================\n";

// Verificar estrutura das tabelas
$tablesToCheck = [
    'contas_pagar' => ['valor', 'status', 'data_pagamento'],
    'contas_receber' => ['valor', 'status', 'data_recebimento'],
    'compras' => ['valor_total', 'status']
];

foreach ($tablesToCheck as $table => $columns) {
    echo "\n📊 Verificando tabela: $table\n";
    
    if (!Schema::hasTable($table)) {
        echo "   ❌ Tabela não existe\n";
        continue;
    }
    
    foreach ($columns as $column) {
        if (Schema::hasColumn($table, $column)) {
            $columnType = DB::select("SHOW COLUMNS FROM $table LIKE '$column'")[0]->Type;
            echo "   ✅ $column: $columnType\n";
        } else {
            echo "   ❌ $column: COLUNA AUSENTE\n";
        }
    }
}

// Verificar controllers
$controllers = [
    'ContasPagarController',
    'ContasReceberController', 
    'CompraController'
];

echo "\n🛠️ Verificando controllers:\n";
foreach ($controllers as $controller) {
    $path = "app/Http/Controllers/$controller.php";
    if (file_exists($path)) {
        echo "   ✅ $controller: EXISTE\n";
    } else {
        echo "   ❌ $controller: AUSENTE\n";
    }
}

// Verificar rotas
echo "\n🛣️ Verificando rotas:\n";
$routes = [
    'contas-pagar.index',
    'contas-pagar.pagar',
    'contas-receber.index',
    'compras.index'
];

foreach ($routes as $route) {
    try {
        route($route);
        echo "   ✅ $route: OK\n";
    } catch (Exception $e) {
        echo "   ❌ $route: FALHOU\n";
    }
}

echo "\n🎉 VERIFICAÇÃO CONCLUÍDA!\n";
echo "\n📋 PRÓXIMOS PASSOS:\n";
echo "   1. Teste criar uma conta a pagar com valor grande (ex: 1234567,89)\n";
echo "   2. Teste editar, excluir e mudar status\n";
echo "   3. Verifique se as máscaras monetárias funcionam corretamente\n";
