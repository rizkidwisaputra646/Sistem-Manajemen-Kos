<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KosKu') }} - Premium Boarding House</title>
    
    <!-- Premium Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; 
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .blob-1 { animation: float 6s ease-in-out infinite; }
        .blob-2 { animation: float 8s ease-in-out infinite reverse; }
        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
            100% { transform: translateY(0px) scale(1); }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-slate-50 text-slate-800 selection:bg-indigo-500 selection:text-white">
    
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-nav border-b border-white/20 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-600 to-blue-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <span class="text-xl sm:text-2xl font-black tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700">KOSKU</span>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <a href="#about" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Tentang Kami</a>
                <a href="#features" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Keunggulan</a>
                <a href="#rooms" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Kamar</a>
                <a href="#contact" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Kontak</a>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 sm:px-6 sm:py-2.5 bg-slate-900 text-white rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold shadow-lg shadow-slate-200 hover:bg-slate-800 transition-all hover:-translate-y-0.5">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-3 py-1.5 sm:px-6 sm:py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg sm:rounded-xl text-xs sm:text-sm font-bold shadow-md sm:shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:-translate-y-0.5 transition-all">Daftar</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden bg-slate-50">
        <!-- Abstract Background Blobs -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-indigo-100/60 blur-3xl blob-1 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-100/60 blur-3xl blob-2 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10 flex flex-col lg:flex-row items-center gap-12">
            <!-- Text Content -->
            <div class="flex-1 text-center lg:text-left pt-10">
                <h1 class="text-5xl md:text-6xl font-black mb-6 tracking-tight leading-[1.1]">
                    {!! $settings['hero_title'] ?? 'Cari Kamar Kontrakan <br/> <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-blue-500">Jadi Lebih Mudah</span>' !!}
                </h1>
                <p class="text-lg md:text-xl text-slate-600 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    {{ $settings['hero_subtitle'] ?? 'Temukan ribuan kamar kontrakan di lokasi strategis yang nyaman, aman, dan cocok dengan budget Anda.' }}
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold shadow-xl shadow-slate-200 hover:bg-slate-800 hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                        Cari Kamar Sekarang
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Image Composition (Hidden on very small screens) -->
            <div class="flex-1 w-full hidden md:block relative h-[500px]">
                @if(isset($rooms) && $rooms->count() > 0)
                    @foreach($rooms->take(3) as $index => $room)
                        @if($index == 0)
                            <div class="absolute top-0 right-10 w-56 h-72 rounded-3xl overflow-hidden shadow-2xl shadow-indigo-200 border-[6px] border-white transform rotate-3 hover:rotate-0 hover:z-40 hover:scale-105 transition-all duration-300 z-10 group bg-slate-100">
                                @if($room->image)
                                    <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs text-slate-500 font-bold p-4 text-center">Kamar {{ $room->name }}</div>
                                @endif
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-900/80 to-transparent p-4">
                                    <p class="text-white font-bold text-sm">{{ $room->name }}</p>
                                </div>
                            </div>
                        @elseif($index == 1)
                            <div class="absolute top-24 left-0 w-64 h-64 rounded-3xl overflow-hidden shadow-2xl shadow-blue-200 border-[6px] border-white transform -rotate-6 hover:rotate-0 hover:z-40 hover:scale-105 transition-all duration-300 z-20 group bg-slate-100">
                                @if($room->image)
                                    <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs text-slate-500 font-bold p-4 text-center">Kamar {{ $room->name }}</div>
                                @endif
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-900/80 to-transparent p-4">
                                    <p class="text-white font-bold text-sm">{{ $room->name }}</p>
                                </div>
                            </div>
                        @elseif($index == 2)
                            <div class="absolute bottom-0 right-16 w-60 h-48 rounded-3xl overflow-hidden shadow-2xl shadow-emerald-200 border-[6px] border-white transform rotate-2 hover:rotate-0 hover:z-40 hover:scale-105 transition-all duration-300 z-30 group bg-slate-100">
                                @if($room->image)
                                    <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs text-slate-500 font-bold p-4 text-center">Kamar {{ $room->name }}</div>
                                @endif
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-900/80 to-transparent p-4">
                                    <p class="text-white font-bold text-sm">{{ $room->name }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section id="about" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-100 to-blue-50 rounded-3xl transform rotate-3 scale-105"></div>
                @if(isset($rooms) && $rooms->count() > 0 && $rooms->first()->image)
                    <img src="{{ asset('images/rooms/'.$rooms->first()->image) }}" alt="Tentang Kami" class="relative rounded-3xl shadow-2xl object-cover h-[400px] w-full">
                @else
                    <div class="relative rounded-3xl shadow-2xl h-[400px] w-full bg-white flex flex-col items-center justify-center border-4 border-slate-50/50">
                        <div class="w-20 h-20 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-400 mb-4 shadow-inner">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="text-slate-400 font-bold text-sm">Fasilitas Terbaik</span>
                    </div>
                @endif
            </div>
            <div>
                <span class="text-indigo-600 font-black tracking-widest uppercase text-xs mb-3 block">Tentang Kami</span>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-6 leading-tight">
                    {{ $settings['about_title'] ?? 'Solusi Hunian Nyaman Untuk Anda' }}
                </h2>
                <p class="text-slate-600 text-lg leading-relaxed mb-8">
                    {{ $settings['about_desc'] ?? 'Kami berdedikasi menyediakan pilihan kamar kontrakan terbaik yang telah melalui proses verifikasi ketat. Memastikan setiap penghuni mendapatkan tempat yang aman, bersih, dan berlokasi strategis untuk menunjang aktivitas sehari-hari.' }}
                </p>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold text-slate-700">Terpercaya</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-bold text-slate-700">Harga Jujur</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-indigo-600 font-black tracking-widest uppercase text-xs mb-3 block">Kenapa Memilih Kami?</span>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900">Keunggulan Layanan Kami</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-300 border border-slate-100">
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-black text-xl mb-3 text-slate-900">{{ $settings['feature_1_title'] ?? 'Kamar Terverifikasi' }}</h3>
                    <p class="text-slate-500 leading-relaxed">{{ $settings['feature_1_desc'] ?? 'Semua unit telah diperiksa secara langsung oleh tim kami untuk menjamin kebersihan dan fasilitas yang sesuai.' }}</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-100 transition-all duration-300 border border-slate-100">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="font-black text-xl mb-3 text-slate-900">{{ $settings['feature_2_title'] ?? 'Booking Super Cepat' }}</h3>
                    <p class="text-slate-500 leading-relaxed">{{ $settings['feature_2_desc'] ?? 'Pesan kamar idaman Anda secara online dalam hitungan menit. Pembayaran terintegrasi langsung.' }}</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-100 hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-100 transition-all duration-300 border border-slate-100">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-black text-xl mb-3 text-slate-900">{{ $settings['feature_3_title'] ?? 'Harga Transparan' }}</h3>
                    <p class="text-slate-500 leading-relaxed">{{ $settings['feature_3_desc'] ?? 'Bayar sesuai dengan yang tertera. Kami tidak mengenakan biaya tambahan tersembunyi yang merugikan Anda.' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Available Rooms Preview -->
    <section id="rooms" class="py-24 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <span class="text-indigo-600 font-black tracking-widest uppercase text-xs mb-3 block">Katalog Kamar</span>
                    <h2 class="text-3xl md:text-4xl font-black text-slate-900">Kamar Tersedia Saat Ini</h2>
                </div>
                <a href="{{ route('register') }}" class="mt-4 md:mt-0 font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-2">
                    Lihat Semua Kamar <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($rooms as $room)
                    <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-xl shadow-slate-100/50 hover:shadow-2xl hover:shadow-slate-200 transition-all duration-300 group">
                        <div class="aspect-[4/3] bg-slate-200 relative overflow-hidden">
                            @if($room->image)
                                <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="flex items-center justify-center h-full text-slate-400 font-medium">Foto Tidak Tersedia</div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur text-slate-900 text-xs font-black px-3 py-1.5 rounded-xl shadow-sm">
                                    {{ $room->type ?? 'Kamar' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-black text-slate-900 mb-2">{{ $room->name }}</h3>
                            <p class="text-2xl font-black text-indigo-600 mb-6">Rp {{ number_format($room->price) }}<span class="text-sm text-slate-500 font-medium">/bln</span></p>
                            <a href="{{ route('login') }}" class="block w-full text-center bg-slate-50 text-slate-800 font-bold py-3 rounded-xl hover:bg-slate-900 hover:text-white transition-colors">Lihat Detail</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                        <p class="text-slate-500 font-bold">Maaf, saat ini belum ada kamar yang tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Location & Contact -->
    <section id="contact" class="py-24 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-indigo-400 font-black tracking-widest uppercase text-xs mb-3 block">Lokasi & Kontak</span>
                <h2 class="text-3xl md:text-4xl font-black mb-6">Tertarik Menyewa? <br/> Hubungi Kami Sekarang.</h2>
                <p class="text-slate-300 text-lg mb-8 leading-relaxed">
                    Jangan ragu untuk datang langsung ke lokasi atau menghubungi kami melalui WhatsApp untuk bertanya seputar fasilitas dan ketersediaan kamar.
                </p>
                
                <div class="space-y-6 mb-10">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Alamat</h4>
                            <p class="text-slate-400">{{ $settings['address'] ?? 'Jl. Contoh Alamat No. 123, Kota Anda' }}</p>
                        </div>
                    </div>
                </div>

                @if(!empty($settings['whatsapp_number']))
                    <a href="https://wa.me/{{ $settings['whatsapp_number'] }}" target="_blank" class="inline-flex items-center gap-3 px-8 py-4 bg-emerald-500 text-white rounded-2xl font-bold shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 transition-all hover:-translate-y-1">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                @endif
            </div>

            <div class="h-[400px] w-full bg-white/5 rounded-3xl overflow-hidden shadow-2xl border border-white/10 p-2">
                @if(!empty($settings['google_maps_iframe']))
                    <div class="w-full h-full rounded-2xl overflow-hidden [&>iframe]:w-full [&>iframe]:h-full">
                        {!! $settings['google_maps_iframe'] !!}
                    </div>
                @else
                    <div class="w-full h-full bg-slate-800 rounded-2xl flex items-center justify-center text-slate-500 font-bold">
                        Peta belum dikonfigurasi
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 py-12 text-center border-t border-white/10">
        <div class="flex items-center justify-center gap-2 mb-6">
            <div class="w-8 h-8 bg-indigo-600 rounded flex items-center justify-center text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </div>
            <span class="text-xl font-black tracking-tight text-white">KOSKU</span>
        </div>
        <p class="text-slate-500 text-sm font-bold uppercase tracking-widest">
            © {{ date('Y') }} KosKu Platform. Hak Cipta Dilindungi.
        </p>
    </footer>
</body>

</html>
