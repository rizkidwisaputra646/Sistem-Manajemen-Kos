<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Simple Flat Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-gray-800">
                <div class="bg-white p-8 rounded-xl border-2 border-gray-100 shadow-sm">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">Total Pendapatan</p>
                    <h3 class="text-3xl font-black text-blue-600">Rp {{ number_format($totalRevenue) }}</h3>
                </div>

                <div class="bg-white p-8 rounded-xl border-2 border-gray-100 shadow-sm">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">Kamar Kosong</p>
                    <h3 class="text-3xl font-black text-green-600">{{ $availableRooms }} / {{ $totalRooms }}</h3>
                </div>

                <div class="bg-white p-8 rounded-xl border-2 border-gray-100 shadow-sm">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">Penghuni Aktif</p>
                    <h3 class="text-3xl font-black text-amber-600">{{ $filledRooms }} Kamar</h3>
                </div>
            </div>

            <!-- Simple Chart Section -->
            <div class="bg-white p-8 rounded-xl border-2 border-gray-100 shadow-sm mb-8">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h3 class="font-bold text-xl text-gray-800">Statistik Pendapatan</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Laporan 6 bulan terakhir</p>
                    </div>
                </div>
                <div class="h-[300px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Simple Recent Transactions Table -->
            <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="font-bold text-xl text-gray-800">Transaksi Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Monitoring pembayaran masuk secara real-time</p>
                    </div>

                    <a href="{{ route('admin.bookings.export') }}"
                        class="flex items-center gap-2 bg-white text-gray-700 px-6 py-3 rounded-lg font-bold text-xs uppercase tracking-widest border-2 border-gray-100 hover:bg-gray-50 transition-all shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Laporan
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                <th class="px-8 py-5">Nama Penyewa</th>
                                <th class="px-8 py-5">Unit Kamar</th>
                                <th class="px-8 py-5">Total Bayar</th>
                                <th class="px-8 py-5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentBookings as $booking)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-8 py-6">
                                        <p class="font-bold text-gray-800">{{ $booking->user->name }}</p>
                                        <p class="text-xs text-gray-400 font-medium">{{ $booking->user->email }}</p>
                                    </td>
                                    <td class="px-8 py-6 text-sm font-bold text-gray-600">
                                        {{ $booking->room->name }}
                                    </td>
                                    <td class="px-8 py-6 font-black text-blue-600 text-lg">
                                        Rp {{ number_format($booking->total_price) }}
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 rounded-md text-[10px] font-bold uppercase border 
                                            {{ $booking->status == 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-12 text-center text-gray-400 font-bold">Belum ada transaksi transaksi terbaru.</td>
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
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0,
                pointRadius: 4,
                pointBackgroundColor: '#2563eb',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        font: { size: 11, weight: 'bold' },
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                },
                x: { 
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: 'bold' } }
                }
            }
        }
    });
</script>