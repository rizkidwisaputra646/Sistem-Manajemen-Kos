<x-guest-layout>
    <div class="text-center mb-10">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight mb-2">Buat Akun Baru</h1>
        <p class="text-gray-500 text-sm font-medium">Lengkapi data di bawah untuk mendaftar KosKu.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="space-y-1">
            <label for="name" class="block text-gray-700 font-bold text-xs uppercase tracking-widest">{{ __('Nama Lengkap') }}</label>
            <input id="name" class="w-full border-gray-300 bg-white rounded px-4 py-3 focus:border-blue-500 outline-none font-medium text-gray-900 placeholder:text-gray-300" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukan nama lengkap" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-500 text-[10px] font-bold uppercase" />
        </div>

        <!-- Email Address -->
        <div class="space-y-1">
            <label for="email" class="block text-gray-700 font-bold text-xs uppercase tracking-widest">{{ __('Email') }}</label>
            <input id="email" class="w-full border-gray-300 bg-white rounded px-4 py-3 focus:border-blue-500 outline-none font-medium text-gray-900 placeholder:text-gray-300" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-[10px] font-bold uppercase" />
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <label for="password" class="block text-gray-700 font-bold text-xs uppercase tracking-widest">{{ __('Password') }}</label>
            <div class="relative" x-data="{ show: false }">
                <input id="password" class="w-full border-gray-300 bg-white rounded px-4 py-3 pr-12 focus:border-blue-500 outline-none font-medium text-gray-900 placeholder:text-gray-300"
                                :type="show ? 'text' : 'password'"
                                name="password"
                                required autocomplete="new-password" placeholder="••••••••" />
                
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none transition-colors">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-[10px] font-bold uppercase" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-1">
            <label for="password_confirmation" class="block text-gray-700 font-bold text-xs uppercase tracking-widest">{{ __('Ulangi Password') }}</label>
            <div class="relative" x-data="{ show: false }">
                <input id="password_confirmation" class="w-full border-gray-300 bg-white rounded px-4 py-3 pr-12 focus:border-blue-500 outline-none font-medium text-gray-900 placeholder:text-gray-300"
                                :type="show ? 'text' : 'password'"
                                name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                                
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none transition-colors">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-500 text-[10px] font-bold uppercase" />
        </div>

        <div class="pt-4">
             <button class="w-full bg-blue-600 text-white py-4 rounded font-bold text-sm hover:bg-blue-700 transition-colors uppercase tracking-widest">
                {{ __('Daftar Sekarang') }}
            </button>
            
            <p class="text-center mt-8 text-xs font-bold text-gray-400 uppercase tracking-widest">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Masuk di sini</a>
            </p>
        </div>
    </form>
</x-guest-layout>
