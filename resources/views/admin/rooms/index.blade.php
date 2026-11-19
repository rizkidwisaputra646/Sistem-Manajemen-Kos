<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
    <h2 class="font-bold text-2xl text-gray-800">
        Manajemen Kamar
    </h2>

    <a href="{{ route('admin.rooms.create') }}"
       class="w-full sm:w-auto text-center bg-blue-600 text-white px-6 py-3 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-blue-700 shadow-md">
        + Tambah Kamar Baru
    </a>
</div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl border-2 border-gray-100 shadow-sm mb-8 flex flex-wrap items-center justify-between gap-4">
                <form action="{{ route('admin.rooms.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama kamar atau deskripsi..."
                        class="border-gray-300 rounded-lg text-sm px-4 py-2 w-full md:w-72 focus:border-blue-500 outline-none">
                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-blue-700 transition">
                        Cari
                    </button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.rooms.index') }}"
                            class="text-xs font-bold text-gray-400 hover:text-blue-600 underline">Reset</a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm overflow-x-auto">
                <table class="w-full text-left min-w-max">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">
                            <th class="px-8 py-5">Unit Kamar</th>
                            <th class="px-8 py-5">Biaya / Bln</th>
                            <th class="px-8 py-5">Status Unit</th>
                            <th class="px-8 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($rooms as $room)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-14 h-14 object-cover rounded-lg border-2 border-gray-100 shadow-sm">
                                    <span class="font-bold text-gray-800">{{ $room->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 font-bold text-blue-600">
                                Rp {{ number_format($room->price) }}
                            </td>
                            <td class="px-8 py-6">
                                <form action="{{ route('admin.rooms.toggle-status', $room) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="flex flex-col gap-1 items-start group">
                                        <span class="px-3 py-1 rounded-md text-[10px] font-bold uppercase border transition-all {{ $room->status == 'available' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                            {{ $room->status }}
                                        </span>
                                        <span class="text-[9px] text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity font-bold">Ganti Status</span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="p-2.5 bg-white border-2 border-gray-100 text-amber-600 rounded-lg hover:bg-amber-50 transition-colors">✏️</a>
                                    
                                    <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Hapus kamar ini permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 bg-white border-2 border-gray-100 text-red-600 rounded-lg hover:bg-red-50 transition-colors">🗑️</button>
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