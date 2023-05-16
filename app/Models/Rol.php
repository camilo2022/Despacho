<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Rol
 * 
 * @property int $id
 * @property string $slug
 * @property string $descripcion
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $delete_at
 * 
 * @property Collection|Notificacion[] $notificacions
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Rol extends Model
{
	protected $table = 'rol';

	protected $dates = [
		'delete_at'
	];

	protected $fillable = [
		'slug',
		'descripcion',
		'delete_at'
	];

	public function notificacions()
	{
		return $this->hasMany(Notificacion::class);
	}

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
