<?php
namespace App\Models\Despachos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DetallesPedido extends Model
{
    protected $connection = 'proynew';

	protected $table = 'detalles';
    public $timestamps = false;
    
	protected $fillable = [
        'id',
        'pedido_id',
        'referencia',
        'color',
        'colornombre',
        't04',
        't06',
        't08',
        't10',
        't12',
        't14',
        't16',
        't18',
        't20',
        't22',
        't24',
        't26',
        't28',
        't30',
        't32',
        't34',
        't36',
        't38',
        'xs',
        's',
        'm',
        'l',
        'xl',
        'user_id',
        'fecha',
        'faprobado',
        'fdespacho',
        'observaciones',
        'despacho',
        'filtro',
	];

}
