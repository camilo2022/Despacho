<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DescuentoLibranza extends Model
{
	protected $table = 'descuentos_libranza';

	protected $fillable = [
	    'id',
	    'historial_libranza_id',
	    'monto',
	    'users_id',
	    'created_at',
	    'updated_at'
	];

	public function users()
	{
		return $this->hasMany(User::class);
	}
}