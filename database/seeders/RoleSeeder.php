<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'manage-branches',
            'manage-users',
            'manage-products',
            'view-reports',
            'manage-settings',
            'manage-sales',
            'manage-maintenance',
            'manage-customers',
            'manage-inventory',
            'view-branch-reports',
            'create-sale',
            'process-return',
            'view-customers',
            'handle-payments',
            'manage-maintenance-own',
            'view-assigned-tickets',
            'create-maintenance',
            'manage-wallets',
            'manage-transfers',
            'manage-expenses',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'manage-branches',
            'manage-users',
            'manage-products',
            'view-reports',
            'manage-settings',
            'manage-sales',
            'create-sale',
            'process-return',
            'manage-maintenance',
            'manage-customers',
            'manage-inventory',
            'manage-wallets',
            'manage-transfers',
            'manage-expenses',
        ]);

        $branchManager = Role::firstOrCreate(['name' => 'branch_manager', 'guard_name' => 'web']);
        $branchManager->syncPermissions([
            'manage-sales',
            'manage-maintenance',
            'manage-customers',
            'manage-inventory',
            'view-branch-reports',
            'create-sale',
            'process-return',
            'view-customers',
            'handle-payments',
            'manage-expenses',
        ]);

        $cashier = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions([
            'create-sale',
            'view-customers',
            'handle-payments',
        ]);

        $technician = Role::firstOrCreate(['name' => 'technician', 'guard_name' => 'web']);
        $technician->syncPermissions([
            'manage-maintenance-own',
            'view-assigned-tickets',
        ]);

        $customerService = Role::firstOrCreate(['name' => 'customer_service', 'guard_name' => 'web']);
        $customerService->syncPermissions([
            'manage-customers',
            'create-maintenance',
            'view-customers',
        ]);
    }
}
