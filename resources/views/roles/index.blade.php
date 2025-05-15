@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Roles</h5>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">Create New Role</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    <tr>
                                        <td>{{ ucfirst($role->name) }}</td>
                                        <td>
                                            @foreach($role->permissions as $permission)
                                                <span class="badge bg-info me-1">{{ str_replace('_', ' ', $permission->name) }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-primary">Edit</a>
                                            @if(!in_array($role->name, ['super_admin', 'admin', 'manager', 'intern']))
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 