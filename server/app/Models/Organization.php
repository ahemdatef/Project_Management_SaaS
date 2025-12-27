<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'slug', 'owner_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
