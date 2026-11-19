<div class="hidden md:flex flex-col w-64 bg-white border-r border-gray-300 h-screen sticky top-0">
    <div class="flex items-center px-6 h-16 border-b border-gray-200">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="text-lg font-bold text-gray-800">KOSKU</span>
        </a>
    </div>

    <nav class="flex-1 mt-6 px-3 space-y-1">
        <div class="px-3 mb-2 text-[10px] font-bold text-gray-500 uppercase">Navigasi</div>
        
        <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
            Beranda
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('user.bookings.index')" :active="request()->routeIs('user.bookings.index') || request()->routeIs('user.bookings.show')" icon="ticket">
            Pesanan
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('user.bookings.history')" :active="request()->routeIs('user.bookings.history')" icon="history">
            Riwayat
        </x-nav-link-sidebar>

        <x-nav-link-sidebar :href="route('user.damage-reports.index')" :active="request()->routeIs('user.damage-reports.*')" icon="wrench" :badge="$userUnreadReportsCount ?? null">
            Lapor Kerusakan
        </x-nav-link-sidebar>

        <div class="pt-6 mt-6 border-t border-gray-200">
            <div class="px-3 mb-2 text-[10px] font-bold text-gray-500 uppercase">Akun</div>
            
            <x-nav-link-sidebar :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">
                Profil
            </x-nav-link-sidebar>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full flex items-center px-3 py-2 text-gray-600 hover:bg-gray-100 rounded transition-colors font-bold text-sm">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="p-4 border-t border-gray-200">
        <div class="bg-gray-50 p-4 border border-gray-200 rounded">
            <p class="text-xs font-bold text-gray-700">Ada Masalah?</p>
            <a href="https://wa.me/6285357597653" target="_blank" class="mt-2 block text-center py-2 bg-white border border-gray-300 rounded text-xs font-bold text-gray-600 hover:bg-gray-50">Kontak Admin</a>
        </div>
    </div>
</div>