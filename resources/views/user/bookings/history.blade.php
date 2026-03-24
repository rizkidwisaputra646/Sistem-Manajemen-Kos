<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-left">
            <div>
                 <h2 class="font-extrabold text-3xl text-slate-900 leading-tight tracking-tight">
                    Riwayat Selesai
                </h2>
                <p class="text-slate-500 text-sm mt-1 font-medium italic">Catatan perjalanan tinggalmu di platform KosKu.</p>
            </div>
            @if($bookings->count() > 0)
                <form id="clear-all-form" action="{{ route('user.bookings.clear-history') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmClearAll()" class="group flex items-center gap-2 translate-y-2 text-[11px] font-extrabold text-rose-600 bg-rose-50 px-5 py-3 rounded-2xl hover:bg-rose-600 hover:text-white transition-all duration-300 uppercase tracking-wider shadow-sm border border-rose-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Sapu Bersih
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="space-y-4">
                @forelse($bookings as $booking)
                    <div class="relative group bg-white p-5 rounded-[2.5rem] border border-slate-200 flex items-center gap-6 transition-all duration-500 hover:bg-slate-50/50 hover:shadow-lg hover:shadow-slate-100">
                        
                        <div class="relative shrink-0">
                            <img src="{{ asset('images/rooms/'.$booking->room->image) }}" class="w-20 h-20 object-cover rounded-2xl grayscale transition-all duration-700 group-hover:grayscale-0">
                            <div class="absolute inset-0 bg-slate-900/5 rounded-2xl"></div>
                        </div>
                        
                        <div class="flex-1 text-left">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-extrabold text-slate-400 group-hover:text-slate-900 transition-colors leading-tight uppercase text-lg tracking-tight">{{ $booking->room->name }}</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5 font-bold uppercase tracking-widest italic">
                                        Check-out pada {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}
                                    </p>
                                </div>
                                <span class="text-[9px] font-black px-3 py-1 bg-slate-100 text-slate-400 rounded-full uppercase tracking-tighter group-hover:bg-green-100 group-hover:text-green-700 transition-colors">
                                    {{ $booking->status }}
                                </span>
                            </div>
                            
                            <div class="mt-4 flex justify-between items-center overflow-hidden">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest leading-none mb-1">Total Transaksi</span>
                                    <span class="text-lg font-black text-slate-300 group-hover:text-indigo-600 transition-colors tracking-tight">Rp {{ number_format($booking->total_price) }}</span>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('user.bookings.show', $booking) }}" class="p-2.5 text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200 opacity-0 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0">
                                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <form id="delete-form-{{ $booking->id }}" action="{{ route('user.bookings.destroy', $booking) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $booking->id }}')" class="p-2.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-24 bg-white rounded-[3rem] border-2 border-dashed border-slate-200 shadow-sm">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl opacity-50">🧹</div>
                        <h3 class="text-slate-900 font-extrabold text-xl tracking-tight">Belum ada riwayat sewa</h3>
                        <p class="text-slate-400 text-sm mt-1 max-w-xs mx-auto font-medium italic">Semua riwayat sudah dihapus atau memang perjalananmu baru saja dimulai.</p>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 mt-8 px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 group">
                            Cari Kamar Baru
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // 1. Konfirmasi Sapu Bersih Semua
    function confirmClearAll() {
        Swal.fire({
            title: 'Wushhh! Yakin mau disapu?',
            text: "Semua riwayat lama bakal ilang selamanya dari memori!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e', 
            cancelButtonColor: '#6366f1', 
            confirmButtonText: 'Ya, Bersihkan Semua!',
            cancelButtonText: 'Nggak Jadi',
            borderRadius: '24px',
            customClass: {
                title: 'font-extrabold tracking-tight',
                content: 'font-medium text-slate-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('clear-all-form').submit();
            }
        })
    }

    // 2. Konfirmasi Hapus Satuan
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus satu ini?',
            text: "Catatan riwayat ini akan dibuang permanen.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#9ca3af', 
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '24px'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>