<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800">Data Penghuni Aktif</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-8 rounded-xl mb-8 border-2 border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <h3 class="text-3xl font-black text-gray-900 leading-none">Total {{ $residents->count() }} Orang</h3>
                    <p class="text-gray-500 text-sm font-bold uppercase tracking-widest mt-2">Sedang menempati kamar saat ini</p>
                </div>
                <div class="text-4xl text-gray-100 font-bold uppercase">Resident</div>
            </div>

            <div class="bg-white p-6 rounded-xl border-2 border-gray-100 shadow-sm mb-8 flex flex-wrap items-center justify-between gap-4">
                <form action="{{ route('admin.residents.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, email, atau kamar..."
                        class="border-gray-300 rounded-lg text-sm px-4 py-2 w-full md:w-72 focus:border-blue-500 outline-none">
                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-blue-700 transition">
                        Cari
                    </button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.residents.index') }}"
                            class="text-xs font-bold text-gray-400 hover:text-blue-600 underline">Reset</a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-xl border-2 border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[11px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                <th class="px-8 py-5">Nama Penghuni</th>
                                <th class="px-8 py-5">Unit Kamar</th>
                                <th class="px-8 py-5">Durasi Sewa</th>
                                <th class="px-8 py-5">Sisa Hari</th>
                                <th class="px-8 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($residents as $resident)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-gray-100 border border-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold">
                                                {{ substr($resident->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 leading-none mb-1">{{ $resident->user->name }}</p>
                                                <p class="text-xs text-gray-400 font-medium">{{ $resident->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 bg-white border-2 border-gray-50 text-gray-700 rounded-lg text-xs font-bold uppercase">
                                            {{ $resident->room->name }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-gray-600 font-bold uppercase text-[11px] tracking-tight">
                                            {{ \Carbon\Carbon::parse($resident->check_in_date)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($resident->check_out_date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $end = \Carbon\Carbon::parse($resident->check_out_date)->endOfDay();
                                            $remaining = ceil(now()->diffInDays($end, false));
                                        @endphp

                                        @if ($remaining > 0)
                                            <span class="text-blue-600 font-bold text-base">
                                                {{ (int) $remaining }} Hari Lagi
                                            </span>
                                        @elseif($remaining == 0)
                                            <span class="text-red-600 font-bold text-base">
                                                Checkout Hari Ini! ⚠️
                                            </span>
                                        @else
                                            <span class="text-gray-400 font-bold text-base">
                                                Masa Sewa Habis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('user.bookings.show', $resident) }}" target="_blank"
                                                class="p-2.5 bg-white border-2 border-gray-100 text-gray-400 hover:text-blue-600 rounded-lg transition shadow-sm" title="Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center text-gray-400 font-bold">
                                        Belum ada penghuni aktif tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
