<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-left">
            <div>
                <h2 class="font-extrabold text-3xl text-slate-900 leading-tight tracking-tight">
                    Temukan Hunian Favoritmu
                </h2>
                <p class="text-slate-500 text-sm mt-1 font-medium">Jelajahi berbagai pilihan kontrakan premium dengan fasilitas terbaik.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="p-2 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>
                <div class="relative">
                    <input type="text" placeholder="Cari lokasi atau nama..." class="bg-white border border-slate-200 rounded-xl px-4 py-2 pl-10 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none w-full sm:w-64 shadow-sm">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($rooms as $room)
                <div class="group bg-white rounded-[2rem] shadow-sm hover:shadow-xl hover:shadow-indigo-100/50 border border-slate-200 overflow-hidden transition-all duration-500 flex flex-col hover:-translate-y-1 text-left">
                    <div class="relative aspect-[16/11] overflow-hidden bg-slate-100">
                        @if($room->image)
                            <img src="{{ asset('images/rooms/'.$room->image) }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="flex items-center justify-center h-full text-slate-400 text-xs">No Photo</div>
                        @endif
                        
                        <div class="absolute top-4 right-4">
                            <button class="p-2 bg-white/80 backdrop-blur rounded-full text-slate-400 hover:text-rose-500 transition-colors shadow-sm">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path></svg>
                            </button>
                        </div>

                        <div class="absolute bottom-4 left-4">
                            <span class="bg-indigo-600 px-3 py-1.5 rounded-xl text-[10px] md:text-xs font-bold text-white shadow-lg shadow-indigo-200 uppercase tracking-wider">
                                Rp {{ number_format($room->price) }} <span class="opacity-70 font-medium">/ bln</span>
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-2">
                             <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">Tersedia</span>
                             <div class="flex items-center gap-1">
                                 <svg class="w-3 h-3 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                 <span class="text-[10px] font-bold text-slate-700">4.8</span>
                             </div>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors tracking-tight leading-tight mb-2">
                            {{ $room->name }}
                        </h3>
                        
                        <div class="flex flex-wrap gap-1.5 mb-6">
                            @foreach(explode(',', $room->facilities) as $facility)
                                <span class="px-2 py-0.5 bg-slate-50 border border-slate-100 rounded-md text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ trim($facility) }}</span>
                            @endforeach
                        </div>

                        <div class="mt-auto flex gap-3 pt-4 border-t border-slate-50">
                            <a href="{{ route('rooms.show', $room->id) }}" class="flex-1 text-center bg-slate-50 text-slate-600 py-3 rounded-xl text-xs font-bold hover:bg-slate-100 transition-all active:scale-95 border border-slate-200">
                                Detail
                            </a>
                        
                            <a href="{{ route('rooms.checkout', $room->id) }}" class="flex-[2] text-center bg-indigo-600 text-white py-3 rounded-xl text-xs font-bold hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Pesan
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-32 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-200">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold text-slate-300">!</div>
                    <h3 class="text-xl font-bold text-slate-900">Wah, Kamar Kosong!</h3>
                    <p class="text-slate-400 text-sm mt-1 max-w-xs mx-auto">Kami sedang mempersiapkan unit-unit baru untuk kamu. Coba cek lagi nanti ya!</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>