<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting; // Pastikan model di-import
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil data key => value
        $settings = Setting::pluck('value', 'key'); 
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Ambil semua input kecuali token CSRF dan Method PATCH
        $inputs = $request->except('_token', '_method');

        foreach ($inputs as $key => $value) {
            // updateOrCreate: Jika KEY ada -> Update VALUE-nya. Jika KEY tidak ada -> Buat BARIS BARU.
            Setting::updateOrCreate(
                ['key' => $key],   // Pencarian berdasarkan Key
                ['value' => $value] // Data yang diisi/diubah
            );
        }

        return back()->with('success', 'Pengaturan berhasil disimpan! ✨');
    }
}