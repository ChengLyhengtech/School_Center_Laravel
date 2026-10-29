<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('User & Account Management') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage staff, teachers, students, parents, and role assignments.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New User
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Filters & Search Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Role Filter Badges -->
                <div class="flex items-center flex-wrap gap-1.5">
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ empty($roleFilter) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        All ({{ \App\Models\User::count() }})
                    </a>
                    @foreach($roles as $role)
                        <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $roleFilter === $role->name ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ ucfirst($role->name) }}
                        </a>
                    @endforeach
                </div>

                <!-- Search Input -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center space-x-2">
                    @if($roleFilter)
                        <input type="hidden" name="role" value="{{ $roleFilter }}">
                    @endif
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name or email..." class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-64">
                    <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg hover:bg-gray-900 transition">Search</button>
                    @if($search)
                        <a href="{{ route('admin.users.index', ['role' => $roleFilter]) }}" class="text-xs text-gray-500 hover:text-gray-800 underline">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Roles</th>
                                <th class="px-6 py-4">Associated Info</th>
                                <th class="px-6 py-4">Joined Date</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                                {{ $user->name }}
                                            </a>
                                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($user->roles as $r)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $r->name === 'admin' ? 'bg-purple-100 text-purple-800' : ($r->name === 'teacher' ? 'bg-emerald-100 text-emerald-800' : ($r->name === 'student' ? 'bg-blue-100 text-blue-800' : ($r->name === 'parent' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800'))) }}">
                                                    {{ ucfirst($r->name) }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-400">None</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @if($user->hasRole('parent') && $user->children->isNotEmpty())
                                            <span class="text-gray-500 font-medium">Children: </span>
                                            <span class="text-gray-800">{{ $user->children->pluck('name')->join(', ') }}</span>
                                        @elseif($user->hasRole('student') && $user->parents->isNotEmpty())
                                            <span class="text-gray-500 font-medium">Parents: </span>
                                            <span class="text-gray-800">{{ $user->parents->pluck('name')->join(', ') }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $user->created_at?->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            View
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                            Edit
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-xs">
                                        No users found matching the query.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
