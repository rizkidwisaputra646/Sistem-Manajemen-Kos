<div class="hidden md:flex flex-col w-72 bg-white border-r border-slate-200 h-screen sticky top-0 shadow-sm">
    <div class="flex items-center px-8 h-20 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="text-lg font-bold tracking-tight text-slate-900 uppercase">KOS<span class="text-indigo-600">KU</span></span>
        </div>
    </div>

    <nav class="flex-1 mt-6 px-4 space-y-1.5 overflow-y-auto">
        <div class="px-4 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Main Menu</div>
        
        <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">
            Dashboard
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="search">
            Eksplor Kamar
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('user.bookings.index')" :active="request()->routeIs('user.bookings.index')" icon="ticket">
            Booking Saya
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('user.bookings.history')" :active="request()->routeIs('user.bookings.history')" icon="history">
            Riwayat Sewa
        </x-nav-link-sidebar>

        <div class="pt-6 mt-6 border-t border-slate-100">
            <div class="px-4 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Account</div>
            
            <x-nav-link-sidebar :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">
                Profil Saya
            </x-nav-link-sidebar>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 text-slate-500 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition-all duration-200 group font-medium text-sm">
                    <svg class="w-5 h-5 mr-3 opacity-70 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Keluar Akun
                </button>
            </form>
        </div>
    </nav>

    <div class="p-4 mt-auto">
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
            <p class="text-xs font-bold text-slate-900">Butuh Bantuan?</p>
            <p class="text-[10px] text-slate-500 mt-1">Hubungi admin jika ada kendala pembayaran.</p>
            <a href="#" class="mt-3 block text-center py-2 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-slate-700 hover:border-indigo-300 hover:text-indigo-600 transition-all">Hubungi CS</a>
        </div>
    </div>
</div>