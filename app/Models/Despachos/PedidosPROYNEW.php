<?php
namespace App\Models\Despachos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PedidosPROYNEW extends Model
{
    protected $connection = 'proynew';

	protected $table = 'pedidos';
    public $timestamps = false;
    
	protected $fillable = [
        'id',
        'documento',
        'codigo',
        'cupo',
        'deuda',
        'nombre',
        'departamento',
        'ciudad',
        'direccion',
        'sucursal',
        'estado',
        'revision',
        'despacho',
        'fecha',
        'user_id',
        'despachar',
        'fdespacho',
        'observaciones',
        'bandera',
        'correria'
	];

}
