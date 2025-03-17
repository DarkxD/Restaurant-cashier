{{--      NYOMTATÁS GOMB     --}}
<button class="button green" id="printReceipt" onClick="printReceiptContent('invoice-POS', {{ $invoice->id }})">Nyomtatás</button>



{{--     MODAL A RECEIPTNEK    --}}
<div class="modal">
    <div id="print">
        @include('reports.receipt')
    </div>
</div>


{{--     SCRIPT     --}}
<script>
document.getElementById('printReceipt').addEventListener('click', function () {
    const invoiceId = {{ $invoice->id }};
    fetch(`/get-data-for-receipt/${invoiceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Dinamikusan generáljuk a nyugta HTML tartalmát
            const receiptContent = `
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
                                ${data.invoiceItems.map(item => `
                                    <tr class="service">
                                        <td class="tableitem"><p class="itemtext">${item.quantity} db</p></td>
                                        <td class="tableitem"><p class="itemtext">${item.name}</p></td>
                                        <td class="tableitem"><p class="itemtext">${item.total_price} Ft</p></td>
                                    </tr>
                                `).join('')}
                                <tr class="tabletitle">
                                    <td class="Rate"><h2>Adó</h2></td>
                                    <td></td>
                                    <td class="payment"><h2>${data.invoice.total_vat} Ft</h2></td>
                                </tr>
                                <tr class="tabletitle">
                                    <td class="Rate"><h2>Nettó összeg</h2></td>
                                    <td></td>
                                    <td class="payment"><h2>${data.invoice.net_price} Ft</h2></td>
                                </tr>
                                <tr class="tabletitle">
                                    <td class="Rate"><h2>Végösszeg</h2></td>
                                    <td></td>
                                    <td class="payment"><h2>${data.invoice.total_price} Ft</h2></td>
                                </tr>
                            </table>
                        </div>
                        <div id="legalcopy">
                            <p class="legal">Nyugtaszám: ${data.invoice.invoice_number}</p>
                            <p class="legal">Időpont: ${data.invoice.issue_time}</p>
                            <p class="legal">Kassza: ${data.cashier.nev}</p>
                            <p class="legal"><strong>Mosolygós szép napot!</strong></p>
                        </div>
                    </div>
                </div>
            `;

            // CSS hozzáadása az új ablakhoz
            const css = `
                <style>
                    #invoice-POS {
                        box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
                        padding: 2mm;
                        margin: 0 auto;
                        width: 44mm;
                        background: #FFF;
                    }
                    ::selection { background: #f31544; color: #FFF; }
                    ::-moz-selection { background: #f31544; color: #FFF; }
                    h1 { font-size: 1.5em; color: #222; }
                    h2 { font-size: .9em; }
                    h3 { font-size: 1.2em; font-weight: 300; line-height: 2em; }
                    p { font-size: .7em; color: #666; line-height: 1.2em; }
                    #top, #mid, #bot { border-bottom: 1px solid #EEE; }
                    #top { min-height: 100px; }
                    #mid { min-height: 80px; }
                    #bot { min-height: 50px; }
                    #top .logo {
                        height: 60px;
                        width: 60px;
                        background: url(http://michaeltruong.ca/images/logo1.png) no-repeat;
                        background-size: 60px 60px;
                    }
                    .clientlogo {
                        float: left;
                        height: 60px;
                        width: 60px;
                        background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
                        background-size: 60px 60px;
                        border-radius: 50px;
                    }
                    .info { display: block; margin-left: 0; }
                    .title { float: right; }
                    .title p { text-align: right; }
                    table { width: 100%; border-collapse: collapse; }
                    td { padding: 5px 0 5px 15px; border: 1px solid #EEE; }
                    .tabletitle { padding: 5px; font-size: .5em; background: #EEE; }
                    .service { border-bottom: 1px solid #EEE; }
                    .item { width: 24mm; }
                    .itemtext { font-size: .5em; }
                    #legalcopy { margin-top: 5mm; }
                    @media print {
                        #printPageButton {
                            display: none; /* Elrejti a gombot a nyomtatás során */
                        }
                    }
                </style>
            `;

            // Nyomtatási gomb hozzáadása
            const printButton = '<input type="button" id="printPageButton" class="printPageButton" style="display:block; width:100%; border:none; background-color: #008B8B; color:#fff; padding:14px 28px; font-size:16px; cursor:pointer; text-align:center;" value="Nyomtatás!" onClick="window.print()">';

            // Új ablak megnyitása és az adatok beillesztése
            const myReceipt = window.open("", "myWin", "left=150, top=130, width=400, height=600");
            myReceipt.document.write(`
                <html>
                    <head>
                        <title>Nyugta nyomtatása</title>
                        ${css}
                    </head>
                    <body>
                        ${printButton}
                        ${receiptContent}
                    </body>
                </html>
            `);
            myReceipt.document.close();
            myReceipt.focus();
        })
        .catch(error => {
            console.error('Hiba történt az adatok lekérésekor:', error);
        });
});
</script>