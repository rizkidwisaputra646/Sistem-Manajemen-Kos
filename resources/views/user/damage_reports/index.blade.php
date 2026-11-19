<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Kerusakan Fasilitas</h1>
                <p class="text-xs text-gray-500 mt-0.5">Sampaikan keluhan fasilitas kamar atau area kontrakan untuk segera ditangani</p>
            </div>
            <a href="{{ route('user.damage-reports.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-sm w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Laporan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-4 space-y-6">

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        timer: 3000,
                        showConfirmButton: false,
                        borderRadius: '1rem'
                    });
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Perhatian',
                        text: "{{ session('error') }}",
                        borderRadius: '1rem'
                    });
                });
            </script>
        @endif

        <!-- Summary Cards -->
        @php
            $totalCount = $reports->total();
            $menungguCount = \App\Models\DamageReport::where('user_id', Auth::id())->where('status', 'Menunggu')->count();
            $diprosesCount = \App\Models\DamageReport::where('user_id', Auth::id())->where('status', 'Diproses')->count();
            $selesaiCount = \App\Models\DamageReport::where('user_id', Auth::id())->where('status', 'Selesai')->count();
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Laporan</div>
                <div class="text-2xl font-bold text-slate-800 mt-1">{{ $totalCount }}</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-[11px] font-semibold text-amber-500 uppercase tracking-wider">Menunggu</div>
                <div class="text-2xl font-bold text-amber-600 mt-1">{{ $menungguCount }}</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-[11px] font-semibold text-blue-500 uppercase tracking-wider">Diproses</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $diprosesCount }}</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-[11px] font-semibold text-emerald-500 uppercase tracking-wider">Selesai</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $selesaiCount }}</div>
            </div>
        </div>

        <!-- Daftar Laporan -->
        <div class="space-y-4">
            @forelse($reports as $report)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition-shadow relative overflow-hidden">
                    @if(!$report->is_read_by_user && $report->status !== 'Menunggu')
                        <div class="absolute top-0 right-0 bg-blue-600 text-white text-[9px] font-bold px-3 py-0.5 rounded-bl-xl uppercase tracking-wider">
                            Tanggapan Baru
                        </div>
                    @endif

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <!-- Thumbnail Foto jika ada -->
                            @if($report->photo_path)
                                <a href="{{ asset('images/damage_reports/' . $report->photo_path) }}" target="_blank" class="shrink-0 group">
                                    <img src="{{ asset('images/damage_reports/' . $report->photo_path) }}" 
                                         alt="{{ $report->facility_name }}" 
                                         class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-xl border border-slate-200 group-hover:opacity-90 transition">
                                </a>
                            @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                        {{ $report->category }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        ID #{{ strtoupper(substr($report->id, 0, 8)) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        • {{ $report->created_at->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                                <h3 class="text-base font-bold text-slate-800 break-words">
                                    {{ $report->facility_name }}
                                </h3>

                                <div class="text-xs text-slate-500 mt-1 line-clamp-2">
                                    {{ $report->description }}
                                </div>

                                <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
                                    <span class="font-medium">Kamar:</span>
                                    <span class="font-semibold text-slate-800">{{ $report->room?->name ?? 'Kamar tidak tertaut' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-3 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                            <!-- Status Badge -->
                            <div>
                                {!! $report->status_badge !!}
                            </div>

                            <a href="{{ route('user.damage-reports.show', $report) }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition">
                                <span>Lihat Detail</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    @if($report->admin_response)
                        <div class="mt-4 pt-3 border-t border-slate-100 bg-slate-50 -mx-5 -mb-5 px-5 py-3 rounded-b-2xl">
                            <div class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5 mb-1">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Tanggapan Pengelola:</span>
                                @if($report->resolved_at)
                                    <span class="text-[10px] text-slate-400 font-normal">({{ $report->resolved_at->format('d M Y, H:i') }})</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed italic">
                                "{{ $report->admin_response }}"
                            </p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center shadow-sm">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Laporan Kerusakan</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                        Jika ada fasilitas kamar seperti lampu rusak, pipa bocor, atau perabot rusak, Anda dapat mengirim laporan kepada pengelola.
                    </p>
                    <a href="{{ route('user.damage-reports.create') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Buat Laporan Sekarang
                    </a>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
