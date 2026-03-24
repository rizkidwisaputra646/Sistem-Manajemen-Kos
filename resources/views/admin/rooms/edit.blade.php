<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kamar: ' . $room->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Nama Kamar</label>
                            <input type="text" name="name" value="{{ $room->name }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Harga per Bulan (Rp)</label>
                                <input type="number" name="price" value="{{ $room->price }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Status Kamar</label>
                                <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                                    <option value="full" {{ $room->status == 'full' ? 'selected' : '' }}>Penuh (Full)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Fasilitas (Pisahkan dengan koma)</label>
                            <input type="text" name="facilities" value="{{ $room->facilities }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="AC, WiFi, Kamar Mandi Dalam">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Deskripsi Kamar</label>
                            <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $room->description }}</textarea>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Foto Kamar</label>
                            
                            @if($room->image)
                                <div class="mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Foto saat ini:</p>
                                    <img src="{{ asset('images/rooms/' . $room->image) }}" class="h-32 rounded-lg border">
                                </div>
                            @endif

                            <input type="file" name="image" class="w-full text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-400 mt-1 italic">Kosongkan jika tidak ingin mengganti foto.</p>
                        </div>

                        <div class="flex justify-end gap-3 mt-4">
                            <a href="{{ route('admin.rooms.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Batal</a>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 shadow-lg transition font-bold">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>