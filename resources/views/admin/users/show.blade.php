<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ $user->name }}
                    </h2>
                    <p class="text-xs text-gray-500">{{ $user->email }} &bull; Member since {{ $user->created_at?->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold rounded-lg transition">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Roles & Permissions Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                    <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider pb-2 border-b">Assigned Roles</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                            <div class="p-2.5 rounded-lg border border-gray-100 bg-gray-50 w-full">
                                <span class="text-xs font-bold uppercase text-indigo-700">{{ $role->name }}</span>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @forelse($role->permissions as $p)
                                        <span class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] text-gray-600">
                                            {{ $p->name }}
                                        </span>
                                    @empty
                                        <span class="text-[10px] text-gray-400">No explicit permissions</span>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Parent-Student Linking Hub -->
                <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b">
                        <div>
                            <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Parent-Student Linking Hub</h3>
                            <p class="text-xs text-gray-500">Connect parents to their children to allow progress and invoice tracking.</p>
                        </div>
                    </div>

                    @if($user->hasRole('parent'))
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-gray-800 uppercase">Linked Children (Students)</h4>
                            <div class="divide-y divide-gray-100 border border-gray-100 rounded-lg overflow-hidden">
                                @forelse($user->children as $child)
                                    <div class="p-3 flex items-center justify-between hover:bg-gray-50">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $child->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $child->email }}</p>
                                        </div>
                                        <form action="{{ route('admin.users.unlink-child', [$user, $child]) }}" method="POST" onsubmit="return confirm('Remove student linkage?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Unlink</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-xs text-gray-400">No children linked to this parent yet.</div>
                                @endforelse
                            </div>

                            <!-- Add Child Link Form -->
                            @if($availableStudents->isNotEmpty())
                                <form action="{{ route('admin.users.link-child', $user) }}" method="POST" class="pt-3 flex items-center space-x-2">
                                    @csrf
                                    <select name="student_id" required class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 flex-1">
                                        <option value="">-- Select a student to link --</option>
                                        @foreach($availableStudents as $student)
                                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg transition">
                                        Link Student
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    @if($user->hasRole('student'))
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-gray-800 uppercase">Linked Parents</h4>
                            <div class="divide-y divide-gray-100 border border-gray-100 rounded-lg overflow-hidden">
                                @forelse($user->parents as $parent)
                                    <div class="p-3 flex items-center justify-between hover:bg-gray-50">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $parent->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $parent->email }}</p>
                                        </div>
                                        <span class="text-xs text-emerald-600 font-medium">Verified Guardian</span>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-xs text-gray-400">No parent accounts linked to this student.</div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Academic & Activity Tabs -->
            @if($user->hasRole('teacher') && $user->taughtClasses->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
                    <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Assigned Classes as Instructor</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($user->taughtClasses as $cls)
                            <div class="p-4 rounded-lg border border-gray-200 bg-gray-50/50">
                                <a href="{{ route('admin.classes.show', $cls) }}" class="font-bold text-sm text-gray-900 hover:text-indigo-600">{{ $cls->name }}</a>
                                <p class="text-xs text-gray-500 mt-0.5">Course: {{ $cls->course->title ?? '-' }}</p>
                                <p class="text-xs text-gray-400 mt-1">Room: {{ $cls->room ?? 'N/A' }} &bull; {{ $cls->days }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($user->hasRole('student') && $user->enrolledClasses->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
                    <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Active Course Enrollments</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($user->enrolledClasses as $cls)
                            <div class="p-4 rounded-lg border border-gray-200 bg-gray-50/50">
                                <a href="{{ route('admin.classes.show', $cls) }}" class="font-bold text-sm text-gray-900 hover:text-indigo-600">{{ $cls->name }}</a>
                                <p class="text-xs text-gray-500 mt-0.5">Course: {{ $cls->course->title ?? '-' }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">Enrolled Date: {{ $cls->pivot->enrolled_date ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
