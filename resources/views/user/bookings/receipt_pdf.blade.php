<!DOCTYPE html>
<html>
<head>
    <title>Kuitansi Pembayaran - KosKu</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .container { width: 100%; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #6366f1; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #6366f1; text-transform: uppercase; }
        .info-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .info-table td { padding: 10px; vertical-align: top; }
        .label { font-weight: bold; color: #666; font-size: 12px; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: bold; border-bottom: 1px solid #eee; }
        .status-box { background: #ecfdf5; color: #059669; padding: 10px; border-radius: 10px; text-align: center; margin-top: 20px; font-weight: bold; border: 1px solid #10b981; }
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KOS<span style="color: #333;">KU</span></h1>
            <p>Bukti Pembayaran Resmi - Elektronik</p>
        </div>

        <table class="info-table">
            <tr>
                <td width="50%">
                    <p class="label">No. Transaksi</p>
                    <p class="value">#{{ strtoupper(substr($booking->id, 0, 8)) }}</p>
                </td>
                <td width="50%">
                    <p class="label">Tanggal Bayar</p>
                    <p class="value">{{ $booking->updated_at->format('d F Y') }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="label">Nama Penyewa</p>
                    <p class="value">{{ $booking->user->name ?? '-' }}</p>
                    <p style="font-size: 11px; color: #64748b; margin: 4px 0 0 0;">{{ $booking->user->email ?? '' }}</p>
                </td>
                <td>
                    <p class="label">Kamar</p>
                    <p class="value">{{ $booking->room->name }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="label">Periode Sewa</p>
                    <p class="value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</p>
                </td>
                <td>
                    <p class="label">Total Bayar</p>
                    <p class="value" style="color: #6366f1;">Rp {{ number_format($booking->total_price) }}</p>
                </td>
            </tr>
        </table>

        <div class="status-box">
            PEMBAYARAN LUNAS & TERVERIFIKASI
        </div>

        <div style="margin-top: 40px; text-align: right;">
            <p style="font-size: 12px;">Dikeluarkan secara otomatis oleh sistem KosKu pada {{ date('d/m/Y H:i') }} WIB.</p>
        </div>

        <div class="footer">
            Kuitansi ini adalah bukti sah pembayaran sewa kos dan tidak memerlukan tanda tangan basah.
        </div>
    </div>
</body>
</html>