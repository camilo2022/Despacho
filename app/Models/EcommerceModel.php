<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EcommerceModel extends Model
{
	protected $table = 'ecommerce';

	protected $fillable = [
		'cliente_id',
        'ventaconcreta',
        'tiporequerimiento',
        'observacion',
		'user_id',
		'clasificacion',
		'talla',
	];

    public function user(){
		return $this->belongsTo(User::class);
	}

    public function cliente(){
		return $this->belongsTo(Cliente::class);
	}
	
}
