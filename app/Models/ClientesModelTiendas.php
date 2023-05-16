<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientesModelTiendas extends Model
{
    protected $connection = 'tiendasz';

	protected $table = 'clientes';

	protected $fillable = [
		'nombres',
		'apellidos',
		'documento',
		'direccion',
		'telefono',
		'cargo',
		'fecha_nacimiento',
		'correo',
		'id_zona',
		'id_tienda',
		'configuraciones',
		'puntos',
		'aplica_libranza',
		'monto_libranza',
		'cupo',
		'porcentaje_descuento',
		'entidad',
		'estado_empresa',
	];

}