<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RefTranslados extends Model
{
	protected $table = 'RefTranslados';

	protected $fillable = [
	    'referencia',
	    'cantidad',
	    'esmarra',
	    'numTransferencia',
	    'essaldo',
	];
}
