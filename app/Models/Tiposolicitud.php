<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tiposolicitud
 * 
 * @property int $id
 * @property string $descripcion
 * @property string $csv
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Solicitud[] $solicituds
 *
 * @package App\Models
 */
class Tiposolicitud extends Model
{
	protected $table = 'tiposolicitud';

	protected $fillable = [
		'descripcion',
		'csv'
	];

	public function solicituds()
	{
		return $this->hasMany(Solicitud::class);
	}
}
