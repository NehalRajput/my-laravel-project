<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Task;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Http\Requests\AdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::with('role')->get();
        return view('admin.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id'
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id
        ]);

        return redirect()->route('admin.login')->with('success', 'Admin created successfully. Please login.');
    }

    public function edit(Admin $admin)
    {
        try {
            $permissions = Permission::all();
            $adminPermissions = $admin->permissions->pluck('id')->toArray();
            return view('Admin.edit', compact('admin', 'permissions', 'adminPermissions'));
        } catch (\Exception $e) {
            Log::error('Error loading admin edit form', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.admins.index')
                ->with('error', 'Failed to load admin edit page.');
        }
    }

    public function update(AdminRequest $request, Admin $admin)
    {
        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $admin->update($updateData);

            // Delete existing permissions
            RolePermission::where('admin_id', $admin->id)->delete();

            // Assign new permissions
            foreach ($request->permissions as $permissionId) {
                RolePermission::create([
                    'admin_id' => $admin->id,
                    'permission_id' => $permissionId
                ]);
            }

            DB::commit();

            Log::info('Admin updated successfully', ['admin_id' => $admin->id]);

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating admin', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update admin. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Admin $admin)
    {
        try {
            RolePermission::where('admin_id', $admin->id)->delete();
            $admin->delete();

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting admin', ['error' => $e->getMessage()]);
            return redirect()->route('admin.admins.index')
                ->with('error', 'Failed to delete admin. Please try again.');
        }
    }

    public function dashboard()
    {
        try {
            $tasks = Task::with('interns')->get();
            $interns = User::all();
            return view('Admin.Dashboard', [
                'tasks' => $tasks,
                'interns' => $interns
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load dashboard', ['error' => $e->getMessage()]);
            return redirect()->route('admin.admins.index')->with('error', 'Failed to load dashboard.');
        }
    }

    public function deleteUser(User $user)
    {
        try {
            $user->delete();
            return redirect()->back()->with('success', 'Intern account deleted successfully');
        } catch (ModelNotFoundException $e) {
            Log::warning('User not found for deletion', ['user_id' => $user->id ?? 'unknown']);
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting user', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete user. Please try again.');
        }
    }
}
