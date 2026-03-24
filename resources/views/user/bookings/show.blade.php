<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-left">
            <div>
                <nav class="flex text-xs font-bold text-slate-400 uppercase tracking-widest mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('user.bookings.index') }}" class="hover:text-indigo-600 transition-colors">Pesanan</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 md:ml-2">Detail #{{ $booking->id }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight tracking-tight">
                    Ringkasan Pesanan
                </h2>
            </div>
            <div class="shrink-0 flex items-center">
                 <span class="px-4 py-2 {{ $booking->status == 'success' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-amber-50 text-amber-700 border-amber-100' }} border rounded-2xl text-xs font-bold uppercase tracking-wider shadow-sm">
                    Status: {{ $booking->status }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Informasi Kamar</h3>
                                <p class="text-xs text-slate-500 font-medium">Detail hunian yang kamu sewa</p>
                            </div>
                        </div>
                        <div class="p-6 text-left">
                            <div class="flex flex-col sm:flex-row gap-6 mb-8">
                                <img src="{{ asset('images/rooms/' . $booking->room->image) }}" class="w-full sm:w-40 h-40 object-cover rounded-2xl shadow-sm">
                                <div class="flex flex-col justify-center">
                                    <h4 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $booking->room->name }}</h4>
                                    <p class="text-slate-500 text-sm mt-1 leading-relaxed">{{ $booking->room->description ?? 'Hunian nyaman dengan fasilitas lengkap untuk kenyamanan beristirahat kamu.' }}</p>
                                    <div class="flex flex-wrap gap-2 mt-4 text-left">
                                        <span class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[10px] font-bold text-slate-600 uppercase tracking-tighter">WiFi Gratis</span>
                                        <span class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[10px] font-bold text-slate-600 uppercase tracking-tighter">AC</span>
                                        <span class="px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[10px] font-bold text-slate-600 uppercase tracking-tighter">KM Dalam</span>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="border-slate-100 mb-8">
                            
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-none italic">Tanggal Check-in</p>
                                    <p class="text-lg text-slate-900 font-extrabold">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d F Y') }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-none italic">Tanggal Check-out</p>
                                    <p class="text-lg text-slate-900 font-extrabold">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Summary -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 text-left">
                        <h3 class="font-bold text-slate-900 mb-6 flex items-center gap-2">
                             <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Rincian Pembayaran
                        </h3>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 font-medium">Harga per Hari</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($booking->room->price) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 font-medium">Durasi Sewa</span>
                                @php
                                    $start = \Carbon\Carbon::parse($booking->check_in_date);
                                    $end = \Carbon\Carbon::parse($booking->check_out_date);
                                    $days = $start->diffInDays($end) ?: 1;
                                @endphp
                                <span class="font-bold text-slate-900">{{ $days }} Hari</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 font-medium italic underline decoration-slate-200 decoration-2">Pajak & Biaya</span>
                                <span class="font-bold text-slate-900">Rp 0</span>
                            </div>
                            <div class="pt-4 border-t border-slate-100 flex justify-between items-end">
                                <span class="text-xs font-extrabold text-slate-900 uppercase tracking-tighter italic">Total Bayar</span>
                                <span class="text-2xl font-extrabold text-indigo-600 tracking-tight">Rp {{ number_format($booking->total_price) }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @if($booking->status == 'pending')
                                <button id="pay-button" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-100 hover:shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Bayar Sekarang
                                </button>

                                <form id="cancel-form" action="{{ route('user.bookings.cancel', $booking) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="confirmCancel()" class="w-full bg-white text-slate-400 border border-slate-200 py-3 rounded-2xl font-bold text-xs hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 transition-all uppercase tracking-widest mt-2">
                                        Batalkan Pesanan
                                    </button>
                                </form>
                            @endif

                            @if($booking->status == 'success')
                                <div class="bg-green-50 p-6 rounded-3xl text-center border border-green-100 shadow-sm shadow-green-50 flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center mb-1">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="text-green-700 font-extrabold text-sm uppercase tracking-tighter">Pembayaran Lunas</span>
                                    <p class="text-green-600/70 text-[10px] font-medium leading-tight">Selamat beristirahat! Kamar siap kamu tempati.</p>
                                </div>
                            @endif

                            <a href="{{ route('user.bookings.index') }}" class="block text-center text-[10px] font-bold text-slate-400 hover:text-indigo-600 transition-all uppercase tracking-widest mt-6 italic underline decoration-slate-200 hover:decoration-indigo-200">
                                Kembali ke Daftar Pesanan
                            </a>
                        </div>
                    </div>
                    
                    <!-- Need Help Card -->
                    <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-lg shadow-indigo-100 text-left relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                        <h4 class="font-bold text-sm">Butuh Bantuan?</h4>
                        <p class="text-[10px] text-indigo-100 mt-1 mb-4 leading-relaxed">Admin kami siap membantu memandu proses check-in kamu 24/7.</p>
                        <a href="https://wa.me/6285357597653" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-[10px] font-bold transition-all">
                             <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.224-3.82l.303.18c1.397.831 3.013 1.27 4.673 1.271 5.385 0 9.765-4.38 9.768-9.764.001-2.61-1.017-5.064-2.868-6.913-1.851-1.85-4.307-2.864-6.918-2.865-5.387 0-9.765 4.382-9.768 9.765-.001 2.105.547 4.154 1.588 5.95l.199.345-1.054 3.848 3.937-1.033z"/></svg>
                             Hubungi Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // 1. LOGIKA MIDTRANS
    const payButton = document.getElementById('pay-button');
    if(payButton) {
        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $booking->snap_token }}', {
                onSuccess: function(result) {
                    Swal.fire({
                        title: 'PEMBAYARAN BERHASIL!',
                        text: 'Pesanan kamu sudah lunas. Selamat beristirahat!',
                        icon: 'success',
                        confirmButtonText: 'MANTAP',
                        confirmButtonColor: '#4f46e5',
                        borderRadius: '24px'
                    }).then(() => { 
                        window.location.href = "{{ route('user.bookings.index') }}"; 
                    });
                },
                onPending: function(result) {
                    Swal.fire({ 
                        title: 'Menunggu Pembayaran', 
                        text: 'Silakan cek aplikasi bank kamu.', 
                        icon: 'info',
                        borderRadius: '24px',
                        confirmButtonColor: '#4f46e5'
                    });
                },
                onError: function(result) {
                    Swal.fire({ 
                        title: 'Gagal!', 
                        text: 'Pembayaran gagal, silakan coba lagi.', 
                        icon: 'error',
                        borderRadius: '24px',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            });
        });
    }

    // 2. KONFIRMASI PEMBATALAN (SWEETALERT)
    function confirmCancel() {
        Swal.fire({
            title: 'Yakin mau batal?',
            text: "Kamar ini incaran banyak orang lho!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Batalin Aja',
            cancelButtonText: 'Nggak Jadi',
            borderRadius: '24px'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form').submit();
            }
        });
    }
</script>