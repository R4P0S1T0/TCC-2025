<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';
    protected $primaryKey = 'id_compra';

    protected $fillable = [
        'descricao',
        'id_fornecedor',
        'valor_total',
        'data_compra',
        'nota_fiscal',
        'status',
        'observacoes',
        'tipo',
    ];

    /** 🔹 Relação com fornecedor */
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor', 'id_fornecedor');
    }

    /** 🔹 Relação com contas a pagar */
    public function contasPagar()
    {
        return $this->hasMany(ContaPagar::class, 'id_compra', 'id_compra');
    }

    /** 🔁 Eventos automáticos */
    protected static function booted()
    {
        // 🔄 Atualiza valor/nota fiscal automaticamente
        static::updated(function ($compra) {
            if ($compra->isDirty(['valor_total', 'nota_fiscal'])) {
                \App\Models\ContaPagar::where('id_compra', $compra->id_compra)->update([
                    'valor' => $compra->valor_total,
                    'nota_fiscal' => $compra->nota_fiscal,
                ]);
            }
        });

        // ✅ Corrigido: apenas atualiza o status, não deleta
        static::updated(function ($compra) {
            if ($compra->status === 'finalizada') {
                \App\Models\ContaPagar::where('id_compra', $compra->id_compra)->update([
                    'status' => 'pago',
                ]);
            } elseif ($compra->status === 'pendente') {
                \App\Models\ContaPagar::where('id_compra', $compra->id_compra)->update([
                    'status' => 'pendente',
                ]);
            } elseif ($compra->status === 'cancelada') {
                // se quiser, pode manter pendente pra conferência
                \App\Models\ContaPagar::where('id_compra', $compra->id_compra)->update([
                    'status' => 'pendente',
                ]);
            }
        });
    }
}
