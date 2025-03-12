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
</head>
<body>
    <button class="toggle-sidebar">☰</button>
    <div class="header">
        <h1>{{ session()->get('felhasznalo_nev') }} - {{ Carbon\Carbon::now()->toDateTimeString() }}</h1>
        <div class="actions">
            <button class="admin_access" style="display:none;">Admin</button>
            <button>Étlap módosítás</button>
        </div>
    </div>

    <div class="tabs">
        <form action="{{ route('home') }}">
            <button type="submit">Ügyfelek</button>
        </form>
        
        <button class="button new-client">Új ügyfél</button>
    </div>

    <div class="main">
        <div class="sidebar">
            <button class="button red">Ügyfél zárása</button>
            
            <form action="{{ route('logout') }}" method="POST"> 
                @csrf
                <button class="button red" type="submit">Kijelentkezés</button>
            </form>
        </div>

        <div class="content">
            <div class="orders">
                <h2>{{ $client->name }} #{{ $client->id }}</h2>
                <ul id="invoice-items-list">
                    @foreach($invoiceItems as $item)
                        <li data-invoice-item-id="{{ $item->id }}">
                            <input type="checkbox">
                            {{ $item->item->name }}&emsp;
                            {{ $item->unit_price_netto }} Ft&emsp;
                            ({{ $item->vat }}%): {{ $item->total_price-$item->unit_price_netto }} Ft&emsp;
                            {{ $item->total_price }} Ft&emsp;
                            <?php $invoice->netto += $item->unit_price_netto ?>
                            <button class="delete-item" data-invoice-item-id="{{ $item->id }}">&#10005;</button>
                        </li>
                    @endforeach
                </ul>
                <div>
                    <p>Nettó összeg: <span id="netto-total">{{ $invoice->netto ?? 0 }}</span> Ft</p>
                    <p>ÁFA összeg: <span id="vat-total">{{ round($invoice->total_price - $invoice->netto ?? 0, 0) }}</span> Ft</p>
                    <p>Bruttó összeg: <span id="brutto-total">{{ round($invoice->total_price ?? 0, 0) }}</span> Ft</p>
                </div>
                <div>
                    <button class="button green">Nyomtatás</button>
                    <button class="button green">Fizetés</button>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Áfakulcs kiválasztása</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Válassz áfakulcsot a termékhez:</p>
                    <button id="vat-5" class="btn btn-primary">5%</button>
                    <button id="vat-27" class="btn btn-primary">27%</button>
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
                    const newItem = document.createElement('li');
                    newItem.setAttribute('data-invoice-item-id', data.invoiceItem.id);
                    
                    newItem.innerHTML = `
                        <input type="checkbox">
                        ${data.item.name}&emsp;
                        ${data.unit_price_netto} Ft&emsp;
                        (${data.invoiceItem.vat}%): ${data.szamolt_vat_ertek} Ft&emsp;
                        ${data.invoiceItem.total_price} Ft&emsp;
                        <button class="delete-item" data-invoice-item-id="${data.invoiceItem.id}">&#10005;</button>
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
                    const itemElement = this.closest('li');
                    deleteInvoiceItem(invoiceItemId, itemElement);
                });
            });
        });






        document.querySelector('.button.new-client').addEventListener('click', function () {
            const selectedItems = Array.from(document.querySelectorAll('#invoice-items-list input[type="checkbox"]:checked'))
                .map(checkbox => checkbox.closest('li').getAttribute('data-invoice-item-id'));

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
                        // Kassza oldal megnyitása
                        window.location.href = `/cashier/${newClientId}`;
                    } else {
                        throw new Error('Hiba történt az új számla létrehozása közben.');
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
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>