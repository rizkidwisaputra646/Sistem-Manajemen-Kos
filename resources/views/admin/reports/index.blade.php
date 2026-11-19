<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800">Laporan & Pelaporan Transaksi</h2>
                <p class="text-xs text-gray-500 mt-1">Laporan menyeluruh transaksi pembayaran, pemesanan kamar, dan total pendapatan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol Cetak / Buka PDF di Tab Baru -->
                <a href="{{ route('admin.reports.pdf', array_merge(request()->query(), ['stream' => 1])) }}" target="_blank"
                    class="bg-white text-gray-700 border-2 border-gray-200 px-4 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-gray-50 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    🖨️ Cetak PDF
                </a>
                <!-- Tombol Download PDF Langsung -->
                <a href="{{ route('admin.reports.pdf', request()->query()) }}"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-blue-700 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Panel -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
                
                <!-- Quick Period Filter Buttons (Hari Ini, Mingguan, Bulanan, Tahunan, Semua Waktu) -->
                <div class="flex flex-wrap items-center gap-2 pb-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2">Filter Cepat:</span>
                    <a href="{{ route('admin.reports.index', array_merge(request()->except(['page', 'start_date', 'end_date']), ['period' => 'today'])) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === 'today' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->except(['page', 'start_date', 'end_date']), ['period' => 'weekly'])) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === 'weekly' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Mingguan (Minggu Ini)
                    </a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->except(['page', 'start_date', 'end_date']), ['period' => 'monthly'])) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === 'monthly' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Bulanan (Bulan Ini)
                    </a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->except(['page', 'start_date', 'end_date']), ['period' => 'yearly'])) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === 'yearly' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Tahunan (Tahun Ini)
                    </a>
                    <a href="{{ route('admin.reports.index', array_merge(request()->except(['page', 'start_date', 'end_date']), ['period' => 'all'])) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $period === 'all' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semua Waktu
                    </a>
                </div>

                <!-- Custom Date & Details Filter Form -->
                <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                    <input type="hidden" name="period" value="custom">

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                            class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 focus:border-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                            class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 focus:border-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status Pembayaran</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 focus:border-blue-500 outline-none">
                            <option value="">Semua Status</option>
                            <option value="success" {{ $statusFilter === 'success' ? 'selected' : '' }}>Success (Lunas)</option>
                            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                            <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled (Batal)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pencarian Data</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Penyewa, kamar, ID..."
                            class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 focus:border-blue-500 outline-none">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-blue-700 transition">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.reports.index') }}" 
                            class="py-2 px-3 text-xs font-bold text-gray-400 hover:text-blue-600 underline">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Ringkasan KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Transaksi & Pemesanan -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Transaksi</span>
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-lg text-xs">📋</span>
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-800">{{ number_format($totalTransactions) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($totalBookings) }} pemesanan kamar tercatat</p>
                </div>

                <!-- Pembayaran Berhasil -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pembayaran Berhasil</span>
                        <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg text-xs">✅</span>
                    </div>
                    <h3 class="text-3xl font-extrabold text-emerald-600">{{ number_format($successTransactions) }}</h3>
                    <p class="text-xs text-emerald-700 mt-1">Transaksi terverifikasi lunas</p>
                </div>

                <!-- Menunggu / Pending -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Menunggu Bayar</span>
                        <span class="p-2 bg-amber-50 text-amber-600 rounded-lg text-xs">⏳</span>
                    </div>
                    <h3 class="text-3xl font-extrabold text-amber-600">{{ number_format($pendingTransactions) }}</h3>
                    <p class="text-xs text-amber-700 mt-1">{{ number_format($cancelledTransactions) }} dibatalkan</p>
                </div>

                <!-- Total Pendapatan Valid -->
                <div class="bg-white p-5 rounded-xl border-2 border-emerald-500 shadow-sm bg-gradient-to-br from-white to-emerald-50/30">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Total Pendapatan</span>
                        <span class="p-2 bg-emerald-600 text-white rounded-lg text-xs font-bold">Rp</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-700">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-emerald-600 mt-1 font-semibold">*Hanya dari status lunas (success)</p>
                </div>
            </div>

            <!-- Tabel Rincian Seluruh Transaksi & Pemesanan Kamar -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-base text-gray-800">Rincian Transaksi & Pemesanan Kamar</h3>
                        <p class="text-xs text-gray-500">
                            Periode: 
                            @if($startDate && $endDate)
                                <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($endDate)) }}</strong>
                            @else
                                <strong>Semua Waktu</strong>
                            @endif
                        </p>
                    </div>
                    <div class="text-xs text-gray-500 font-semibold">
                        Menampilkan {{ $reports->total() }} data
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] font-bold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                <th class="px-6 py-4">ID Pesanan</th>
                                <th class="px-6 py-4">Waktu Pesan</th>
                                <th class="px-6 py-4">Penyewa</th>
                                <th class="px-6 py-4">Kamar</th>
                                <th class="px-6 py-4">Periode Sewa</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Total Biaya</th>
                                <th class="px-6 py-4 text-center">Kuitansi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($reports as $report)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-mono font-bold text-gray-800">
                                        #{{ strtoupper(substr($report->id, 0, 8)) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $report->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $report->user->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $report->user->email ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-blue-600 uppercase text-xs">
                                            {{ $report->room->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($report->check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($report->check_out_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase border
                                            {{ $report->status == 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($report->status == 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                            {{ $report->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900">
                                        Rp {{ number_format($report->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($report->status == 'success')
                                            <a href="{{ route('admin.reservations.receipt', $report) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white rounded border border-indigo-200 text-xs font-bold transition shadow-sm"
                                                title="Cetak & Unduh Kuitansi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                PDF
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium">
                                        Tidak ada transaksi atau pemesanan yang sesuai dengan kriteria filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($reports->count() > 0)
                            <tfoot>
                                <tr class="bg-gray-50/80 border-t-2 border-gray-200">
                                    <td colspan="6" class="px-6 py-4 text-right font-extrabold text-gray-700 uppercase text-xs">
                                        Total Pendapatan Sah (Status Success):
                                    </td>
                                    <td class="px-6 py-4 text-right font-extrabold text-emerald-600 text-lg">
                                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
