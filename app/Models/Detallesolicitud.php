<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Detallesolicitud
 * 
 * @property int $id
 * @property int $solicitud_id
 * @property string $refItem
 * @property string $codbarras
 * @property int $cantidad
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Solicitud $solicitud
 *
 * @package App\Models
 */
class Detallesolicitud extends Model
{
	protected $table = 'detallesolicitud';

	protected $casts = [
		'solicitud_id' => 'int',
		'cantidad' => 'int'
	];

	protected $fillable = [
		'solicitud_id',
		'refItem',
		'codbarras',
		'cantidad'
	];

	public function solicitud()
	{
		return $this->belongsTo(Solicitud::class);
	}
}
