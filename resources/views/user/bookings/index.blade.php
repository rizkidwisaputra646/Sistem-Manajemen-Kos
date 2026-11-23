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

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-base leading-none">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-base leading-none">&times;</button>
                </div>
            @endif

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
                        $activeExt = $booking->activeExtension;
                        $latestExt = $booking->extensions->first();
                    @endphp

                    <!-- Card Grid: Kompak & Berjajar -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-col justify-between hover:shadow-md transition-shadow">

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
                                    <span class="px-2 py-1 rounded-lg bg-amber-50 text-[10px] font-bold text-amber-600 border border-amber-100">PENDING</span>
                                @elseif($isExpired)
                                    <span class="px-2 py-1 rounded-lg bg-rose-50 text-[10px] font-bold text-rose-600 border border-rose-100">EXPIRED</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-emerald-50 text-[10px] font-bold text-emerald-600 border border-emerald-100">ACTIVE</span>
                                @endif
                            </div>

                            <!-- Info Sewa -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center gap-2 text-sm text-slate-500">
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                            <!-- Section Status Perpanjangan Sewa -->
                            @if ($isPaid && $activeExt)
                                @if ($activeExt->status === 'pending')
                                    <div class="mb-4 p-3.5 bg-amber-50/80 border border-amber-200/90 rounded-xl space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-800">
                                                <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                Perpanjangan Menunggu Persetujuan
                                            </div>
                                            <form action="{{ route('user.bookings.extensions.cancel', $activeExt) }}" method="POST" class="inline" onsubmit="return confirmCancelExtension(event, this)">
                                                @csrf
                                                <button type="submit" class="text-[11px] font-semibold text-rose-600 hover:text-rose-800 hover:underline cursor-pointer">
                                                    Batalkan
                                                </button>
                                            </form>
                                        </div>
                                        <div class="text-xs text-amber-700 flex flex-wrap items-center justify-between gap-1 pt-1.5 border-t border-amber-200/60">
                                            <span>Periode baru: <strong>{{ \Carbon\Carbon::parse($activeExt->new_check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($activeExt->new_check_out_date)->format('d M Y') }}</strong></span>
                                            <span class="font-bold text-amber-900">Rp {{ number_format($activeExt->price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @elseif ($activeExt->status === 'approved')
                                    @php
                                        $payment = $activeExt->latestPayment;
                                        $expiresAt = $payment?->payment_expires_at;
                                    @endphp
                                    <div class="mb-4 p-4 bg-blue-50/90 border border-blue-200 rounded-xl space-y-2.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-1.5 text-xs font-bold text-blue-900">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Perpanjangan Disetujui!
                                            </div>
                                            @if($expiresAt)
                                                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full">
                                                    Batas bayar: {{ $expiresAt->format('H:i') }} WIB
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-blue-800 flex justify-between items-center">
                                            <span>Periode: <strong>{{ \Carbon\Carbon::parse($activeExt->new_check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($activeExt->new_check_out_date)->format('d M Y') }}</strong></span>
                                            <span class="text-sm font-extrabold text-blue-900">Rp {{ number_format($activeExt->price, 0, ',', '.') }}</span>
                                        </div>
                                        <button type="button" onclick="payExtension('{{ $payment?->snap_token }}')"
                                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition flex items-center justify-center gap-2 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            Bayar Tagihan Perpanjangan
                                        </button>
                                    </div>
                                @endif
                            @elseif ($isPaid && $latestExt && $latestExt->status === 'rejected' && $latestExt->created_at->diffInDays(now()) <= 7)
                                <div class="mb-4 p-3 bg-rose-50 border border-rose-200/70 rounded-xl text-xs text-rose-800">
                                    <p class="font-bold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pengajuan perpanjangan sebelumnya ditolak:
                                    </p>
                                    <p class="text-slate-600 mt-1 italic pl-4">"{{ $latestExt->admin_notes ?? 'Kamar tidak dapat diperpanjang.' }}"</p>
                                </div>
                            @endif
                        </div>

                        <!-- Footer: Harga & Aksi Responsif -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-slate-100">
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Tarif Sewa</p>
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

                                    @if (!$activeExt)
                                        <form id="form-ext-{{ $booking->id }}" action="{{ route('user.bookings.extensions.store', $booking) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="button"
                                                onclick="requestExtension('{{ $booking->id }}', '{{ addslashes($booking->room->name) }}', '{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}', '{{ \Carbon\Carbon::parse($booking->check_out_date)->addMonth()->format('d M Y') }}', '{{ number_format($booking->room->price, 0, ',', '.') }}')"
                                                class="px-3.5 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all cursor-pointer">
                                                Ajukan Perpanjangan
                                            </button>
                                        </form>
                                    @endif
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

    <script>
        function requestExtension(bookingId, roomName, startDate, endDate, price) {
            Swal.fire({
                title: 'Ajukan Perpanjangan Sewa?',
                html: `
                    <div class="text-left text-xs text-slate-600 space-y-2.5 mt-2">
                        <div class="p-3 bg-slate-50 rounded-xl space-y-1 border border-slate-100">
                            <div>Kamar: <strong class="text-slate-900">${roomName}</strong></div>
                            <div>Periode Baru: <strong class="text-slate-900">${startDate} s.d. ${endDate}</strong> (1 Bulan)</div>
                            <div>Estimasi Tarif: <strong class="text-blue-600">Rp ${price}</strong></div>
                        </div>
                        <div class="p-2.5 bg-blue-50 text-blue-800 rounded-xl text-[11px] leading-relaxed border border-blue-100">
                            Pengajuan perpanjangan akan dikirim ke Admin untuk diperiksa ketersediaan jadwalnya. Setelah disetujui, tagihan pembayaran berdurasi 1 jam akan diterbitkan.
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Ajukan Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-ext-' + bookingId);
                    if (form) form.submit();
                }
            });
        }

        function confirmCancelExtension(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                text: 'Pengajuan perpanjangan sewa yang sedang menunggu admin akan dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        function payExtension(snapToken) {
            if (!snapToken) {
                Swal.fire({
                    title: 'Token Belum Siap',
                    text: 'Token pembayaran belum tersedia. Silakan muat ulang halaman atau hubungi pengelola kontrakan.',
                    icon: 'warning',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            if (typeof window.snap === 'undefined') {
                Swal.fire({
                    title: 'Midtrans Snap Tidak Siap',
                    text: 'Gagal memuat sistem gateway pembayaran. Periksa koneksi internet Anda.',
                    icon: 'error',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    Swal.fire({
                        title: 'Pembayaran Berhasil!',
                        text: 'Masa sewa kamar Anda berhasil diperpanjang 1 bulan.',
                        icon: 'success',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                onPending: function(result) {
                    Swal.fire({
                        title: 'Menunggu Pembayaran',
                        text: 'Silakan selesaikan pembayaran sebelum batas waktu berakhir.',
                        icon: 'info',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                onError: function(result) {
                    Swal.fire({
                        title: 'Pembayaran Gagal',
                        text: 'Pembayaran tidak dapat diproses atau telah dibatalkan.',
                        icon: 'error',
                        confirmButtonColor: '#2563eb'
                    });
                },
                onClose: function() {
                    // Modal ditutup oleh user
                }
            });
        }
    </script>
</x-app-layout>