<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService
{

    private RoleRepository $roleRepository;

    /**
     * Creates a new RoleService instance
     * 
     * @return void
     */
    public function __construct(RoleRepository $roleRepository) {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Gets all application roles
     * 
     * @return Collection
     */
    public function getAllRoles(array $filters = [], $withInVisible = false)
    {
        return $this->roleRepository->findAll($filters, $withInVisible);
    }
}
