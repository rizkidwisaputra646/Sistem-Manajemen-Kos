<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 italic underline decoration-indigo-500">Data Penghuni Aktif 👥</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div
                class="bg-indigo-600 p-8 rounded-[2.5rem] mb-8 text-white shadow-xl shadow-indigo-100 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-black italic italic">Total {{ $residents->count() }} Orang</h3>
                    <p class="text-indigo-100 text-xs font-bold uppercase tracking-widest mt-1">Sedang menempati kamar
                        hari ini</p>
                </div>
                <div class="text-5xl opacity-20">🏠</div>
            </div>

            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <th class="px-8 py-5">Penghuni</th>
                                <th class="px-8 py-5">No. Kamar</th>
                                <th class="px-8 py-5">Durasi Menginap</th>
                                <th class="px-8 py-5">Sisa Waktu</th>
                                <th class="px-8 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($residents as $resident)
                                <tr class="hover:bg-gray-50/50 transition-colors italic">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-black">
                                                {{ substr($resident->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-800 leading-none">
                                                    {{ $resident->user->name }}</p>
                                                <p class="text-[10px] text-gray-400 mt-1 font-bold">
                                                    {{ $resident->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span
                                            class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-xl text-[10px] font-black uppercase tracking-tighter">
                                            {{ $resident->room->name }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-[11px] text-gray-600 font-bold">
                                            {{ \Carbon\Carbon::parse($resident->check_in_date)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($resident->check_out_date)->format('d M Y') }}
                                        </p>
                                    </td>
                                    <td class="px-8 py-5">
                                        @php
                                            // Kita set jamnya ke akhir hari (23:59) agar hitungannya lebih adil
                                            $end = \Carbon\Carbon::parse($resident->check_out_date)->endOfDay();

                                            // Kita hitung selisih hari dan bulatkan ke atas pakai ceil()
                                            $remaining = ceil(now()->diffInDays($end, false));
                                        @endphp

                                        @if ($remaining > 0)
                                            <span class="text-indigo-600 font-black text-sm italic">
                                                {{ (int) $remaining }} Hari Lagi
                                            </span>
                                        @elseif($remaining == 0)
                                            <span class="text-rose-600 font-black text-sm italic animate-pulse">
                                                Checkout Hari Ini! 🏃‍♂️
                                            </span>
                                        @else
                                            <span class="text-gray-400 font-black text-sm italic">
                                                Lewat Batas! ⚠️
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('user.bookings.show', $resident) }}" target="_blank"
                                                class="p-2 bg-gray-50 text-gray-400 hover:text-indigo-600 rounded-xl transition shadow-sm border border-gray-100">👁️</a>

                                            {{-- <a href="https://wa.me/{{ $resident->user->phone }}?text=Halo%20{{ $resident->user->name }},%20mengingatkan%20sisa%20sewa%20kamar%20{{ $resident->room->name }}%20tinggal%20{{ (int) $remaining }}%20hari%20lagi.%20Terima%20kasih!"
                                                target="_blank"
                                                class="p-2 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-xl transition shadow-sm border border-green-100">
                                                📱
                                            </a> --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center text-gray-400 italic">
                                        <span class="text-4xl block mb-2">📭</span>
                                        Belum ada penghuni aktif hari ini.
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
