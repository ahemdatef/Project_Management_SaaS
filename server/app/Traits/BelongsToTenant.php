<?php

namespace App\Traits;

use App\Models\Organization;
use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    /**
     * The "booted" method of the model.
     * Laravel automatically looks for methods named boot[TraitName].
     */

    protected static function bootBelongsToTenant(): void
    {
        // 1. Add the Global Scope
        static::addGlobalScope(new TenantScope());

        // 2. Auto-assign organization_id when creating a new record
        static::creating(function ($model) {
            if (app()->has('organization') && !$model->organization_id) {
                $model->organization_id = app('organization')->id;
            }
        });
    }

    /**
     * Get the organization that owns the model.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
