<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Pesanan</h1>
            <a href="{{ route('user.bookings.history') }}" class="text-xs font-bold text-gray-400 hover:text-blue-600">
                Histori Sewa →
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- Search Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <form action="{{ route('user.bookings.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kamar atau ID..." 
                        class="text-xs border-slate-200 rounded-xl px-3.5 py-2 w-full sm:w-64 focus:border-blue-500 focus:ring-blue-500 outline-none">
                    <button type="submit" class="bg-blue-600 text-white text-xs px-4 py-2 rounded-xl font-bold hover:bg-blue-700 transition">
                        Cari
                    </button>
                    @if(request()->filled('search'))
                        <a href="{{ route('user.bookings.index') }}" class="text-xs text-slate-400 hover:text-blue-600 underline">Reset</a>
                    @endif
                </form>
                <div class="text-xs font-semibold text-slate-500">
                    {{ $bookings->count() }} Pesanan Aktif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse($bookings as $booking)
                    @php
                        $today = \Carbon\Carbon::today();
                        $checkOutDate = \Carbon\Carbon::parse($booking->check_out_date);
                        $daysRemaining = $today->diffInDays($checkOutDate, false);

                        $isExpired = $daysRemaining < 0;
                        $isNear = $daysRemaining >= 0 && $daysRemaining <= 3;
                        $isPaid = $booking->status == 'success';
                    @endphp

                    <!-- Card Grid: Kompak & Berjajar -->
                    <div
                        class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition-shadow">

                        <div>
                            <!-- Header: Nama Kamar & Status -->
                            <div class="flex justify-between items-start mb-3 gap-2">
                                <div class="min-w-0">
                                    <h3 class="text-lg font-bold text-slate-800 break-words">{{ $booking->room->name }}</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono">
                                        ID #{{ strtoupper(substr($booking->id, 0, 8)) }}
                                    </p>
                                </div>

                                @if (!$isPaid)
                                    <span
                                        class="px-2 py-1 rounded-lg bg-amber-50 text-[10px] font-bold text-amber-600 border border-amber-100">PENDING</span>
                                @elseif($isExpired)
                                    <span
                                        class="px-2 py-1 rounded-lg bg-rose-50 text-[10px] font-bold text-rose-600 border border-rose-100">EXPIRED</span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-lg bg-emerald-50 text-[10px] font-bold text-emerald-600 border border-emerald-100">ACTIVE</span>
                                @endif
                            </div>

                            <!-- Info Sewa -->
                            <div class="space-y-2 mb-6">
                                <div class="flex items-center gap-2 text-sm text-slate-500">
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M') }} -
                                        {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</span>
                                </div>

                                @if ($isPaid && !$isExpired)
                                    <p class="text-xs {{ $isNear ? 'text-orange-600 font-bold' : 'text-slate-400' }}">
                                        {{ $daysRemaining == 0 ? '⚠️ Hari ini terakhir' : '⏳ ' . $daysRemaining . ' Hari lagi' }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Footer: Harga & Aksi Responsif -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-slate-100">
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Total</p>
                                <p class="text-lg font-bold text-slate-900">Rp
                                    {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5">
                                @if ($isPaid)
                                    <a href="{{ route('user.bookings.show', $booking) }}"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-colors border border-transparent hover:border-slate-200"
                                        title="Detail Pesanan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('user.bookings.receipt', $booking) }}" target="_blank"
                                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition-colors border border-transparent hover:border-slate-200"
                                        title="Cetak & Unduh Kuitansi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('user.bookings.renew', $booking) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="px-3.5 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all">
                                            Perpanjang
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('user.bookings.show', $booking) }}"
                                        class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-black rounded-xl shadow-sm">
                                        Bayar Sekarang
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16">
                        <p class="text-slate-400 font-medium">Belum ada pesanan aktif</p>
                        <a href="{{ route('dashboard') }}"
                            class="text-blue-600 font-bold text-sm mt-2 inline-block hover:underline">Cari Kontrakan
                            Sekarang →</a>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
