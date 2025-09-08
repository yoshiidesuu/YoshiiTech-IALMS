<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permissions.manage');
    }

    /**
     * Display a listing of permissions.
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
            });
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $permissions = $query->orderBy('module')->orderBy('display_name')->paginate(15)->withQueryString();
        $modules = Permission::distinct('module')->whereNotNull('module')->pluck('module')->sort();

        return view('admin.permissions.index', compact('permissions', 'modules'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        $modules = Permission::distinct('module')->whereNotNull('module')->pluck('module')->sort();
        return view('admin.permissions.create', compact('modules'));
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions', 'regex:/^[a-z_\.]+$/'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'module' => ['required', 'string', 'max:100', 'regex:/^[a-z_]+$/'],
            'is_active' => ['boolean'],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? '',
            'module' => $validated['module'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $permission->load(['roles' => function ($query) {
            $query->orderBy('display_name');
        }]);
        
        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        $modules = Permission::distinct('module')->whereNotNull('module')->pluck('module')->sort();
        return view('admin.permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id), 'regex:/^[a-z_\.]+$/'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'module' => ['required', 'string', 'max:100', 'regex:/^[a-z_]+$/'],
            'is_active' => ['boolean'],
        ]);

        $permission->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? '',
            'module' => $validated['module'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                           ->with('error', 'Cannot delete permission that is assigned to roles. Remove from roles first.');
        }

        // Check if permission is assigned to any users directly
        if ($permission->users()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                           ->with('error', 'Cannot delete permission that is assigned to users. Remove from users first.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Toggle the active status of the specified permission.
     */
    public function toggleStatus(Permission $permission)
    {
        $permission->update([
            'is_active' => !$permission->is_active
        ]);

        $status = $permission->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.permissions.index')
                        ->with('success', "Permission {$status} successfully.");
    }
}