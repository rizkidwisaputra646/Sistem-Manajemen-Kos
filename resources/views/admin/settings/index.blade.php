<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 italic underline decoration-indigo-500">Pengaturan Landing Page ⚙️
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.settings.update') }}" method="POST"
                class="bg-white p-10 rounded-[3rem] shadow-xl border border-gray-100">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Hero Title</label>
                        <input type="text" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}"
                            class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Hero Subtitle</label>
                        <textarea name="hero_subtitle" class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h4 class="font-black text-gray-800 italic mb-4">Tentang Kami 🏢</h4>
                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">About Title</label>
                                <input type="text" name="about_title" value="{{ $settings['about_title'] ?? '' }}" class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">About Description</label>
                                <textarea name="about_desc" rows="3" class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500">{{ $settings['about_desc'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h4 class="font-black text-gray-800 italic mb-4">Keunggulan Utama (Features) 🌟</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Feature 1 -->
                            <div class="space-y-4 p-4 border border-gray-100 rounded-2xl bg-gray-50/50">
                                <h5 class="text-xs font-bold text-gray-600">Feature 1</h5>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Title</label>
                                    <input type="text" name="feature_1_title" value="{{ $settings['feature_1_title'] ?? '' }}" class="w-full mt-1 border-gray-100 bg-white rounded-xl focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Description</label>
                                    <textarea name="feature_1_desc" rows="3" class="w-full mt-1 border-gray-100 bg-white rounded-xl focus:ring-indigo-500 text-sm">{{ $settings['feature_1_desc'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <!-- Feature 2 -->
                            <div class="space-y-4 p-4 border border-gray-100 rounded-2xl bg-gray-50/50">
                                <h5 class="text-xs font-bold text-gray-600">Feature 2</h5>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Title</label>
                                    <input type="text" name="feature_2_title" value="{{ $settings['feature_2_title'] ?? '' }}" class="w-full mt-1 border-gray-100 bg-white rounded-xl focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Description</label>
                                    <textarea name="feature_2_desc" rows="3" class="w-full mt-1 border-gray-100 bg-white rounded-xl focus:ring-indigo-500 text-sm">{{ $settings['feature_2_desc'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <!-- Feature 3 -->
                            <div class="space-y-4 p-4 border border-gray-100 rounded-2xl bg-gray-50/50">
                                <h5 class="text-xs font-bold text-gray-600">Feature 3</h5>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Title</label>
                                    <input type="text" name="feature_3_title" value="{{ $settings['feature_3_title'] ?? '' }}" class="w-full mt-1 border-gray-100 bg-white rounded-xl focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Description</label>
                                    <textarea name="feature_3_desc" rows="3" class="w-full mt-1 border-gray-100 bg-white rounded-xl focus:ring-indigo-500 text-sm">{{ $settings['feature_3_desc'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h4 class="font-black text-gray-800 italic mb-4">Informasi Lokasi 📍</h4>

                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Alamat Lengkap
                                    (Teks)</label>
                                <input type="text" name="address" value="{{ $settings['address'] ?? '' }}"
                                    placeholder="Contoh: Jl. Ryacudu No. 123, Bandar Lampung"
                                    class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Embed Google
                                    Maps (Iframe Code)</label>
                                <textarea name="google_maps_iframe" rows="4" placeholder='Paste kode <iframe> di sini...'
                                    class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500 text-xs font-mono">{{ $settings['google_maps_iframe'] ?? '' }}</textarea>
                                <p class="text-[9px] text-gray-400 mt-2 ml-2 italic">
                                    *Cara ambil: Buka Google Maps > Share > Embed a map > Copy HTML.
                                </p>
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-gray-400 ml-2 italic">Nomor WhatsApp
                                    (Gunakan 62...)</label>
                                <input type="text" name="whatsapp_number"
                                    value="{{ $settings['whatsapp_number'] ?? '' }}" placeholder="Contoh: 628123456789"
                                    class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="mt-8 w-full bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                    Simpan Perubahan 💾
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
