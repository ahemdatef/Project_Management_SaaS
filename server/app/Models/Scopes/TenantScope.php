<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Check if the 'organization' key has been bound in the service container.
        if (app()->has('organization')) {
            // If yes, filter the query by that organization's ID
            $builder->where('organization_id', app('organization')->id);
        }
    }
}
