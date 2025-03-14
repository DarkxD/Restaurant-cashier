@extends('admin.admin_layout')

@section('title', "Dashboard")

@section('content')
    <h1>Üdvözöljük, {{ session('felhasznalo_nev') }}! - Jogosultsága: {{ session('felhasznalo_jogosultsag') }}</h1>
    <div id="success_message"></div>

    <!-- Tartalom -->
    <div class="container mt-4">
        <div id="clientlist" class="row row-cols-1 row-cols-md-3 g-4"></div>
    </div>

    <!-- Kijelentkezés -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Kijelentkezés</button>
    </form>

    <!-- Modal ablak -->
    <div id="receiptModal" class="modal fade" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptModalLabel">Nyugta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="receiptContent">
                    <!-- Ide kerül a dinamikusan betöltött nyugta tartalom -->
                    @include('reports.receipt')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                    <button type="button" class="btn btn-primary" onclick="printReceipt()">Nyomtatás</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        $(document).ready(function () {
            fetchCashierUsers();

            function fetchCashierUsers() {
                $.ajax({
                    type: "GET",
                    url: "/clients/fetch-clients",
                    dataType: "json",
                    success: function (response) {
                        console.log(response.clientUsers);
                        $('#clientlist').html(
                            '<div class="createClientCard" style="cursor: pointer; padding: 20px; border: 1px solid #ccc; border-radius: 10px; text-align: center;">\
                                <h3>Új Ügyfél</h3>\
                                <p>Kattints ide egy új ügyfél létrehozásához</p>\
                            </div>'
                        );

                        $.each(response.clientUsers, function (key, item) {
                            let products = [];
                            if (item.invoices && item.invoices.length > 0) {
                                item.invoices.forEach(invoice => {
                                    if (invoice.items && invoice.items.length > 0) {
                                        invoice.items.forEach(invoiceItem => {
                                            if (invoiceItem.item) {
                                                products.push(invoiceItem.item.name);
                                            }
                                        });
                                    }
                                });
                            }

                            let invoiceInfo = '';
                            let totalPrice = 0;
                            if (item.invoices && item.invoices.length > 0) {
                                item.invoices.forEach(invoice => {
                                    invoiceInfo += `<p>Sorszám: ${invoice.invoice_number}, Állapot: ${invoice.status}</p>`;
                                    totalPrice = invoice.total_price;
                                });
                            }

                            $('#clientlist').append(
                                `<div class="col openClientCard" id="${item.id}" style="cursor: pointer; text-align: center;">\
                                    <div class="card h-100">\
                                        <div class="card-body">\
                                            <h5 class="card-title" style="border-bottom: 3px solid; border-color: ${item.color};">${item.name} #${item.id} (${item.status})</h5>\
                                            <p class="card-text">${products.join('<br>')}</p>\
                                            <p class="card-text"><strong>Bruttó összeg: ${item.invoices[0].formatted_total_price} Ft</strong></p>\
                                        </div>\
                                        <div class="card-footer">\
                                            <small class="text-body-secondary">${invoiceInfo}</small>\
                                            <button class="btn btn-primary btn-sm print-invoice" data-client-id="${item.id}}">Nyugta nyomtatása</button>\
                                        </div>\
                                    </div>\
                                </div>`
                            );
                        });

                        $('#success_message').text("Sikeres frissítés");
                    }
                });
            }

            $(document).on('click', '.print-invoice', function (e) {
                e.stopPropagation();
                const clientId = $(this).data('client-id'); // Az client ID lekérése a gomb adatattribútumából

                $.ajax({
                    type: "GET",
                    url: `/get-receipt-data-by-client/${clientId}`,
                    dataType: "json",
                    success: function (response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }

                        // A modal tartalmának frissítése
                        $('#receiptContent').html(response.html);

                        // Modal megnyitása
                        $('#receiptModal').modal('show');
                    },
                    error: function (error) {
                        console.error('Hiba történt az adatok lekérésekor:', error);
                    }
                });
            });

            $(document).on('click', '.openClientCard', function (e) {
                window.location.href = "/cashier/" + this.id;
            });

            $(document).on('click', '.createClientCard', function (e) {
                e.preventDefault();
                var data = {};

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "/clients/store",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if (response.status != 200) {
                            $('#saveform_errorList').html('');
                            $('#saveform_errorList').addClass('alert alert-danger');
                            $.each(response.errors, function (key, err_values) {
                                $('#saveform_errorList').append('<li>' + err_values + '</li>');
                            });
                        } else {
                            window.location.href = "/cashier/" + response.clientID;
                        }
                        console.log(response);
                    }
                });
            });
        });

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
@endsection