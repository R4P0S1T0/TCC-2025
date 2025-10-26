<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧪 TESTE DE CADASTRO DE CONTA\n";
echo "==============================\n";

try {
    // Verificar fornecedores disponíveis
    $fornecedores = DB::table('fornecedores')->get();
    echo "📋 Fornecedores disponíveis:\n";
    foreach ($fornecedores as $fornecedor) {
        echo "   ID: {$fornecedor->id_fornecedor} - {$fornecedor->nome}\n";
    }
    
    if ($fornecedores->count() > 0) {
        // Testar cadastro
        $testData = [
            'id_fornecedor' => $fornecedores->first()->id_fornecedor,
            'descricao' => 'Conta de Teste Automático',
            'valor' => '150,75',
            'data_vencimento' => date('Y-m-d', strtotime('+30 days')),
            'observacoes' => 'Conta criada pelo script de teste',
            'status' => 'pendente',
            'data_criacao' => now()
        ];
        
        echo "\n📝 Inserindo conta de teste...\n";
        
        $id = DB::table('contas_pagar')->insertGetId($testData);
        
        echo "✅ Conta inserida com ID: $id\n";
        
        // Verificar se aparece na listagem
        $contasCount = DB::table('contas_pagar')->count();
        echo "📊 Total de contas no banco: $contasCount\n";
        
        // Limpar teste
        DB::table('contas_pagar')->where('id_conta', $id)->delete();
        echo "🧹 Conta de teste removida\n";
        
    } else {
        echo "❌ Nenhum fornecedor cadastrado para testar\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro no teste: " . $e->getMessage() . "\n";
}

echo "\n🎯 INSTRUÇÕES PARA TESTAR MANUALMENTE:\n";
echo "   1. Acesse: http://localhost:8000/contas-pagar/create\n";
echo "   2. Preencha o formulário\n";
echo "   3. Verifique se aparece mensagem de sucesso\n";
echo "   4. Verifique se a conta aparece na listagem\n";
echo "   5. Verifique o arquivo storage/logs/laravel.log se houver erros\n";
