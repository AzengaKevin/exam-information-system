<?php

namespace App\Scopes;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class NotDiskusUserScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $diskusAdminRoleId = Role::firstOrCreate(['name' => Role::SUPER_ROLE])->id;

        $builder->whereNull('role_id')->orWhere('role_id', '!=', $diskusAdminRoleId);
    }
    
}
