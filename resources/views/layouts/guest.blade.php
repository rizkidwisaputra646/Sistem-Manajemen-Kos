<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- System Fonts for a Normal Look -->
        <style>
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; 
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-800">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-16 sm:pt-0 relative">
            
            <!-- Tombol Kembali -->
            <a href="/" class="absolute top-6 left-6 flex items-center justify-center w-10 h-10 bg-white border border-gray-200 rounded-full shadow-sm text-gray-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>

            <!-- Plain Header -->
            <div class="mb-6">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-black text-gray-900">KOSKU</span>
                </a>
            </div>

            <!-- Plain Card -->
            <div class="w-full sm:max-w-md px-6 py-8 sm:px-10 sm:py-10 bg-white border-y sm:border-x border-gray-300 sm:rounded-lg shadow-sm">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center">
                 <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-relaxed">
                    © {{ date('Y') }} KosKu Platform. <br>
                    Tampilan Sederhana, Penggunaan Mudah.
                </p>
            </div>
        </div>
    </body>
</html>
