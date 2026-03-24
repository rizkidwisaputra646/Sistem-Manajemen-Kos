<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-6">
            <div class="flex items-center justify-between mb-8 text-left">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Pesanan Aktif</h1>
                    <p class="text-slate-500 text-sm mt-1 font-medium italic">Kamar yang sedang kamu tempati atau tunggu pembayarannya.</p>
                </div>
                <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-xl shadow-sm">🏷️</div>
            </div>

            <div class="space-y-4"> 
                @forelse($bookings as $booking)
                    <a href="{{ route('user.bookings.show', $booking) }}" class="group block bg-white rounded-3xl p-4 border border-slate-200 shadow-sm hover:shadow-xl hover:shadow-indigo-100/50 hover:-translate-y-1 transition-all duration-300">
                        <div class="flex flex-col sm:flex-row gap-6">
                            <div class="relative shrink-0">
                                <img src="{{ asset('images/rooms/'.$booking->room->image) }}" class="w-full sm:w-28 h-48 sm:h-28 object-cover rounded-2xl shadow-sm group-hover:shadow-md transition-shadow">
                                @if($booking->status == 'success')
                                    <div class="absolute -top-2 -right-2 bg-green-500 text-white p-1 rounded-full shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 flex flex-col justify-between py-1 text-left">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-lg group-hover:text-indigo-600 transition-colors uppercase">{{ $booking->room->name }}</h3>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 italic">
                                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M') }} — {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $booking->status == 'success' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                        {{ $booking->status }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center mt-6 sm:mt-auto">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Total Pembayaran</span>
                                        <span class="text-xl font-extrabold text-slate-900 tracking-tight">Rp {{ number_format($booking->total_price) }}</span>
                                    </div>
                                    <div class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all">
                                        Detail Pesanan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200 shadow-sm">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">📭</div>
                        <h3 class="text-lg font-bold text-slate-900">Belum ada pesanan aktif</h3>
                        <p class="text-slate-400 text-sm mt-1 max-w-xs mx-auto text-medium italic">Silakan jelajahi kamar yang tersedia dan lakukan pemesanan pertama kamu!</p>
                        <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Eksplor Kamar Sekarang</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>