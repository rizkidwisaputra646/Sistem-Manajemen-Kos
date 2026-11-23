<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800">Data Reservasi</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Simple Filter Bar -->
            <div
                class="bg-white p-6 rounded-xl border-2 border-gray-100 shadow-sm mb-8 flex flex-wrap items-center justify-between gap-4">
                <form action="{{ route('admin.reservations.index') }}" method="GET"
                    class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, email, kamar, atau ID..."
                        class="border-gray-300 rounded-lg text-sm focus:border-blue-500 px-4 py-2 w-full md:w-72 outline-none">

                    <select name="status"
                        class="border-gray-300 rounded-lg text-sm focus:border-blue-500 px-4 py-2 outline-none">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>

                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-blue-700">Filter</button>
                    <a href="{{ route('admin.reservations.index') }}"
                        class="text-xs font-bold text-gray-400 hover:text-blue-600 underline">Reset</a>
                </form>

                <a href="{{ route('admin.bookings.export') }}"
                    class="text-xs font-bold text-gray-700 bg-white px-5 py-2.5 rounded-lg border-2 border-gray-100 hover:bg-gray-50 uppercase tracking-widest shadow-sm">
                    📊 Export Excel
                </a>
            </div>

            <!-- Plain Table -->
            <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-gray-50 text-[11px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                <th class="px-8 py-5">Penyewa & Unit</th>
                                <th class="px-8 py-5">Waktu Sewa</th>
                                <th class="px-8 py-5">Total</th>
                                <th class="px-8 py-5">Status</th>
                                <th class="px-8 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($bookings as $booking)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900">{{ $booking->user->name }}</span>
                                            <span
                                                class="text-xs text-blue-600 font-bold uppercase tracking-tight">{{ $booking->room->name }}</span>
                                            <div class="mt-1">
                                                @if($booking->payment_method === 'manual')
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                        Bayar Tunai / Manual
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                                        Online (Midtrans)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-gray-600 font-medium">
                                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="font-bold text-gray-800">Rp
                                            {{ number_format($booking->total_price) }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span
                                            class="px-3 py-1.5 rounded-md text-[10px] font-bold uppercase border 
                                            {{ $booking->status == 'success' ? 'bg-green-50 text-green-700 border-green-200' : ($booking->status == 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-red-50 text-red-700 border-red-200') }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-center flex justify-center gap-2">
                                        <a href="{{ route('user.bookings.show', $booking) }}" target="_blank"
                                            class="p-2.5 bg-white text-gray-400 hover:text-blue-600 border-2 border-gray-100 rounded-lg transition shadow-sm"
                                            title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>

                                        @if ($booking->status == 'success')
                                            <a href="{{ route('admin.reservations.receipt', $booking) }}" target="_blank"
                                                class="p-2.5 bg-indigo-50 text-indigo-600 border-2 border-indigo-100 hover:bg-indigo-600 hover:text-white rounded-lg transition shadow-sm"
                                                title="Cetak & Unduh Kuitansi PDF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                    </path>
                                                </svg>
                                            </a>
                                        @endif

                                        @if ($booking->status == 'pending')
                                            <form action="{{ route('admin.reminders.send-single', $booking) }}" method="POST" class="inline" title="Kirim Pengingat Tagihan">
                                                @csrf
                                                <button type="submit" 
                                                    class="p-2.5 bg-blue-50 text-blue-600 border-2 border-blue-100 hover:bg-blue-600 hover:text-white rounded-lg transition shadow-sm"
                                                    title="Kirim Email Pengingat Tagihan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($booking->status == 'pending')
                                            <form id="approve-form-{{ $booking->id }}"
                                                action="{{ route('admin.reservations.update-status', $booking) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="success">
                                                <button type="button" onclick="confirmApprove('{{ $booking->id }}')"
                                                    class="p-2.5 bg-green-50 text-green-600 border-2 border-green-100 hover:bg-green-600 hover:text-white rounded-lg transition shadow-sm">
                                                    ✅
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
                                                    class="p-2.5 bg-red-50 text-red-600 border-2 border-red-100 hover:bg-red-600 hover:text-white rounded-lg transition shadow-sm">
                                                    ❌
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center text-gray-400 font-bold">
                                        Data reservasi tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 border-t border-gray-100">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function confirmApprove(id) {
        Swal.fire({
            title: 'Sahkan Pembayaran?',
            text: "Pastikan dana telah diterima sebelum melakukan konfirmasi.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Sahkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approve-form-' + id).submit();
            }
        });
    }

    function confirmAdminCancel(id) {
        Swal.fire({
            title: 'Batalkan Reservasi?',
            text: "Pesanan akan dibatalkan dan kamar akan tersedia kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Jangan'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + id).submit();
            }
        });
    }
</script>
