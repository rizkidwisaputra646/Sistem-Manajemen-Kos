<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800">Pelaporan Kerusakan Fasilitas</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola dan tindaklanjuti seluruh aduan kerusakan dari penghuni kontrakan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    {{ $statusCounts['Menunggu'] ?? 0 }} Aduan Perlu Ditinjau
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
                            title: 'Terjadi Kesalahan',
                            text: "{{ session('error') }}",
                            borderRadius: '1rem'
                        });
                    });
                </script>
            @endif

            <!-- KPI Metric Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <a href="{{ route('admin.damage-reports.index') }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-blue-300 transition {{ empty($status) ? 'ring-2 ring-blue-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Aduan</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $statusCounts['total'] ?? 0 }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Seluruh laporan</div>
                </a>

                <a href="{{ route('admin.damage-reports.index', ['status' => 'Menunggu']) }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-amber-300 transition {{ $status === 'Menunggu' ? 'ring-2 ring-amber-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-amber-500 uppercase tracking-wider">Menunggu</div>
                    <div class="text-2xl font-bold text-amber-600 mt-1">{{ $statusCounts['Menunggu'] ?? 0 }}</div>
                    <div class="text-[11px] text-amber-600/70 mt-1">Belum diproses</div>
                </a>

                <a href="{{ route('admin.damage-reports.index', ['status' => 'Diproses']) }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-blue-300 transition {{ $status === 'Diproses' ? 'ring-2 ring-blue-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Diproses</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $statusCounts['Diproses'] ?? 0 }}</div>
                    <div class="text-[11px] text-blue-600/70 mt-1">Dalam perbaikan</div>
                </a>

                <a href="{{ route('admin.damage-reports.index', ['status' => 'Selesai']) }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-emerald-300 transition {{ $status === 'Selesai' ? 'ring-2 ring-emerald-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Selesai</div>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $statusCounts['Selesai'] ?? 0 }}</div>
                    <div class="text-[11px] text-emerald-600/70 mt-1">Tuntas diperbaiki</div>
                </a>

                <a href="{{ route('admin.damage-reports.index', ['status' => 'Ditolak']) }}" 
                   class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-rose-300 transition {{ $status === 'Ditolak' ? 'ring-2 ring-rose-500/20' : '' }}">
                    <div class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Ditolak</div>
                    <div class="text-2xl font-bold text-rose-600 mt-1">{{ $statusCounts['Ditolak'] ?? 0 }}</div>
                    <div class="text-[11px] text-rose-600/70 mt-1">Tidak memenuhi syarat</div>
                </a>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <form action="{{ route('admin.damage-reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Laporan</label>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Nama penghuni, fasilitas, kamar..."
                               class="w-full border-gray-300 rounded-lg text-xs px-3.5 py-2.5 focus:border-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg text-xs px-3.5 py-2.5 focus:border-blue-500 outline-none bg-white">
                            <option value="">Semua Status</option>
                            <option value="Menunggu" {{ $status === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="Diproses" {{ $status === 'Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Selesai" {{ $status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Ditolak" {{ $status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kategori Kerusakan</label>
                        <select name="category" class="w-full border-gray-300 rounded-lg text-xs px-3.5 py-2.5 focus:border-blue-500 outline-none bg-white">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition shadow-sm">
                            Terapkan Filter
                        </button>
                        @if(!empty($search) || !empty($status) || !empty($category))
                            <a href="{{ route('admin.damage-reports.index') }}" 
                               class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-bold transition"
                               title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Laporan Kerusakan -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-3.5">ID / Tanggal</th>
                                <th class="px-5 py-3.5">Penghuni</th>
                                <th class="px-5 py-3.5">Kamar</th>
                                <th class="px-5 py-3.5">Fasilitas / Kategori</th>
                                <th class="px-5 py-3.5">Foto</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($reports as $report)
                                <tr class="hover:bg-gray-50/80 transition {{ !$report->is_read_by_admin ? 'bg-amber-50/20' : '' }}">
                                    <!-- ID & Tanggal -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 font-mono">
                                            #{{ strtoupper(substr($report->id, 0, 8)) }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $report->created_at->format('d/m/Y H:i') }} WIB
                                        </div>
                                    </td>

                                    <!-- Penghuni -->
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-gray-900">
                                            {{ $report->user?->name ?? 'User Terhapus' }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 truncate max-w-xs">
                                            {{ $report->user?->email }}
                                        </div>
                                    </td>

                                    <!-- Kamar -->
                                    <td class="px-5 py-4 whitespace-nowrap font-semibold text-gray-800">
                                        {{ $report->room?->name ?? 'Tidak terdata' }}
                                    </td>

                                    <!-- Fasilitas & Kategori -->
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-gray-900">
                                            {{ $report->facility_name }}
                                        </div>
                                        <div class="mt-0.5">
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700">
                                                {{ $report->category }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Thumbnail Foto -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($report->photo_path)
                                            <a href="{{ asset('images/damage_reports/' . $report->photo_path) }}" target="_blank" class="block group">
                                                <img src="{{ asset('images/damage_reports/' . $report->photo_path) }}" 
                                                     alt="Foto" 
                                                     class="w-10 h-10 object-cover rounded-lg border border-gray-200 group-hover:opacity-80 transition">
                                            </a>
                                        @else
                                            <span class="text-[11px] text-gray-400 italic">Tanpa foto</span>
                                        @endif
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        {!! $report->status_badge !!}
                                    </td>

                                    <!-- Tombol Aksi -->
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.damage-reports.show', $report) }}" 
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs rounded-lg transition"
                                               title="Buka detail dan perbarui status">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Detail & Tanggapi
                                            </a>

                                            <form action="{{ route('admin.damage-reports.destroy', $report) }}" method="POST" 
                                                  onsubmit="return confirmDelete(event, this)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition"
                                                        title="Hapus Laporan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">
                                        Tidak ada laporan kerusakan yang sesuai kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $reports->links() }}
                </div>
            </div>

        </div>
    </div>

    <script>
        function confirmDelete(e, form) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Laporan?',
                text: 'Laporan kerusakan ini akan dihapus secara permanen beserta fotonya.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                borderRadius: '1rem'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
    </script>
</x-app-layout>
