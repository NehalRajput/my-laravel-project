<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function roles()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function permissions()
    {
        $permissions = Permission::all();
        $roles = Role::all();
        return view('permissions.index', compact('permissions', 'roles'));
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Role created successfully');
    }

    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Permission created successfully');
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($request->permissions);

        return redirect()->back()->with('success', 'Permissions assigned successfully');
    }
}
