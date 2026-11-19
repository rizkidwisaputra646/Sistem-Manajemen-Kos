<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.damage-reports.index') }}" 
               class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Detail Laporan Kerusakan</h1>
                <p class="text-xs text-gray-500">ID Laporan: #{{ strtoupper(substr($report->id, 0, 8)) }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Card Status & Timeline -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-6 border-b border-slate-100">
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status Laporan Saat Ini</span>
                        <div class="mt-1">
                            {!! $report->status_badge !!}
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tanggal Pengajuan</span>
                        <p class="text-xs font-bold text-slate-700 mt-0.5">
                            {{ $report->created_at->format('d F Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                <!-- Progress Tracker -->
                <div class="pt-6">
                    <div class="grid grid-cols-3 gap-2 text-center relative">
                        <!-- Step 1: Menunggu -->
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ in_array($report->status, ['Menunggu', 'Diproses', 'Selesai']) ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                1
                            </div>
                            <span class="text-xs font-bold mt-2 text-slate-800">Menunggu</span>
                            <span class="text-[10px] text-slate-400">Laporan terkirim</span>
                        </div>

                        <!-- Step 2: Diproses -->
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ in_array($report->status, ['Diproses', 'Selesai']) ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                2
                            </div>
                            <span class="text-xs font-bold mt-2 {{ in_array($report->status, ['Diproses', 'Selesai']) ? 'text-slate-800' : 'text-slate-400' }}">Diproses</span>
                            <span class="text-[10px] text-slate-400">Sedang ditindaklanjuti</span>
                        </div>

                        <!-- Step 3: Selesai / Ditolak -->
                        <div class="flex flex-col items-center">
                            @if($report->status === 'Ditolak')
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-rose-600 text-white">
                                    ✕
                                </div>
                                <span class="text-xs font-bold mt-2 text-rose-600">Ditolak</span>
                                <span class="text-[10px] text-slate-400">Laporan tidak disetujui</span>
                            @else
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $report->status === 'Selesai' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    3
                                </div>
                                <span class="text-xs font-bold mt-2 {{ $report->status === 'Selesai' ? 'text-emerald-600' : 'text-slate-400' }}">Selesai</span>
                                <span class="text-[10px] text-slate-400">Perbaikan rampung</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Informasi Detail -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 space-y-6">
                
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">
                        {{ $report->category }}
                    </span>
                    <h2 class="text-xl font-bold text-slate-800 mt-2">
                        {{ $report->facility_name }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <span class="block text-[11px] font-semibold text-slate-400">Kamar / Lokasi</span>
                        <span class="block text-xs font-bold text-slate-800 mt-0.5">
                            {{ $report->room?->name ?? 'Kamar tidak tertaut' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-semibold text-slate-400">Terakhir Diperbarui</span>
                        <span class="block text-xs font-bold text-slate-800 mt-0.5">
                            {{ $report->updated_at->format('d M Y, H:i') }} WIB
                        </span>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Kerusakan</h3>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                        {{ $report->description }}
                    </div>
                </div>

                <!-- Foto Kerusakan -->
                @if($report->photo_path)
                    <div>
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Foto Kerusakan Terlampir</h3>
                        <div class="border border-slate-200 rounded-xl p-2 bg-slate-50 inline-block">
                            <a href="{{ asset('images/damage_reports/' . $report->photo_path) }}" target="_blank" class="block group">
                                <img src="{{ asset('images/damage_reports/' . $report->photo_path) }}" 
                                     alt="{{ $report->facility_name }}"
                                     class="max-h-80 rounded-lg object-contain group-hover:opacity-90 transition">
                                <p class="text-[11px] text-blue-600 font-semibold text-center mt-2 group-hover:underline">
                                    Klik untuk memperbesar gambar &rarr;
                                </p>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Tanggapan Admin / Pengelola -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        Tanggapan dari Pengelola Kontrakan
                    </h3>

                    @if($report->admin_response)
                        <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-4 text-xs text-slate-800 space-y-2">
                            <div class="flex items-center justify-between text-[11px] text-blue-700 font-semibold">
                                <span>Oleh: {{ $report->admin?->name ?? 'Admin / Pengelola' }}</span>
                                @if($report->resolved_at)
                                    <span>{{ $report->resolved_at->format('d M Y, H:i') }} WIB</span>
                                @endif
                            </div>
                            <p class="leading-relaxed whitespace-pre-line text-slate-700">
                                {{ $report->admin_response }}
                            </p>
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-xl p-4 text-center text-xs text-slate-400">
                            Pengelola belum memberikan tanggapan resmi. Laporan Anda sudah masuk dalam antrean pengecekan tim teknisi.
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
