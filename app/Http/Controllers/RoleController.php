<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.manage');
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $query = Role::withCount(['users', 'permissions']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $roles = $query->paginate(15)->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::active()->orderBy('module')->orderBy('display_name')->get()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles', 'regex:/^[a-z_]+$/'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? '',
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Assign permissions
            if (!empty($validated['permissions'])) {
                $permissionData = [];
                foreach ($validated['permissions'] as $permissionId) {
                    $permissionData[$permissionId] = ['assigned_at' => now()];
                }
                $role->permissions()->sync($permissionData);
            }
        });

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load(['permissions' => function ($query) {
            $query->orderBy('module')->orderBy('display_name');
        }, 'users']);
        
        $permissionsByModule = $role->permissions->groupBy('module');
        
        return view('admin.roles.show', compact('role', 'permissionsByModule'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::active()->orderBy('module')->orderBy('display_name')->get();
        $role->load('permissions');
        $assignedPermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'assignedPermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id), 'regex:/^[a-z_]+$/'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        DB::transaction(function () use ($validated, $role) {
            $role->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? '',
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Update permissions
            if (isset($validated['permissions'])) {
                $permissionData = [];
                foreach ($validated['permissions'] as $permissionId) {
                    $permissionData[$permissionId] = ['assigned_at' => now()];
                }
                $role->permissions()->sync($permissionData);
            } else {
                $role->permissions()->detach();
            }
        });

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        $systemRoles = ['super_admin', 'admin'];
        if (in_array($role->name, $systemRoles)) {
            return redirect()->route('admin.roles.index')
                           ->with('error', 'System roles cannot be deleted.');
        }

        // Check if role is assigned to users
        if ($role->users()->count() > 0 || $role->primaryUsers()->count() > 0) {
            return redirect()->route('admin.roles.index')
                           ->with('error', 'Cannot delete role that is assigned to users.');
        }

        DB::transaction(function () use ($role) {
            // Remove all permission assignments
            $role->permissions()->detach();
            
            // Delete the role
            $role->delete();
        });

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role deleted successfully.');
    }

    /**
     * Toggle role active status.
     */
    public function toggleStatus(Role $role)
    {
        // Prevent deactivating system roles
        $systemRoles = ['super_admin', 'admin'];
        if (in_array($role->name, $systemRoles) && $role->is_active) {
            return redirect()->route('admin.roles.index')
                           ->with('error', 'System roles cannot be deactivated.');
        }

        $role->update(['is_active' => !$role->is_active]);
        
        $status = $role->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.roles.index')
                        ->with('success', "Role {$status} successfully.");
    }
}