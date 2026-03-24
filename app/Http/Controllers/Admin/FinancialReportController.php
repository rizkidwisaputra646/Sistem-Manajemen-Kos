<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        // Default: Ambil data bulan ini jika tanggal tidak diisi
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = Booking::with(['user', 'room'])
            ->where('status', 'success') // Hanya hitung yang lunas
            ->whereBetween('updated_at', [
                Carbon::parse($startDate)->startOfDay(), 
                Carbon::parse($endDate)->endOfDay()
            ]);

        // Hitung Ringkasan
        $totalRevenue = $query->sum('total_price');
        $totalTransactions = $query->count();
        $reports = $query->latest()->paginate(15);

        return view('admin.reports.index', compact('reports', 'totalRevenue', 'totalTransactions', 'startDate', 'endDate'));
    }
}