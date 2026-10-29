<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('student.catalog.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Back to Catalog
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">{{ $course->title }}</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ $course->title }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('student.catalog.wishlist.toggle', $course) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold border transition-all inline-flex items-center gap-1.5 shadow-2xs {{ $isWishlisted ? 'bg-rose-50 text-rose-700 border-rose-300 hover:bg-rose-100' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                        <span>{{ $isWishlisted ? '❤️ Saved in Wishlist' : '🤍 Add to Wishlist' }}</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Course Header Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                    <div>
                        <span class="px-3 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $course->duration ?? 'Standard Course' }}
                        </span>
                        <h1 class="text-2xl font-black text-slate-900 mt-2">{{ $course->title }}</h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Scheduled: {{ $course->start_date?->format('M d, Y') }} &mdash; {{ $course->end_date?->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="sm:text-right shrink-0">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tuition Fee</span>
                        <div class="text-3xl font-black text-slate-900 font-mono mt-0.5">
                            ${{ number_format($course->price, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Description & Syllabus Outline (Requirement #4) -->
                <div class="space-y-3">
                    <h3 class="font-extrabold text-slate-900 text-base">Course Curriculum & Syllabus</h3>
                    <div class="prose max-w-none text-slate-600 text-sm leading-relaxed">
                        <p>{{ $course->description }}</p>
                    </div>
                </div>

                <!-- Enrollment Guidance Card -->
                <div class="p-5 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-xs text-indigo-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="font-extrabold text-sm text-indigo-950 flex items-center gap-1.5">
                            <span>🎓 How to Register for this Course</span>
                        </div>
                        <p class="text-indigo-800 mt-0.5">
                            Save this course to your personal wishlist or visit the front-desk center registrar to finalize schedule selection and payment.
                        </p>
                    </div>
                    @if($isEnrolled)
                        <span class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-extrabold text-xs shrink-0">
                            ✓ Already Enrolled
                        </span>
                    @else
                        <form method="POST" action="{{ route('student.catalog.wishlist.toggle', $course) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-sm transition-all whitespace-nowrap">
                                {{ $isWishlisted ? '✓ Wishlisted' : '+ Add to Wishlist' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Available Class Sections (Requirement #1 & #4) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-sm">
                <h3 class="font-extrabold text-slate-900 text-base mb-1">Available Class Sections & Schedules</h3>
                <p class="text-xs text-slate-500 mb-6">Classroom allocations and faculty instructors for this program</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($course->classes as $class)
                        <div class="p-5 rounded-2xl border border-slate-200 hover:border-indigo-400 transition-all bg-slate-50/40 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <h4 class="font-extrabold text-slate-900 text-base">{{ $class->name }}</h4>
                                    <span class="px-2 py-0.5 text-xs font-bold rounded bg-blue-100 text-blue-800">
                                        📍 {{ $class->room ?? 'Lab TBA' }}
                                    </span>
                                </div>
                                <div class="mt-3 space-y-1 text-xs text-slate-600">
                                    <p><span class="font-bold text-slate-700">Days:</span> {{ $class->schedule_day ?? 'TBA' }}</p>
                                    <p><span class="font-bold text-slate-700">Time:</span> {{ $class->schedule_time ?? 'TBA' }}</p>
                                    <p><span class="font-bold text-slate-700">Instructor:</span> {{ $class->teacher->name ?? 'Faculty Staff' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 text-[11px] text-slate-500 flex items-center justify-between">
                                <span>Batch Status: {{ ucfirst($class->status) }}</span>
                                <span class="font-semibold text-emerald-600">Capacity: {{ $class->capacity ?? 25 }} students</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                            No class sections currently scheduled for this course. Please consult administration.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
