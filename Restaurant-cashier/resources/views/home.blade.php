@extends('admin.admin_layout')

@section('title', "Dashboard")

@section('content')

<style>
    .card-text {
        /* width: 80%; */
        margin-left: 0;
        padding-left: 0;
        
    }
    .card-text p {
        margin-bottom: 0.25rem;
        text-align: left;
    }
</style>





    <h1>Üdvözöljük, {{ session('felhasznalo_nev') }}! - Jogosultsága: {{ session('felhasznalo_jogosultsag') }}</h1>
    <div id="success_message"></div>

    <!-- Szűrő gomb -->
    <div class="mt-4">
        <label>
            <input type="checkbox" id="showOldClosedClients"> Régebbi lezárt ügyfelek megjelenítése
        </label>
    </div>

    <!-- Tartalom -->
    <div class="container mt-4">
        <div id="clientlist" class="row row-cols-1 row-cols-md-3 g-4"></div>
        <hr> <!-- Elválasztó vonal -->
        <div id="closedClientList" class="row row-cols-1 row-cols-md-3 g-4 mt-4"></div>
    </div>

    <!-- Kijelentkezés -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Kijelentkezés</button>
    </form>

    @include('printreceipt_modal')

    <!-- JavaScript -->
    <script>
        $(document).ready(function () {
            const showOldClosedClientsCheckbox = $('#showOldClosedClients');

            // Ügyfelek betöltése
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

                        $('#closedClientList').html('');

                        const today = new Date().toISOString().split('T')[0]; // Mai dátum (YYYY-MM-DD)
                        const oneDayAgo = new Date();
                        oneDayAgo.setDate(oneDayAgo.getDate() - 1); // Egy nappal ezelőtti dátum
                        console.log("ma: ", today);
                        console.log("tegnap: ", oneDayAgo);

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
                            let paymentMethod = '';
                            let issueTime = '';
                            if (item.invoices && item.invoices.length > 0) {
                                item.invoices.forEach(invoice => {
                                    invoiceInfo += `<p>Sorszám: ${invoice.invoice_number}, Állapot: ${invoice.status}</p>`;
                                    totalPrice = invoice.total_price;
                                    paymentMethod = invoice.payment_method;

                                    // Dátum megjelenítése
                                    if (invoice.status === 'open') {
                                        // Ha a számla nyitott, akkor a created_at értéke jelenjen meg
                                        issueTime = invoice.created_at ? new Date(invoice.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                                    } else {
                                        // Ha a számla lezárt, akkor az issue_time értéke jelenjen meg
                                        issueTime = invoice.issue_time ? new Date(invoice.issue_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                                    }
                                });
                            }

                            const clientCard = `
                                <div class="col ${item.status === 'closed' ? 'closedClientCard' : 'openClientCard'}" id="${item.id}" style="cursor: pointer; text-align: center;">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title" style="border-bottom: 3px solid; border-color: ${item.color};">${issueTime || ''} ${item.name} #${item.id} (${item.status})</h5>
                                            <div class="card-text">
                                                <p>${products.join('<br>')}</p>
                                                <p><strong>Bruttó összeg: ${item.invoices[0]?.formatted_total_price || 'N/A'} Ft</strong></p>
                                                ${item.status === 'closed' ? `<p><strong>Fizetés módja: ${item.invoices[0]?.payment_method || 'N/A'}</strong></p>` : ''}
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <small class="text-body-secondary">${invoiceInfo}</small>
                                            <button class="btn btn-primary btn-sm print-invoice" data-client-id="${item.id}">Nyugta nyomtatása</button>
                                        </div>
                                    </div>
                                </div>
                            `;

                            if (item.status === 'closed') {
                                const invoiceDate = item.invoices[0]?.issue_time?.split('T')[0]; // Számla dátuma (YYYY-MM-DD)
                                const isOldClient = invoiceDate && new Date(invoiceDate) < new Date(today);

                                // Ha a számla dátuma megegyezik a mai dátummal, vagy a régebbi ügyfelek gomb be van pipálva
                                if (invoiceDate && new Date(invoiceDate) > oneDayAgo || (showOldClosedClientsCheckbox.is(':checked'))) {
                                    $('#closedClientList').append(clientCard);
                                }
                            } else {
                                $('#clientlist').append(clientCard);
                            }
                        });

                        $('#success_message').text("Sikeres frissítés");
                    }
                });
            }

            // Gomb eseménykezelője
            showOldClosedClientsCheckbox.on('change', function () {
                fetchCashierUsers();
            });

            // Eseménykezelők
            $(document).on('click', '.openClientCard', function (e) {
                if ($(e.target).closest('.print-invoice').length === 0) {
                    window.location.href = "/cashier/" + this.id;
                }
            });

            $(document).on('click', '.closedClientCard', function (e) {
                if ($(e.target).closest('.print-invoice').length === 0) {
                    e.preventDefault();
                }
            });

            $(document).on('click', '.print-invoice', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const clientId = $(this).data('client-id');
                console.log("Nyugta nyomtatása ügyfél ID: " + clientId);
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

            // Betöltjük az ügyfeleket
            fetchCashierUsers();
        });
    </script>
@endsection