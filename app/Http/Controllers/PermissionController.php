<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('permissions.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('roles')) {
            $permission->roles()->attach($request->roles);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        $roles = Role::all();
        $selectedRoles = $permission->roles->pluck('id')->toArray();
        return view('permissions.edit', compact('permission', 'roles', 'selectedRoles'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('roles')) {
            $permission->roles()->sync($request->roles);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->roles()->detach();
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
} 