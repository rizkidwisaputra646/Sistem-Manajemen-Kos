<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Cari Kamar Kontrakan</h1>
                <p class="text-gray-600 text-sm">Pilih kamar yang sesuai dengan keinginan Anda.</p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative w-full md:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kamar, fasilitas..." 
                        class="w-full bg-white border border-gray-300 rounded-lg pl-9 pr-4 py-2 text-sm focus:border-blue-500 outline-none">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors whitespace-nowrap">
                    Cari
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('dashboard') }}" class="text-xs font-bold text-gray-500 hover:text-blue-600 underline whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($rooms as $room)
                <div class="bg-white border border-gray-300 rounded overflow-hidden flex flex-col">
                    <div class="aspect-video bg-gray-200">
                        @if($room->image)
                            <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400 text-xs">Foto tidak tersedia</div>
                        @endif
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-2">
                            @if(!$room->is_currently_booked)
                                <span class="text-[10px] font-bold text-emerald-600 uppercase border border-emerald-100 bg-emerald-50 px-1.5 py-0.5 rounded">Tersedia</span>
                            @else
                                <span class="text-[10px] font-bold text-rose-600 uppercase border border-rose-100 bg-rose-50 px-1.5 py-0.5 rounded">Sold Out</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $room->name }}</h3>
                        <p class="text-xl font-bold text-gray-900 mb-4">Rp {{ number_format($room->price) }}<span class="text-xs text-gray-500 font-normal">/bln</span></p>

                        <div class="mt-auto flex gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('rooms.show', $room->id) }}" class="flex-1 text-center bg-gray-100 text-gray-700 py-2 rounded text-xs font-bold border border-gray-200 hover:bg-gray-200 transition-colors">
                                Detail
                            </a>
                            @if(!$room->is_currently_booked)
                                <a href="{{ route('rooms.checkout', $room->id) }}" class="flex-1 text-center bg-blue-600 text-white py-2 rounded text-xs font-bold hover:bg-blue-700 shadow-sm shadow-blue-200 transition-all">
                                    Pesan
                                </a>
                            @else
                                <button disabled class="flex-1 text-center bg-gray-50 text-gray-400 py-2 rounded text-xs font-bold cursor-not-allowed border border-gray-100">
                                    Penuh
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white border border-gray-200 rounded-xl">
                    <p class="text-gray-500 font-bold">
                        @if(request()->filled('search'))
                            Tidak ditemukan kamar dengan kata kunci "{{ request('search') }}".
                        @else
                            Maaf, saat ini belum ada kamar yang tersedia.
                        @endif
                    </p>
                    @if(request()->filled('search'))
                        <a href="{{ route('dashboard') }}" class="text-blue-600 text-xs font-bold mt-2 inline-block hover:underline">
                            Tampilkan Semua Kamar
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>