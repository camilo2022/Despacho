<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class conteoPrenda extends Model
{
	protected $table = 'conteoPrendas';

	protected $fillable = [
	   'referencia',
	   'estado',
	   'obs',
	   'total',
	   'tipo',
	   'restante',
	   'user_id',
	   't04',
	   't06',
	   't08',
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
	   't38'
	];
}
