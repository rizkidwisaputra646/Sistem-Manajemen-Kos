<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
     class="fixed inset-y-0 left-0 z-50 w-72 bg-white transition-transform duration-500 transform md:relative md:translate-x-0 border-r border-slate-200">
    
    <div class="flex items-center px-8 h-24 mb-4">
        <div class="flex items-center gap-3">
             <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="text-xl font-black tracking-tighter text-slate-900 uppercase">ADMIN<span class="text-indigo-600 font-black">PANEL</span></span>
        </div>
    </div>

    <nav class="px-6 space-y-1">
        <p class="text-[10px] text-slate-400 uppercase font-black mb-4 px-2 tracking-[0.2em] italic">Menu Utama</p>
        
        <x-nav-link-sidebar :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="dashboard">
            Dashboard
        </x-nav-link-sidebar>

        <p class="text-[10px] text-slate-400 uppercase font-black mt-10 mb-4 px-2 tracking-[0.2em] italic">Aset & Hunian</p>
        
        <x-nav-link-sidebar :href="route('admin.rooms.index')" :active="request()->routeIs('admin.rooms.*')" icon="home">
            Manajemen Kamar
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('admin.reservations.index')" :active="request()->routeIs('admin.reservations.*')" icon="ticket">
            Data Reservasi
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('admin.residents.index')" :active="request()->routeIs('admin.residents.*')" icon="users">
            Data Penghuni
        </x-nav-link-sidebar>

        <p class="text-[10px] text-slate-400 uppercase font-black mt-10 mb-4 px-2 tracking-[0.2em] italic">Finansial</p>

        <x-nav-link-sidebar :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')" icon="chart">
            Laporan Keuangan
        </x-nav-link-sidebar>

        <div class="pt-8 mt-12 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 text-slate-400 hover:bg-rose-50 hover:text-rose-600 rounded-2xl transition-all duration-300 group font-bold text-sm">
                    <span class="mr-3 scale-110 group-hover:scale-125 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </span>
                    Logout Akun
                </button>
            </form>
        </div>
    </nav>
</div>