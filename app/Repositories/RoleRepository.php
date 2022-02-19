<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    
    /**
     * Retrieve all roles from the database
     * 
     * @param array $filters
     * @param bool $withInVisible
     * @return Collection
     */
    public function findAll(array $filters = [], bool $withInVisible = false)
    {
        $roleQuery = Role::orderBy('name');

        if(!$withInVisible) $roleQuery->visible();

        return $roleQuery->get();
    }
}
