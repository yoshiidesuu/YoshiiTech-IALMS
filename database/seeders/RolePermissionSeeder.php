<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'System administrator with most permissions',
                'is_active' => true,
            ],
            [
                'name' => 'registrar',
                'display_name' => 'Registrar',
                'description' => 'Academic records and student information management',
                'is_active' => true,
            ],
            [
                'name' => 'faculty',
                'display_name' => 'Faculty',
                'description' => 'Teaching staff with grade and class management access',
                'is_active' => true,
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Student portal access',
                'is_active' => true,
            ],
            [
                'name' => 'parent',
                'display_name' => 'Parent/Guardian',
                'description' => 'Parent portal access to student information',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Create Permissions
        $permissions = [
            // System Administration
            ['name' => 'system.manage', 'display_name' => 'Manage System', 'module' => 'system'],
            ['name' => 'users.manage', 'display_name' => 'Manage Users', 'module' => 'users'],
            ['name' => 'roles.manage', 'display_name' => 'Manage Roles', 'module' => 'roles'],
            ['name' => 'permissions.manage', 'display_name' => 'Manage Permissions', 'module' => 'permissions'],
            ['name' => 'configurations.manage', 'display_name' => 'Manage Configurations', 'module' => 'configurations'],
            ['name' => 'settings.manage', 'display_name' => 'Manage Settings', 'module' => 'settings'],
            
            // Academic Management
            ['name' => 'academic.manage', 'display_name' => 'Manage Academic Structure', 'module' => 'academic'],
            ['name' => 'students.manage', 'display_name' => 'Manage Students', 'module' => 'students'],
            ['name' => 'students.view', 'display_name' => 'View Students', 'module' => 'students'],
            ['name' => 'grades.manage', 'display_name' => 'Manage Grades', 'module' => 'grades'],
            ['name' => 'grades.view', 'display_name' => 'View Grades', 'module' => 'grades'],
            ['name' => 'classes.manage', 'display_name' => 'Manage Classes', 'module' => 'classes'],
            ['name' => 'curriculum.manage', 'display_name' => 'Manage Curriculum', 'module' => 'curriculum'],
            ['name' => 'policies.manage', 'display_name' => 'Manage Policies', 'module' => 'policies'],
            ['name' => 'grade-encoding-periods.manage', 'display_name' => 'Manage Grade Encoding Periods', 'module' => 'grades'],

            // Enrollment & Admission
            ['name' => 'enrollment.manage', 'display_name' => 'Manage Enrollment', 'module' => 'enrollment'],
            ['name' => 'admission.manage', 'display_name' => 'Manage Admission', 'module' => 'admission'],
            
            // Financial Management
            ['name' => 'finance.manage', 'display_name' => 'Manage Finance', 'module' => 'finance'],
            ['name' => 'finance.view', 'display_name' => 'View Finance', 'module' => 'finance'],
            
            // Reports
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'module' => 'reports'],
            ['name' => 'reports.generate', 'display_name' => 'Generate Reports', 'module' => 'reports'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                array_merge($permission, [
                    'description' => $permission['display_name'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Assign permissions to roles
        $rolePermissions = [
            'super_admin' => array_column($permissions, 'name'), // All permissions
            'admin' => [
                'users.manage', 'roles.manage', 'permissions.manage', 'configurations.manage', 'settings.manage',
                'academic.manage', 'students.manage', 'grades.manage', 'grade-encoding-periods.manage', 'classes.manage',
                'curriculum.manage', 'policies.manage', 'enrollment.manage', 'admission.manage',
                'finance.manage', 'reports.view', 'reports.generate'
            ],
            'registrar' => [
                'students.manage', 'students.view', 'grades.view',
                'enrollment.manage', 'admission.manage', 'reports.view'
            ],
            'faculty' => [
                'students.view', 'grades.manage', 'grades.view',
                'classes.manage', 'reports.view'
            ],
            'student' => [
                'grades.view'
            ],
            'parent' => [
                'grades.view'
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = DB::table('roles')->where('name', $roleName)->first();
            if ($role) {
                foreach ($permissionNames as $permissionName) {
                    $permission = DB::table('permissions')->where('name', $permissionName)->first();
                    if ($permission) {
                        DB::table('role_permissions')->updateOrInsert(
                            [
                                'role_id' => $role->id,
                                'permission_id' => $permission->id,
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                }
            }
        }

        // Create default admin user
        $adminRole = DB::table('roles')->where('name', 'super_admin')->first();
        
        $adminUser = DB::table('users')->updateOrInsert(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'System Administrator',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin'),
                'primary_role_id' => $adminRole->id,
                'is_active' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Assign admin role to admin user
        $user = DB::table('users')->where('email', 'admin@admin.com')->first();
        if ($user && $adminRole) {
            DB::table('user_roles')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'role_id' => $adminRole->id,
                ],
                [
                    'assigned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
