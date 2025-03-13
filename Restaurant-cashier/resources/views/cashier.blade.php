<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Cashier</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/cashier.css') }}" rel="stylesheet">
    <!-- Font Awesome hozzáadása a head részben -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <button class="toggle-sidebar">☰</button>
    <div class="header">
        <h1>{{ session()->get('felhasznalo_nev') }} - {{ Carbon\Carbon::now()->toDateTimeString() }}</h1>
        <div class="actions">
            <button class="admin_access" style="display:none;">Admin</button>
        </div>
    </div>

    <div class="tabs">
        <form action="{{ route('home') }}">
            <button type="submit">Ügyfelek</button>
        </form>
        
        <button class="button new-client">Új ügyfél</button>
        <button class="button red">Ügyfél zárása</button>
        <form action="{{ route('logout') }}" method="POST"> 
            @csrf
            <button class="button red" type="submit">Kijelentkezés</button>
        </form>
        <button>Étlap módosítás</button>
    </div>

    <div class="main">
        <div class="sidebar">
            
            
            
        </div>

        <div class="content">
            <div class="orders">
                <h2>{{ $client->name }} #{{ $client->id }}<button id="edit-client-btn" class="button" style="margin:0; margin-left:10px; padding:0;background: none; border: none; cursor: pointer; font-size:3vh;">
                    <i class="fas fa-edit"></i>
                </button></h2>
                



                <table id="invoice-items-list">
                    @foreach($invoiceItems as $item)
                        <tr data-invoice-item-id="{{ $item->id }}">
                            <td><input type="checkbox"></td>
                            <td>{{ $item->item->name }}&emsp;</td>
                            <td>{{ $item->unit_price_netto }} Ft&emsp;</td>
                            <td>({{ $item->vat }}%): {{ $item->total_price-$item->unit_price_netto }} Ft&emsp;</td>
                            <td>{{ $item->total_price }} Ft&emsp;</td>
                            <?php $invoice->netto += $item->unit_price_netto ?>
                            <td><i class="delete-item fas fa-trash" data-invoice-item-id="{{ $item->id }}"></i></td>
                        </tr>
                    @endforeach
                </table>
                <div class="orders-footer">
                    <button class="button green">Nyomtatás</button>
                    <button class="button red" id="close-invoice">Zárás</button>
                    <div class="prices">
                        <p>Nettó összeg: <span id="netto-total">{{ $invoice ? $invoice->netto : 0 }}</span> Ft</p>
                        <p>ÁFA összeg: <span id="vat-total">{{ $invoice ? round($invoice->total_price - $invoice->netto, 0) : 0 }}</span> Ft</p>
                        <p>Bruttó összeg: <strong><span id="brutto-total" style="font-size: 3vh;">{{ $invoice ? round($invoice->total_price, 0) : 0 }}</span> Ft</strong></p>
                    </div>
                </div>
            </div>

            <div class="menu">
                <h2>Étlap</h2>
                <div class="product">
                    @foreach($categories as $category)
                        @foreach($category->items as $item)
                            <div data-category-id="{{ $category->id }}" data-item-id="{{ $item->id }}">
                                <img src="{{ asset('/storage/' . $item->image) }}" alt="{{ $item->name }}">
                                <p>{{ $item->name }}</p>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div class="notifications">
                <h2>Értesítések</h2>
                <div class="notification-box">
                    <p><b>Ügyfél #8</b></p>
                    <p>Kisscsillag pizza elkészült 12:30</p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="category" data-category-id="all">
            <img src="{{ asset('/storage/app_images/400x400all_category.webp') }}" alt="Minden kategória">
        </div>
        @foreach($categories as $category)
            <div class="category" data-category-id="{{ $category->id }}">
                <img src="{{ asset('/storage/' . $category->image) }}" alt="{{ $category->name }}">
            </div>
        @endforeach
    </div>

    <!-- Áfakulcs kiválasztó modal -->
    <div id="vat-modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered"> <!-- Középre igazítás -->
            <div class="modal-content">
                <div class="modal-header text-center"> <!-- Fejléc középre igazítva -->
                    <h5 class="modal-title w-100">Áfakulcs kiválasztása</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center"> <!-- Tartalom középre igazítva -->
                    <p>Válassz áfakulcsot a termékhez:</p>
                    <div class="d-flex flex-column gap-3"> <!-- Gombok egymás alatt, közötti térrel -->
                        <button id="vat-5" class="btn btn-primary btn-lg">5%</button> <!-- Nagyobb gomb -->
                        <button id="vat-27" class="btn btn-primary btn-lg">27%</button> <!-- Nagyobb gomb -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client szerkesztési panel -->
    <div id="edit-client-modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ügyfél szerkesztése</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-client-form">
                        <div class="mb-3">
                            <label for="client-name" class="form-label">Név</label>
                            <input type="text" class="form-control" id="client-name" name="name" value="{{ $client->name }}">
                        </div>
                        <div class="mb-3">
                            <label for="client-iranyitoszam" class="form-label">Irányítószám</label>
                            <input type="text" class="form-control" id="client-iranyitoszam" name="iranyitoszam" value="{{ $client->iranyitoszam }}">
                        </div>
                        <div class="mb-3">
                            <label for="client-telepules" class="form-label">Település</label>
                            <input type="text" class="form-control" id="client-telepules" name="telepules" value="{{ $client->telepules }}">
                        </div>
                        <div class="mb-3">
                            <label for="client-utca_hazszam" class="form-label">Utca, házszám</label>
                            <input type="text" class="form-control" id="client-utca_hazszam" name="utca_hazszam" value="{{ $client->utca_hazszam }}">
                        </div>
                        <div class="mb-3">
                            <label for="client-note" class="form-label">Megjegyzés</label>
                            <textarea class="form-control" id="client-note" name="note">{{ $client->note }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="client-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="client-email" name="email" value="{{ $client->email }}">
                        </div>
                        <div class="mb-3">
                            <label for="client-phone" class="form-label">Telefonszám</label>
                            <input type="text" class="form-control" id="client-phone" name="phone" value="{{ $client->phone }}">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                    <button type="button" class="btn btn-primary" id="save-client-changes">Mentés</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Zárás modal -->
    <div id="close-invoice-modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Zárás</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button id="close-cash" class="btn btn-success">Készpénz</button>
                    <button id="close-card" class="btn btn-primary">Bankkártya</button>
                    <button id="delete-invoice" class="btn btn-danger">Törlés</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        const toggleButton = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        document.querySelectorAll('.category').forEach(category => {
            category.addEventListener('click', () => {
                const categoryId = category.getAttribute('data-category-id');
                filterMenuItems(categoryId);
            });
        });

        function filterMenuItems(categoryId) {
            const menuItems = document.querySelectorAll('.menu .product div');
            menuItems.forEach(item => {
                const itemCategoryId = item.getAttribute('data-category-id');
                if (itemCategoryId === categoryId || categoryId === 'all') {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        let selectedVatRate = null;
        let selectedItemId = null;

        // Áfakulcs kiválasztása modalban
        document.querySelectorAll('.menu .product div').forEach(item => {
            item.addEventListener('click', () => {
                selectedItemId = item.getAttribute('data-item-id');
                const modal = new bootstrap.Modal(document.getElementById('vat-modal'));
                modal.show();
            });
        });

        // 5% áfakulcs kiválasztása
        document.getElementById('vat-5').addEventListener('click', () => {
            selectedVatRate = 5;
            addItemToInvoice(selectedItemId, selectedVatRate);
            const modal = bootstrap.Modal.getInstance(document.getElementById('vat-modal'));
            modal.hide();
        });

        // 27% áfakulcs kiválasztása
        document.getElementById('vat-27').addEventListener('click', () => {
            selectedVatRate = 27;
            addItemToInvoice(selectedItemId, selectedVatRate);
            const modal = bootstrap.Modal.getInstance(document.getElementById('vat-modal'));
            modal.hide();
        });

        document.getElementById('edit-client-btn').addEventListener('click', () => {
            const editModal = new bootstrap.Modal(document.getElementById('edit-client-modal'));
            editModal.show();
        });

        document.getElementById('save-client-changes').addEventListener('click', () => {
            const formData = new FormData(document.getElementById('edit-client-form'));
            const clientId = {{ $client->id }};

            fetch(`/clients/${clientId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Ügyfél adatai sikeresen frissítve!');
                    window.location.reload(); // Oldal frissítése az új adatokkal
                } else {
                    alert('Hiba történt az adatok frissítése közben.');
                }
            })
            .catch(error => {
                console.error('Hiba:', error);
            });
        });




        // Tétel hozzáadása a számlához
        function addItemToInvoice(itemId, vatRate) {
            const clientId = {{ $client->id }};
            const quantity = 1; // Alapértelmezett mennyiség
            const cashierId = {{ $cashier->id }}; // Kasszás azonosítója

            fetch('/add-item-to-invoice', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    client_id: clientId,
                    item_id: itemId,
                    quantity: quantity,
                    cashier_id: cashierId,
                    vat: vatRate,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Új tétel hozzáadása a listához
                    const invoiceItemsList = document.getElementById('invoice-items-list');
                    const newItem = document.createElement('tr');
                    newItem.setAttribute('data-invoice-item-id', data.invoiceItem.id);
                    
                    newItem.innerHTML = `
                        <td><input type="checkbox"></td>
                        <td>${data.item.name}&emsp;</td>
                        <td>${data.unit_price_netto} Ft&emsp;</td>
                        <td>(${data.invoiceItem.vat}%): ${data.szamolt_vat_ertek} Ft&emsp;</td>
                        <td>${data.invoiceItem.total_price} Ft&emsp;</td>
                        <td><i class="delete-item fas fa-trash" data-invoice-item-id="${data.invoiceItem.id}"></i></td>
                    `;
                    invoiceItemsList.appendChild(newItem);

                    // Eseményfigyelő hozzáadása az új törlés gombhoz
                    const deleteButton = newItem.querySelector('.delete-item');
                    deleteButton.addEventListener('click', () => {
                        deleteInvoiceItem(data.invoiceItem.id, newItem);
                    });

                    // Összesített adatok frissítése
                    updateTotals(data.invoice);
                } else {
                    alert('Hiba történt a termék hozzáadása közben.');
                }
            })
            .catch(error => {
                console.error('Hiba:', error);
            });
        }

        // Tétel törlése
        function deleteInvoiceItem(invoiceItemId, itemElement) {
            fetch(`/delete-invoice-item/${invoiceItemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Tétel eltávolítása a listából
                    itemElement.remove();
                    // Összesített adatok frissítése
                    updateTotals(data.invoice);
                } else {
                    alert('Hiba történt a tétel törlése közben.');
                }
            })
            .catch(error => {
                console.error('Hiba:', error);
            });
        }

        // Összesített adatok frissítése
        function updateTotals(invoice) {
            if (!invoice || !invoice.items) {
                console.error("Hiba: Az invoice objektum hiányzik vagy hibás szerkezetű.");
                return;
            }

            const nettoTotal = invoice.items.reduce((sum, item) => sum + (item.unit_price_netto), 0);
            const vatTotal = invoice.items.reduce((sum, item) => sum + (item.total_price - item.unit_price_netto), 0);
            const bruttoTotal = invoice.total_price;

            document.getElementById('netto-total').textContent = nettoTotal;
            document.getElementById('vat-total').textContent = vatTotal;
            document.getElementById('brutto-total').textContent = Math.round(bruttoTotal / 5) * 5;
        }

        // Eseményfigyelők hozzáadása a törlés gombokhoz az oldal betöltésekor
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-item').forEach(button => {
                button.addEventListener('click', function() {
                    const invoiceItemId = this.getAttribute('data-invoice-item-id');
                    const itemElement = this.closest('tr');
                    deleteInvoiceItem(invoiceItemId, itemElement);
                });
            });
        });






        document.querySelector('.button.new-client').addEventListener('click', function () {
            const selectedItems = Array.from(document.querySelectorAll('#invoice-items-list input[type="checkbox"]:checked'))
                .map(checkbox => checkbox.closest('tr').getAttribute('data-invoice-item-id'));

            const originalClientName = "{{ $client->name }}"; // Az eredeti ügyfél neve
            const originalClientColor = "{{ $client->color }}"; // Az eredeti ügyfél színe

            // Név és szín beállítása
            let newClientName, newClientColor;
            let newClientId; // A változót itt deklaráljuk, hogy minden ágban elérhető legyen

            if (selectedItems.length > 0) {
                // Ha vannak kijelölt tételek, megtartjuk az eredeti nevet és színt, de hozzáfűzzük, hogy "osztott"
                newClientName = `${originalClientName} osztott`;
                newClientColor = originalClientColor;
            } else {
                // Ha nincsenek kijelölt tételek, akkor a /clients/store endpointot használjuk
                fetch('/clients/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({}) // Üres body, mivel a szerver generálja a nevet és a színt
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Hiba történt az új ügyfél létrehozása közben.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 200) {
                        newClientId = data.clientID; // Itt adjuk értéket a változónak

                        // Kassza oldal megnyitása
                        window.location.href = `/cashier/${newClientId}`;
                    } else {
                        throw new Error('Hiba történt az új ügyfél létrehozása közben.');
                    }
                })
                .catch(error => {
                    console.error('Hiba:', error);
                    alert(error.message);
                });

                return; // Kilépünk a függvényből, mivel a /clients/store már kezeli a folyamatot
            }

            // Ha vannak kijelölt tételek, akkor a korábbi logika szerint folytatjuk
            fetch('/create-new-client', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: newClientName,
                    color: newClientColor,
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Hiba történt az új ügyfél létrehozása közben.');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    newClientId = data.client.id; // Itt adjuk értéket a változónak

                    // Új számla létrehozása
                    return fetch('/create-new-invoice', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            client_id: newClientId,
                            cashier_id: {{ $cashier->id }},
                        })
                    });
                } else {
                    throw new Error('Hiba történt az új ügyfél létrehozása közben.');
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Hiba történt az új számla létrehozása közben.');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const newInvoiceId = data.invoice.id;

                    // Tételek átmozgatása az új számlára
                    if (selectedItems.length > 0) {
                        return fetch('/move-items-to-new-invoice', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                original_invoice_id: {{ $invoice->id }},
                                new_invoice_id: newInvoiceId,
                                items: selectedItems,
                            })
                        });
                    } else {
                        // Ha nincs kijelölt tétel, akkor csak térjünk vissza egy üres válasszal
                        return Promise.resolve({ status: 'success' });
                    }
                } else {
                    throw new Error('Hiba történt az új számla létrehozása közben.');
                }
            })
            .then(response => {
                if (response && !response.ok) {
                    throw new Error('Hiba történt a tételek átmozgatása közben.');
                }
                return response ? response.json() : { status: 'success' };
            })
            .then(data => {
                if (data.status === 'success') {
                    // Kassza oldal megnyitása
                    window.location.href = `/cashier/${newClientId}`;
                } else {
                    throw new Error('Hiba történt a tételek átmozgatása közben.');
                }
            })
            .catch(error => {
                console.error('Hiba:', error);
                alert(error.message);
            });
        });



        document.getElementById('close-invoice').addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('close-invoice-modal'));
            modal.show();
        });

        document.getElementById('close-cash').addEventListener('click', function () {
            closeInvoice('cash');
        });

        document.getElementById('close-card').addEventListener('click', function () {
            closeInvoice('card');
        });

        document.getElementById('delete-invoice').addEventListener('click', function () {
            if (confirm('Biztosan törölni szeretnéd a számlát?')) {
                deleteInvoice();
            }
        });

        function closeInvoice(paymentMethod) {
            const clientId = {{ $client->id }};
            const invoiceId = {{ $invoice->id }};

            fetch('/close-invoice', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    client_id: clientId,
                    invoice_id: invoiceId,
                    payment_method: paymentMethod,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (confirm('Szeretnéd kinyomtatni a nyugtát?')) {
                        // Nyomtatás logikája itt lehetne
                    } else {
                        window.location.href = '/home';
                    }
                } else {
                    alert('Hiba történt a zárás közben.');
                }
            })
            .catch(error => {
                console.error('Hiba:', error);
            });
        }

        function deleteInvoice() {
            const clientId = {{ $client->id }};
            const invoiceId = {{ $invoice->id }};

            fetch('/delete-invoice', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    client_id: clientId,
                    invoice_id: invoiceId,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = '/home';
                } else {
                    alert('Hiba történt a törlés közben.');
                }
            })
            .catch(error => {
                console.error('Hiba:', error);
            });
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>