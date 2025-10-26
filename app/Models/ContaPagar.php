<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ContaPagar extends Model
{
    protected $table = 'contas_pagar';
    protected $primaryKey = 'id_cpagar';
    public $timestamps = false;

    // ✅ Inclui nota_fiscal
    protected $fillable = [
        'id_compra',
        'valor',
        'nota_fiscal',
        'data_vencimento',
        'status',
    ];

    // ✅ Evita arredondamento automático
    protected $casts = [
        'valor' => 'string',
    ];

    /** 🔁 Getter - exibe data no formato BR */
    public function getDataVencimentoAttribute($value)
    {
        if (!$value) return null;
        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Exception $e) {
            return $value;
        }
    }

    /** 🔁 Setter - salva data no formato SQL */
    public function setDataVencimentoAttribute($value)
    {
        if (!$value) {
            $this->attributes['data_vencimento'] = null;
            return;
        }

        try {
            if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $value)) {
                $this->attributes['data_vencimento'] =
                    Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            } else {
                $this->attributes['data_vencimento'] = $value;
            }
        } catch (\Exception $e) {
            $this->attributes['data_vencimento'] = $value;
        }
    }

    /** 🔗 Relacionamento com compras */
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra', 'id_compra');
    }
}
