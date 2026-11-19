<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white transition-transform duration-300 transform md:relative md:translate-x-0 border-r border-gray-300">
    
    <div class="flex items-center px-6 h-16 border-b border-gray-200">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="text-lg font-black text-gray-800 tracking-tight">ADMIN PANEL</span>
        </div>
    </div>

    <nav class="mt-6 px-3 space-y-1 pb-10">
        <p class="text-[10px] text-gray-400 uppercase font-bold mb-2 px-3 tracking-widest">Main Menu</p>
        
        <x-nav-link-sidebar :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="dashboard">
            Dashboard
        </x-nav-link-sidebar>

        <p class="text-[10px] text-gray-400 uppercase font-bold mt-8 mb-2 px-3 tracking-widest">Manajemen</p>
        
        <x-nav-link-sidebar :href="route('admin.rooms.index')" :active="request()->routeIs('admin.rooms.*')" icon="home">
            Data Kamar
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('admin.reservations.index')" :active="request()->routeIs('admin.reservations.*')" icon="ticket">
            Data Pesanan
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('admin.residents.index')" :active="request()->routeIs('admin.residents.*')" icon="user">
            Data Penghuni
        </x-nav-link-sidebar>

        <p class="text-[10px] text-gray-400 uppercase font-bold mt-8 mb-2 px-3 tracking-widest">Layanan & Fasilitas</p>

        <x-nav-link-sidebar :href="route('admin.damage-reports.index')" :active="request()->routeIs('admin.damage-reports.*')" icon="wrench" :badge="$adminNewReportsCount ?? null">
            Pelaporan Kerusakan
        </x-nav-link-sidebar>

        <p class="text-[10px] text-gray-400 uppercase font-bold mt-8 mb-2 px-3 tracking-widest">Laporan & Notifikasi</p>

        <x-nav-link-sidebar :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')" icon="chart">
            Pelaporan Keuangan
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('admin.reminders.index')" :active="request()->routeIs('admin.reminders.*')" icon="mail">
            Pengingat Email
        </x-nav-link-sidebar>

        <p class="text-[10px] text-gray-400 uppercase font-bold mt-8 mb-2 px-3 tracking-widest">Pengaturan & Akun</p>

        <x-nav-link-sidebar :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" icon="settings">
            Setting Web
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">
            Profil Akun
        </x-nav-link-sidebar>

        <div class="pt-6 mt-10 border-t border-gray-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-3 py-2 text-gray-500 hover:bg-red-50 hover:text-red-600 rounded transition-colors font-bold text-sm">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout Akun
                </button>
            </form>
        </div>
    </nav>
</div>