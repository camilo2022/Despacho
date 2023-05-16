<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Despachos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DetallesEmpaque extends Model
{
    protected $connection = 'despachosbd';

	protected $table = 'detalles_empaque';

	protected $fillable = [
        'empaque_id',
        'id_amarrador',
        'referencia',
        't4',
        't6',
        't8',
        't10',
        't12',
        't14',
        't16',
        't18',
        't20',
        't22',
        't24',
        't26',
        't28',
        't30',
        't32',
        't34',
        't36',
        't38',
        's',
        'm',
        'l',
        'xl',
        'total',
	];

}
