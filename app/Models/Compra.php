<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';
    protected $primaryKey = 'id_compra';

    // ✅ Campos preenchíveis
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

    /**
     * 🔹 Relação com fornecedor
     */
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor', 'id_fornecedor');
    }

    /**
     * 🔹 Relação com contas a pagar
     */
    public function contasPagar()
    {
        return $this->hasMany(ContaPagar::class, 'id_compra', 'id_compra');
    }

    /**
     * 🔁 Eventos automáticos
     */
    protected static function booted()
    {
        // 🔄 Quando o valor_total ou nota_fiscal mudarem, atualiza automaticamente as contas vinculadas
        static::updated(function ($compra) {
            if ($compra->isDirty(['valor_total', 'nota_fiscal'])) {
                \App\Models\ContaPagar::where('id_compra', $compra->id_compra)->update([
                    'valor' => $compra->valor_total,
                ]);
            }
        });

        // 🚫 Se o status for "finalizada" ou "cancelada", exclui automaticamente as contas a pagar associadas
        static::updated(function ($compra) {
            if (in_array($compra->status, ['finalizada', 'cancelada'])) {
                \App\Models\ContaPagar::where('id_compra', $compra->id_compra)->delete();
            }
        });
    }
}
