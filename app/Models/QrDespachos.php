<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class QrDespachos extends Model
{
    protected $connection = 'proynew';

	protected $table = 'qr_despachos';

	protected $fillable = [
		'destinatario',
        'nit',
        'direccion',
        'departamento',
        'ciudad',
        'phone',
        'factura_numero',
        'ncaja',
        'cajas',
        'cantidad',
        'peso',
        'ndespacho',
        'npedido',
        'filtro',
        'user_id'
	];

}
