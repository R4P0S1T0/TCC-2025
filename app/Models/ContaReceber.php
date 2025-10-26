<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    protected $table = 'contas_receber'; // 👈 Nome exato no seu banco
    protected $primaryKey = 'id_conta_receber'; // ajuste se for outro nome
    public $timestamps = false;

    protected $fillable = [
        'descricao',
        'valor',
        'vencimento',
        'status'
    ];
}
