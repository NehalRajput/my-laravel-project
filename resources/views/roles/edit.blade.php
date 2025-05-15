@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Role: {{ ucfirst($role->name) }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                value="{{ old('name', $role->name) }}" required
                                {{ in_array($role->name, ['super_admin', 'admin', 'manager', 'intern']) ? 'readonly' : '' }}>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                value="{{ $permission->id }}" id="permission{{ $permission->id }}"
                                                {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || 
                                                    (!old('permissions') && $role->permissions->contains($permission->id)) ? 'checked' : '' }}
                                                {{ $role->name === 'super_admin' ? 'checked disabled' : '' }}>
                                            <label class="form-check-label" for="permission{{ $permission->id }}">
                                                {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <span class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 