<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\DamageReport;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamageReportController extends Controller
{
    /**
     * Tampilkan riwayat laporan kerusakan milik penghuni yang login.
     */
    public function index()
    {
        $reports = DamageReport::where('user_id', Auth::id())
            ->with(['room', 'admin'])
            ->latest()
            ->paginate(10);

        // Tandai laporan yang sudah dibaca user
        DamageReport::where('user_id', Auth::id())
            ->where('is_read_by_user', false)
            ->update(['is_read_by_user' => true]);

        return view('user.damage_reports.index', compact('reports'));
    }

    /**
     * Tampilkan form pembuatan laporan kerusakan.
     */
    public function create()
    {
        $user = Auth::user();

        // Cari kamar aktif penyewa
        $activeBooking = Booking::where('user_id', $user->id)
            ->where('status', 'success')
            ->where('check_out_date', '>=', now()->toDateString())
            ->with('room')
            ->latest()
            ->first();

        if (!$activeBooking) {
            $activeBooking = Booking::where('user_id', $user->id)
                ->with('room')
                ->latest()
                ->first();
        }

        $currentRoom = $activeBooking ? $activeBooking->room : null;
        $rooms = Room::all();
        $categories = DamageReport::CATEGORIES;

        return view('user.damage_reports.create', compact('user', 'currentRoom', 'rooms', 'categories'));
    }

    /**
     * Simpan laporan kerusakan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'facility_name' => 'required|string|max:150',
            'category' => 'required|string',
            'description' => 'required|string|min:5|max:1500',
            'room_id' => 'required|exists:rooms,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'facility_name.required' => 'Nama fasilitas yang rusak wajib diisi.',
            'category.required' => 'Silakan pilih kategori kerusakan fasilitas.',
            'description.required' => 'Deskripsi kerusakan wajib dijelaskan agar admin paham.',
            'description.min' => 'Deskripsi minimal 5 karakter.',
            'room_id.required' => 'Pilih kamar tempat fasilitas berada.',
            'photo.image' => 'File foto harus berupa gambar valid.',
            'photo.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        $file = $request->file('photo') ?? $request->file('image');
        $imageName = null;
        if ($file) {
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/damage_reports'), $imageName);
        }

        $report = DamageReport::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'facility_name' => $request->facility_name,
            'category' => $request->category,
            'description' => $request->description,
            'image' => $imageName,
            'status' => 'Menunggu',
            'is_read_by_admin' => false,
            'is_read_by_user' => true,
        ]);

        return redirect()->route('user.damage-reports.index')
            ->with('success', 'Laporan kerusakan fasilitas berhasil dikirim! Admin akan segera meninjau dan menindaklanjutinya.');
    }

    /**
     * Tampilkan detail laporan kerusakan milik penghuni.
     */
    public function show(DamageReport $damageReport)
    {
        // Keamanan: Penghuni hanya boleh melihat laporan miliknya sendiri
        if ($damageReport->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin melihat laporan ini.');
        }

        // Tandai telah dibaca
        if (!$damageReport->is_read_by_user) {
            $damageReport->update(['is_read_by_user' => true]);
        }

        $report = $damageReport;
        return view('user.damage_reports.show', compact('report', 'damageReport'));
    }
}
