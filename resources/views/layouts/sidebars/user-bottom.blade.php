<div class="md:hidden fixed bottom-6 left-6 right-6 bg-white/90 backdrop-blur-xl border border-slate-200 p-3 z-50 shadow-2xl rounded-3xl">
    <div class="flex justify-around items-center">

        <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center p-2 rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-[9px] font-bold mt-1 uppercase tracking-tighter">Eksplor</span>
        </a>

        <a href="{{ route('user.bookings.index') }}"
            class="flex flex-col items-center p-2 rounded-2xl transition-all {{ request()->routeIs('user.bookings.index') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
            </svg>
            <span class="text-[9px] font-bold mt-1 uppercase tracking-tighter">Pesanan</span>
        </a>

        <a href="{{ route('user.bookings.history') }}?tab=history"
            class="flex flex-col items-center p-2 rounded-2xl transition-all {{ request()->fullUrlIs(route('user.bookings.index') . '*tab=history*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-[9px] font-bold mt-1 uppercase tracking-tighter">Riwayat</span>
        </a>

        <a href="{{ route('profile.edit') }}"
            class="flex flex-col items-center p-2 rounded-2xl transition-all {{ request()->routeIs('profile.edit') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-400 hover:text-slate-600' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-[9px] font-bold mt-1 uppercase tracking-tighter">Profil</span>
        </a>

    </div>
</div>
