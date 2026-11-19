<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-50 pb-[env(safe-area-inset-bottom)] shadow-[0_-8px_30px_-15px_rgba(0,0,0,0.1)]">
    <div class="flex justify-around items-center px-2 py-1.5">

        <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center justify-center w-16 h-14 transition-all duration-300 group">
            <div class="relative flex items-center justify-center transition-all duration-300 {{ request()->routeIs('dashboard') ? 'w-12 h-8 bg-indigo-600 rounded-2xl text-white shadow-md shadow-indigo-200' : 'w-10 h-8 text-slate-400 group-hover:text-slate-600' }}">
                <svg class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('dashboard') ? '2.5' : '2' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="text-[10px] font-semibold mt-1 transition-colors duration-300 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-500' }}">Beranda</span>
        </a>

        <a href="{{ route('user.bookings.index') }}"
            class="flex flex-col items-center justify-center w-16 h-14 transition-all duration-300 group">
            <div class="relative flex items-center justify-center transition-all duration-300 {{ request()->routeIs('user.bookings.index') ? 'w-12 h-8 bg-indigo-600 rounded-2xl text-white shadow-md shadow-indigo-200' : 'w-10 h-8 text-slate-400 group-hover:text-slate-600' }}">
                <svg class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('user.bookings.index') ? '2.5' : '2' }}" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
            </div>
            <span class="text-[10px] font-semibold mt-1 transition-colors duration-300 {{ request()->routeIs('user.bookings.index') ? 'text-indigo-600' : 'text-slate-500' }}">Pesanan</span>
        </a>

        <a href="{{ route('user.bookings.history') }}?tab=history"
            class="flex flex-col items-center justify-center w-16 h-14 transition-all duration-300 group">
            <div class="relative flex items-center justify-center transition-all duration-300 {{ request()->fullUrlIs(route('user.bookings.index') . '*tab=history*') ? 'w-12 h-8 bg-indigo-600 rounded-2xl text-white shadow-md shadow-indigo-200' : 'w-10 h-8 text-slate-400 group-hover:text-slate-600' }}">
                <svg class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->fullUrlIs(route('user.bookings.index') . '*tab=history*') ? '2.5' : '2' }}" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-[10px] font-semibold mt-1 transition-colors duration-300 {{ request()->fullUrlIs(route('user.bookings.index') . '*tab=history*') ? 'text-indigo-600' : 'text-slate-500' }}">Riwayat</span>
        </a>

        <a href="{{ route('user.damage-reports.index') }}"
            class="flex flex-col items-center justify-center w-14 h-14 transition-all duration-300 group relative">
            <div class="relative flex items-center justify-center transition-all duration-300 {{ request()->routeIs('user.damage-reports.*') ? 'w-11 h-8 bg-indigo-600 rounded-2xl text-white shadow-md shadow-indigo-200' : 'w-10 h-8 text-slate-400 group-hover:text-slate-600' }}">
                <svg class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('user.damage-reports.*') ? '2.5' : '2' }}" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"></path>
                </svg>
                @if(!empty($userUnreadReportsCount) && $userUnreadReportsCount > 0)
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border border-white"></span>
                @endif
            </div>
            <span class="text-[10px] font-semibold mt-1 transition-colors duration-300 {{ request()->routeIs('user.damage-reports.*') ? 'text-indigo-600' : 'text-slate-500' }}">Lapor</span>
        </a>

        <a href="{{ route('profile.edit') }}"
            class="flex flex-col items-center justify-center w-16 h-14 transition-all duration-300 group">
            <div class="relative flex items-center justify-center transition-all duration-300 {{ request()->routeIs('profile.edit') ? 'w-12 h-8 bg-indigo-600 rounded-2xl text-white shadow-md shadow-indigo-200' : 'w-10 h-8 text-slate-400 group-hover:text-slate-600' }}">
                <svg class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('profile.edit') ? '2.5' : '2' }}" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="text-[10px] font-semibold mt-1 transition-colors duration-300 {{ request()->routeIs('profile.edit') ? 'text-indigo-600' : 'text-slate-500' }}">Profil</span>
        </a>

    </div>
</div>
