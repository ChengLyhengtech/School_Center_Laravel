<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Course Catalog & Wishlist Hub') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Discover available academic programs and save desired courses for walk-in enrollment.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('student.classes.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                    My Enrolled Classes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Filter, Search & Tabs Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Navigation Tabs -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('student.catalog.index', ['tab' => 'all', 'search' => $search]) }}" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all {{ $tab === 'all' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        All Courses
                    </a>
                    <a href="{{ route('student.catalog.index', ['tab' => 'wishlist', 'search' => $search]) }}" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center gap-1.5 {{ $tab === 'wishlist' ? 'bg-rose-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>❤️ Saved Wishlist</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'wishlist' ? 'bg-rose-700 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ count($wishlistedCourseIds) }}
                        </span>
                    </a>
                </div>

                <!-- Search Input -->
                <form method="GET" action="{{ route('student.catalog.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search course title or topics..." class="w-full md:w-72 rounded-xl border-slate-300 text-xs focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 font-medium">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-2xs">
                        Search
                    </button>
                    @if($search)
                        <a href="{{ route('student.catalog.index', ['tab' => $tab]) }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Wishlist Total Tuition Estimate (Requirement #4) -->
            @if($tab === 'wishlist' && count($wishlistedCourseIds) > 0)
                <div class="p-5 rounded-2xl bg-gradient-to-r from-rose-50 to-orange-50 border border-rose-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg">📋</span>
                            <h4 class="font-extrabold text-sm text-rose-950">Walk-In Registration Wishlist Summary</h4>
                        </div>
                        <p class="text-xs text-rose-800 mt-0.5">
                            You have selected {{ count($wishlistedCourseIds) }} course(s) for your academic plan.
                        </p>
                    </div>
                    <div class="sm:text-right shrink-0">
                        <span class="text-xs text-rose-700 font-semibold uppercase tracking-wider">Estimated Total Tuition:</span>
                        <div class="text-2xl font-black text-rose-950 font-mono mt-0.5">
                            ${{ number_format($wishlistTotalTuition, 2) }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Courses Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($courses as $course)
                    @php
                        $isWishlisted = in_array($course->id, $wishlistedCourseIds);
                        $isEnrolled = in_array($course->id, $enrolledCourseIds);
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Header Pill Row -->
                            <div class="flex items-start justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700">
                                    {{ $course->duration ?? 'Self-paced' }}
                                </span>

                                <!-- Wishlist Toggle Button -->
                                <form method="POST" action="{{ route('student.catalog.wishlist.toggle', $course) }}">
                                    @csrf
                                    <button type="submit" title="{{ $isWishlisted ? 'Remove from wishlist' : 'Save to wishlist' }}" class="p-2 rounded-xl transition-all {{ $isWishlisted ? 'bg-rose-50 text-rose-600 hover:bg-rose-100' : 'bg-slate-100 text-slate-400 hover:text-rose-600 hover:bg-rose-50' }}">
                                        <svg class="w-4 h-4 {{ $isWishlisted ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </button>
                                </form>
                            </div>

                            <!-- Course Title & Pricing -->
                            <h3 class="text-lg font-extrabold text-slate-900 mt-2 leading-snug group-hover:text-indigo-600 transition-colors">
                                <a href="{{ route('student.catalog.show', $course) }}">
                                    {{ $course->title }}
                                </a>
                            </h3>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-2xl font-black text-slate-900 font-mono">
                                    ${{ number_format($course->price, 2) }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">Standard Tuition Fee</span>
                            </div>

                            <p class="text-xs text-slate-600 mt-3 line-clamp-3 leading-relaxed">
                                {{ $course->description }}
                            </p>

                            <!-- Course Metadata -->
                            <div class="mt-4 pt-3 border-t border-slate-100 space-y-1.5 text-xs text-slate-500">
                                @if($course->start_date && $course->end_date)
                                    <div class="flex items-center justify-between">
                                        <span>Upcoming Schedule:</span>
                                        <span class="font-bold text-slate-800 font-mono">
                                            {{ $course->start_date->format('M d') }} - {{ $course->end_date->format('M d, Y') }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span>Active Classes:</span>
                                    <span class="font-bold text-slate-800">{{ $course->classes->count() }} Section(s)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Row -->
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2">
                            @if($isEnrolled)
                                <span class="w-full text-center py-2.5 px-3 rounded-xl bg-emerald-50 text-emerald-800 font-extrabold text-xs border border-emerald-200">
                                    ✓ Currently Enrolled
                                </span>
                            @else
                                <a href="{{ route('student.catalog.show', $course) }}" class="w-full text-center py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-2xs transition-colors">
                                    View Syllabus &amp; Enroll &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                        <p class="font-bold text-slate-700">No courses match your criteria.</p>
                        <p class="text-xs text-slate-400 mt-1">Try clearing your search query or check back later.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
