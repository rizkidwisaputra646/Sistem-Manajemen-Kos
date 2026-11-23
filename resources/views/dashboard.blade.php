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
        {{-- Banner Notifikasi Status Sewa / Pending --}}
        @if(isset($userActiveRental) && $userActiveRental)
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Status Sewa Aktif</h4>
                        <p class="text-xs text-blue-700">
                            Anda sedang aktif menyewa <strong class="text-blue-950">{{ $userActiveRental->room->name }}</strong> hingga <strong>{{ \Carbon\Carbon::parse($userActiveRental->check_out_date)->format('d F Y') }}</strong>. 
                            <span class="text-blue-500">(Aturan: 1 akun maksimal menyewa 1 kamar).</span>
                        </p>
                    </div>
                </div>
                <a href="{{ route('user.bookings.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl whitespace-nowrap transition shadow-sm">
                    Kelola / Perpanjang Sewa →
                </a>
            </div>
        @elseif(isset($userPendingBooking) && $userPendingBooking)
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Menunggu Pembayaran</h4>
                        <p class="text-xs text-amber-700">
                            Anda memiliki pesanan kamar <strong class="text-amber-950">{{ $userPendingBooking->room->name }}</strong> yang belum diselesaikan. Harap selesaikan atau batalkan pesanan tersebut sebelum menyewa kamar lain.
                        </p>
                    </div>
                </div>
                <a href="{{ route('user.bookings.show', $userPendingBooking) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl whitespace-nowrap transition shadow-sm">
                    Selesaikan Pembayaran →
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($rooms as $room)
                @php
                    $isMyActiveRoom = isset($userActiveRental) && $userActiveRental && $userActiveRental->room_id === $room->id;
                    $isMyPendingRoom = isset($userPendingBooking) && $userPendingBooking && $userPendingBooking->room_id === $room->id;
                    $hasOtherActiveRental = isset($userActiveRental) && $userActiveRental && $userActiveRental->room_id !== $room->id;
                    $hasOtherPendingBooking = isset($userPendingBooking) && $userPendingBooking && $userPendingBooking->room_id !== $room->id;
                @endphp
                <div class="bg-white border {{ $isMyActiveRoom ? 'border-blue-300 ring-2 ring-blue-100' : 'border-gray-300' }} rounded-xl overflow-hidden flex flex-col shadow-sm">
                    <div class="aspect-video bg-gray-200 relative">
                        @if($room->image)
                            <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400 text-xs">Foto tidak tersedia</div>
                        @endif

                        @if($isMyActiveRoom)
                            <div class="absolute top-2 right-2 bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
                                Kamar Anda
                            </div>
                        @endif
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-2">
                            @if($isMyActiveRoom)
                                <span class="text-[10px] font-bold text-blue-700 uppercase border border-blue-200 bg-blue-50 px-2 py-0.5 rounded-full">Sewa Aktif Anda</span>
                            @elseif(!$room->is_currently_booked)
                                <span class="text-[10px] font-bold text-emerald-600 uppercase border border-emerald-100 bg-emerald-50 px-1.5 py-0.5 rounded">Tersedia</span>
                            @else
                                <span class="text-[10px] font-bold text-rose-600 uppercase border border-rose-100 bg-rose-50 px-1.5 py-0.5 rounded">Sold Out</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $room->name }}</h3>
                        <p class="text-xl font-bold text-gray-900 mb-4">Rp {{ number_format($room->price) }}<span class="text-xs text-gray-500 font-normal">/bln</span></p>

                        <div class="mt-auto flex gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('rooms.show', $room->id) }}" class="flex-1 text-center bg-gray-100 text-gray-700 py-2 rounded-lg text-xs font-bold border border-gray-200 hover:bg-gray-200 transition-colors">
                                Detail
                            </a>

                            @if($isMyActiveRoom)
                                <a href="{{ route('user.bookings.index') }}" class="flex-1 text-center bg-slate-900 text-white py-2 rounded-lg text-xs font-bold hover:bg-black transition-colors" title="Kamar yang sedang Anda sewa">
                                    Kamar Anda
                                </a>
                            @elseif($isMyPendingRoom)
                                <a href="{{ route('user.bookings.show', $userPendingBooking) }}" class="flex-1 text-center bg-amber-500 hover:bg-amber-600 text-white py-2 rounded-lg text-xs font-bold shadow-sm transition-all" title="Lanjutkan pembayaran pesanan">
                                    Bayar
                                </a>
                            @elseif($hasOtherActiveRental)
                                {{-- User sedang aktif menyewa kamar lain: tombol tidak boleh biru, dibuat disable/terkunci --}}
                                <button disabled class="flex-1 text-center bg-slate-100 text-slate-400 py-2 rounded-lg text-xs font-bold cursor-not-allowed border border-slate-200" title="Anda sedang aktif menyewa kamar lain (Maksimal 1 kamar per akun)">
                                    Terkunci
                                </button>
                            @elseif($hasOtherPendingBooking)
                                {{-- User memiliki pesanan pending di kamar lain --}}
                                <button disabled class="flex-1 text-center bg-amber-50 text-amber-500 py-2 rounded-lg text-xs font-bold cursor-not-allowed border border-amber-200" title="Selesaikan atau batalkan pesanan pending Anda terlebih dahulu">
                                    Ada Pending
                                </button>
                            @elseif(!$room->is_currently_booked)
                                <a href="{{ route('rooms.checkout', $room->id) }}" class="flex-1 text-center bg-blue-600 text-white py-2 rounded-lg text-xs font-bold hover:bg-blue-700 shadow-sm shadow-blue-200 transition-all">
                                    Pesan
                                </a>
                            @else
                                <button disabled class="flex-1 text-center bg-gray-50 text-gray-400 py-2 rounded-lg text-xs font-bold cursor-not-allowed border border-gray-100">
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