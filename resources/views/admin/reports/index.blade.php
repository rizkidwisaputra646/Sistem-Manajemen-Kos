<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 italic underline decoration-indigo-500">Laporan Keuangan 💰</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
                <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-end gap-6">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl text-sm focus:ring-indigo-500">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl text-sm focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                        Tampilkan Laporan
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-emerald-500 p-8 rounded-[3rem] text-white shadow-xl shadow-emerald-100 border-4 border-white">
                    <p class="text-emerald-100 text-[10px] font-black uppercase tracking-widest">Total Pemasukan Bersih</p>
                    <h3 class="text-4xl font-black mt-2 italic">Rp {{ number_format($totalRevenue) }}</h3>
                    <p class="mt-4 text-[10px] font-bold text-emerald-100 italic italic">Periode: {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</p>
                </div>

                <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Jumlah Transaksi</p>
                        <h3 class="text-4xl font-black mt-2 text-gray-800 italic">{{ $totalTransactions }} <span class="text-lg text-gray-300">Selesai</span></h3>
                    </div>
                    <div class="text-5xl opacity-20">📊</div>
                </div>
            </div>

            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-black text-xl text-gray-800 italic italic">Rincian Transaksi 🧾</h3>
                    <button onclick="window.print()" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest italic">
                        🖨️ Cetak Halaman
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <th class="px-8 py-5">Tanggal Lunas</th>
                                <th class="px-8 py-5">Penyewa</th>
                                <th class="px-8 py-5">Kamar</th>
                                <th class="px-8 py-5 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($reports as $report)
                            <tr class="hover:bg-gray-50/50 transition-colors italic text-sm">
                                <td class="px-8 py-5 text-gray-400 font-bold">
                                    {{ $report->updated_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-8 py-5 font-black text-gray-800">
                                    {{ $report->user->name }}
                                </td>
                                <td class="px-8 py-5 font-bold text-indigo-500 uppercase tracking-tighter">
                                    {{ $report->room->name }}
                                </td>
                                <td class="px-8 py-5 text-right font-black text-gray-900">
                                    Rp {{ number_format($report->total_price) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center text-gray-400 italic font-bold">Tidak ada pemasukan pada periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($reports->count() > 0)
                        <tfoot>
                            <tr class="bg-indigo-50/50">
                                <td colspan="3" class="px-8 py-5 text-right font-black text-indigo-600 uppercase italic">Total Keseluruhan</td>
                                <td class="px-8 py-5 text-right font-black text-indigo-600 text-xl italic">Rp {{ number_format($totalRevenue) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <div class="mt-8 px-4">
                {{ $reports->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</x-app-layout>