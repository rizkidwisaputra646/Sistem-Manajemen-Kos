<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-indigo-100 selection:text-indigo-700">
        <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full -z-10 opacity-30">
                <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-200 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[10%] right-[-5%] w-[30%] h-[30%] bg-blue-100 rounded-full blur-[100px]"></div>
            </div>

            <header class="fixed top-0 w-full z-50 px-6 py-4">
                <nav class="max-w-7xl mx-auto flex items-center justify-between bg-white/70 backdrop-blur-xl border border-slate-200/50 px-6 py-3 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-200">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="text-lg font-bold tracking-tight text-slate-900 uppercase">KOS<span class="text-indigo-600">KU</span></span>
                    </div>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Log in</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 transition-all active:scale-95">
                                        Get Started
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </nav>
            </header>

            <main class="relative z-10 w-full max-w-5xl px-6 pt-32 pb-20 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 border border-indigo-100 rounded-full text-indigo-700 text-xs font-bold mb-8 animate-fade-in">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Solusi Hunian Modern & Nyaman
                </div>

                <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 leading-[1.1] tracking-tight mb-8">
                    Cari Kontrakan <br> 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Tanpa Ribet.</span>
                </h1>

                <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto mb-12 leading-relaxed">
                    Platform reservasi hunian online terbaik di Indonesia. Booking kamar impianmu hanya dalam hitungan detik dengan sistem pembayaran yang aman.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 hover:-translate-y-1 transition-all active:scale-95 text-lg">
                        Mulai Sekarang
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-white text-slate-700 border border-slate-200 font-bold rounded-2xl hover:bg-slate-50 hover:border-slate-300 transition-all text-lg">
                        Lihat Kamar
                    </a>
                </div>

                <!-- App Preview Mask -->
                <div class="relative max-w-4xl mx-auto rounded-3xl overflow-hidden shadow-2xl border-8 border-white/50 bg-white">
                    <div class="aspect-video bg-slate-100 flex items-center justify-center">
                         <!-- Redesigned Booking Preview Card -->
                        <div class="max-w-sm w-full bg-white rounded-3xl p-6 shadow-xl border border-slate-100 text-left">
                            <div class="w-full h-48 bg-slate-200 rounded-2xl mb-4 overflow-hidden relative">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                <div class="absolute bottom-3 left-3 px-2 py-1 bg-white/90 backdrop-blur rounded-lg text-[10px] font-bold text-slate-900 uppercase">Kamar Suite</div>
                            </div>
                            <h3 class="font-bold text-lg text-slate-900">Kontrakan Modern Minimalis</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex text-amber-400">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <svg class="w-4 h-4 fill-current text-slate-300" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">(120 Reviews)</span>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <p class="text-xl font-black text-indigo-600">Rp 1.500.000 <span class="text-xs text-slate-400 font-medium italic">/ bln</span></p>
                                <button class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">Booking</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="w-full py-10 px-6 border-t border-slate-200 mt-20">
                <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-2 grayscale brightness-50">
                        <span class="text-sm font-bold tracking-tight text-slate-900 uppercase">KOS<span class="text-indigo-600">KU</span></span>
                    </div>
                    <p class="text-slate-400 text-sm">© {{ date('Y') }} KosKu Platform. All rights reserved.</p>
                    <div class="flex items-center gap-6">
                        <a href="#" class="text-slate-400 hover:text-indigo-600 text-sm transition-colors">Privacy Policy</a>
                        <a href="#" class="text-slate-400 hover:text-indigo-600 text-sm transition-colors">Terms of Service</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
