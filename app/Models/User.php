<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property string $names
 * @property string $apellidos
 * @property int $rol_id
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Rol $rol
 * @property Collection|Notificacion[] $notificacions
 * @property Collection|Revisionsolicitud[] $revisionsolicituds
 * @property Collection|Solicitud[] $solicituds
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;
	
	protected $table = 'users';

	protected $casts = [
		'rol_id' => 'int'
	];

	protected $dates = [
		'email_verified_at'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
	    'documento',
		'names',
		'apellidos',
		'rol_id',
		'email',
		'email_verified_at',
		'password',
		'remember_token',
		'tiendacargo'
	];

	public function rol()
	{
		return $this->belongsTo(Rol::class);
	}

	public function notificacions()
	{
		return $this->hasMany(Notificacion::class);
	}

	public function revisionsolicituds()
	{
		return $this->hasMany(Revisionsolicitud::class);
	}

	public function solicituds()
	{
		return $this->hasMany(Solicitud::class);
	}
}
