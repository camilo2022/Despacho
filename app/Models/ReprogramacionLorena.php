<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ReprogramacionLorena extends Model
{
	protected $table = 'reprogramacionlorena';

	protected $fillable = [
       'reprogramacion',
       'categoria',
       'descripcionref',
       'mes',
       'undxmes',
       'undxref',
       'nref',
	];
	
	protected $casts = [
        'created_at' => 'datetime',
    ];

}
