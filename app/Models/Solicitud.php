<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Solicitud
 * 
 * @property int $id
 * @property int $tiposolicitud_id
 * @property int $estado_id
 * @property int $user_id
 * @property string|null $observacion
 * @property int $cantidadtotal
 * @property Carbon $delete_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Tiposolicitud $tiposolicitud
 * @property Estado $estado
 * @property User $user
 * @property Collection|Detallesolicitud[] $detallesolicituds
 * @property Collection|Revisionsolicitud[] $revisionsolicituds
 *
 * @package App\Models
 */
class Solicitud extends Model
{
	protected $table = 'solicitud';

	protected $casts = [
		'tiposolicitud_id' => 'int',
		'estado_id' => 'int',
		'user_id' => 'int',
		'cantidadtotal' => 'int'
	];

	protected $dates = [
		'delete_at'
	];

	protected $fillable = [
		'tiposolicitud_id',
		'solicituduid',
		'estado_id',
		'user_id',
		'observacion',
		'cantidadtotal'
	];

	public function tiposolicitud()
	{
		return $this->belongsTo(Tiposolicitud::class);
	}

	public function estado()
	{
		return $this->belongsTo(Estado::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function detallesolicituds()
	{
		return $this->hasMany(Detallesolicitud::class);
	}

	public function revisionsolicituds()
	{
		return $this->hasMany(Revisionsolicitud::class);
	}
}
