<?php

namespace Modules\Core\Entities;

use Greabock\Tentacles\EloquentTentacle;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	use EloquentTentacle;
	
    protected $fillable = [];
}
