<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col text-left">
            <nav class="flex text-xs font-bold text-slate-400 uppercase tracking-widest mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Eksplor</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 md:ml-2">Konfirmasi Booking</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="font-extrabold text-2xl text-slate-900 leading-tight tracking-tight">Konfirmasi Pesanan</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div
                    class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-2xl shadow-sm text-left">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-bold">Aduh, ada yang salah:</p>
                    </div>
                    <ul class="list-disc ml-8 text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-slate-200 relative z-10">
                <div class="p-8 text-left">
                    <div class="flex flex-col sm:flex-row items-center gap-8 mb-10 pb-8 border-b border-slate-100">
                        <div class="relative shrink-0">
                            <img src="{{ asset('images/rooms/' . $room->image) }}"
                                class="w-40 h-40 object-cover rounded-2xl shadow-md border-4 border-white">
                            <div class="absolute -bottom-2 -right-2 bg-indigo-600 text-white p-2 rounded-xl shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $room->name }}</h3>
                            <p class="text-slate-500 text-sm mt-2 leading-relaxed max-w-md">
                                {{ $room->description ?? 'Hunian premium dengan fasilitas lengkap, menjamin kenyamanan istirahat dan produktivitas kamu selama di sini.' }}
                            </p>
                            <div class="mt-4 flex items-baseline gap-2">
                                <span class="text-2xl font-black text-indigo-600 tracking-tight">Rp
                                    {{ number_format($room->price) }}</span>
                                <span class="text-slate-400 font-bold text-xs uppercase tracking-widest italic">/
                                    bulan</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('rooms.store', $room->id) }}" method="POST" class="space-y-8">
                        @csrf

                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Rencana Tanggal
                                Masuk</label>
                            <input type="date" name="check_in_date" min="{{ date('Y-m-d') }}" required
                                class="w-full mt-1 border-slate-200 bg-white rounded-2xl focus:ring-indigo-500 font-bold text-slate-700">
                            <p class="text-[9px] text-slate-400 mt-2 ml-2 italic">*Masa sewa otomatis berlaku selama 1
                                bulan sejak tanggal masuk.</p>
                        </div>

                        <div class="bg-indigo-50/50 p-8 rounded-3xl border border-indigo-100/50 space-y-4 shadow-sm">
                            <div class="flex justify-between items-center group">
                                <span
                                    class="text-slate-500 font-medium transition-colors group-hover:text-slate-700">Harga
                                    Kamar</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($room->price) }}</span>
                            </div>
                            <div class="flex justify-between items-center group pb-2">
                                <span
                                    class="text-slate-500 font-medium transition-colors group-hover:text-slate-700 italic underline decoration-indigo-100 decoration-2">Biaya
                                    Administrasi</span>
                                <span class="font-bold text-slate-900 italic">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-indigo-100 pt-4 mt-2">
                                <div class="flex flex-col">
                                    <span class="font-extrabold text-slate-900 text-lg tracking-tight uppercase">Total
                                        Pembayaran</span>
                                    <span
                                        class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">*Sudah
                                        termasuk pajak</span>
                                </div>
                                <span class="font-black text-indigo-600 text-3xl tracking-tighter">Rp
                                    {{ number_format($room->price) }}</span>
                            </div>
                        </div>
                        
                        <!-- Panduan Pembayaran untuk User Awam -->
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-sm text-amber-800 shadow-sm">
                            <p class="font-bold mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Panduan Pembayaran:
                            </p>
                            <ol class="list-decimal ml-5 space-y-2 font-medium">
                                <li>Pilih <strong>Rencana Tanggal Masuk</strong> di atas.</li>
                                <li>Klik tombol biru <strong>Konfirmasi & Bayar Sekarang</strong> di bawah.</li>
                                <li>Akan muncul layar otomatis untuk memilih pembayaran (Transfer ATM, m-Banking, GoPay, atau minimarket).</li>
                                <li>Ikuti petunjuk di layar HP Anda sampai selesai.</li>
                            </ol>
                        </div>

                        <button type="submit" id="btn-submit"
                            class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-extrabold text-xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 hover:shadow-indigo-200 transition-all active:scale-[0.98] cursor-pointer flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            Konfirmasi & Bayar Sekarang
                        </button>

                        <p class="text-center text-[10px] text-slate-400 font-medium px-8 italic">
                            Dengan menekan tombol di atas, kamu menyetujui <a href="#"
                                class="underline hover:text-indigo-600">Syarat & Ketentuan</a> yang berlaku di platform
                            KosKu.
                        </p>
                    </form>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-center gap-2 grayscale brightness-125 opacity-50">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/Midtrans.png" class="h-6 object-contain"
                    alt="Midtrans Secured">
            </div>
        </div>
    </div>
</x-app-layout>
