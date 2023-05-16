<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CuotaLibranza extends Model
{
	protected $table = 'cuotas_libranza';

	protected $fillable = [
	    'id',
		'historial_libranza_id',
		'fechaCuota',
		'monto',
		'fechaPago',
		'estado'
	];

}
