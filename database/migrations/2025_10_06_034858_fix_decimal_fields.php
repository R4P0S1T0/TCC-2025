<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Corrigir tabela contas_pagar - aumentar precisão do valor
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->decimal('valor', 15, 2)->change();
        });

        // Corrigir tabela contas_receber - aumentar precisão do valor  
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->decimal('valor', 15, 2)->change();
        });

        // Corrigir tabela compras - aumentar precisão do valor_total
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('valor_total', 15, 2)->change();
        });

        // Adicionar campos de status se não existirem
        if (!Schema::hasColumn('contas_pagar', 'status')) {
            Schema::table('contas_pagar', function (Blueprint $table) {
                $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
                $table->date('data_pagamento')->nullable();
            });
        }

        if (!Schema::hasColumn('contas_receber', 'status')) {
            Schema::table('contas_receber', function (Blueprint $table) {
                $table->enum('status', ['pendente', 'recebido', 'cancelado'])->default('pendente');
                $table->date('data_recebimento')->nullable();
            });
        }

        if (!Schema::hasColumn('compras', 'status')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->enum('status', ['pendente', 'finalizada', 'cancelada'])->default('pendente');
            });
        }
    }

    public function down()
    {
        // Reverter as alterações se necessário
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->decimal('valor', 8, 2)->change();
        });

        Schema::table('contas_receber', function (Blueprint $table) {
            $table->decimal('valor', 8, 2)->change();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('valor_total', 8, 2)->change();
        });
    }
};
