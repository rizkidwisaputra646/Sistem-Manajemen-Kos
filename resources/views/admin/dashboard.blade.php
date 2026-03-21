<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight bg-red-600 p-4 rounded-lg">
            {{ __('ADMIN PANEL - Manajemen Kos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-500">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Halo, Admin {{ Auth::user()->name }}!</h3>
                    <p class="mb-6 text-gray-600">Ini adalah halaman khusus pemilik kos. Di sini kamu bisa mengelola kamar dan melihat laporan.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                            <p class="text-sm text-blue-600 uppercase font-bold">Total Kamar</p>
                            <p class="text-3xl font-black">12</p>
                        </div>
                        <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded">
                            <p class="text-sm text-green-600 uppercase font-bold">Kamar Tersedia</p>
                            <p class="text-3xl font-black">5</p>
                        </div>
                        <div class="p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                            <p class="text-sm text-yellow-600 uppercase font-bold">Pending Reservasi</p>
                            <p class="text-3xl font-black">3</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                            + Tambah Kamar Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>