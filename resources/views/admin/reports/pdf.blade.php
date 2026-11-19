<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi & Pendapatan - KosKu</title>
    <style>
        @page {
            margin: 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 4px 0 0 0;
        }
        .kpi-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .kpi-box {
            padding: 10px 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .kpi-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin: 0 0 4px 0;
        }
        .kpi-value {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .kpi-value.green {
            color: #15803d;
        }
        .kpi-value.blue {
            color: #1d4ed8;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            border: 1px solid #1e3a8a;
        }
        .data-table td {
            padding: 7px 6px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-success {
            background-color: #dcfce7;
            color: #15803d;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #b45309;
        }
        .status-cancelled {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer-note {
            margin-top: 25px;
            font-size: 9px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="vertical-align: top;">
                    <h1 class="title">KOSKU KONTRAKAN</h1>
                    <p class="subtitle">Laporan Resmi Transaksi, Pemesanan Kamar, & Total Pendapatan</p>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <p style="margin: 0; font-size: 10px; color: #64748b;"><strong>Waktu Cetak:</strong> {{ $printedAt }}</p>
                    <p style="margin: 3px 0 0 0; font-size: 10px; color: #64748b;">
                        <strong>Periode:</strong> 
                        @if($startDate && $endDate)
                            {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
                        @else
                            Semua Waktu
                        @endif
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Ringkasan KPI -->
    <table class="kpi-table">
        <tr>
            <td width="25%" style="padding-right: 6px;">
                <div class="kpi-box">
                    <p class="kpi-label">Total Transaksi</p>
                    <p class="kpi-value">{{ number_format($totalTransactions) }} <span style="font-size: 10px; font-weight: normal; color: #64748b;">pesanan</span></p>
                </div>
            </td>
            <td width="25%" style="padding-right: 6px; padding-left: 6px;">
                <div class="kpi-box">
                    <p class="kpi-label">Pembayaran Berhasil</p>
                    <p class="kpi-value green">{{ number_format($successTransactions) }} <span style="font-size: 10px; font-weight: normal; color: #64748b;">lunas</span></p>
                </div>
            </td>
            <td width="25%" style="padding-right: 6px; padding-left: 6px;">
                <div class="kpi-box">
                    <p class="kpi-label">Menunggu / Pending</p>
                    <p class="kpi-value" style="color: #b45309;">{{ number_format($pendingTransactions) }} <span style="font-size: 10px; font-weight: normal; color: #64748b;">tagihan</span></p>
                </div>
            </td>
            <td width="25%" style="padding-left: 6px;">
                <div class="kpi-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <p class="kpi-label" style="color: #166534;">Total Pendapatan Valid</p>
                    <p class="kpi-value green" style="font-size: 16px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="12%" class="text-center">ID / Kode</th>
                <th width="12%">Tgl Pemesanan</th>
                <th width="18%">Nama Penyewa</th>
                <th width="14%">Unit Kamar</th>
                <th width="16%" class="text-center">Periode Sewa</th>
                <th width="10%" class="text-center">Status</th>
                <th width="14%" class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $booking)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center" style="font-family: monospace; font-weight: bold;">
                        #{{ strtoupper(substr($booking->id, 0, 8)) }}
                    </td>
                    <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $booking->user->name ?? '-' }}</strong><br>
                        <span style="font-size: 8px; color: #64748b;">{{ $booking->user->email ?? '' }}</span>
                    </td>
                    <td style="font-weight: bold; color: #1e3a8a;">{{ $booking->room->name ?? '-' }}</td>
                    <td class="text-center">
                        {{ date('d/m/y', strtotime($booking->check_in_date)) }} - {{ date('d/m/y', strtotime($booking->check_out_date)) }}
                    </td>
                    <td class="text-center">
                        @if($booking->status === 'success')
                            <span class="status-badge status-success">Success</span>
                        @elseif($booking->status === 'pending')
                            <span class="status-badge status-pending">Pending</span>
                        @else
                            <span class="status-badge status-cancelled">Cancelled</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 25px; color: #94a3b8;">
                        Tidak ada data transaksi yang sesuai dengan filter laporan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="7" class="text-right" style="padding: 10px; font-size: 11px;">
                    TOTAL PENDAPATAN DARI TRANSAKSI VALID (SUCCESS):
                </td>
                <td class="text-right" style="padding: 10px; font-size: 12px; color: #15803d;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-note">
        <p>Laporan ini digenerate secara otomatis oleh sistem KosKu Kontrakan sebagai dokumen arsip manajemen resmi.</p>
    </div>
</body>
</html>
