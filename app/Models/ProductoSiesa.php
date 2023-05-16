<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProductoSiesa extends Model
{
    protected $connection = 'zarethTiendas';

	protected $table = 'productos_siesa';

	protected $fillable = [
       'sku',
       'referencia',
       'talla',
       'color',
       'descItem',
       'precio'
	];

}
