@extends('Layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Permission Management</h2>
            </div>

            <!-- Create Permission Form -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Create New Permission</h3>
                <form action="{{ route('admin.permissions.create') }}" method="POST">
                    @csrf
                    <div class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Permission Name</label>
                            <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                id="name" name="name" required placeholder="e.g., create-task, view-reports">
                        </div>
                        <div>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Create Permission
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Permissions Matrix -->
            <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Permission
                                </th>
                                @foreach($roles as $role)
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $role->name === 'super_admin' ? 'bg-red-100 text-red-800' :
                                           ($role->name === 'admin' ? 'bg-indigo-100 text-indigo-800' :
                                           ($role->name === 'manager' ? 'bg-green-100 text-green-800' :
                                           'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                    </span>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($permissions as $permission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="flex items-center">
                                        <span class="h-2 w-2 flex-shrink-0 rounded-full
                                            {{ Str::contains($permission->name, 'delete') ? 'bg-red-400' :
                                               (Str::contains($permission->name, 'create') ? 'bg-green-400' :
                                               (Str::contains($permission->name, 'edit') ? 'bg-blue-400' :
                                               'bg-gray-400')) }} mr-2"></span>
                                        {{ $permission->name }}
                                    </div>
                                </td>
                                @foreach($roles as $role)
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <form action="{{ route('admin.roles.assign-permissions', $role) }}" 
                                          method="POST" 
                                          class="inline-block permission-form">
                                        @csrf
                                        <label class="inline-flex items-center justify-center">
                                            <input type="checkbox" 
                                                name="permissions[]" 
                                                value="{{ $permission->id }}"
                                                {{ $role->permissions->contains($permission) ? 'checked' : '' }}
                                                class="form-checkbox h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 transition-all duration-150 ease-in-out cursor-pointer
                                                    {{ $role->name === 'super_admin' ? 'hover:text-red-500' :
                                                       ($role->name === 'admin' ? 'hover:text-indigo-500' :
                                                       ($role->name === 'manager' ? 'hover:text-green-500' :
                                                       'hover:text-yellow-500')) }}">
                                        </label>
                                    </form>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle checkbox changes
    const checkboxes = document.querySelectorAll('.permission-form input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Show loading state
            this.disabled = true;
            
            // Submit the form
            const form = this.closest('form');
            form.submit();
        });
    });
});
</script>
@endpush

@endsection 