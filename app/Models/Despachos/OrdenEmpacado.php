<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Despachos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrdenEmpacado extends Model
{
    protected $connection = 'despachosbd';

	protected $table = 'orden_empacado';

	protected $fillable = [
        'ordendespacho_id',
       'user_id_alista',
       'estado',
       'user_id_rechaza'
	];

}
