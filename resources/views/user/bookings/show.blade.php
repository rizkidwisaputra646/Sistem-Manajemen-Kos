<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 text-left">
            <div>
                <a href="{{ route('user.bookings.index') }}"
                    class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Daftar Pesanan
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 break-words">
                    Detail Pesanan #{{ strtoupper(substr($booking->id, 0, 8)) }}
                </h1>
                <p class="text-xs text-gray-400 font-mono break-all mt-0.5">
                    UUID: {{ $booking->id }}
                </p>
            </div>
            <div>
                <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider inline-block border {{ $booking->status == 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($booking->status == 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                    Status: {{ $booking->status }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Kolom Kiri: Informasi Kamar & Detail Pemesanan Lengkap -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Card Informasi Kamar -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm text-left">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/70 font-bold text-xs text-gray-600 uppercase tracking-wider">
                        Informasi Kamar
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row gap-6 mb-6">
                            <div class="w-full sm:w-36 h-36 shrink-0 bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
                                @if($booking->room->image)
                                    <img src="{{ asset('images/rooms/' . $booking->room->image) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-400 text-xs">Foto tidak tersedia</div>
                                @endif
                            </div>
                            <div class="flex flex-col justify-center min-w-0">
                                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 break-words">{{ $booking->room->name }}</h3>
                                <p class="text-gray-500 text-sm mb-3 leading-relaxed break-words">
                                    {{ $booking->room->description ?? 'Hunian nyaman, bersih, dan strategis untuk tempat tinggal produktif.' }}
                                </p>
                                <div class="flex flex-wrap gap-1.5 text-left">
                                    @php
                                        $facilities = !empty($booking->room->facilities) 
                                            ? explode(',', $booking->room->facilities) 
                                            : ['Wi-Fi Cepat', 'Kamar Mandi Dalam', 'Kasur Nyaman', 'Lemari'];
                                    @endphp
                                    @foreach($facilities as $fac)
                                        <span class="px-2.5 py-1 bg-gray-100 border border-gray-200 rounded text-[11px] font-semibold text-gray-700">
                                            {{ trim($fac) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                            <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Mulai (Check-in)</p>
                                <p class="text-sm sm:text-base font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d F Y') }}
                                </p>
                            </div>
                            <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Berakhir (Check-out)</p>
                                <p class="text-sm sm:text-base font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Detail Pemesanan & Penyewa Lengkap -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm text-left">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/70 font-bold text-xs text-gray-600 uppercase tracking-wider">
                        Data Lengkap Pemesanan & Penyewa
                    </div>
                    <div class="p-6">
                        <dl class="divide-y divide-gray-100 text-sm">
                            <div class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                                <dt class="text-gray-500 font-medium">Nama Penyewa</dt>
                                <dd class="font-bold text-gray-800 break-words">{{ $booking->user->name ?? '-' }}</dd>
                            </div>
                            <div class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                                <dt class="text-gray-500 font-medium">Alamat Email</dt>
                                <dd class="font-bold text-gray-800 break-all">{{ $booking->user->email ?? '-' }}</dd>
                            </div>
                            <div class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                                <dt class="text-gray-500 font-medium">Waktu Pemesanan Dibuat</dt>
                                <dd class="font-bold text-gray-800">{{ $booking->created_at->format('d F Y, H:i') }} WIB</dd>
                            </div>
                            <div class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                                <dt class="text-gray-500 font-medium">Metode Pembayaran</dt>
                                <dd class="font-bold text-gray-800">
                                    @if($booking->payment_method === 'manual')
                                        <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 text-xs">
                                            Bayar ke Pemilik (Manual)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200 text-xs">
                                            Online (Midtrans)
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                                <dt class="text-gray-500 font-medium">Durasi Sewa</dt>
                                <dd class="font-bold text-gray-800">
                                    1 Bulan ({{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) }} Hari)
                                </dd>
                            </div>
                            <div class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                                <dt class="text-gray-500 font-medium">Jatuh Tempo Pembayaran</dt>
                                <dd class="font-bold text-rose-600">
                                    {{ \Carbon\Carbon::parse($booking->effective_due_date)->format('d F Y') }}
                                </dd>
                            </div>
                            @if($booking->status == 'success')
                                <div class="py-3 flex flex-col sm:flex-row sm:justify-between gap-1">
                                    <dt class="text-gray-500 font-medium">Waktu Konfirmasi Lunas</dt>
                                    <dd class="font-bold text-emerald-600">{{ $booking->updated_at->format('d F Y, H:i') }} WIB</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Rincian Biaya & Aksi Pembayaran -->
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-left shadow-sm">
                    <h2 class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-4 pb-2 border-b border-gray-100">
                        Rincian Biaya
                    </h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Harga Sewa Kamar</span>
                            <span class="font-bold text-gray-800">Rp {{ number_format($booking->room->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Biaya Layanan & Pajak</span>
                            <span class="font-bold text-emerald-600">Gratis (Rp 0)</span>
                        </div>
                        <div class="pt-4 border-t border-gray-100 flex justify-between items-baseline">
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Total Tagihan</span>
                            <span class="text-2xl font-extrabold text-blue-600">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if ($booking->status == 'pending')
                            @if ($booking->payment_method === 'manual')
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-900 text-xs space-y-2">
                                    <div class="flex items-center gap-1.5 font-bold text-emerald-800">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Menunggu Konfirmasi Pemilik Kos
                                    </div>
                                    <p class="leading-relaxed text-emerald-700">
                                        Anda memilih metode bayar langsung ke pemilik. Silakan serahkan uang tunai (cash) saat survey/serah terima kunci, atau transfer langsung ke rekening pemilik kos.
                                    </p>
                                </div>

                                @php
                                    $waMessage = rawurlencode("Halo Pengelola KosKu, saya ingin konfirmasi pemesanan kamar:\n\n- Kamar: {$booking->room->name}\n- Nama: " . auth()->user()->name . "\n- Periode: " . \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') . " s/d " . \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') . "\n- Total: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n- Metode: Bayar Langsung ke Pemilik\n\nMohon konfirmasi dan info pembayarannya ya.");
                                @endphp

                                <a href="https://wa.me/6285357597653?text={{ $waMessage }}" target="_blank"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 px-4 rounded-xl font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                    Konfirmasi via WhatsApp
                                </a>

                                <form id="cancel-form" action="{{ route('user.bookings.cancel', $booking) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="confirmCancel()"
                                        class="w-full text-gray-500 border border-gray-200 py-2.5 rounded-xl font-bold text-xs hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors uppercase mt-1 cursor-pointer">
                                        Batalkan Pesanan
                                    </button>
                                </form>
                            @else
                                <button id="pay-button"
                                    class="w-full bg-blue-600 text-white py-3.5 px-4 rounded-xl font-bold text-base hover:bg-blue-700 shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Bayar Sekarang (Midtrans)
                                </button>

                                <button type="button" onclick="checkPaymentStatus()"
                                    class="w-full bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 py-2.5 rounded-xl font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Cek Status Pembayaran
                                </button>

                                <form id="cancel-form" action="{{ route('user.bookings.cancel', $booking) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="confirmCancel()"
                                        class="w-full text-gray-500 border border-gray-200 py-2.5 rounded-xl font-bold text-xs hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors uppercase mt-1 cursor-pointer">
                                        Batalkan Pesanan
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if ($booking->status == 'success')
                            <div class="bg-emerald-50 p-4 rounded-xl text-center border border-emerald-200 font-bold text-sm text-emerald-700">
                                ✅ PEMBAYARAN LUNAS
                            </div>

                            <a href="{{ route('user.bookings.receipt', $booking) }}" target="_blank"
                                class="flex items-center justify-center gap-2 w-full bg-slate-900 text-white py-3 px-4 rounded-xl font-bold text-xs hover:bg-black shadow-sm transition-colors uppercase tracking-wider">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                Cetak Kuitansi (PDF)
                            </a>
                        @endif

                        @if ($booking->status == 'cancelled')
                            <div class="bg-rose-50 p-4 rounded-xl text-center border border-rose-200 font-bold text-sm text-rose-700">
                                ❌ PESANAN TELAH DIBATALKAN
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-slate-900 rounded-2xl p-6 text-white text-left shadow-sm">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-300 mb-2">Bantuan & Konfirmasi</h4>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                        Jika Anda membutuhkan bantuan terkait metode pembayaran atau proses check-in, hubungi pengelola kontrakan.
                    </p>
                    <a href="https://wa.me/6285357597653" target="_blank"
                        class="block w-full text-center py-2.5 bg-white text-slate-900 rounded-xl font-bold text-xs hover:bg-slate-100 transition-colors">
                        Hubungi via WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    const payBtn = document.getElementById('pay-button');
    if (payBtn) {
        payBtn.addEventListener('click', function() {
            window.snap.pay('{{ $booking->snap_token }}', {
                onSuccess: function(result) {
                    handleSyncPayment(result);
                },
                onPending: function(result) {
                    handleSyncPayment(result);
                },
                onError: function(result) {
                    Swal.fire({
                        title: 'Pembayaran Gagal',
                        text: 'Pembayaran tidak dapat diproses oleh sistem.',
                        icon: 'error',
                        confirmButtonColor: '#2563eb'
                    });
                },
                onClose: function() {
                    // Cek status sekiranya user sudah bayar di simulator lalu menutup popup
                    checkPaymentStatus(false);
                }
            });
        });
    }

    function handleSyncPayment(result) {
        Swal.fire({
            title: 'Memverifikasi Pembayaran...',
            text: 'Mohon tunggu sebentar, kami sedang menyinkronkan status pesanan Anda.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch("{{ route('user.bookings.sync-payment', $booking) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(result)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.status === 'success') {
                Swal.fire({
                    title: 'LUNAS & BERHASIL! 🎉',
                    text: 'Pembayaran Anda telah diterima. Selamat menikmati kamar sewa Anda!',
                    icon: 'success',
                    confirmButtonColor: '#2563eb'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Status Pesanan',
                    text: data.message || 'Status pembayaran sedang diproses.',
                    icon: 'info',
                    confirmButtonColor: '#2563eb'
                }).then(() => {
                    window.location.reload();
                });
            }
        })
        .catch(err => {
            window.location.reload();
        });
    }

    function checkPaymentStatus(showAlert = true) {
        if (showAlert) {
            Swal.fire({
                title: 'Mengecek Status...',
                text: 'Menghubungkan ke gateway pembayaran...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        fetch("{{ route('user.bookings.sync-payment', $booking) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.status === 'success') {
                Swal.fire({
                    title: 'LUNAS & BERHASIL! 🎉',
                    text: 'Pembayaran Anda telah berhasil diverifikasi!',
                    icon: 'success',
                    confirmButtonColor: '#2563eb'
                }).then(() => {
                    window.location.reload();
                });
            } else if (showAlert) {
                Swal.fire({
                    title: 'Belum Lunas',
                    text: 'Pembayaran belum terkonfirmasi dari Midtrans. Jika sudah membayar, silakan tunggu beberapa saat atau hubungi admin.',
                    icon: 'warning',
                    confirmButtonColor: '#2563eb'
                });
            }
        })
        .catch(err => {
            if (showAlert) {
                window.location.reload();
            }
        });
    }

    function confirmCancel() {
        Swal.fire({
            title: 'Batalkan Pesanan Ini?',
            text: 'Pesanan akan dibatalkan dan kamar akan tersedia kembali untuk penyewa lain.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form').submit();
            }
        });
    }
</script>
