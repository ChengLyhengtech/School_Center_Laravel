<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CLH School Center') }} - Portal Access</title>

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
        .portal-backdrop {
            background-color: #0b132b;
            background-image: radial-gradient(rgba(59, 130, 246, 0.15) 1px, transparent 1px), radial-gradient(rgba(99, 102, 241, 0.1) 1px, #0b132b 1px);
            background-size: 32px 32px;
            background-position: 0 0, 16px 16px;
        }
    </style>
</head>
<body class="portal-backdrop text-slate-800 antialiased min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white py-8 px-4 sm:px-6">

    <!-- Top Navigation Header -->
    <div class="max-w-md w-full mx-auto flex items-center justify-between mb-4">
        <a href="{{ route('home', ['preview' => 1]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to School Homepage</span>
        </a>
        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-900/60 text-indigo-300 border border-indigo-700/50">
            Secure Portal
        </span>
    </div>

    <!-- Center Form Card -->
    <div class="max-w-md w-full mx-auto">
        <!-- School Crest & Header -->
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white shadow-xl shadow-indigo-500/25 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                <h1 class="text-xl font-extrabold text-white tracking-tight mt-3">CLH School Center</h1>
                <p class="text-xs text-indigo-300 font-medium">Academic, Student & Staff Management Hub</p>
            </a>
        </div>

        <!-- Main Card Form Slot -->
        <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8 border border-slate-100">
            {{ $slot }}
        </div>
    </div>

    <!-- Bottom Footer Note -->
    <div class="max-w-md w-full mx-auto text-center mt-6 text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} CLH School & Education Center. All rights reserved.</p>
    </div>

</body>
</html>
