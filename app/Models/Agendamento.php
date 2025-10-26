<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';
    protected $primaryKey = 'id_agendamento'; // ✅ chave correta!
    public $timestamps = true;

    protected $fillable = [
        'id_cliente',
        'cliente',
        'servico',
        'data_hora',
        'status',
        'observacoes',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }


}


