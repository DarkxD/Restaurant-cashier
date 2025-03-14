<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div id="invoice-POS">
        <center id="top">
            <div class="logo"></div>
            <div class="info">
                <h2>SBISTechs Inc</h2>
            </div>
        </center>

        <div id="mid">
            <div class="info">
                <h2>Contact Info</h2>
                <p>
                    Address : street city, state 0000<br>
                    Email : JohnDoe@gmail.com<br>
                    Phone : 555-555-5555<br>
                </p>
            </div>
        </div>

        <div id="bot">
            <div id="table">
                <table>
                    <tr class="tabletitle">
                        <td class="item"><h2>Menny.</h2></td>
                        <td class="Hours"><h2>Név</h2></td>
                        <td class="Rate"><h2>Összeg</h2></td>
                    </tr>
                    @php
                        $totalVat = 0;
                    @endphp
                    @foreach($invoice->items as $invoiceItem)
                        <tr class="service">
                            <td class="tableitem"><p class="itemtext">{{ $invoiceItem->quantity }} db</p></td>
                            <td class="tableitem"><p class="itemtext">{{ $invoiceItem->item->name }}</p></td>
                            <td class="tableitem"><p class="itemtext">{{ number_format($invoiceItem->total_price, 0, ',', '.') }} Ft</p></td>
                        </tr>
                        @php
                            $vatAmount = ($invoiceItem->total_price - $invoiceItem->unit_price_netto);
                            $totalVat += $vatAmount;
                        @endphp
                    @endforeach

                    <tr class="tabletitle">
                        <td class="Rate"><h2>Adó</h2></td>
                        <td></td>
                        <td class="payment"><h2>{{ number_format($totalVat, 0, ',', '.') }} Ft</h2></td>
                    </tr>
                    <tr class="tabletitle">
                        <td class="Rate"><h2>Nettó összeg</h2></td>
                        <td></td>
                        <td class="payment"><h2>{{ number_format($invoice->total_price - $totalVat, 0, ',', '.') }} Ft</h2></td>
                    </tr>
                    <tr class="tabletitle">
                        <td class="Rate"><h2>Végösszeg</h2></td>
                        <td></td>
                        <td class="payment"><h2>{{ number_format($invoice->total_price, 0, ',', '.') }} Ft</h2></td>
                    </tr>
                </table>
            </div>
            <div id="legalcopy">
                <p class="legal">Nyugtaszám: {{ $invoice->invoice_number }}</p>
                <p class="legal">Időpont: {{ $invoice->issue_time ?? date("Y-m-d H:i:s") }}</p>
                <p class="legal">Kassza: {{ $invoice->cashier->nev }}</p>
                <p class="legal"><strong>Mosolygós szép napot!</strong></p>
            </div>
        </div>
    </div>
</body>
</html>