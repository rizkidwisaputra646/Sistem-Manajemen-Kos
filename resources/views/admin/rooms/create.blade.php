<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800">Tambah Kamar Baru</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-gray-100 rounded-xl p-8 shadow-sm">
                <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Nama Kamar</label>
                            <input type="text" name="name" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium" placeholder="Contoh: Kamar Melati 01" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Harga per Bulan (Rp)</label>
                                <input type="number" name="price" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium" placeholder="1500000" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Fasilitas Utama</label>
                                <input type="text" name="facilities" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium" placeholder="AC, WiFi, KM Dalam">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Deskripsi Lengkap</label>
                            <textarea name="description" rows="5" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium" placeholder="Jelaskan detail dan kelebihan kamar ini..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Unggah Foto Kamar</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-bold text-blue-600 hover:text-blue-500 focus-within:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <span>Pilih File</span>
                                            <input id="file-upload" name="image" type="file" class="sr-only">
                                        </label>
                                        <p class="pl-1">atau tarik ke sini</p>
                                    </div>
                                    <p class="text-xs text-gray-400">PNG, JPG, JPEG (Maks 2MB)</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-50">
                            <a href="{{ route('admin.rooms.index') }}" class="px-8 py-3 bg-white border-2 border-gray-100 rounded-lg font-bold text-gray-500 hover:bg-gray-50 transition-all text-sm uppercase tracking-widest">Batal</a>
                            <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all text-sm uppercase tracking-widest">Simpan Unit Kamar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>