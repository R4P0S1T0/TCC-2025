<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transacao extends Model
{
    protected $table = 'transacoes'; // 👈 força o nome correto
    protected $fillable = ['descricao', 'tipo', 'valor', 'data'];
    protected $dates = ['data'];
}
