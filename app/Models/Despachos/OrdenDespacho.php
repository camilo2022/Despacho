<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Despachos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrdenDespacho extends Model
{
    protected $connection = 'despachosbd';

	protected $table = 'orden_despacho';

	protected $fillable = [
        'consecutivo',
        'cliente',
        'nit',
        'prioridad',
        'observacion',
        'ciudad',
        'direccion',
        'user_factura',
        'departamento',
        'identificador',
        'estado',
        'facturas',
        'fdespacho',
        'user_despachos'
	];

}
