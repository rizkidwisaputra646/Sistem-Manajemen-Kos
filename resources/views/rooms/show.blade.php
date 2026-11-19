<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4">
            
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-blue-600 hover:underline mb-6">
                ← Kembali ke Daftar Kamar
            </a>

            <div class="bg-white border border-gray-300 rounded overflow-hidden text-left">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="aspect-square bg-gray-100 border-b md:border-b-0 md:border-r border-gray-200">
                        @if($room->image)
                            <img src="{{ asset('images/rooms/'.$room->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">Foto tidak tersedia</div>
                        @endif
                    </div>

                    <div class="p-6 md:p-10 flex flex-col justify-center">
                        <div class="mb-4">
                            @if(!$room->is_currently_booked)
                                <span class="bg-green-50 text-green-700 px-2 py-1 rounded text-[10px] font-bold uppercase border border-green-200">Tersedia</span>
                            @else
                                <span class="bg-red-50 text-red-700 px-2 py-1 rounded text-[10px] font-bold uppercase border border-red-200">Terisi</span>
                            @endif
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $room->name }}</h1>
                        <p class="text-2xl font-bold text-blue-600 mb-6">Rp {{ number_format($room->price) }}<span class="text-xs text-gray-500 font-normal"> / bln</span></p>

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xs font-bold text-gray-400 uppercase mb-2">Fasilitas</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(explode(',', $room->facilities) as $f)
                                        <span class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded text-xs text-gray-700 font-bold">
                                            {{ trim($f) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <h3 class="text-xs font-bold text-gray-400 uppercase mb-2">Tentang Kamar</h3>
                                <p class="text-gray-600 text-base">
                                    {{ $room->description ?? 'Hunian nyaman dengan lokasi strategis dan fasilitas lengkap untuk kebutuhan Anda.' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-100">
                            @if(!$room->is_currently_booked)
                                <a href="{{ route('rooms.checkout', $room->id) }}" class="w-full bg-blue-600 text-white py-4 rounded font-bold text-lg hover:bg-blue-700 block text-center shadow-sm shadow-blue-200 transition-all">
                                    Pesan Sekarang
                                </a>
                            @else
                                <button disabled class="w-full bg-gray-100 text-gray-400 py-4 rounded font-bold text-lg cursor-not-allowed border border-gray-200">
                                    Kamar Sudah Terisi
                                </button>
                            @endif
                            <p class="text-center text-[10px] text-gray-400 mt-4">
                                *Pembayaran aman melalui sistem Midtrans.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>