<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // Define roles
        $roles = [
            'super_admin',
            'admin',
            'accounts',
            'teacher',
            'parent',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Define permissions
        $permissions = [
            'students.view', 'students.create', 'students.edit', 'students.delete',
            'guardians.view', 'guardians.create', 'guardians.edit', 'guardians.delete',
            'classes.view', 'classes.create', 'classes.edit', 'classes.delete',
            'fees.view', 'fees.create', 'fees.edit', 'fees.delete',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send', 'invoices.delete',
            'payments.view', 'payments.create', 'payments.record',
            'staff.view', 'staff.create', 'staff.edit', 'staff.delete',
            'leave.view', 'leave.apply', 'leave.approve', 'leave.configure',
            'payroll.view', 'payroll.run', 'payroll.confirm', 'payroll.configure',
            'finance.view', 'finance.export',
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        
        // super_admin: all permissions
        $superAdmin = Role::findByName('super_admin');
        $superAdmin->syncPermissions(Permission::all());

        // admin: all permissions except settings that are super_admin only
        $admin = Role::findByName('admin');
        $adminPermissions = Permission::whereNotIn('name', [
            'settings.view', 'settings.edit', 
            // Any other super admin only permissions can be added here
        ])->get();
        $admin->syncPermissions($adminPermissions);

        // accounts: fees.*, invoices.*, payments.*, payroll.*, finance.*
        $accounts = Role::findByName('accounts');
        $accountsPermissions = Permission::whereIn('name', [
            'fees.view', 'fees.create', 'fees.edit', 'fees.delete',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send', 'invoices.delete',
            'payments.view', 'payments.create', 'payments.record',
            'payroll.view', 'payroll.run', 'payroll.confirm', 'payroll.configure',
            'finance.view', 'finance.export',
        ])->get();
        $accounts->syncPermissions($accountsPermissions);

        // teacher: students.view, guardians.view, classes.view, leave.view, leave.apply
        $teacher = Role::findByName('teacher');
        $teacherPermissions = Permission::whereIn('name', [
            'students.view',
            'guardians.view',
            'classes.view',
            'leave.view', 'leave.apply',
        ])->get();
        $teacher->syncPermissions($teacherPermissions);

        // parent: (no backend permissions — access controlled via ParentPortal routes)
        $parent = Role::findByName('parent');
        $parent->syncPermissions([]);
    }
}
