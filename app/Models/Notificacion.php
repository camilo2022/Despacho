<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notificacion
 * 
 * @property int $id
 * @property int $rol_id
 * @property int $user_id
 * @property string $title
 * @property string $descripcion
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $delete_at
 * 
 * @property Rol $rol
 * @property User $user
 *
 * @package App\Models
 */
class Notificacion extends Model
{
	protected $table = 'notificacion';

	protected $casts = [
		'rol_id' => 'int',
		'user_id' => 'int'
	];

	protected $dates = [
		'delete_at'
	];

	protected $fillable = [
		'rol_id',
		'user_id',
		'title',
		'descripcion',
		'delete_at'
	];

	public function rol()
	{
		return $this->belongsTo(Rol::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
