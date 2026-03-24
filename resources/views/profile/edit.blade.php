<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col text-left">
            <h2 class="font-extrabold text-3xl text-slate-900 leading-tight tracking-tight">
                {{ __('Pengaturan Akun') }}
            </h2>
            <p class="text-slate-500 text-sm mt-1 font-medium italic">Kelola informasi profil, keamanan, dan preferensi akun kamu.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="p-8 bg-white shadow-sm rounded-[2.5rem] border border-slate-200">
                <div class="max-w-xl text-left">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-8 bg-white shadow-sm rounded-[2.5rem] border border-slate-200">
                <div class="max-w-xl text-left">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-8 bg-rose-50/30 shadow-sm rounded-[2.5rem] border border-rose-100 italic">
                <div class="max-w-xl text-left">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
