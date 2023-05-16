<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Estado
 * 
 * @property int $id
 * @property string $estado
 * @property string $descripcion
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $delete_at
 * 
 * @property Collection|Solicitud[] $solicituds
 *
 * @package App\Models
 */
class Estado extends Model
{
	protected $table = 'estado';

	protected $dates = [
		'delete_at'
	];

	protected $fillable = [
		'estado',
		'descripcion',
		'delete_at'
	];

	public function solicituds()
	{
		return $this->hasMany(Solicitud::class);
	}
}
