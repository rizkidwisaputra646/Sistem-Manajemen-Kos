<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800">Edit Data Kamar</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-gray-100 rounded-xl p-8 shadow-sm">
                
                <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') 
                    
                    <div class="space-y-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Nama Kamar</label>
                            <input type="text" name="name" value="{{ $room->name }}" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Harga per Bulan (Rp)</label>
                                <input type="number" name="price" value="{{ $room->price }}" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Status Unit</label>
                                <select name="status" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium bg-white">
                                    <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                                    <option value="full" {{ $room->status == 'full' ? 'selected' : '' }}>Sudah Terisi (Full)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Fasilitas (Pisahkan dengan koma)</label>
                            <input type="text" name="facilities" value="{{ $room->facilities }}" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium" placeholder="Contoh: AC, WiFi, KM Dalam">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Deskripsi Kamar</label>
                            <textarea name="description" rows="5" class="w-full border-2 border-gray-100 rounded-lg px-4 py-3 focus:border-blue-500 outline-none font-medium">{{ $room->description }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Foto Unit Kamar</label>
                            
                            @if($room->image)
                                <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-100 inline-block">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Foto Saat Ini</p>
                                    <img src="{{ asset('images/rooms/' . $room->image) }}" class="h-40 rounded-lg border-2 border-white shadow-sm">
                                </div>
                            @endif

                            <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-bold text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Ganti Foto Baru</span>
                                            <input id="file-upload" name="image" type="file" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-400 font-medium">Kosongkan jika tidak ingin mengganti</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-8 border-t border-gray-50">
                            <a href="{{ route('admin.rooms.index') }}" class="px-8 py-3 bg-white border-2 border-gray-100 rounded-lg font-bold text-gray-500 hover:bg-gray-50 transition-all text-sm uppercase tracking-widest">Batal</a>
                            <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all text-sm uppercase tracking-widest">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>