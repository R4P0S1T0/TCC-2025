<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nome',
        'email', 
        'senha',
        'telefone',
        'endereco', 
        'tipo'
    ];

    protected $hidden = [
        'senha'
    ];

    // 🔑 CONFIGURAÇÃO CRÍTICA: Mapeia 'senha' para o sistema de auth
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // 🚫 Desabilita completamente remember token
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Não faz nada
    }

    public function getRememberTokenName()
    {
        return null;
    }

    // ✅ Garante que o email seja usado como identificador
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getAuthIdentifier()
    {
        return $this->email;
    }
}
