<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full -z-10 opacity-20">
                <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-indigo-200 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-blue-100 rounded-full blur-[100px]"></div>
            </div>

            <div class="mb-8 relative">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tighter text-slate-900 uppercase">KOS<span class="text-indigo-600 font-black">KU</span></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-10 py-12 bg-white/80 backdrop-blur-xl shadow-2xl shadow-indigo-100 border border-white sm:rounded-[2.5rem] overflow-hidden">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center">
                 <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-relaxed italic">
                    © {{ date('Y') }} KosKu Platform. <br>
                    Hunian Modern, Bayar Instan.
                </p>
            </div>
        </div>
    </body>
</html>
