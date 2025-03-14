<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        #invoice-POS{
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
            padding:2mm;
            margin: 0 auto;
            width: 72mm;
            background: #FFF;
            
            
        ::selection {background: #f31544; color: #FFF;}
        ::moz-selection {background: #f31544; color: #FFF;}
        h1{
            font-size: 1.5em;
            color: #222;
        }
        h2{font-size: .9em;}
        h3{
            font-size: 1.2em;
            font-weight: 300;
            line-height: 2em;
        }
        p{
            font-size: .7em;
            color: #666;
            line-height: 1.2em;
        }
        
        #top, #mid,#bot{ /* Targets all id with 'col-' */
            border-bottom: 1px solid #EEE;
        }
        
        #top{min-height: 100px;}
        #mid{min-height: 80px;} 
        #bot{ min-height: 50px;}
        
        #top .logo{
            //float: left;
            height: 60px;
            width: 60px;
            background: url(http://michaeltruong.ca/images/logo1.png) no-repeat;
            background-size: 60px 60px;
        }
        .clientlogo{
            float: left;
            height: 60px;
            width: 60px;
            background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
            background-size: 60px 60px;
            border-radius: 50px;
        }
        .info{
            display: block;
            //float:left;
            margin-left: 0;
        }
        .title{
            float: right;
        }
        .title p{text-align: right;} 
        table{
            width: 100%;
            border-collapse: collapse;
        }
        td{
            //padding: 5px 0 5px 15px;
            //border: 1px solid #EEE
        }
        .tabletitle{
            //padding: 5px;
            font-size: .5em;
            background: #EEE;
        }
        .service{border-bottom: 1px solid #EEE;}
        .item{width: 24mm;}
        .itemtext{font-size: .5em;}
        
        #legalcopy{
            margin-top: 5mm;
        }
        @media print {
            /* Elrejtjük a nem kívánt elemeket */
            body * {
                visibility: hidden;
            }

            /* Csak a nyugta tartalma látszik */
            #invoice-POS, #invoice-POS * {
                visibility: visible;
            }

            /* A nyugta pozicionálása */
            #invoice-POS {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
            
            
        }
    </style>
</head>
<body>
    <input type="button" id="printPageButton" class="printPageButton" style="display:block; width:100%; border:none; background-color: #008B8B; color:#fff; padding:14px 28px; font-size:16px; cursor:pointer; text-align:center;" value="Nyomtatás!" onClick="printReceipt()">
    {{-- <button type="button" class="btn btn-primary" onclick="printReceipt()">Nyomtatás222</button> --}}
    @if(isset($invoice) && isset($invoiceItems) && isset($cashier))
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


        
        </div>
    @else
        <p>Hiba: Hiányzó adatok a nyugta megjelenítéséhez.</p>
    @endif
    <div id="invoice-POS">
        
</body>


<script>
    function printReceipt() {
    const printContent = document.getElementById('receiptContent').innerHTML;
    const originalContent = document.body.innerHTML;

    // Az oldal tartalmának cseréje a nyugta tartalmára
    document.body.innerHTML = printContent;

    // Nyomtatás indítása
    window.print();

    // Visszaállítás az eredeti tartalomra
    document.body.innerHTML = originalContent;

    // Az oldal újratöltése (opcionális)
    window.location.reload();
}
</script>
</html>