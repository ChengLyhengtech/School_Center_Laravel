<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Edit User: {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Update credentials, role assignments, or linked accounts.</p>
            </div>
            <a href="{{ route('admin.users.show', $user) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                &larr; Back to Details
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Account Information -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider pb-2 border-b">Account Information</h3>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Roles Assignment -->
                <div class="space-y-3 pt-4 border-t">
                    <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Role Assignment <span class="text-red-500">*</span></h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($roles as $role)
                            <label class="flex items-center space-x-2 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }} 
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-semibold text-gray-800">{{ ucfirst($role->name) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Parent-Student Linking -->
                <div class="space-y-3 pt-4 border-t">
                    <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Linked Children (For Parents)</h3>
                    <select name="children[]" multiple class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 h-28">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ in_array($student->id, old('children', $user->children->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->email }})
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[11px] text-gray-400">Hold Ctrl (or Cmd) to select multiple students.</span>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.users.show', $user) }}" class="px-4 py-2 border rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
