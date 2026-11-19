<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Pembayaran Sewa</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-text-size-adjust: none;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f6f9;
            padding: 30px 0;
        }
        .main-card {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .header {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 28px 32px;
            text-align: left;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.5px;
            font-weight: 700;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #cbd5e1;
        }
        .content {
            padding: 32px;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .badge-info {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .greeting {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #1e293b;
        }
        .invoice-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-table td {
            padding: 8px 0;
            font-size: 14px;
            vertical-align: top;
        }
        .invoice-table td.label {
            color: #64748b;
            width: 40%;
        }
        .invoice-table td.value {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
        }
        .amount-row td {
            border-top: 1px dashed #cbd5e1;
            padding-top: 14px;
            font-size: 16px;
        }
        .amount-row td.value {
            color: #1d4ed8;
            font-size: 18px;
            font-weight: 700;
        }
        .btn-wrapper {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
        }
        .note {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
            margin-top: 24px;
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
        }
        .footer {
            text-align: center;
            padding: 24px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-card">
            <div class="header">
                <h1>KOSKU KONTRAKAN</h1>
                <p>Sistem Pengingat Tagihan Sewa Otomatis</p>
            </div>

            <div class="content">
                @if($reminderType === 'overdue')
                    <span class="badge badge-danger">⚠️ Tagihan Melewati Jatuh Tempo</span>
                @elseif($reminderType === 'h-1')
                    <span class="badge badge-danger">⏰ Batas Waktu Pembayaran (H-1)</span>
                @elseif($reminderType === 'h-3')
                    <span class="badge badge-warning">🔔 Pengingat Pembayaran (H-3)</span>
                @else
                    <span class="badge badge-info">📅 Pengingat Pembayaran (H-7)</span>
                @endif

                <div class="greeting">
                    Halo <strong>{{ $user->name ?? 'Penghuni' }}</strong>,<br>
                    @if($reminderType === 'overdue')
                        Kami memberitahukan bahwa tagihan sewa kamar Anda telah melewati batas tanggal jatuh tempo. Mohon segera melakukan pembayaran untuk menghindari pembatalan atau denda keterlambatan.
                    @else
                        Ini adalah pengingat pembayaran sewa kamar kontrakan Anda yang akan segera jatuh tempo. Berikut adalah rincian tagihan Anda:
                    @endif
                </div>

                <div class="invoice-box">
                    <table class="invoice-table">
                        <tr>
                            <td class="label">Nama Penghuni</td>
                            <td class="value">{{ $user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Unit Kamar</td>
                            <td class="value">{{ $room->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kode Pesanan / ID</td>
                            <td class="value">#{{ strtoupper(substr($booking->id, 0, 8)) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Periode Sewa</td>
                            <td class="value">{{ date('d M Y', strtotime($booking->check_in_date)) }} - {{ date('d M Y', strtotime($booking->check_out_date)) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Jatuh Tempo</td>
                            <td class="value" style="color: {{ $reminderType === 'overdue' ? '#dc2626' : '#1e293b' }};">
                                {{ date('d F Y', strtotime($dueDate)) }}
                            </td>
                        </tr>
                        <tr class="amount-row">
                            <td class="label">Jumlah Tagihan</td>
                            <td class="value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>

                <div class="btn-wrapper">
                    <a href="{{ url('/my-bookings/' . $booking->id) }}" class="btn">
                        Lihat & Bayar Tagihan Sekarang
                    </a>
                </div>

                <div class="note">
                    <strong>Catatan:</strong>
                    <ul style="margin: 6px 0; padding-left: 18px;">
                        <li>Jika Anda telah melakukan pembayaran, abaikan email ini atau hubungi pengelola untuk verifikasi.</li>
                        <li>Sistem secara otomatis menghentikan pengingat setelah pembayaran Anda berhasil dikonfirmasi.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer">
            Email ini dikirim otomatis oleh sistem KosKu Kontrakan ke alamat terdaftar <strong>{{ $user->email }}</strong>.<br>
            Jika butuh bantuan, hubungi pengelola kontrakan.
        </div>
    </div>
</body>
</html>
