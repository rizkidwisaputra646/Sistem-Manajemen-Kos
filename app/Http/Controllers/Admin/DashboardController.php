<?php

// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Shuchkin\SimpleXLSXGen;

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

        $totalRevenue = $bookings->sum('total_price');

        // Struktur baris Excel dengan tata letak dan pewarnaan rapi & elegan
        $rows = [
            // Baris 1: Judul Laporan
            ['<b><style font-size="14"><color rgb="#1E3A8A">LAPORAN PENDAPATAN & TRANSAKSI KONTRAKAN</color></style></b>'],
            // Baris 2: Subtitle & Waktu Cetak
            ['<style font-size="9"><color rgb="#64748B">Waktu Cetak: ' . date('d F Y, H:i') . ' WIB  |  Status Data: Transaksi Berhasil (Success)</color></style>'],
            [''],
            // Baris 4: Ringkasan Cepat (KPI)
            [
                '<b><bgcolor rgb="#F1F5F9">Ringkasan:</bgcolor></b>',
                '<b>Total Transaksi: ' . count($bookings) . ' pesanan</b>',
                '',
                '<b>Total Pendapatan: Rp ' . number_format($totalRevenue, 0, ',', '.') . '</b>',
            ],
            [''],
            // Baris 6: Header Tabel Utama
            [
                '<center><b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> NO </bgcolor></color></b></center>',
                '<center><b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> ID / KODE PESANAN </bgcolor></color></b></center>',
                '<center><b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> TANGGAL BAYAR </bgcolor></color></b></center>',
                '<b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> NAMA PENYEWA </bgcolor></color></b>',
                '<b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> EMAIL PENYEWA </bgcolor></color></b>',
                '<b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> KAMAR </bgcolor></color></b>',
                '<center><b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> CHECK-IN </bgcolor></color></b></center>',
                '<center><b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> CHECK-OUT </bgcolor></color></b></center>',
                '<right><b><color rgb="#FFFFFF"><bgcolor rgb="#1E3A8A"> TOTAL HARGA </bgcolor></color></b></right>',
            ],
        ];

        // Baris Data Transaksi
        $no = 1;
        foreach ($bookings as $booking) {
            // Efek zebra striping (latar berselang-seling lembut)
            $bgColor = ($no % 2 === 0) ? '#F8FAFC' : '#FFFFFF';
            $bgOpen = '<bgcolor rgb="' . $bgColor . '">';
            $bgClose = '</bgcolor>';

            $kodePesanan = '#' . substr($booking->id, 0, 8);
            $tanggal = $booking->updated_at ? $booking->updated_at->format('d-m-Y H:i') : '-';
            $nama = $booking->user->name ?? '-';
            $email = $booking->user->email ?? '-';
            $kamar = $booking->room->name ?? '-';
            $checkIn = $booking->check_in_date ?? '-';
            $checkOut = $booking->check_out_date ?? '-';
            $total = 'Rp ' . number_format($booking->total_price ?? 0, 0, ',', '.');

            $rows[] = [
                '<center>' . $bgOpen . $no . $bgClose . '</center>',
                '<center>' . $bgOpen . $kodePesanan . $bgClose . '</center>',
                '<center>' . $bgOpen . $tanggal . $bgClose . '</center>',
                $bgOpen . $nama . $bgClose,
                $bgOpen . $email . $bgClose,
                $bgOpen . $kamar . $bgClose,
                '<center>' . $bgOpen . $checkIn . $bgClose . '</center>',
                '<center>' . $bgOpen . $checkOut . $bgClose . '</center>',
                '<right>' . $bgOpen . $total . $bgClose . '</right>',
            ];

            $no++;
        }

        // Baris Pemisah & Total Akhir
        $rows[] = [''];
        $rows[] = [
            '', '', '', '', '', '', '',
            '<right><b><bgcolor rgb="#E2E8F0"><color rgb="#1E293B">TOTAL PENDAPATAN :</color></bgcolor></b></right>',
            '<right><b><bgcolor rgb="#E2E8F0"><color rgb="#15803D">Rp ' . number_format($totalRevenue, 0, ',', '.') . '</color></bgcolor></b></right>',
        ];

        // Buat file Excel dan atur lebar kolom agar proporsional
        $xlsx = SimpleXLSXGen::fromArray($rows);
        $xlsx->setColWidth(1, 8);   // NO
        $xlsx->setColWidth(2, 22);  // ID / KODE
        $xlsx->setColWidth(3, 20);  // TANGGAL
        $xlsx->setColWidth(4, 26);  // NAMA
        $xlsx->setColWidth(5, 28);  // EMAIL
        $xlsx->setColWidth(6, 22);  // KAMAR
        $xlsx->setColWidth(7, 16);  // CHECK-IN
        $xlsx->setColWidth(8, 16);  // CHECK-OUT
        $xlsx->setColWidth(9, 24);  // TOTAL

        $fileName = 'Laporan_Pendapatan_' . date('d_M_Y') . '.xlsx';

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}