<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links (Role Aware) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->hasRole('admin'))
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                            {{ __('Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.classes.index')" :active="request()->routeIs('admin.classes.*')">
                            {{ __('Classes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.enrollments.index')" :active="request()->routeIs('admin.enrollments.*')">
                            {{ __('Enrollments') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.attendances.index')" :active="request()->routeIs('admin.attendances.*')">
                            {{ __('Attendance') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.invoices.index')" :active="request()->routeIs('admin.invoices.*')">
                            {{ __('Invoices') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                            {{ __('Payments') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.discounts.index')" :active="request()->routeIs('admin.discounts.*')">
                            {{ __('Discounts') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.roles-permissions.index')" :active="request()->routeIs('admin.roles-permissions.*')">
                            {{ __('RBAC') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.settings.profile')" :active="request()->routeIs('admin.settings.*')">
                            {{ __('Settings') }}
                        </x-nav-link>
                    @elseif(Auth::user()->hasRole('teacher'))
                        <x-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.classes.index')" :active="request()->routeIs('teacher.classes.*')">
                            {{ __('Classes & Schedules') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.roster.index')" :active="request()->routeIs('teacher.roster.*')">
                            {{ __('Student Rosters') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.attendance.index')" :active="request()->routeIs('teacher.attendance.*')">
                            {{ __('Attendance') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.grades.index')" :active="request()->routeIs('teacher.grades.*')">
                            {{ __('Gradebook & Scores') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.profile.edit')" :active="request()->routeIs('teacher.profile.*')">
                            {{ __('Teacher Profile') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.classes.index')" :active="request()->routeIs('student.classes.*')">
                            {{ __('My Classes & Schedule') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.attendance.index')" :active="request()->routeIs('student.attendance.*')">
                            {{ __('Attendance') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.grades.index')" :active="request()->routeIs('student.grades.*')">
                            {{ __('Grades & Feedback') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.invoices.index')" :active="request()->routeIs('student.invoices.*')">
                            {{ __('Invoices & Payments') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.catalog.index')" :active="request()->routeIs('student.catalog.*')">
                            {{ __('Courses & Wishlist') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.profile.edit')" :active="request()->routeIs('student.profile.*')">
                            {{ __('Profile & Family') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Role Badge -->
                <span class="me-3 px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider rounded-full {{ Auth::user()->hasRole('admin') ? 'bg-indigo-100 text-indigo-800' : (Auth::user()->hasRole('teacher') ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ Auth::user()->role ?? 'User' }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(Auth::user()->hasRole('teacher'))
                            <x-dropdown-link :href="route('teacher.profile.edit')">
                                {{ __('Teacher Profile') }}
                            </x-dropdown-link>
                        @elseif(Auth::user()->hasRole('admin'))
                            <x-dropdown-link :href="route('admin.settings.profile')">
                                {{ __('School Profile') }}
                            </x-dropdown-link>
                        @elseif(Auth::user()->hasRole('student'))
                            <x-dropdown-link :href="route('student.profile.edit')">
                                {{ __('Student Profile') }}
                            </x-dropdown-link>
                        @else
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->hasRole('admin'))
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Admin Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                    {{ __('Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.classes.index')" :active="request()->routeIs('admin.classes.*')">
                    {{ __('Classes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.enrollments.index')" :active="request()->routeIs('admin.enrollments.*')">
                    {{ __('Enrollments') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.attendances.index')" :active="request()->routeIs('admin.attendances.*')">
                    {{ __('Attendance') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.invoices.index')" :active="request()->routeIs('admin.invoices.*')">
                    {{ __('Invoices') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                    {{ __('Payments') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.discounts.index')" :active="request()->routeIs('admin.discounts.*')">
                    {{ __('Discounts') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.roles-permissions.index')" :active="request()->routeIs('admin.roles-permissions.*')">
                    {{ __('RBAC') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings.profile')" :active="request()->routeIs('admin.settings.*')">
                    {{ __('Settings') }}
                </x-responsive-nav-link>
            @elseif(Auth::user()->hasRole('teacher'))
                <x-responsive-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                    {{ __('Teacher Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.classes.index')" :active="request()->routeIs('teacher.classes.*')">
                    {{ __('Classes & Schedules') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.roster.index')" :active="request()->routeIs('teacher.roster.*')">
                    {{ __('Student Rosters') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.attendance.index')" :active="request()->routeIs('teacher.attendance.*')">
                    {{ __('Attendance Tracker') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.grades.index')" :active="request()->routeIs('teacher.grades.*')">
                    {{ __('Gradebook & Scores') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.profile.edit')" :active="request()->routeIs('teacher.profile.*')">
                    {{ __('Teacher Profile') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                    {{ __('Student Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.classes.index')" :active="request()->routeIs('student.classes.*')">
                    {{ __('My Classes & Schedule') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.attendance.index')" :active="request()->routeIs('student.attendance.*')">
                    {{ __('Attendance') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.grades.index')" :active="request()->routeIs('student.grades.*')">
                    {{ __('Grades & Feedback') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.invoices.index')" :active="request()->routeIs('student.invoices.*')">
                    {{ __('Invoices & Payments') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.catalog.index')" :active="request()->routeIs('student.catalog.*')">
                    {{ __('Courses & Wishlist') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.profile.edit')" :active="request()->routeIs('student.profile.*')">
                    {{ __('Profile & Family') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center justify-between">
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <span class="px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider rounded-full {{ Auth::user()->hasRole('admin') ? 'bg-indigo-100 text-indigo-800' : (Auth::user()->hasRole('teacher') ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ Auth::user()->role ?? 'User' }}
                </span>
            </div>

            <div class="mt-3 space-y-1">
                @if(Auth::user()->hasRole('teacher'))
                    <x-responsive-nav-link :href="route('teacher.profile.edit')">
                        {{ __('Teacher Profile') }}
                    </x-responsive-nav-link>
                @elseif(Auth::user()->hasRole('admin'))
                    <x-responsive-nav-link :href="route('admin.settings.profile')">
                        {{ __('School Profile') }}
                    </x-responsive-nav-link>
                @elseif(Auth::user()->hasRole('student'))
                    <x-responsive-nav-link :href="route('student.profile.edit')">
                        {{ __('Student Profile') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
