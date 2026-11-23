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

                        <!-- Pilihan Metode Pembayaran -->
                        <div class="space-y-2.5">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                Metode Pembayaran
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Opsi 1: Midtrans Online -->
                                <label for="method-midtrans" 
                                    class="payment-option relative flex items-start gap-3 p-3.5 bg-white border-2 border-indigo-600 rounded-xl cursor-pointer hover:border-indigo-600 transition-all shadow-sm">
                                    <input type="radio" id="method-midtrans" name="payment_method" value="midtrans" checked
                                        class="text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5" onchange="switchPaymentMethod('midtrans')">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-bold text-slate-900 text-sm">Pembayaran Online</span>
                                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                                                Otomatis
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate">QRIS, Virtual Account, E-Wallet</p>
                                    </div>
                                </label>

                                <!-- Opsi 2: Bayar Langsung ke Pemilik -->
                                <label for="method-manual" 
                                    class="payment-option relative flex items-start gap-3 p-3.5 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-slate-300 transition-all shadow-sm">
                                    <input type="radio" id="method-manual" name="payment_method" value="manual"
                                        class="text-indigo-600 focus:ring-indigo-500 w-4 h-4 mt-0.5" onchange="switchPaymentMethod('manual')">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-bold text-slate-900 text-sm">Bayar ke Pemilik</span>
                                            <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                                Manual
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate">Tunai saat check-in / transfer langsung</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Petunjuk Singkat 1 Baris -->
                            <p id="payment-hint" class="text-xs text-slate-500 flex items-center gap-1.5 pl-1 pt-0.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="hint-text">Diverifikasi otomatis dalam 45 menit via Midtrans.</span>
                            </p>
                        </div>

                        <!-- Ringkasan Biaya -->
                        <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 space-y-2.5">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Harga Sewa (1 Bulan)</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($room->price) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Biaya Layanan & Administrasi</span>
                                <span class="font-bold text-emerald-600">Gratis (Rp 0)</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-slate-200 pt-3 mt-1">
                                <div>
                                    <span class="font-extrabold text-slate-900 text-base">Total Tagihan</span>
                                    <span class="block text-[10px] text-slate-400 font-medium">Sudah termasuk pajak</span>
                                </div>
                                <span class="font-black text-indigo-600 text-2xl tracking-tight">
                                    Rp {{ number_format($room->price) }}
                                </span>
                            </div>
                        </div>

                        <button type="submit" id="btn-submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 px-6 rounded-xl font-bold text-base shadow-sm hover:shadow transition-all active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2">
                            <span id="btn-text">Lanjut ke Pembayaran</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
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

<script>
    function switchPaymentMethod(method) {
        const midtransLabel = document.querySelector('label[for="method-midtrans"]');
        const manualLabel = document.querySelector('label[for="method-manual"]');
        const hintText = document.getElementById('hint-text');
        const btnText = document.getElementById('btn-text');

        if (method === 'manual') {
            midtransLabel.classList.remove('border-2', 'border-indigo-600');
            midtransLabel.classList.add('border', 'border-slate-200');

            manualLabel.classList.remove('border', 'border-slate-200');
            manualLabel.classList.add('border-2', 'border-indigo-600');

            hintText.textContent = 'Bayar tunai saat serah terima kunci atau transfer ke rekening pemilik kos.';
            btnText.textContent = 'Konfirmasi Pemesanan';
        } else {
            manualLabel.classList.remove('border-2', 'border-indigo-600');
            manualLabel.classList.add('border', 'border-slate-200');

            midtransLabel.classList.remove('border', 'border-slate-200');
            midtransLabel.classList.add('border-2', 'border-indigo-600');

            hintText.textContent = 'Diverifikasi otomatis dalam 45 menit via Midtrans.';
            btnText.textContent = 'Lanjut ke Pembayaran';
        }
    }
</script>
