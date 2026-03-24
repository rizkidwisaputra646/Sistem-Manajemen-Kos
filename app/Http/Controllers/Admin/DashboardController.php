<?php

// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse; // Tambahkan ini di paling atas file


class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Cuan (Hanya yang statusnya Success)
        $totalRevenue = Booking::where('status', 'success')->sum('total_price');

        // 2. Hitung Status Kamar
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $filledRooms = Room::where('status', 'full')->count();

        // 3. Ambil 5 Transaksi Terakhir
        $recentBookings = Booking::with(['user', 'room'])
            ->latest()
            ->take(5)
            ->get();

        $monthlyRevenue = [];
        $monthLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->format('M Y');

            $revenue = Booking::where('status', 'success')
                ->whereMonth('updated_at', $month->month)
                ->whereYear('updated_at', $month->year)
                ->sum('total_price');

            $monthlyRevenue[] = $revenue;
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalRooms',
            'availableRooms',
            'filledRooms',
            'recentBookings',
            'monthlyRevenue',
            'monthLabels'
        ));
    }


    public function export()
    {
        // Ambil semua data booking yang SUKSES
        $bookings = Booking::with(['user', 'room'])
            ->where('status', 'success')
            ->latest()
            ->get();

        $response = new StreamedResponse(function () use ($bookings) {
            $handle = fopen('php://output', 'w');

            // Tambahkan Header Tabel Excel
            fputcsv($handle, [
                'ID PESANAN',
                'TANGGAL BAYAR',
                'NAMA PENYEWA',
                'KAMAR',
                'CHECK-IN',
                'CHECK-OUT',
                'TOTAL HARGA'
            ]);

            // Isi Data dari Database
            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    '#' . $booking->id,
                    $booking->updated_at->format('d-m-Y H:i'),
                    $booking->user->name,
                    $booking->room->name,
                    $booking->check_in_date,
                    $booking->check_out_date,
                    $booking->total_price,
                ]);
            }

            fclose($handle);
        });

        // Beri nama file otomatis berdasarkan tanggal hari ini
        $fileName = 'Laporan_Pendapatan_' . date('d_M_Y') . '.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}
