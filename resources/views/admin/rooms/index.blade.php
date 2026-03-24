<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 italic underline decoration-indigo-500">Kelola Kamar 🏠</h2>
            <a href="{{ route('admin.rooms.create') }}" class="bg-indigo-600 text-white px-6 py-2.5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                + Tambah Kamar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <th class="px-8 py-5">Kamar</th>
                            <th class="px-8 py-5">Harga/Hari</th>
                            <th class="px-8 py-5">Status</th>
                            <th class="px-8 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($rooms as $room)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-12 h-12 object-cover rounded-xl shadow-sm">
                                    <span class="font-bold text-gray-800 italic">{{ $room->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 font-black text-indigo-600 italic">
                                Rp {{ number_format($room->price) }}
                            </td>
                            <td class="px-8 py-5">
                                <form action="{{ route('admin.rooms.toggle-status', $room) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="flex items-center gap-2 group">
                                        <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-tighter transition-all {{ $room->status == 'available' ? 'bg-green-100 text-green-600 group-hover:bg-green-200' : 'bg-rose-100 text-rose-600 group-hover:bg-rose-200' }}">
                                            {{ $room->status }}
                                        </span>
                                        <span class="text-[10px] text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity italic">Klik untuk ubah</span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition-colors text-sm">✏️</a>
                                    
                                    <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Hapus kamar ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition-colors text-sm">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>