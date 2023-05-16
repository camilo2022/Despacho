<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Cliente extends Model
{
	protected $table = 'cliente';
	protected $fillable = [
        'telefono',
        'nombres',
		'user_id',
	];

	public function user(){
		return $this->belongsTo(User::class);
	}
}
