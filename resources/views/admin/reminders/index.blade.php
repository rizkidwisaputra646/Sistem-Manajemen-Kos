<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800">Riwayat Pengingat Email</h2>
                <p class="text-xs text-gray-500 mt-1">Monitoring pengiriman otomatis notifikasi jatuh tempo pembayaran via email.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('admin.reminders.run') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-blue-700 shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Jalankan Pengingat Sekarang
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Email Berhasil Dikirim</p>
                    <h3 class="text-3xl font-extrabold text-emerald-600 mt-2">{{ number_format($totalSent) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Total notifikasi terkirim</p>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pengiriman Gagal</p>
                    <h3 class="text-3xl font-extrabold text-rose-600 mt-2">{{ number_format($totalFailed) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Periksa kredensial atau alamat email</p>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Terakhir Mengirim</p>
                    <h3 class="text-lg font-bold text-gray-800 mt-2">
                        {{ $lastSent ? $lastSent->sent_at->format('d M Y, H:i') : 'Belum Ada' }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Otomatisasi cron jam 08:00 WIB</p>
                </div>
            </div>

            <!-- Form Uji Coba Kirim Email Cepat -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Uji Coba Pengiriman Email (Test Email)
                </h3>
                <form action="{{ route('admin.reminders.test') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="email" name="test_email" required value="{{ auth()->user()->email }}"
                        placeholder="Masukkan alamat email penerima tes..."
                        class="flex-1 border-gray-300 rounded-lg text-sm px-4 py-2.5 focus:border-blue-500 outline-none">
                    <button type="submit"
                        class="bg-gray-800 text-white px-6 py-2.5 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-black transition-colors">
                        Kirim Email Tes
                    </button>
                </form>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.reminders.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari penerima, email, kamar, atau ID..."
                        class="border-gray-300 rounded-lg text-sm px-4 py-2 w-full md:w-72 focus:border-blue-500 outline-none">

                    <select name="type" class="border-gray-300 rounded-lg text-sm px-4 py-2 focus:border-blue-500 outline-none">
                        <option value="">Semua Tipe</option>
                        <option value="h-7" {{ request('type') == 'h-7' ? 'selected' : '' }}>H-7 Jatuh Tempo</option>
                        <option value="h-3" {{ request('type') == 'h-3' ? 'selected' : '' }}>H-3 Jatuh Tempo</option>
                        <option value="h-1" {{ request('type') == 'h-1' ? 'selected' : '' }}>H-1 Jatuh Tempo</option>
                        <option value="overdue" {{ request('type') == 'overdue' ? 'selected' : '' }}>Keterlambatan (Overdue)</option>
                        <option value="manual" {{ request('type') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>

                    <select name="status" class="border-gray-300 rounded-lg text-sm px-4 py-2 focus:border-blue-500 outline-none">
                        <option value="">Semua Status</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Terkirim</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>

                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-blue-700 transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.reminders.index') }}"
                        class="text-xs font-bold text-gray-400 hover:text-blue-600 underline">Reset</a>
                </form>
            </div>

            <!-- Table Logs -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] font-bold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                <th class="px-6 py-4">Waktu Kirim</th>
                                <th class="px-6 py-4">Penerima & Email</th>
                                <th class="px-6 py-4">Kamar & Pesanan</th>
                                <th class="px-6 py-4">Tipe Pengingat</th>
                                <th class="px-6 py-4">Jatuh Tempo</th>
                                <th class="px-6 py-4">Tagihan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-600 font-medium whitespace-nowrap">
                                        {{ $log->sent_at ? $log->sent_at->format('d M Y, H:i') : $log->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $log->recipient_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->recipient_email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-blue-600 uppercase">{{ $log->room_name }}</div>
                                        <div class="text-xs text-gray-400">#{{ strtoupper(substr($log->booking_id, 0, 8)) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->reminder_type === 'h-7')
                                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded text-[11px] font-bold uppercase">H-7</span>
                                        @elseif($log->reminder_type === 'h-3')
                                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[11px] font-bold uppercase">H-3</span>
                                        @elseif($log->reminder_type === 'h-1')
                                            <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded text-[11px] font-bold uppercase">H-1</span>
                                        @elseif($log->reminder_type === 'overdue')
                                            <span class="px-2.5 py-1 bg-red-100 text-red-800 border border-red-300 rounded text-[11px] font-bold uppercase">Overdue</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 border border-gray-200 rounded text-[11px] font-bold uppercase">Manual</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-700 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($log->due_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($log->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($log->status === 'sent')
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[11px] font-bold uppercase inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Terkirim
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded text-[11px] font-bold uppercase inline-flex items-center gap-1" title="{{ $log->error_message }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Gagal
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-medium">
                                        Belum ada riwayat pengiriman pengingat email.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
