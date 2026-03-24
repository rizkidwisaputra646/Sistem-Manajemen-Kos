<x-guest-layout>
    <div class="text-center mb-10">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2 italic">Selamat Datang 👋</h1>
        <p class="text-slate-500 text-sm font-medium">Silakan masuk ke akun KosKu kamu.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-2xl text-indigo-700 text-xs font-bold italic" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-slate-700 font-bold text-xs uppercase tracking-widest italic">{{ __('Email') }}</label>
            <input id="email" class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner placeholder:text-slate-300" 
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 text-[10px] font-bold uppercase italic" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <label for="password" class="block text-slate-700 font-bold text-xs uppercase tracking-widest italic">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-bold text-indigo-500 hover:text-indigo-700 transition-colors uppercase tracking-widest italic underline decoration-indigo-100" href="{{ route('password.request') }}">
                        {{ __('Lupa Password?') }}
                    </a>
                @endif
            </div>

            <input id="password" class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner placeholder:text-slate-300"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500 text-[10px] font-bold uppercase italic" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded-lg border-slate-200 text-indigo-600 shadow-sm focus:ring-indigo-500/20 bg-slate-50 w-5 h-5 transition-all" name="remember">
                <span class="ms-3 text-xs font-bold text-slate-400 group-hover:text-slate-600 transition-colors uppercase tracking-tighter italic">{{ __('Ingat saya di perangkat ini') }}</span>
            </label>
        </div>

        <div class="pt-4">
            <button class="w-full bg-indigo-600 text-white py-5 rounded-[1.5rem] font-black text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-100 hover:shadow-indigo-200 transition-all active:scale-[0.98] uppercase tracking-tighter">
                {{ __('Masuk Sekarang') }}
            </button>
            
            <p class="text-center mt-8 text-xs font-bold text-slate-400 uppercase tracking-widest italic">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-indigo-500 hover:text-indigo-700 transition-colors underline decoration-indigo-200">Daftar Gratis</a>
            </p>
        </div>
    </form>
</x-guest-layout>
