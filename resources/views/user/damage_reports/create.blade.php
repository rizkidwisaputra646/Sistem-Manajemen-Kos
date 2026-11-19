<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.damage-reports.index') }}" 
               class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Form Laporan Kerusakan Fasilitas</h1>
                <p class="text-xs text-gray-500">Lengkapi informasi di bawah ini agar tim pengelola dapat segera menindaklanjuti</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-3xl mx-auto">
            <form action="{{ route('user.damage-reports.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 space-y-6"
                  x-data="{ photoPreview: null }">
                @csrf

                <!-- Identitas Penghuni (Otomatis) -->
                <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-4 sm:p-5">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Identitas Pelapor (Otomatis Terisi)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <span class="block text-[11px] font-semibold text-slate-400">Nama Penghuni</span>
                            <span class="block text-xs font-bold text-slate-800 truncate mt-0.5">{{ $user->name }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-semibold text-slate-400">Email Akun</span>
                            <span class="block text-xs font-bold text-slate-800 truncate mt-0.5">{{ $user->email }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-semibold text-slate-400">Kamar Anda</span>
                            <span class="block text-xs font-bold text-blue-600 truncate mt-0.5">
                                {{ $currentRoom?->name ?? 'Belum ada sewa aktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Pemilihan Kamar -->
                <div>
                    <label for="room_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Lokasi Kamar / Ruangan <span class="text-rose-500">*</span>
                    </label>
                    <select name="room_id" id="room_id" required
                            class="w-full text-xs border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-blue-500 bg-white">
                        @if($currentRoom)
                            <option value="{{ $currentRoom->id }}" selected>
                                {{ $currentRoom->name }} (Kamar Anda Saat Ini)
                            </option>
                        @endif
                        @foreach($rooms as $r)
                            @if(!$currentRoom || $r->id !== $currentRoom->id)
                                <option value="{{ $r->id }}" {{ old('room_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('room_id')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori & Nama Fasilitas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Kategori Kerusakan <span class="text-rose-500">*</span>
                        </label>
                        <select name="category" id="category" required
                                class="w-full text-xs border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-blue-500 bg-white">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="facility_name" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Fasilitas yang Rusak <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="facility_name" id="facility_name" 
                               value="{{ old('facility_name') }}"
                               placeholder="Contoh: Lampu Utama, Keran Kamar Mandi, Gagang Pintu" 
                               required
                               class="w-full text-xs border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                        @error('facility_name')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi Kerusakan -->
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Deskripsi Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="4" required
                              placeholder="Jelaskan secara jelas kondisi kerusakan, penyebab (jika diketahui), dan sejak kapan terjadi agar perbaikan dapat dipersiapkan dengan tepat..."
                              class="w-full text-xs border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload Foto Kerusakan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Unggah Foto Kerusakan <span class="text-slate-400 font-normal">(Opsional namun sangat dianjurkan)</span>
                    </label>
                    
                    <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-6 text-center cursor-pointer transition relative bg-slate-50/50">
                        <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
                        
                        <template x-if="!photoPreview">
                            <div>
                                <svg class="w-10 h-10 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-xs font-bold text-slate-700">Klik untuk memilih foto kerusakan</p>
                                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, JPEG, PNG (Maksimal 5 MB)</p>
                            </div>
                        </template>

                        <template x-if="photoPreview">
                            <div class="relative inline-block">
                                <img :src="photoPreview" class="h-44 rounded-xl object-cover border border-slate-200 shadow-sm mx-auto">
                                <span class="inline-block mt-2 text-xs font-bold text-blue-600 underline">Klik untuk ganti foto</span>
                            </div>
                        </template>
                    </div>
                    @error('photo')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('user.damage-reports.index') }}" 
                       class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-sm inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Laporan Kerusakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
