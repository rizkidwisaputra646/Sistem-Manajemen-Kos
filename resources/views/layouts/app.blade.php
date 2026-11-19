<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Premium Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; 
        }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
</head>

<body class="antialiased bg-gray-100 text-gray-800" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        @auth
            @if (Auth::user()?->role == 'admin')
                @include('layouts.sidebars.admin')
            @else
                @include('layouts.sidebars.user')
            @endif
        @endauth

        <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden {{ Auth::user()?->role == 'user' ? 'pb-24 md:pb-0' : '' }}">

            <!-- Premium Header with Shadow & Blur -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm h-16 flex items-center justify-between px-6 sticky top-0 z-30">
                
                <div class="flex items-center gap-4">
                    @if (Auth::user()?->role == 'admin')
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 md:hidden p-2 border border-gray-300 rounded hover:bg-gray-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    @endif
                    
                    <h2 class="text-base font-bold text-gray-700">
                        @php
                            $segment = Request::segment(1);
                            $title = 'Beranda';
                            if ($segment == 'dashboard') $title = 'Cari Kamar';
                            elseif ($segment == 'my-bookings') $title = 'Pesanan Saya';
                            elseif ($segment == 'damage-reports') $title = 'Lapor Kerusakan';
                            elseif ($segment == 'profile') $title = 'Profil Akun';
                            elseif ($segment == 'admin') {
                                $seg2 = Request::segment(2);
                                if ($seg2 == 'reports') $title = 'Pelaporan Transaksi';
                                elseif ($seg2 == 'damage-reports') $title = 'Pelaporan Kerusakan';
                                elseif ($seg2 == 'reminders') $title = 'Pengingat Email';
                                elseif ($seg2 == 'reservations') $title = 'Data Pesanan';
                                elseif ($seg2 == 'rooms') $title = 'Data Kamar';
                                elseif ($seg2 == 'residents') $title = 'Data Penghuni';
                                elseif ($seg2 == 'settings') $title = 'Pengaturan Web';
                                else $title = 'Panel Admin';
                            }
                            elseif ($segment == 'rooms') $title = 'Detail Kamar';
                            else if ($segment) $title = ucwords(str_replace('-', ' ', $segment));
                        @endphp
                        {{ $title }}
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        @if (Auth::user()?->role === 'admin')
                            <!-- Top Bar Notification Bell (Admin) -->
                            <div class="relative" x-data="{ bellOpen: false }">
                                <button @click="bellOpen = !bellOpen" @click.away="bellOpen = false" 
                                    class="relative p-2 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none cursor-pointer"
                                    title="Notifikasi Laporan Kerusakan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    @if(($adminNewReportsCount ?? 0) > 0)
                                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">
                                            {{ ($adminNewReportsCount ?? 0) > 9 ? '9+' : $adminNewReportsCount }}
                                        </span>
                                    @endif
                                </button>

                                <!-- Dropdown Laporan Masuk -->
                                <div x-show="bellOpen" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden"
                                     style="display: none;">
                                     
                                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                        <span class="text-xs font-bold text-gray-800">Laporan Kerusakan Baru</span>
                                        <span class="text-[10px] font-semibold bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full">
                                            {{ $adminNewReportsCount ?? 0 }} Baru
                                        </span>
                                    </div>

                                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                                        @forelse($adminRecentReports ?? [] as $recent)
                                            <a href="{{ route('admin.damage-reports.show', $recent->id) }}" 
                                               class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ !$recent->is_read_by_admin ? 'bg-blue-50/40' : '' }}">
                                                <div class="flex items-start justify-between gap-2">
                                                    <span class="text-xs font-bold text-gray-800 truncate">
                                                        {{ $recent->facility_name }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 whitespace-nowrap">
                                                        {{ $recent->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <div class="mt-1 flex items-center gap-1.5 text-[11px] text-gray-500">
                                                    <span class="font-medium text-gray-700">{{ $recent->user?->name ?? 'Penghuni' }}</span>
                                                    <span>•</span>
                                                    <span>{{ $recent->room?->name ?? 'Kamar' }}</span>
                                                </div>
                                                <div class="mt-1">
                                                    <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-100 text-amber-800">
                                                        {{ $recent->category }}
                                                    </span>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="px-4 py-6 text-center text-xs text-gray-400">
                                                Tidak ada laporan baru
                                            </div>
                                        @endforelse
                                    </div>

                                    <div class="p-2 bg-gray-50 border-t border-gray-100 text-center">
                                        <a href="{{ route('admin.damage-reports.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                            Lihat Semua Laporan &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            @if(($userUnreadReportsCount ?? 0) > 0)
                                <a href="{{ route('user.damage-reports.index') }}" 
                                   class="relative p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                   title="{{ $userUnreadReportsCount }} tanggapan baru pada laporan Anda">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-indigo-600 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">
                                        {{ $userUnreadReportsCount }}
                                    </span>
                                </a>
                            @endif
                        @endif
                    @endauth

                    <div class="relative" x-data="{ userMenuOpen: false }">
                        @auth
                        <!-- Tombol Profil (Responsif: Desktop & Mobile) -->
                        <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" 
                            class="flex items-center gap-2.5 px-3 py-1.5 border border-gray-200 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none cursor-pointer">
                            <div class="w-7 h-7 {{ Auth::user()?->role === 'admin' ? 'bg-blue-600' : 'bg-indigo-600' }} rounded-full flex items-center justify-center text-xs text-white font-bold shadow-sm">
                                {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <span class="block text-xs font-bold text-gray-800 leading-tight">
                                    {{ Auth::user()?->name }}
                                </span>
                                <span class="block text-[10px] text-gray-400 font-semibold uppercase">
                                    {{ Auth::user()?->role === 'admin' ? 'Admin' : 'Penyewa' }}
                                </span>
                            </div>
                            <!-- Panah Dropdown -->
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Menu Dropdown (Desktop & Mobile) -->
                        <div x-show="userMenuOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 top-full mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden divide-y divide-gray-100"
                             style="display: none;">
                            
                            <div class="px-4 py-3 bg-gray-50/50">
                                <p class="text-xs font-bold text-gray-800 truncate">{{ Auth::user()?->name }}</p>
                                <p class="text-[11px] text-gray-500 truncate">{{ Auth::user()?->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ Auth::user()?->role === 'admin' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    {{ Auth::user()?->role === 'admin' ? 'Administrator' : 'Penyewa' }}
                                </span>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" 
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profil Akun
                                </a>

                                @if(Auth::user()?->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                        </svg>
                                        Dashboard Admin
                                    </a>
                                @else
                                    <a href="{{ route('user.bookings.index') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                        Pesanan Saya
                                    </a>
                                    <a href="{{ route('user.damage-reports.index') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Lapor Kerusakan
                                    </a>
                                @endif
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full text-left px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 font-bold flex items-center gap-2.5 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Keluar (Logout)
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="p-6 pb-6 md:p-8 md:pb-8">
                @isset($header)
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endisset

                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Mobile sidebar toggle check handled in components -->
        @if (Auth::user()?->role == 'user')
            @include('layouts.sidebars.user-bottom')
        @endif

        <!-- Plain Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/40 md:hidden transition-opacity shadow-none"></div>
    </div>
</body>

</html>
