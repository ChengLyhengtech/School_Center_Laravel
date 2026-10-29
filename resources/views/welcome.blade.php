<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $school->name ?? config('app.name', 'CLH School Center') }} - Modern Academic & Vocational Training Center</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .hero-pattern {
            background-color: #0b132b;
            background-image: radial-gradient(rgba(59, 130, 246, 0.15) 1px, transparent 1px), radial-gradient(rgba(99, 102, 241, 0.1) 1px, #0b132b 1px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
        }
        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .glass-dark-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Top Notice / Contact Bar -->
    <div class="bg-indigo-950 text-indigo-200 text-xs py-2 px-4 border-b border-indigo-900/50 hidden md:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-6">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $school->address ?? 'Daun Penh, Phnom Penh, Cambodia' }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Hotline: {{ $school->phone ?? '+855 23 888 999' }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <span>Admissions: {{ $school->email ?? 'admissions@clh-center.edu.kh' }}</span>
                <span class="text-indigo-400">|</span>
                <span class="inline-flex items-center gap-1 text-emerald-400 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Term 2026-2027 Admissions Open
                </span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-50 glass-header border-b border-slate-200/80 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                <div>
                    <span class="block text-lg font-extrabold text-slate-900 tracking-tight leading-none group-hover:text-indigo-600 transition-colors">
                        {{ $school->name ?? 'CLH School Center' }}
                    </span>
                    <span class="block text-xs font-medium text-slate-500 mt-1">
                        {{ $school->tagline ?? 'Center of Excellence in Education & Technology' }}
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#about" class="hover:text-indigo-600 transition-colors">About Us</a>
                <a href="#programs" class="hover:text-indigo-600 transition-colors">Academic Programs</a>
                <a href="#features" class="hover:text-indigo-600 transition-colors">Why Choose Us</a>
                <a href="#admissions" class="hover:text-indigo-600 transition-colors">Admissions</a>
                <a href="#contact" class="hover:text-indigo-600 transition-colors">Contact</a>
            </nav>

            <!-- User Auth Navigation Actions -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard ({{ ucfirst(auth()->user()->roles->first()?->name ?? 'User') }})</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2.5 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Log Out">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-indigo-600 hover:bg-indigo-50/60 transition-colors">
                        Sign In
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-sm font-semibold hover:from-indigo-700 hover:to-blue-700 shadow-sm shadow-indigo-600/25 transition-all hover:-translate-y-0.5">
                            <span>Enroll as Student</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-pattern text-white pt-20 pb-28 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Ambient Decorative Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-gradient-to-r from-blue-600/20 via-indigo-500/20 to-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center max-w-3xl mx-auto space-y-6">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full glass-dark-card text-xs font-semibold text-indigo-300 border border-indigo-400/20 shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Accredited Academic & Career Skills Training Institution</span>
                </div>

                <!-- Main Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                    Shaping <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-300 to-teal-300">Future Leaders</span> Through High-Impact Education
                </h1>

                <!-- Subheadline -->
                <p class="text-lg sm:text-xl text-slate-300 font-normal leading-relaxed">
                    {{ $school->tagline ?? 'Welcome to CLH School & Language Center. We deliver market-driven language fluency, digital technologies, and career mastery with certified instructors and interactive smart facilities.' }}
                </p>

                <!-- Action CTA Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="#programs" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold text-base shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Explore Courses & Programs</span>
                    </a>
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl glass-dark-card hover:bg-white/10 text-white font-semibold text-base transition-all border border-white/20 hover:border-white/40">
                        <span>New Student Registration</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Live Campus Badges -->
                <div class="pt-8 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Official Ministry Accredited
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Morning, Evening & Weekend Batches
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Installment & Discount Support
                    </span>
                </div>
            </div>

            <!-- Metric Highlights Grid -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <div class="glass-dark-card p-6 rounded-2xl text-center border border-white/10 hover:border-indigo-400/30 transition-colors">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">1,850+</p>
                    <p class="text-xs sm:text-sm font-medium text-indigo-200/80 mt-1">Graduated Students</p>
                </div>
                <div class="glass-dark-card p-6 rounded-2xl text-center border border-white/10 hover:border-indigo-400/30 transition-colors">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">98%</p>
                    <p class="text-xs sm:text-sm font-medium text-indigo-200/80 mt-1">Course Completion Rate</p>
                </div>
                <div class="glass-dark-card p-6 rounded-2xl text-center border border-white/10 hover:border-indigo-400/30 transition-colors">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">35+</p>
                    <p class="text-xs sm:text-sm font-medium text-indigo-200/80 mt-1">Certified Faculty Staff</p>
                </div>
                <div class="glass-dark-card p-6 rounded-2xl text-center border border-white/10 hover:border-indigo-400/30 transition-colors">
                    <p class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">4.9/5</p>
                    <p class="text-xs sm:text-sm font-medium text-indigo-200/80 mt-1">Student Satisfaction Score</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Institutional Features / Why Choose Us -->
    <section id="features" class="py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Why Choose Our Institution</h2>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">
                    An Academic Environment Engineered For Real-World Success
                </p>
                <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                    We combine academic rigor with interactive vocational practice, giving students and professionals the edge they need in competitive job markets.
                </p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50 to-white shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5 font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Practical & Project-Based Curriculum</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Say goodbye to purely theoretical lectures. Every module incorporates real projects, lab exercises, and practical case studies.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50 to-white shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-5 font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Industry-Vetted Instructors</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Learn directly from experienced practitioners, senior educators, and bilingual instructors passionate about mentorship.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50 to-white shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-5 font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Accredited Certifications</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Earn recognized credentials upon successful attendance and examination completion to boost your resume and employer trust.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Academic Courses Section -->
    <section id="programs" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-8">
                <div>
                    <h2 class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Active Curriculum</h2>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">Featured Academic Programs</p>
                    <p class="mt-2 text-sm text-slate-600">Select a course to view details, instructors, schedules, and tuition fees.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-blue-100 text-blue-800">
                        {{ $featuredCourses->count() }} Available Courses
                    </span>
                </div>
            </div>

            <!-- Course Cards Grid -->
            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($featuredCourses as $course)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-lg transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                        <!-- Card Header -->
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-3">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    Course
                                </span>
                                <div class="text-right">
                                    <span class="text-xl font-extrabold text-slate-900">${{ number_format($course->price, 2) }}</span>
                                    <span class="block text-[11px] text-slate-400">Total Tuition</span>
                                </div>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 mt-4 group-hover:text-indigo-600 transition-colors line-clamp-2">
                                {{ $course->title }}
                            </h3>

                            <p class="text-xs text-slate-600 mt-2 line-clamp-3 leading-relaxed">
                                {{ $course->description ?? 'Comprehensive practical curriculum developed by our senior department staff.' }}
                            </p>

                            <!-- Class Batches info -->
                            <div class="mt-5 pt-4 border-t border-slate-100 space-y-2 text-xs text-slate-500">
                                @if($course->classes->isNotEmpty())
                                    @php $activeClass = $course->classes->first(); @endphp
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-700">Primary Batch:</span>
                                        <span>{{ $activeClass->name }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-700">Instructor:</span>
                                        <span class="text-indigo-600 font-semibold">{{ $activeClass->teacher->name ?? 'Faculty Staff' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-700">Schedule:</span>
                                        <span>{{ $activeClass->days ?? 'Flexible' }} ({{ substr($activeClass->start_time ?? '08:00', 0, 5) }} - {{ substr($activeClass->end_time ?? '10:00', 0, 5) }})</span>
                                    </div>
                                @else
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-700">Class Batches:</span>
                                        <span class="text-emerald-600 font-medium">Open for Enrollment</span>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between pt-1">
                                    <span class="font-medium text-slate-700">Duration:</span>
                                    <span>{{ $course->start_date?->format('M d') }} - {{ $course->end_date?->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer CTA -->
                        <div class="p-6 pt-0">
                            <a href="{{ route('register') }}" class="w-full text-center block px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-semibold text-xs transition-colors">
                                Enroll in this Program
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                        <p class="font-medium">No courses published at the moment.</p>
                        <p class="text-xs text-slate-400 mt-1">Please check back soon or contact school admissions.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Admission Process -->
    <section id="admissions" class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Enrollment Made Simple</h2>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">4 Easy Steps to Get Started</p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 text-center relative">
                    <span class="w-10 h-10 rounded-full bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center mx-auto mb-4">1</span>
                    <h3 class="font-bold text-slate-900 text-base">Select Your Program</h3>
                    <p class="mt-2 text-xs text-slate-600">Browse language, digital tech, and vocational training options.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 text-center relative">
                    <span class="w-10 h-10 rounded-full bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center mx-auto mb-4">2</span>
                    <h3 class="font-bold text-slate-900 text-base">Register Online / Walk-in</h3>
                    <p class="mt-2 text-xs text-slate-600">Submit your registration or meet an admissions consultant on campus.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 text-center relative">
                    <span class="w-10 h-10 rounded-full bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center mx-auto mb-4">3</span>
                    <h3 class="font-bold text-slate-900 text-base">Confirm & Pay Tuition</h3>
                    <p class="mt-2 text-xs text-slate-600">Support for full payments, promotional coupons, or installment plans.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 text-center relative">
                    <span class="w-10 h-10 rounded-full bg-emerald-600 text-white font-extrabold text-sm flex items-center justify-center mx-auto mb-4">4</span>
                    <h3 class="font-bold text-slate-900 text-base">Commence Learning</h3>
                    <p class="mt-2 text-xs text-slate-600">Access student portal, attend live batches, and track your attendance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Institutional Footer & Contact Information -->
    <footer id="contact" class="bg-slate-900 text-slate-300 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Col 1: School Identity -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold">
                            🎓
                        </div>
                        <span class="text-lg font-bold text-white">{{ $school->name ?? 'CLH School Center' }}</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        {{ $school->tagline ?? 'Dedicated to educational excellence, career empowerment, and recognized certifications.' }}
                    </p>
                    <div class="pt-2 text-xs text-slate-400 space-y-1">
                        <p>📍 {{ $school->address ?? 'Daun Penh, Phnom Penh, Cambodia' }}</p>
                        <p>📞 {{ $school->phone ?? '+855 23 888 999' }}</p>
                        <p>✉️ {{ $school->email ?? 'admissions@clh-center.edu.kh' }}</p>
                    </div>
                </div>

                <!-- Col 2: Academic Portals -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Institution Portals</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Student Learning Portal</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Instructor & Faculty Portal</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">School Administration Portal</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">New Student Online Application</a></li>
                    </ul>
                </div>

                <!-- Col 3: Quick Links -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#about" class="hover:text-white transition-colors">About Our Academy</a></li>
                        <li><a href="#programs" class="hover:text-white transition-colors">Available Courses</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors">Why Choose CLH</a></li>
                        <li><a href="#admissions" class="hover:text-white transition-colors">Tuition & Enrollment</a></li>
                    </ul>
                </div>

                <!-- Col 4: Campus Hours -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Campus Office Hours</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li class="flex justify-between"><span>Mon - Fri:</span> <span class="text-white">07:30 AM - 08:30 PM</span></li>
                        <li class="flex justify-between"><span>Saturday:</span> <span class="text-white">08:00 AM - 05:30 PM</span></li>
                        <li class="flex justify-between"><span>Sunday:</span> <span class="text-white">08:00 AM - 12:00 PM</span></li>
                    </ul>
                    <div class="mt-4 pt-4 border-t border-slate-800 flex items-center gap-3">
                        @if(isset($school->social_links['facebook']))
                            <a href="{{ $school->social_links['facebook'] }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-indigo-600 text-white flex items-center justify-center text-xs transition-colors">FB</a>
                        @endif
                        @if(isset($school->social_links['telegram']))
                            <a href="{{ $school->social_links['telegram'] }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-blue-500 text-white flex items-center justify-center text-xs transition-colors">TG</a>
                        @endif
                        @if(isset($school->social_links['youtube']))
                            <a href="{{ $school->social_links['youtube'] }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition-colors">YT</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-800/80 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} {{ $school->name ?? 'CLH School Center' }}. All rights reserved.</p>
                <p>Empowered by Modern Education & Student Management Architecture.</p>
            </div>
        </div>
    </footer>

</body>
</html>
