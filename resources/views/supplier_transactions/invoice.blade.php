<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $supplier->nama }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #000;
        }

        .controls {
            margin-bottom: 30px;
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-download {
            background-color: #000;
            color: white;
        }

        .btn-download:hover {
            background-color: #333;
        }

        .btn-back {
            background-color: #64748b;
            color: white;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        #invoice-container {
            width: 800px;
            background: white;
            padding: 50px;
            border: 1px solid #000;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }

        .header-left .title-box {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .logo-circle {
            width: 40px;
            height: 40px;
            background: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 900;
            font-size: 20px;
        }

        .header-left h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-left p.periode {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .header-right {
            text-align: left;
        }

        .header-right p {
            margin: 0;
            font-size: 13px;
            color: #555;
        }

        .header-right h2 {
            margin: 5px 0 0;
            font-size: 22px;
            font-weight: 800;
            border-bottom: 2px solid #000;
            display: inline-block;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .invoice-table th {
            border: 2px solid #000;
            padding: 12px 10px;
            background: #f0f0f0;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 800;
            text-align: center;
        }

        .invoice-table td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 13px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: 800;
        }

        .date-separator {
            font-weight: 700;
            text-align: center;
            color: #555;
            font-size: 12px;
        }

        .subtotal-row td {
            border-top: 2px solid #000;
            background: #fafafa;
        }

        .final-total-row td {
            border-top: 3px double #000;
            padding: 15px 10px;
        }

        .footer-sigs {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .sig-box {
            width: 220px;
            text-align: center;
        }

        .sig-box p {
            margin: 0 0 10px;
            font-size: 14px;
            font-weight: 700;
        }

        .sig-dots {
            height: 80px;
            border-bottom: 2px dotted #000;
            margin-bottom: 10px;
        }

        .notice-box {
            border: 1px solid #000;
            padding: 12px;
            width: 240px;
            text-align: center;
            font-size: 11px;
            font-weight: 400;
            line-height: 1.4;
        }
    </style>
</head>

<body>

    <div class="controls">
        <a href="{{ route('supplier_transactions.index', ['supplier_id' => $supplier->id]) }}" class="btn btn-back">
            Kembali</a>
        <button onclick="downloadInvoice()" class="btn btn-download">Download Image</button>
    </div>

    <div id="invoice-container">
        <div class="header">
            <div class="header-left">
                <div class="title-box">
                    {{-- <div class="logo-circle">Z</div> --}}
                    <h1>Invoice <span style="color: red">Pembayaran</span></h1>
                </div>
                <p class="periode">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{
                    \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
            </div>
            <div class="header-right">
                <p>Kepada Yth.</p>
                <h2>{{ strtoupper($supplier->nama) }}</h2>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th width="15%">Banyaknya</th>
                    <th width="45%">Nama Barang</th>
                    <th width="20%">@ Harga</th>
                    <th width="20%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sisa Nota Sebelumnya -->
                <tr>
                    <td></td>
                    <td class="fw-bold">Sisa nota sebelumnya</td>
                    <td></td>
                    <td class="text-right fw-bold">{{ number_format($sisaSebelumnya, 0, ',', '.') }}</td>
                </tr>

                @php
                $runningBalance = $sisaSebelumnya;
                $transactionsByDate = $transactions->groupBy('tanggal');
                @endphp

                @foreach($transactionsByDate as $date => $items)
                <!-- Date Group -->
                <tr>
                    <td></td>
                    <td class="date-separator">({{ \Carbon\Carbon::parse($date)->format('d-m-y') }})</td>
                    <td></td>
                    <td></td>
                </tr>

                @foreach($items as $item)
                @if($item->jumlah > 0)
                @php $runningBalance += $item->jumlah; @endphp
                <tr>
                    <td class="text-center">
                        @if($item->lusin > 0) {{ $item->lusin }} lsn @endif
                        @if($item->lusin > 0 && $item->potong > 0) + @endif
                        @if($item->potong > 0) {{ $item->potong }} ptg @endif
                    </td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($item->tf > 0)
                @php $runningBalance -= $item->tf; @endphp
                <tr style="background-color: #fff5f5;">
                    <td></td>
                    <td></td>
                    <td class="text-right fw-bold" style="color: red;">
                        TF {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m') }}
                    </td>
                    <td class="text-right fw-bold" style="color: red;">
                        {{ number_format($item->tf, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
                @endforeach

                <!-- Running Subtotal after date group -->
                <tr class="subtotal-row">
                    <td colspan="3"></td>
                    <td class="text-right fw-bold" style="font-size: 14px;">
                        {{ number_format($runningBalance, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach

                <!-- Final Row -->
                <tr class="final-total-row">
                    <td colspan="3" class="text-right fw-bold" style="font-size: 16px;">SISA TAGIHAN AKHIR</td>
                    <td class="text-right fw-bold" style="font-size: 18px;">
                        Rp {{ number_format($runningBalance, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer-sigs">
            <div class="sig-box">
                <p>Tanda terima</p>
                <div style="height: 60px;"></div>
                <p>( ............................ )</p>
            </div>

            <div class="notice-box">
                Perhatian :<br>
                Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian.!!!
            </div>

            <div class="sig-box">
                <p>Hormat kami</p>
                <div style="height: 60px;"></div>
                <p>( ............................ )</p>
            </div>
        </div>
    </div>

    <script>
        function downloadInvoice() {
            const element = document.getElementById('invoice-container');
            const supplierName = "{{ $supplier->nama }}";
            const startDate = "{{ $startDate }}";
            const endDate = "{{ $endDate }}";
            
            html2canvas(element, {
                scale: 2,
                backgroundColor: "#ffffff",
                logging: false,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `Invoice_${supplierName}_${startDate}_to_${endDate}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>

</html>