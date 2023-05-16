<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PickingTipoPrenda extends Model
{
	protected $table = 'picking_tipo_prenda';

	protected $fillable = [
	   'id',
	   'id_referencia',
	   'tipo_prenda',
	   'talla',
	   'cantidad'
	];
}
