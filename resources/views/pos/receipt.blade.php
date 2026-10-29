<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->no_faktur }}</title>
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 8px;
            width: 58mm; /* Standar thermal 58mm */
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .store-name {
            font-size: 14px;
            font-weight: bold;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
        .item-row {
            margin-bottom: 4px;
        }
        .item-name {
            font-weight: bold;
        }
        .item-calc {
            display: flex;
            justify-content: space-between;
        }
        .total-section {
            margin-top: 6px;
        }
        .grand-total {
            font-size: 13px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
        }
        .no-print {
            margin-top: 15px;
            text-align: center;
        }
        .no-print button {
            background: #000;
            color: #fff;
            border: none;
            padding: 6px 12px;
            font-family: inherit;
            font-size: 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="store-name">MINIMARKET POS</div>
        <div>Jl. Sariasih No. 54, Bandung</div>
        <div>Telp: 022-2014050</div>
    </div>

    <div class="divider"></div>

    <div class="info-row">
        <span>No: {{ $transaction->no_faktur }}</span>
    </div>
    <div class="info-row">
        <span>Tgl: {{ $transaction->waktu_transaksi->format('d/m/Y H:i') }}</span>
        <span>Kasir: {{ $transaction->user->name }}</span>
    </div>

    <div class="divider"></div>

    <!-- Items List -->
    <div class="items-section">
        @foreach($transaction->details as $item)
            <div class="item-row">
                <div class="item-name">{{ $item->product->nama_produk }}</div>
                <div class="item-calc">
                    <span>{{ $item->qty }} {{ $item->unit->nama_satuan }} x {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->subtotal_item, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="divider"></div>

    <!-- Totals -->
    <div class="total-section">
        <div class="info-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($transaction->diskon > 0)
            <div class="info-row">
                <span>Diskon:</span>
                <span>- Rp {{ number_format($transaction->diskon, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="info-row grand-total" style="margin-top: 2px;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($transaction->total_akhir, 0, ',', '.') }}</span>
        </div>
        <div class="divider"></div>
        <div class="info-row">
            <span>Metode:</span>
            <span style="text-transform: uppercase;">{{ $transaction->metode_pembayaran }}</span>
        </div>
        <div class="info-row">
            <span>Bayar:</span>
            <span>Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span>Kembalian:</span>
            <span>Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="footer">
        <div>Terima Kasih Atas Kunjungan Anda!</div>
        <div>Barang yang dibeli tidak dapat ditukar/dikembalikan</div>
    </div>

    <div class="no-print">
        <button onclick="window.print()">Cetak Ulang Struk</button>
    </div>

    <script>
        // Trigger print otomatis saat jendela struk dibuka
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
