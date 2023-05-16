<?php

namespace App\Models\Siesa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TercerosModel extends Model
{
    protected $table = 'tercero';

	protected $fillable = [
		'centrooperacion',
        'nit',
        'idcliente',
        'razonsocial',
        'idsucursal',
        'descsucursal',
        'departamento',
        'ciudad',
        'direccion',
        'telefono',
        'direcciondespacho',
        'correo',
        'zona',
        'celulartercero',
        'celularsucursal',
	];

}
