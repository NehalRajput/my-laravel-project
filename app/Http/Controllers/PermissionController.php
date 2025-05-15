<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    private $staticPermissions = [
        ['permission' => 'manage_interns', 'description' => 'Can manage interns'],
        ['permission' => 'manage_tasks', 'description' => 'Can manage tasks'],
        ['permission' => 'view_reports', 'description' => 'Can view reports'],
        ['permission' => 'manage_admins', 'description' => 'Can manage administrators'],
        ['permission' => 'assign_tasks', 'description' => 'Can assign tasks'],
        ['permission' => 'view_tasks', 'description' => 'Can view tasks'],
        ['permission' => 'edit_tasks', 'description' => 'Can edit tasks'],
        ['permission' => 'delete_tasks', 'description' => 'Can delete tasks'],
        ['permission' => 'create_interns', 'description' => 'Can create new interns'],
        ['permission' => 'update_interns', 'description' => 'Can update intern details'],
        ['permission' => 'delete_interns', 'description' => 'Can delete interns'],
        ['permission' => 'read_interns', 'description' => 'Can view intern list'],
        ['permission' => 'create_admins', 'description' => 'Can create new administrators'],
        ['permission' => 'update_admins', 'description' => 'Can update administrator details'],
        ['permission' => 'delete_admins', 'description' => 'Can delete administrators'],
        ['permission' => 'read_admins', 'description' => 'Can view administrator list']
    ];

    public function index()
    {
        return view('permissions.index', ['permissions' => $this->staticPermissions]);
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'permission' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // For now, just redirect since we're using static permissions
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created (simulated).');
    }

    public function edit($permissionName)
    {
        $permission = collect($this->staticPermissions)
            ->firstWhere('permission', $permissionName);

        if (!$permission) {
            abort(404);
        }

        return view('permissions.edit', ['permission' => (object) $permission]);
    }

    public function update(Request $request, $permissionName)
    {
        $request->validate([
            'permission' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // For now, just redirect since we're using static permissions
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated (simulated).');
    }

    public function destroy($permissionName)
    {
        // For now, just redirect since we're using static permissions
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted (simulated).');
    }

    public function assignForm()
    {
        $roles = Role::all();
        return view('permissions.assign', [
            'roles' => $roles,
            'permissions' => $this->staticPermissions
        ]);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'string'
        ]);

        $role = Role::find($request->role_id);

        // For now, just redirect since we're using static permissions
        return redirect()->route('admin.permissions.assign')
            ->with('success', 'Permissions assigned (simulated).');
    }
}
