<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 italic underline decoration-indigo-500">Data Reservasi 📑</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div
                class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 mb-8 flex flex-wrap items-center justify-between gap-4">
                <form action="{{ route('admin.reservations.index') }}" method="GET"
                    class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama penyewa..."
                        class="border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 w-full md:w-64">

                    <select name="status"
                        class="border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>

                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all">Filter</button>
                    <a href="{{ route('admin.reservations.index') }}"
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-indigo-600">Reset</a>
                </form>

                <a href="{{ route('admin.bookings.export') }}"
                    class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-5 py-2.5 rounded-xl hover:bg-emerald-600 hover:text-white transition-all uppercase tracking-widest border border-emerald-100 shadow-sm">
                    📊 Export CSV
                </a>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <th class="px-8 py-5">Penyewa & Kamar</th>
                                <th class="px-8 py-5">Durasi Sewa</th>
                                <th class="px-8 py-5">Total Bayar</th>
                                <th class="px-8 py-5">Status</th>
                                <th class="px-8 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($bookings as $booking)
                                <tr class="hover:bg-gray-50/50 transition-colors italic">
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-900">{{ $booking->user->name }}</span>
                                            <span
                                                class="text-[11px] text-indigo-500 font-bold uppercase tracking-tighter">{{ $booking->room->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="text-[11px] text-gray-500 font-medium">
                                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="font-black text-gray-800">Rp
                                            {{ number_format($booking->total_price) }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span
                                            class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $booking->status == 'success' ? 'bg-green-100 text-green-600' : ($booking->status == 'pending' ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600') }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-center flex justify-center gap-2">
                                        <a href="{{ route('user.bookings.show', $booking) }}" target="_blank"
                                            class="p-2 bg-gray-50 text-gray-400 hover:text-indigo-600 rounded-xl transition shadow-sm border border-gray-100">👁️</a>

                                        @if ($booking->status == 'pending')
                                            <form id="approve-form-{{ $booking->id }}"
                                                action="{{ route('admin.reservations.update-status', $booking) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="success">
                                                <button type="button" onclick="confirmApprove('{{ $booking->id }}')"
                                                    class="p-2 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-xl transition shadow-sm border border-green-100">
                                                    ✔️
                                                </button>
                                            </form>
                                        @endif

                                        @if ($booking->status != 'cancelled')
                                            <form id="cancel-form-{{ $booking->id }}"
                                                action="{{ route('admin.reservations.cancel', $booking) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button"
                                                    onclick="confirmAdminCancel('{{ $booking->id }}')"
                                                    class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition shadow-sm border border-rose-100">
                                                    ❌
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center text-gray-400 italic font-bold">
                                        <span class="text-4xl block mb-2">🔍</span>
                                        Data reservasi tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- SCRIPT SWEETALERT KHUSUS ADMIN --}}
<script>
    // 1. Konfirmasi Approve Pembayaran
    function confirmApprove(id) {
        Swal.fire({
            title: 'Konfirmasi Bayar?',
            text: "Pastikan uang sudah kamu terima secara tunai/transfer!",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981', // Emerald
            cancelButtonColor: '#6366f1',
            confirmButtonText: 'Ya, Sudah Lunas!',
            cancelButtonText: 'Batal',
            borderRadius: '2rem'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approve-form-' + id).submit();
            }
        });
    }

    // 2. Konfirmasi Pembatalan Paksa oleh Admin
    function confirmAdminCancel(id) {
        Swal.fire({
            title: 'Batalkan Pesanan?',
            text: "Pesanan akan dihentikan dan kamar akan otomatis tersedia kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e', // Rose
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Jangan Jadi',
            borderRadius: '2rem'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + id).submit();
            }
        });
    }
</script>
