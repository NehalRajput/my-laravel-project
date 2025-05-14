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
            $users = User::with('role')
                ->whereHas('role', function($query) {
                    $query->where('name', 'intern');
                })
                ->latest()
                ->get();

            Log::info('Interns list loaded successfully', [
                'count' => $users->count()
            ]);

            return view('Admin.interns.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Failed to load interns list', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to load interns list. Please try again.');
        }
    }

    public function create()
    {
        try {
            return view('Admin.interns.create');
        } catch (\Exception $e) {
            Log::error('Failed to load intern creation page', [
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

            $internRole = Role::where('name', 'intern')->firstOrFail();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $internRole->id
            ]);

            DB::commit();

            Log::info('Intern created successfully', [
                'user_id' => $user->id
            ]);

            return redirect()->route('admin.interns.index')
                ->with('success', 'Intern created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create intern', [
                'error' => $e->getMessage(),
                'data' => $request->except('password')
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create intern. Please try again.')
                ->withInput($request->except('password'));
        }
    }

    public function edit(User $user)
    {
        try {
            if (!$user->isIntern()) {
                throw new \Exception('Only intern accounts can be edited here');
            }

            Log::info('Loading intern edit page', [
                'user_id' => $user->id
            ]);

            return view('Admin.interns.edit', compact('user'));
        } catch (\Exception $e) {
            Log::error('Failed to load intern edit page', [
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

            if (!$user->isIntern()) {
                throw new \Exception('Only intern accounts can be edited here');
            }

            $data = $request->except('password');
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            DB::commit();

            Log::info('Intern updated successfully', [
                'user_id' => $user->id
            ]);

            return redirect()->route('admin.interns.index')
                ->with('success', 'Intern updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update intern', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'data' => $request->except('password')
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update intern. ' . $e->getMessage())
                ->withInput($request->except('password'));
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            if (!$user->isIntern()) {
                throw new \Exception('Only intern accounts can be deleted');
            }

            $user->delete();

            DB::commit();

            Log::info('Intern deleted successfully', [
                'user_id' => $user->id
            ]);

            return redirect()->route('admin.interns.index')
                ->with('success', 'Intern deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete intern', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return redirect()->route('admin.interns.index')
                ->with('error', 'Failed to delete intern. ' . $e->getMessage());
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