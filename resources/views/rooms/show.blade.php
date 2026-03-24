<x-app-layout>
    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <a href="{{ route('dashboard') }}" class="group inline-flex items-center text-xs font-bold text-slate-400 uppercase tracking-widest mb-8 transition-colors hover:text-indigo-600">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Eksplor
            </a>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden text-left">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    <div class="aspect-square w-full bg-slate-100 overflow-hidden relative">
                        @if($room->image)
                            <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-slate-300">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/20 to-transparent"></div>
                    </div>

                    <div class="p-8 md:p-12 flex flex-col justify-center">
                        <div class="flex items-center justify-between mb-4">
                            @if($room->status == 'available')
                                <span class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest border border-indigo-100 shadow-sm shadow-indigo-50">Tersedia</span>
                            @else
                                <span class="bg-rose-50 text-rose-600 px-4 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest border border-rose-100">Sudah Terisi</span>
                            @endif
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-xs font-bold text-slate-700">4.9 / 5.0</span>
                            </div>
                        </div>

                        <h1 class="text-4xl font-black text-slate-900 leading-tight tracking-tight mb-2">{{ $room->name }}</h1>
                        <p class="text-3xl font-black text-indigo-600 tracking-tighter">
                            Rp {{ number_format($room->price) }} <span class="text-xs text-slate-400 font-bold uppercase tracking-widest italic decoration-slate-200">/ bulan</span>
                        </p>

                        <div class="mt-8 mb-8">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 italic">Fasilitas Utama</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $room->facilities) as $f)
                                    <span class="bg-slate-50 border border-slate-100 px-3 py-2 rounded-xl text-[10px] font-bold text-slate-600 uppercase tracking-tighter flex items-center shadow-sm">
                                        <svg class="w-3 h-3 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        {{ trim($f) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-10">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 italic">Deskripsi Unit</h3>
                            <p class="text-slate-500 text-sm leading-relaxed">
                                {{ $room->description ?? 'Nikmati hunian eksklusif dengan desain modern dan fungsional. Sangat cocok bagi profesional muda atau mahasiswa yang mendambakan kenyamanan dan ketenangan dalam beristirahat.' }}
                            </p>
                        </div>

                        <div class="mt-auto pt-8 border-t border-slate-50">
                            @if($room->status == 'available')
                                <a href="{{ route('rooms.checkout', $room->id) }}" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-extrabold text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-100 hover:shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-3 group">
                                    Booking Sekarang
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                            @else
                                <button disabled class="w-full bg-slate-200 text-slate-400 py-5 rounded-2xl font-extrabold text-lg cursor-not-allowed">
                                    Kamar Sudah Terisi
                                </button>
                            @endif
                            <p class="text-center text-[10px] text-slate-300 mt-4 italic">
                                *Konfirmasi jadwal masuk akan dilakukan setelah pembayaran diverifikasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Trust Badges -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="p-6">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4">🛡️</div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Pembayaran Aman</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Melalui gateway Midtrans yang terpercaya.</p>
                </div>
                <div class="p-6">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4">🚀</div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Cepat & Instan</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Booking sukses hanya dalam hitungan detik.</p>
                </div>
                <div class="p-6">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4">🏠</div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Hunian Nyaman</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Unit sudah diverifikasi oleh tim internal kami.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>