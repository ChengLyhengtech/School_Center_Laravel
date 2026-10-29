<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Role-Based Access Control (RBAC)') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage system roles, granular capability permissions, and security matrix.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Create Role Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                    <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Create New Role</h3>
                    <form action="{{ route('admin.roles-permissions.roles.store') }}" method="POST" class="flex space-x-2">
                        @csrf
                        <input type="text" name="name" required placeholder="e.g. librarian, registrar" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 flex-1">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg transition">
                            Add Role
                        </button>
                    </form>
                </div>

                <!-- Create Permission Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                    <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Define Granular Permission</h3>
                    <form action="{{ route('admin.roles-permissions.permissions.store') }}" method="POST" class="flex space-x-2">
                        @csrf
                        <input type="text" name="name" required placeholder="e.g. create-course, record-payment" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 flex-1">
                        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs rounded-lg transition">
                            Add Permission
                        </button>
                    </form>
                </div>
            </div>

            <!-- Permission Mapping Matrix by Role -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6 space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Role & Permission Attachment Matrix</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Toggle granular permissions assigned to each security role in the institution.</p>
                </div>

                <div class="space-y-6">
                    @foreach($roles as $role)
                        <form action="{{ route('admin.roles-permissions.roles.update-permissions', $role) }}" method="POST" class="p-5 rounded-xl border border-gray-200 bg-gray-50/50 space-y-3">
                            @csrf
                            @method('PUT')

                            <div class="flex items-center justify-between border-b pb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase 
                                        {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : ($role->name === 'teacher' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ $role->name }}
                                    </span>
                                    <span class="text-xs text-gray-500">({{ $role->users->count() }} active users)</span>
                                </div>
                                <button type="submit" class="px-3 py-1 bg-gray-800 hover:bg-gray-900 text-white font-medium text-xs rounded-lg transition">
                                    Update {{ ucfirst($role->name) }}
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 pt-2">
                                @forelse($permissions as $perm)
                                    <label class="flex items-center space-x-2 p-2 rounded-lg bg-white border border-gray-100 hover:border-indigo-200 cursor-pointer text-xs">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" 
                                            {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="font-medium text-gray-700">{{ $perm->name }}</span>
                                    </label>
                                @empty
                                    <div class="col-span-4 text-xs text-gray-400 py-2">
                                        No permissions defined yet. Use the form above to add custom permissions like 'create-course', 'edit-score', 'record-payment'.
                                    </div>
                                @endforelse
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
