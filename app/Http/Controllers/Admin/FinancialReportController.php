<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    /**
     * Build base query and filter boundaries based on request.
     */
    protected function buildReportData(Request $request): array
    {
        $period = $request->input('period', 'monthly');
        $now = Carbon::now();

        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'yearly':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'custom':
                if ($request->filled('start_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                }
                if ($request->filled('end_date')) {
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
            case 'all':
            default:
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $period = 'custom';
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                } elseif ($period === 'all') {
                    $startDate = null;
                    $endDate = null;
                } else {
                    $period = 'monthly';
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                }
                break;
        }

        // Base query untuk menghitung metrik periode
        $basePeriodQuery = Booking::query();
        if ($startDate && $endDate) {
            $basePeriodQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Metrik Ringkasan (KPI)
        $totalTransactions = (clone $basePeriodQuery)->count();
        $totalBookings = $totalTransactions;
        $successTransactions = (clone $basePeriodQuery)->where('status', 'success')->count();
        $pendingTransactions = (clone $basePeriodQuery)->where('status', 'pending')->count();
        $cancelledTransactions = (clone $basePeriodQuery)->where('status', 'cancelled')->count();
        
        // Pendapatan HANYA dihitung dari transaksi yang valid/lunas (status = 'success')
        $totalRevenue = (clone $basePeriodQuery)->where('status', 'success')->sum('total_price');

        // Query untuk tabel rincian transaksi (dengan filter status & pencarian)
        $tableQuery = Booking::with(['user', 'room']);
        if ($startDate && $endDate) {
            $tableQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('status') && $request->status !== '') {
            $tableQuery->where('status', $request->status);
        }

        if ($request->filled('room_id') && $request->room_id !== '') {
            $tableQuery->where('room_id', $request->room_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $tableQuery->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('room', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        return [
            'period' => $period,
            'startDate' => $startDate ? $startDate->toDateString() : null,
            'endDate' => $endDate ? $endDate->toDateString() : null,
            'statusFilter' => $request->input('status', ''),
            'search' => $request->input('search', ''),
            'roomId' => $request->input('room_id', ''),
            'totalTransactions' => $totalTransactions,
            'totalBookings' => $totalBookings,
            'successTransactions' => $successTransactions,
            'pendingTransactions' => $pendingTransactions,
            'cancelledTransactions' => $cancelledTransactions,
            'totalRevenue' => $totalRevenue,
            'tableQuery' => $tableQuery,
        ];
    }

    public function index(Request $request)
    {
        $data = $this->buildReportData($request);
        $rooms = Room::all();
        $reports = $data['tableQuery']->latest()->paginate(15);

        return view('admin.reports.index', array_merge($data, [
            'reports' => $reports,
            'rooms' => $rooms,
        ]));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->buildReportData($request);
        $reports = $data['tableQuery']->latest()->get();

        $pdf = Pdf::loadView('admin.reports.pdf', array_merge($data, [
            'reports' => $reports,
            'printedAt' => now()->format('d F Y, H:i') . ' WIB',
        ]))->setPaper('a4', 'landscape');

        $filename = 'Laporan_Pelaporan_Kontrakan_' . date('Ymd_His') . '.pdf';

        if ($request->has('stream')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
