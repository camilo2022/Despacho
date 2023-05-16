<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Revisionsolicitud
 * 
 * @property int $id
 * @property int $solicitud_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $delete_at
 * 
 * @property Solicitud $solicitud
 * @property User $user
 *
 * @package App\Models
 */
class Revisionsolicitud extends Model
{
	protected $table = 'revisionsolicitud';

	protected $casts = [
		'solicitud_id' => 'int',
		'user_id' => 'int'
	];

	protected $dates = [
		'delete_at'
	];

	protected $fillable = [
		'solicitud_id',
		'user_id'
	];

	public function solicitud()
	{
		return $this->belongsTo(Solicitud::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
