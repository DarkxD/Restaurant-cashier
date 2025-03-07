<!DOCTYPE html>
<html>
<head>
    <title>Nyugta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 80mm; /*80mm Keskeny nyugta szélessége */
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Étterem neve</h1>
        <img src="" alt="">
        <h4>Progress Étteremhálózat Kft.</h4>
        <p>Cím: 1234 Budapest, <br>Fő utca 1.</p>
        <p>Telefon: +36 1 234 5678</p>
        <p>Adószám: 10624500-2-44</p>
        <h1>-----     NYUGTA     -----</h1>
        <p>NEM ADÓÜGYI BIZONYLAT</p>
    </div>
    <p><strong>Nyugta száma:</strong> {{ $invoice->invoice_number }}</p>
    <p><strong>Dátum:</strong> {{ $invoice->issue_time }}</p>
    <p><strong>Pénztáros:</strong> {{ $invoice->cashier->nev }}</p>

    <h2>Termékek</h2>
    <table>
        <thead>
            <tr>
                <th>Termék</th>
                <th>Egységár</th>
                <th>Áfa</th>
                <th>Ár</th>
            </tr>
        </thead> 
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->quantity }} db {{ $item->item->name }}</td>
                    <td>{{ number_format($item->unit_price_netto, 0) }} Ft</td>
                    <td>{{ number_format($item->quantity*($item->unit_price_netto*($item->vat/100)), 0) }} Ft</td>
                    <td>{{ number_format($item->total_price, 0) }} Ft</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <p class="total"><strong>Nettó ár:</strong> {{ number_format( , 0) }} Ft</p> --}}
    {{-- <p class="total"><strong>Áfa:</strong> {{ number_format( , 0) }} Ft</p> --}}
    {{-- <p class="total"><strong>Kerekítés:</strong> {{ number_format( , 0) }} Ft</p> --}}

    <p class="total"><strong>Összesen:</strong> {{ number_format($invoice->total_price, 0) }} Ft</p>

    <div class="footer">
        <p>Köszönjük a vásárlást!</p>
        <p>www.etterem.hu</p>
    </div>
</body>
</html>