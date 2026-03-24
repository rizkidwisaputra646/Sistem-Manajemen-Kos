<section>
    <header class="mb-8">
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight italic">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 font-medium">
            {{ __("Perbarui nama dan alamat email akun kamu di sini.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-2">
            <label for="name" class="block text-slate-700 font-bold text-sm tracking-tight italic">{{ __('Nama Lengkap') }}</label>
            <input id="name" name="name" type="text" 
                class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900" 
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-rose-500 text-xs font-bold uppercase italic" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
             <label for="email" class="block text-slate-700 font-bold text-sm tracking-tight italic">{{ __('Email') }}</label>
             <input id="email" name="email" type="email" 
                class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium text-slate-900" 
                value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2 text-rose-500 text-xs font-bold uppercase italic" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-tighter italic">
                        {{ __('Email kamu belum diverifikasi.') }}

                        <button form="send-verification" class="ml-2 underline text-amber-600 hover:text-amber-800 transition-colors">
                            {{ __('Klik untuk kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-[10px] text-green-600 uppercase italic">
                            {{ __('Link verifikasi baru telah dikirim ke email kamu.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
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
