@extends('Layouts.app')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="bg-white shadow sm:rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Create Permission</h2>

        <form method="POST" action="{{ route('admin.permissions.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="permission" class="block text-sm font-medium text-gray-700">Permission Name</label>
                <input type="text" 
                       id="permission" 
                       name="permission" 
                       value="{{ old('permission') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('permission') border-red-500 @enderror" 
                       required>
                @error('permission')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">
                    Examples: manage_interns, create_tasks, view_reports, etc.
                </p>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.permissions.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Create Permission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
