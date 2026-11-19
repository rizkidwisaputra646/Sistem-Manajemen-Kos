<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DamageReportController extends Controller
{
    /**
     * Tampilkan semua laporan kerusakan dari penghuni.
     */
    public function index(Request $request)
    {
        $query = DamageReport::with(['user', 'room', 'admin'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('facility_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $query->paginate(15);

        $statusCounts = [
            'total' => DamageReport::count(),
            'Menunggu' => DamageReport::where('status', 'Menunggu')->count(),
            'Diproses' => DamageReport::where('status', 'Diproses')->count(),
            'Selesai' => DamageReport::where('status', 'Selesai')->count(),
            'Ditolak' => DamageReport::where('status', 'Ditolak')->count(),
        ];
        $categories = DamageReport::CATEGORIES;
        $status = $request->status;
        $category = $request->category;
        $search = $request->search;

        return view('admin.damage_reports.index', compact(
            'reports',
            'statusCounts',
            'categories',
            'status',
            'category',
            'search'
        ));
    }

    /**
     * Tampilkan detail laporan kerusakan dan tandai telah dibaca admin.
     */
    public function show(DamageReport $damageReport)
    {
        $damageReport->load(['user', 'room', 'admin']);

        if (!$damageReport->is_read_by_admin) {
            $damageReport->update(['is_read_by_admin' => true]);
        }

        $report = $damageReport;
        $statuses = DamageReport::STATUSES;

        return view('admin.damage_reports.show', compact('report', 'damageReport', 'statuses'));
    }

    /**
     * Update status laporan dan berikan tanggapan admin kepada penghuni.
     */
    public function update(Request $request, DamageReport $damageReport)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak',
            'admin_response' => 'nullable|string|max:1500',
        ], [
            'status.required' => 'Status laporan wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $data = [
            'status' => $request->status,
            'admin_response' => $request->admin_response,
            'admin_id' => Auth::id(),
            'is_read_by_user' => false, // Berikan notifikasi pembaruan ke penghuni
        ];

        if ($request->status === 'Selesai' && !$damageReport->resolved_at) {
            $data['resolved_at'] = now();
        } elseif ($request->status !== 'Selesai') {
            $data['resolved_at'] = null;
        }

        $damageReport->update($data);

        return redirect()->route('admin.damage-reports.show', $damageReport)
            ->with('success', "Status laporan berhasil diubah menjadi '{$request->status}' dan tanggapan telah diteruskan ke penghuni!");
    }

    /**
     * Hapus laporan kerusakan (jika diperlukan).
     */
    public function destroy(DamageReport $damageReport)
    {
        if ($damageReport->image) {
            $imagePath = public_path('images/damage_reports/' . $damageReport->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $damageReport->delete();

        return redirect()->route('admin.damage-reports.index')
            ->with('success', 'Laporan kerusakan berhasil dihapus.');
    }
}
