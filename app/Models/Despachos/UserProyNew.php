<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Despachos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserProyNew extends Model
{
    protected $connection = 'proynew';

	protected $table = 'users';

	protected $fillable = [
        'username',
        'firstname',
        'lastname'
	];

}
