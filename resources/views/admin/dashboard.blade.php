<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight italic">
            Dashboard Admin 🏢
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-white">
                <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-xl shadow-indigo-100 border-4 border-white">
                    <p class="text-indigo-100 text-[10px] font-black uppercase tracking-widest">Total Pendapatan</p>
                    <h3 class="text-3xl font-black mt-1 italic">Rp {{ number_format($totalRevenue) }}</h3>
                </div>

                <div class="bg-emerald-500 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-100 border-4 border-white">
                    <p class="text-emerald-100 text-[10px] font-black uppercase tracking-widest">Kamar Kosong</p>
                    <h3 class="text-3xl font-black mt-1 italic">{{ $availableRooms }} / {{ $totalRooms }}</h3>
                </div>

                <div class="bg-amber-500 p-8 rounded-[2.5rem] shadow-xl shadow-amber-100 border-4 border-white">
                    <p class="text-amber-100 text-[10px] font-black uppercase tracking-widest">Penghuni Aktif</p>
                    <h3 class="text-3xl font-black mt-1 italic">{{ $filledRooms }} Kamar</h3>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-xl text-gray-800 italic underline decoration-indigo-500">Statistik Pendapatan 📈</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase italic tracking-widest text-right">6 Bulan Terakhir</p>
                </div>
                <div class="h-[300px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h3 class="font-black text-xl text-gray-800 italic underline decoration-indigo-500">Transaksi Terbaru 💸</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mt-1 italic tracking-widest">Data pembayaran lunas</p>
                    </div>

                    <a href="{{ route('admin.bookings.export') }}"
                        class="group flex items-center gap-2 bg-emerald-50 text-emerald-600 px-5 py-2.5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all duration-300 shadow-sm border border-emerald-100">
                        <span class="text-sm group-hover:scale-125 transition-transform">📊</span>
                        Download Laporan (Excel/CSV)
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <th class="px-8 py-4">Penyewa</th>
                                <th class="px-8 py-4">Kamar</th>
                                <th class="px-8 py-4">Total</th>
                                <th class="px-8 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentBookings as $booking)
                                <tr class="hover:bg-gray-50 transition-colors italic">
                                    <td class="px-8 py-5">
                                        <p class="font-bold text-gray-800">{{ $booking->user->name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $booking->user->email }}</p>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-bold text-gray-600">
                                        {{ $booking->room->name }}
                                    </td>
                                    <td class="px-8 py-5 font-black text-indigo-600">
                                        Rp {{ number_format($booking->total_price) }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $booking->status == 'success' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-10 text-center text-gray-400 italic">Belum ada transaksi masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthLabels) !!},
            datasets: [{
                label: 'Pendapatan',
                data: {!! json_encode($monthlyRevenue) !!},
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 4,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>