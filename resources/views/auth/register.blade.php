<x-guest-layout>
    <div class="text-center mb-10 text-left">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2 italic">Buat Akun 🚀</h1>
        <p class="text-slate-500 text-sm font-medium">Gabung dengan ribuan penghuni lainnya.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="space-y-2 text-left">
            <label for="name" class="block text-slate-700 font-bold text-xs uppercase tracking-widest italic">{{ __('Nama Lengkap') }}</label>
            <input id="name" class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner placeholder:text-slate-300" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Budi Santoso" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-rose-500 text-[10px] font-bold uppercase italic" />
        </div>

        <!-- Email Address -->
        <div class="space-y-2 text-left">
            <label for="email" class="block text-slate-700 font-bold text-xs uppercase tracking-widest italic">{{ __('Email') }}</label>
            <input id="email" class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner placeholder:text-slate-300" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="budi@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 text-[10px] font-bold uppercase italic" />
        </div>

        <!-- Password -->
        <div class="space-y-2 text-left">
            <label for="password" class="block text-slate-700 font-bold text-xs uppercase tracking-widest italic">{{ __('Password') }}</label>
            <input id="password" class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner placeholder:text-slate-300"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500 text-[10px] font-bold uppercase italic" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2 text-left">
            <label for="password_confirmation" class="block text-slate-700 font-bold text-xs uppercase tracking-widest italic">{{ __('Konfirmasi Password') }}</label>
            <input id="password_confirmation" class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner placeholder:text-slate-300"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-rose-500 text-[10px] font-bold uppercase italic" />
        </div>

        <div class="pt-4">
             <button class="w-full bg-indigo-600 text-white py-5 rounded-[1.5rem] font-black text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-100 hover:shadow-indigo-200 transition-all active:scale-[0.98] uppercase tracking-tighter">
                {{ __('Daftar Sekarang') }}
            </button>
            
            <p class="text-center mt-8 text-xs font-bold text-slate-400 uppercase tracking-widest italic">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-indigo-500 hover:text-indigo-700 transition-colors underline decoration-indigo-200">Masuk di sini</a>
            </p>
        </div>
    </form>
</x-guest-layout>
