<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Despachos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Empaque extends Model
{
    protected $connection = 'despachosbd';

	protected $table = 'empaque';

	protected $fillable = [
        'empacado_id',
        'cantidad',
        'peso',
        'tipo_empaque',
        'state'
	];

}
