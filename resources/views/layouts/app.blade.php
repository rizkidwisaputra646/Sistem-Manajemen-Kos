<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.clientKey') }}"></script>


</head>

<body class="font-sans antialiased bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        @if (Auth::user()->role == 'admin')
            @include('layouts.sidebars.admin')
        @else
            @include('layouts.sidebars.user')
        @endif

        <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden">

            <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 border-b border-slate-200 h-16 flex items-center justify-between px-6 md:px-8 shadow-sm">
                
                <div class="flex items-center gap-4">
                    @if (Auth::user()->role == 'admin')
                        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 md:hidden p-2 hover:bg-slate-100 rounded-xl transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                            </svg>
                        </button>
                    @endif
                    
                    <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider hidden md:block">
                        {{ Request::segment(1) ? str_replace('-', ' ', Request::segment(1)) : 'Dashboard' }}
                    </h2>
                </div>

                <div class="flex items-center gap-4 ml-auto">
                    <div class="flex items-center gap-3 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-full transition-all hover:border-indigo-200">
                        <div class="w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center text-[10px] text-white font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-slate-700 pr-1">
                            {{ Auth::user()->name }}
                        </span>
                    </div>
                </div>
            </header>

            <main class="p-6 md:p-8 pb-32 md:pb-12">
                @isset($header)
                    <div class="mb-8">
                        {{ $header }}
                    </div>
                @endisset

                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @if (Auth::user()->role == 'user')
            @include('layouts.sidebars.user-bottom')
        @endif

        @if (Auth::user()->role == 'admin')
            <div x-show="sidebarOpen" @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm md:hidden transition-opacity"></div>
        @endif
    </div>
</body>

</html>
