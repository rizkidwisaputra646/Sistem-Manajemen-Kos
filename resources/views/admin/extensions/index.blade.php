<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800">Persetujuan Perpanjangan Sewa</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola dan tinjau permintaan perpanjangan masa sewa kamar dari penghuni aktif.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-800">
                    <span class="w-2 h-2 rounded-full bg-blue-500 {{ ($counts['pending'] ?? 0) > 0 ? 'animate-ping' : '' }}"></span>
                    {{ $counts['pending'] ?? 0 }} Pengajuan Menunggu Persetujuan
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <!-- KPI Metric Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <a href="{{ route('admin.extensions.index') }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-blue-300 transition {{ !request()->filled('status') || request('status') == 'all' ? 'ring-2 ring-blue-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Pengajuan</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $counts['all'] ?? 0 }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Seluruh riwayat</div>
                </a>

                <a href="{{ route('admin.extensions.index', ['status' => 'pending']) }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-amber-300 transition {{ request('status') === 'pending' ? 'ring-2 ring-amber-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-amber-500 uppercase tracking-wider">Menunggu</div>
                    <div class="text-2xl font-bold text-amber-600 mt-1">{{ $counts['pending'] ?? 0 }}</div>
                    <div class="text-[11px] text-amber-600/70 mt-1">Perlu ditinjau</div>
                </a>

                <a href="{{ route('admin.extensions.index', ['status' => 'approved']) }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-blue-300 transition {{ request('status') === 'approved' ? 'ring-2 ring-blue-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Disetujui</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $counts['approved'] ?? 0 }}</div>
                    <div class="text-[11px] text-blue-600/70 mt-1">Menunggu bayar (1 jam)</div>
                </a>

                <a href="{{ route('admin.extensions.index', ['status' => 'paid']) }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-emerald-300 transition {{ request('status') === 'paid' ? 'ring-2 ring-emerald-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Lunas</div>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $counts['paid'] ?? 0 }}</div>
                    <div class="text-[11px] text-emerald-600/70 mt-1">Sewa diperpanjang</div>
                </a>

                <a href="{{ route('admin.extensions.index', ['status' => 'rejected']) }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-rose-300 transition {{ request('status') === 'rejected' ? 'ring-2 ring-rose-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Ditolak</div>
                    <div class="text-2xl font-bold text-rose-600 mt-1">{{ $counts['rejected'] ?? 0 }}</div>
                    <div class="text-[11px] text-rose-600/70 mt-1">Tidak disetujui</div>
                </a>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.extensions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Penghuni atau Kamar</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Nama penghuni, email, atau nama kamar..."
                               class="w-full border-gray-300 rounded-lg text-xs px-3.5 py-2.5 focus:border-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status Pengajuan</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg text-xs px-3.5 py-2.5 focus:border-blue-500 outline-none bg-white">
                            <option value="all">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Menunggu Bayar)</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas / Selesai</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition shadow-sm">
                            Terapkan Filter
                        </button>
                        @if(request()->filled('search') || (request()->filled('status') && request('status') !== 'all'))
                            <a href="{{ route('admin.extensions.index') }}" 
                               class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-bold transition"
                               title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Data Pengajuan Perpanjangan -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-3.5">ID / Tanggal</th>
                                <th class="px-5 py-3.5">Penghuni</th>
                                <th class="px-5 py-3.5">Kamar</th>
                                <th class="px-5 py-3.5">Periode Berjalan</th>
                                <th class="px-5 py-3.5">Periode Lanjutan (+1 Bulan)</th>
                                <th class="px-5 py-3.5">Nominal</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-center">Aksi Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($extensions as $ext)
                                <tr class="hover:bg-gray-50/80 transition {{ $ext->status === 'pending' ? 'bg-amber-50/30' : '' }}">
                                    <!-- ID & Waktu Pengajuan -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 font-mono">
                                            #EXT-{{ $ext->id }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $ext->created_at->format('d/m/Y H:i') }} WIB
                                        </div>
                                    </td>

                                    <!-- Penghuni -->
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-gray-900">
                                            {{ $ext->user?->name ?? 'User Terhapus' }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 truncate max-w-xs">
                                            {{ $ext->user?->email }}
                                        </div>
                                    </td>

                                    <!-- Kamar -->
                                    <td class="px-5 py-4 whitespace-nowrap font-bold text-blue-600">
                                        {{ $ext->room?->name ?? 'Kamar Tidak Ditemukan' }}
                                    </td>

                                    <!-- Periode Lama -->
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-500">
                                        <span>Checkout: </span>
                                        <span class="font-semibold text-gray-800">
                                            {{ \Carbon\Carbon::parse($ext->previous_check_out_date)->format('d M Y') }}
                                        </span>
                                    </td>

                                    <!-- Periode Baru -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-emerald-700">
                                            {{ \Carbon\Carbon::parse($ext->new_check_in_date)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($ext->new_check_out_date)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            Durasi: 1 Bulan Penuh
                                        </div>
                                    </td>

                                    <!-- Nominal Tagihan -->
                                    <td class="px-5 py-4 whitespace-nowrap font-bold text-gray-900">
                                        Rp {{ number_format($ext->price, 0, ',', '.') }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        {!! $ext->status_badge !!}
                                        @if($ext->status === 'approved' && $ext->latestPayment)
                                            <div class="text-[10px] text-blue-600 mt-1 font-semibold">
                                                Batas bayar: {{ $ext->latestPayment->payment_expires_at?->format('H:i') }} WIB
                                            </div>
                                        @elseif($ext->status === 'rejected' && $ext->admin_notes)
                                            <div class="text-[10px] text-rose-600 mt-1 max-w-xs truncate" title="{{ $ext->admin_notes }}">
                                                Alasan: {{ $ext->admin_notes }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Tombol Aksi -->
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        @if($ext->status === 'pending')
                                            <div class="flex items-center justify-center gap-1.5">
                                                <!-- Tombol Setujui -->
                                                <form action="{{ route('admin.extensions.approve', $ext) }}" method="POST" 
                                                      onsubmit="return confirmApprove(event, this, '{{ $ext->room?->name }}', '{{ $ext->user?->name }}')">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition shadow-sm cursor-pointer"
                                                            title="Setujui perpanjangan dan terbitkan tagihan Midtrans">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Setujui
                                                    </button>
                                                </form>

                                                <!-- Tombol Tolak -->
                                                <button type="button" 
                                                        onclick="promptReject('{{ route('admin.extensions.reject', $ext) }}', '{{ $ext->user?->name }}')"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-lg transition border border-rose-200 cursor-pointer"
                                                        title="Tolak perpanjangan">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Tolak
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-gray-400 italic">Telah diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">
                                        Belum ada pengajuan perpanjangan sewa yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $extensions->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Hidden Form untuk Rejection dengan SweetAlert Input -->
    <form id="reject-form" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="admin_notes" id="reject-notes-input">
    </form>

    <script>
        function confirmApprove(e, form, roomName, userName) {
            e.preventDefault();
            Swal.fire({
                title: 'Setujui Perpanjangan Sewa?',
                text: `Anda akan menyetujui perpanjangan sewa ${roomName} untuk penghuni ${userName}. Sistem akan mengunci kamar dan menerbitkan tagihan pembayaran (Batas bayar: 1 jam).`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Setujui & Terbitkan Tagihan',
                cancelButtonText: 'Batal',
                borderRadius: '1rem'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        function promptReject(actionUrl, userName) {
            Swal.fire({
                title: 'Tolak Perpanjangan Sewa?',
                text: `Berikan alasan penolakan untuk penghuni ${userName}:`,
                input: 'textarea',
                inputPlaceholder: 'Contoh: Kamar akan direnovasi / Masa sewa maksimal telah terpenuhi...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Tolak Pengajuan',
                cancelButtonText: 'Batal',
                borderRadius: '1rem',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan penolakan wajib diisi agar penghuni paham alasannya!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('reject-form');
                    form.action = actionUrl;
                    document.getElementById('reject-notes-input').value = result.value;
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
