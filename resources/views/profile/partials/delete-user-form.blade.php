<section class="space-y-6">
    <header class="mb-4">
        <h2 class="text-xl font-extrabold text-rose-600 tracking-tight italic">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 font-medium">
            {{ __('Setelah akun kamu dihapus, semua sumber daya dan data akan dihapus secara permanen. Sebelum menghapus akun, mohon download data atau informasi apa pun yang ingin kamu simpan.') }}
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-3 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl font-bold text-sm hover:bg-rose-600 hover:text-white transition-all active:scale-95 uppercase tracking-widest"
    >{{ __('Hapus Akun Permanen') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 text-left bg-white rounded-3xl">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-black text-slate-900 leading-tight tracking-tight mb-4">
                {{ __('Apakah kamu yakin ingin menghapus akun?') }}
            </h2>

            <p class="text-sm text-slate-500 font-medium leading-relaxed italic">
                {{ __('Setelah akun dihapus, semua data akan hilang selamanya. Silakan masukkan password kamu untuk mengonfirmasi bahwa kamu benar-benar ingin menghapus akun secara permanen.') }}
            </p>

            <div class="mt-8 space-y-2">
                <label for="password" class="block text-slate-700 font-bold text-sm tracking-tight italic sr-only">{{ __('Password') }}</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full border-slate-200 bg-slate-50 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all font-medium text-slate-900 shadow-inner"
                    placeholder="{{ __('Password Akun Kamu') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-rose-500 text-xs font-bold uppercase italic" />
            </div>

            <div class="mt-10 flex flex-col sm:flex-row justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-slate-50 text-slate-400 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-100 transition-all uppercase tracking-widest">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="px-6 py-3 bg-rose-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-rose-100 hover:bg-rose-700 transition-all active:scale-95 uppercase tracking-widest">
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
