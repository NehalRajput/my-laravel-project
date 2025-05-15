<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('role')->latest()->get();

            Log::info('Users list loaded successfully', [
                'count' => $users->count()
            ]);

            return view('Admin.interns.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Failed to load users list', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to load users list. Please try again.');
        }
    }

    public function create()
    {
        try {
            $roles = Role::all();
            return view('Admin.interns.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Failed to load user creation page', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to load creation page. Please try again.');
        }
    }

    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $role = Role::findOrFail($request->role_id);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $role->id
            ]);

            DB::commit();

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'role' => $role->name
            ]);

            return redirect()->route('admin.interns.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'data' => $request->except('password')
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create user. Please try again.')
                ->withInput($request->except('password'));
        }
    }

    public function edit(User $user)
    {
        try {
            $roles = Role::all();
            Log::info('Loading user edit page', [
                'user_id' => $user->id
            ]);

            return view('Admin.interns.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            Log::error('Failed to load user edit page', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return redirect()->back()
                ->with('error', 'Failed to load edit page. ' . $e->getMessage());
        }
    }

    public function update(UserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $data = $request->except('password');
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            DB::commit();

            Log::info('User updated successfully', [
                'user_id' => $user->id
            ]);

            return redirect()->route('admin.interns.index')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'data' => $request->except('password')
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update user. ' . $e->getMessage())
                ->withInput($request->except('password'));
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            $user->delete();

            DB::commit();

            Log::info('User deleted successfully', [
                'user_id' => $user->id
            ]);

            return redirect()->route('admin.interns.index')
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return redirect()->route('admin.interns.index')
                ->with('error', 'Failed to delete user. ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        try {
            return view('dashboard');
        } catch (\Exception $e) {
            Log::error('Failed to load dashboard', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to load dashboard. Please try again.');
        }
    }
} 