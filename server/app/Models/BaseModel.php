<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use BelongsToTenant;
}
