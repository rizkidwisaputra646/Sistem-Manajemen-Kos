<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cari Kos Impian Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-4 text-indigo-600">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-500 mb-6">Silakan pilih kamar kos yang tersedia di bawah ini.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border rounded-lg p-4 flex gap-4 hover:shadow-md transition">
                            <div class="w-32 h-32 bg-gray-200 rounded-md flex items-center justify-center text-gray-400">Foto Kamar</div>
                            <div>
                                <h4 class="font-bold text-lg">Kamar Melati #01</h4>
                                <p class="text-sm text-gray-600 italic">AC, Kamar Mandi Dalam, WiFi</p>
                                <p class="text-indigo-600 font-bold mt-2">Rp 1.500.000 / Bulan</p>
                                <button class="mt-2 bg-indigo-600 text-white px-3 py-1 rounded text-sm uppercase font-bold">Pesan Sekarang</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>