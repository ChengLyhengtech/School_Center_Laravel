<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    /**
     * Display the RBAC management interface.
     */
    public function index(): View
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::with('roles')->get();

        return view('admin.rbac.index', compact('roles', 'permissions'));
    }

    /**
     * Store a new role.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name'],
        ]);

        Role::create(['name' => strtolower(trim($validated['name']))]);

        return back()->with('success', "Role '{$validated['name']}' created successfully.");
    }

    /**
     * Store a new permission.
     */
    public function storePermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:permissions,name'],
        ]);

        Permission::create(['name' => strtolower(trim($validated['name']))]);

        return back()->with('success', "Permission '{$validated['name']}' created successfully.");
    }

    /**
     * Update permissions attached to a specific role.
     */
    public function updateRolePermissions(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return back()->with('success', "Permissions for role '{$role->name}' updated successfully.");
    }
}
