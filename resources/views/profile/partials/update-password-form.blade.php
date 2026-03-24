<section>
    <header class="mb-8">
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight italic">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 font-medium">
            {{ __('Pastikan akun kamu menggunakan password yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="space-y-2">
            <label for="update_password_current_password" class="block text-slate-700 font-bold text-sm tracking-tight italic">{{ __('Password Saat Ini') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" 
                class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner" 
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-500 text-xs font-bold uppercase italic" />
        </div>

        <div class="space-y-2">
            <label for="update_password_password" class="block text-slate-700 font-bold text-sm tracking-tight italic">{{ __('Password Baru') }}</label>
            <input id="update_password_password" name="password" type="password" 
                class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner" 
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-500 text-xs font-bold uppercase italic" />
        </div>

        <div class="space-y-2">
            <label for="update_password_password_confirmation" class="block text-slate-700 font-bold text-sm tracking-tight italic">{{ __('Konfirmasi Password Baru') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900 shadow-inner" 
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-500 text-xs font-bold uppercase italic" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs font-bold text-green-600 uppercase tracking-widest italic"
                >{{ __('Berhasil disimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
