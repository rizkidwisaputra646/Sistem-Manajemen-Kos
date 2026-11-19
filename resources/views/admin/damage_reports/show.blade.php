<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.damage-reports.index') }}" 
               class="p-2 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-gray-800 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800">Detail & Tanggapan Laporan Kerusakan</h2>
                <p class="text-xs text-gray-500">ID Laporan: #{{ strtoupper(substr($report->id, 0, 8)) }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kolom Kiri: Info Detail Laporan & Penghuni (2 col) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Kartu Info Utama Kerusakan -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-100">
                            <div>
                                <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded bg-slate-100 text-slate-700">
                                    {{ $report->category }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-900 mt-1">
                                    {{ $report->facility_name }}
                                </h3>
                            </div>
                            <div>
                                {!! $report->status_badge !!}
                            </div>
                        </div>

                        <!-- Deskripsi Laporan -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Kerusakan dari Penghuni</h4>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs text-gray-800 leading-relaxed whitespace-pre-line">
                                {{ $report->description }}
                            </div>
                        </div>

                        <!-- Foto Kerusakan -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Foto Bukti Kerusakan</h4>
                            @if($report->photo_path)
                                <div class="border border-gray-200 rounded-xl p-3 bg-gray-50">
                                    <a href="{{ asset('images/damage_reports/' . $report->photo_path) }}" target="_blank" class="block group text-center">
                                        <img src="{{ asset('images/damage_reports/' . $report->photo_path) }}" 
                                             alt="Foto Kerusakan" 
                                             class="max-h-96 rounded-lg object-contain mx-auto group-hover:opacity-90 transition shadow-sm">
                                        <p class="text-xs text-blue-600 font-semibold mt-2 group-hover:underline">
                                            🔍 Buka foto ukuran penuh di tab baru
                                        </p>
                                    </a>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-6 text-center text-xs text-gray-400">
                                    Penghuni tidak menyertakan foto pada laporan ini.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Kartu Riwayat Tanggapan Sebelumnya jika ada -->
                    @if($report->admin_response)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Tanggapan Terakhir yang Ditampilkan ke Penghuni</h4>
                            <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-4 text-xs text-gray-800 space-y-2">
                                <div class="flex items-center justify-between text-[11px] text-blue-700 font-semibold">
                                    <span>Oleh: {{ $report->admin?->name ?? 'Admin' }}</span>
                                    @if($report->resolved_at)
                                        <span>{{ $report->resolved_at->format('d M Y, H:i') }} WIB</span>
                                    @endif
                                </div>
                                <p class="leading-relaxed whitespace-pre-line text-gray-700">
                                    {{ $report->admin_response }}
                                </p>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Kolom Kanan: Identitas Pelapor & Form Tindak Lanjut (1 col) -->
                <div class="space-y-6">

                    <!-- Kartu Info Pelapor & Kamar -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 pb-2">
                            Identitas Penghuni & Kamar
                        </h4>

                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="block text-[11px] text-gray-400 font-medium">Nama Penghuni</span>
                                <span class="block font-bold text-gray-900 mt-0.5">
                                    {{ $report->user?->name ?? 'User Tidak Ditemukan' }}
                                </span>
                            </div>

                            <div>
                                <span class="block text-[11px] text-gray-400 font-medium">Email Penghuni</span>
                                <span class="block font-bold text-gray-900 mt-0.5">
                                    {{ $report->user?->email ?? '-' }}
                                </span>
                            </div>

                            <div>
                                <span class="block text-[11px] text-gray-400 font-medium">Kamar Terkait</span>
                                <span class="block font-bold text-blue-600 mt-0.5">
                                    {{ $report->room?->name ?? 'Tidak terhubung ke kamar' }}
                                </span>
                            </div>

                            <div>
                                <span class="block text-[11px] text-gray-400 font-medium">Waktu Pengajuan</span>
                                <span class="block font-semibold text-gray-700 mt-0.5">
                                    {{ $report->created_at->format('d F Y, H:i') }} WIB
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Update Status & Tanggapan -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Tindak Lanjut & Update Status
                        </h4>

                        <form action="{{ route('admin.damage-reports.update', $report) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="status" class="block text-xs font-bold text-gray-700 mb-1.5">
                                    Pilih Status Baru <span class="text-rose-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                        class="w-full text-xs border-gray-300 rounded-lg px-3.5 py-2.5 focus:border-blue-500 focus:ring-blue-500 bg-white">
                                    @foreach($statuses as $st)
                                        <option value="{{ $st }}" {{ old('status', $report->status) === $st ? 'selected' : '' }}>
                                            {{ $st }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="admin_response" class="block text-xs font-bold text-gray-700 mb-1.5">
                                    Tanggapan / Instruksi untuk Penghuni
                                </label>
                                <textarea name="admin_response" id="admin_response" rows="5"
                                          placeholder="Contoh: Teknisi akan datang memeriksa pada hari Selasa pukul 10.00 WIB. Mohon pastikan kamar dapat diakses..."
                                          class="w-full text-xs border-gray-300 rounded-lg px-3.5 py-2.5 focus:border-blue-500 focus:ring-blue-500">{{ old('admin_response', $report->admin_response) }}</textarea>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    Tanggapan ini akan langsung tampil pada riwayat laporan di akun penghuni.
                                </p>
                                @error('admin_response')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan Status
                            </button>
                        </form>
                    </div>

                    <!-- Tombol Hapus Laporan -->
                    <div class="text-right">
                        <form action="{{ route('admin.damage-reports.destroy', $report) }}" method="POST"
                              onsubmit="return confirmDelete(event, this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-xs font-semibold text-rose-500 hover:text-rose-700 underline">
                                Hapus Laporan Ini Secara Permanen
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>
        function confirmDelete(e, form) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Laporan Kerusakan?',
                text: 'Data laporan ini dan foto terkait akan dihapus secara permanen.',
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
