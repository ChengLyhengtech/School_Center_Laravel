<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('School Profile & System Configuration') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage institution identity, official branding media, contact info, and social links.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <form action="{{ route('admin.settings.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 1. Identity Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-800">1. Institution Identity</h3>
                            <p class="text-xs text-gray-500">Official name and slogan shown across student receipts, certificates, and portals.</p>
                        </div>
                        <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">School Brand Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $profile->name) }}" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Tagline / Slogan</label>
                            <input type="text" name="tagline" value="{{ old('tagline', $profile->tagline) }}" placeholder="e.g. Empowering Next Generation Leaders" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- 2. Media Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-800">2. Official Branding Media</h3>
                            <p class="text-xs text-gray-500">Upload high-resolution logo and homepage / dashboard banner image.</p>
                        </div>
                        <span class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Logo Upload -->
                        <div class="space-y-3">
                            <label class="block text-xs font-semibold uppercase text-gray-600">Official Logo</label>
                            @if($profile->logo)
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <img src="{{ $profile->logo }}" alt="School Logo" class="h-14 w-14 object-contain rounded bg-white p-1 border">
                                    <span class="text-xs text-gray-500">Current active logo</span>
                                </div>
                            @endif
                            <input type="file" name="logo" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-[11px] text-gray-400">Supported formats: PNG, JPG, WEBP, SVG (Max 2MB).</p>
                        </div>

                        <!-- Banner Upload -->
                        <div class="space-y-3">
                            <label class="block text-xs font-semibold uppercase text-gray-600">Homepage / Portal Banner</label>
                            @if($profile->banner)
                                <div class="p-2 bg-gray-50 rounded-lg border border-gray-200">
                                    <img src="{{ $profile->banner }}" alt="School Banner" class="h-20 w-full object-cover rounded">
                                </div>
                            @endif
                            <input type="file" name="banner" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            <p class="text-[11px] text-gray-400">Supported formats: PNG, JPG, WEBP (Max 4MB).</p>
                        </div>
                    </div>
                </div>

                <!-- 3. Contact & Location Hub -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-800">3. Contact & Location Hub</h3>
                            <p class="text-xs text-gray-500">Physical address, support phone, official email, and social networks.</p>
                        </div>
                        <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Support Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" placeholder="+855 12 345 678" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Official Support Email</label>
                            <input type="email" name="email" value="{{ old('email', $profile->email) }}" placeholder="info@school.edu.kh" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Campus Physical Address</label>
                            <textarea name="address" rows="2" placeholder="Building #, Street, Khan, City/Province" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $profile->address) }}</textarea>
                        </div>
                    </div>

                    <!-- Social Links (Stored as JSON in DB) -->
                    <div class="pt-3 border-t border-gray-100">
                        <h4 class="text-xs font-bold uppercase text-gray-700 tracking-wider mb-3">Official Social Media Links (Stored in JSON)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Facebook URL</label>
                                <input type="url" name="facebook" value="{{ old('facebook', $profile->social_links['facebook'] ?? '') }}" placeholder="https://facebook.com/your-school" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Telegram Channel / Link</label>
                                <input type="text" name="telegram" value="{{ old('telegram', $profile->social_links['telegram'] ?? '') }}" placeholder="https://t.me/your-school" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">YouTube Channel URL</label>
                                <input type="url" name="youtube" value="{{ old('youtube', $profile->social_links['youtube'] ?? '') }}" placeholder="https://youtube.com/@your-school" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Website URL</label>
                                <input type="url" name="website" value="{{ old('website', $profile->social_links['website'] ?? '') }}" placeholder="https://your-school.edu.kh" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
