<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class HistorialLibranza extends Model
{
	protected $table = 'historial_libranza';

	protected $fillable = [
	    'id',
		'clientes_id',
		'users_id',
		'valormonto',
		'numfactura',
		'codigo',
		'smsID',
		'estado',
		'cuotas',
		'fechaCuota1',
		'fechaCuota2',
		'fechaCuota3',
		'fechaCuota4',
		'created_at',
		'updated_at',
	];

	public function clientes()
	{
		return $this->belongsTo(ClientesModelTiendas::class);
	}

	public function users()
	{
		return $this->belongsTo(User::class);
	}
}
