<<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étlap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Alap stílusok */
        body.with-admin-header {
            padding-top: 70px;
        }
        
        .menu-header {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .menu-header {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .category-header {
            margin-top: 2rem;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 0.5rem;
        }
        
        .item-card {
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
        
        .item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .item-name {
            font-size: 1.25rem;
            font-weight: bold;
            margin-top: 0.5rem;
            color: #333;
        }
        
        .item-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .item-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #28a745;
        }
        
        .tag-badge {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            background-color: #f8f9fa;
            color: #212529;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        
        .tags-container {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        /* Admin panel stílusok */
        .admin-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1000;
            padding: 20px;
            overflow-y: auto;
        }
        
        .admin-panel.active {
            right: 0;
        }
        
        .admin-toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            display: none;
            padding: 10px 15px;
            border-radius: 50px;
            font-weight: bold;
        }
        
        .category-admin-header {
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            padding-bottom: 0.3rem;
            border-bottom: 1px solid #eee;
        }
        
        .admin-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }
        
        .admin-item:hover {
            background-color: #f8f9fa;
        }
        
        .admin-item-name {
            flex-grow: 1;
            padding-right: 15px;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #28a745;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        /* Alert stílusok */
        .alert-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }
        
        /* Reszponzív beállítások */
        @media (max-width: 768px) {
            .admin-panel {
                width: 100%;
                right: -100%;
            }
            
            .admin-panel.active {
                right: 0;
            }
            
            .category-header {
                font-size: 1.5rem;
            }
        }

        /* Admin fejléc stílusai */
        .admin-menu-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .admin-menu-header .navbar-brand {
            font-weight: bold;
        }
        
        .admin-menu-header .nav-link.active {
            font-weight: bold;
            color: #28a745 !important;
        }
    </style>
</head>
<body class="@if(session('pinkod_authenticated') && session('felhasznalo_jogosultsag') == 'administrator')with-admin-header @endif">
    <!-- Admin fejléc (csak ha be van jelentkezve adminként) -->
    @if(session('pinkod_authenticated') && session('felhasznalo_jogosultsag') == 'administrator')
        <nav class="navbar navbar-expand-lg navbar-light bg-light admin-menu-header">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">Admin</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenuNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminMenuNavbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('menu') }}">Étlap</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                Admin
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.items') }}">Termékek</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.categories') }}">Kategóriák</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.tags') }}">Címkék</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    @endif

    <!-- Eredeti fejléc -->
    <header class="menu-header">
        <div class="container text-center">
            <h1 class="display-4">Étlap</h1>
            <p class="lead">Fedezze fel ínycsiklandó ételeinket és italainkat!</p>
        </div>
    </header>

    <div class="container">
        <!-- Admin panel toggle gomb -->
        @if(session('pinkod_authenticated'))
            <button id="adminToggleBtn" class="btn btn-primary admin-toggle-btn">
                <i class="bi bi-gear-fill"></i> Menü Szerkesztő
            </button>
        @endif

        <!-- Étkezési menü -->
        <div id="menuContainer">
            @foreach($categories as $category)
                <div class="menu-category" data-category-id="{{ $category->id }}">
                    <div class="category-header">
                        {{ $category->name }}
                    </div>
                    <div class="row">
                        @foreach($category->items as $item)
                            <div class="col-md-4 mb-4 menu-item" data-id="{{ $item->id }}">
                                <div class="card item-card">
                                    @if($item->image)
                                        <img src="{{ asset('/storage/' . $item->image) }}" class="card-img-top item-image" alt="{{ $item->name }}">
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title item-name">{{ $item->name }}</h5>
                                        <p class="card-text item-description">{{ $item->description }}</p>
                                        
                                        @if($item->tags->count() > 0)
                                            <div class="tags-container">
                                                @foreach($item->tags as $tag)
                                                    <span class="badge tag-badge">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <p class="card-text item-price">{{ number_format($item->price_brutto, 0, ',', ' ') }} Ft</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Admin panel -->
        <div id="adminPanel" class="admin-panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Menü Szerkesztő</h3>
                <button id="closeAdminPanel" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="mb-3">
                <input type="text" id="adminSearch" class="form-control" placeholder="Keresés...">
            </div>
            
            <div id="adminItemsContainer">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Betöltés...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
$(document).ready(function() {

    if($('body').hasClass('with-admin-header')) {
                $('#adminPanel').css('top', '70px');
                $('#adminToggleBtn').css('top', '80px');
                
                // Alert pozíció módosítása
                window.showAlert = function(type, message) {
                    const $alert = $(`
                        <div class="alert alert-${type} alert-dismissible fade show" 
                            style="position: fixed; top: 90px; right: 20px; z-index: 1100;">
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    
                    $('body').append($alert);
                    setTimeout(() => $alert.alert('close'), 3000);
                }
            }

    // Admin panel kezelése
    const adminPanel = $('#adminPanel');
    const adminToggleBtn = $('#adminToggleBtn');
    
    @if(session('pinkod_authenticated'))
        adminToggleBtn.show();
    @endif
    
    // Panel megnyitása/bezárása
    adminToggleBtn.click(function() {
        adminPanel.toggleClass('active');
        if(adminPanel.hasClass('active')) {
            loadItemsForAdmin();
        }
    });
    
    $('#closeAdminPanel').click(function() {
        adminPanel.removeClass('active');
    });
    
    // Ételek betöltése az admin panelba
    function loadItemsForAdmin() {
        $.ajax({
            url: '/api/items/all',
            method: 'GET',
            beforeSend: function() {
                $('#adminItemsContainer').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Betöltés...</span>
                        </div>
                    </div>
                `);
            },
            success: function(response) {
                renderAdminItems(response.categories);
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON?.message || 'Hiba történt az ételek betöltése közben');
                console.error('Hiba:', xhr.responseText);
            }
        });
    }
    
    // Admin panelban lévő tételek renderelése
    function renderAdminItems(categories) {
        const container = $('#adminItemsContainer');
        container.empty();
        
        if (!categories?.length) {
            container.html('<div class="alert alert-info">Nincsenek ételek</div>');
            return;
        }
        
        categories.forEach(category => {
            if (category.items?.length > 0) {
                container.append(`
                    <div class="category-admin-header">
                        <i class="bi bi-bookmark-fill"></i> ${category.name}
                    </div>
                    <div class="mb-4">
                        ${category.items.map(item => `
                            <div class="admin-item" data-id="${item.id}" data-name="${item.name.toLowerCase()}">
                                <div class="admin-item-name">${item.name}</div>
                                <label class="switch">
                                    <input type="checkbox" ${item.show_menu ? 'checked' : ''}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        `).join('')}
                    </div>
                `);
            }
        });
    }
    
    // Kapcsoló változtatásának kezelése
    $(document).on('change', '.switch input', function() {
        const $switch = $(this);
        const $item = $switch.closest('.admin-item');
        const itemId = $item.data('id');
        const isVisible = $switch.is(':checked');
        
        $switch.prop('disabled', true);
        
        $.ajax({
            url: `/items/${itemId}/toggle-menu`,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                _token: '{{ csrf_token() }}',
                show_menu: isVisible
            }),
            success: function(response) {
                if (response.success) {
                    if (isVisible) {
                        // Elem visszaadása az étlapra
                        addItemToMenu(itemId);
                    } else {
                        // Elem eltávolítása
                        $(`.menu-item[data-id="${itemId}"]`).remove();
                        removeEmptyCategories();
                    }
                    showAlert('success', 'Státusz frissítve');
                } else {
                    $switch.prop('checked', !isVisible);
                    showAlert('error', response.message || 'Hiba történt');
                }
            },
            error: function(xhr) {
                $switch.prop('checked', !isVisible);
                showAlert('error', xhr.responseJSON?.message || 'Szerver hiba');
                console.error('Hiba:', xhr.responseText);
            },
            complete: function() {
                $switch.prop('disabled', false);
            }
        });
    });
    
    // Elem hozzáadása az étlaphoz
    function addItemToMenu(itemId) {
        if ($(`.menu-item[data-id="${itemId}"]`).length) return;
        
        $.get(`/api/items/${itemId}`, function(item) {
            if (!item.category) {
                console.error('Nincs kategória!');
                return;
            }
            
            let $category = $(`.menu-category[data-category-id="${item.category.id}"]`);
            
            // Kategória létrehozása ha nem létezik
            if (!$category.length) {
                $category = $(`
                    <div class="menu-category" data-category-id="${item.category.id}">
                        <div class="category-header">${item.category.name}</div>
                        <div class="row"></div>
                    </div>
                `);
                insertCategoryInOrder($category);
            }
            
            // Elem létrehozása
            const $item = $(`
                <div class="col-md-4 mb-4 menu-item" data-id="${item.id}">
                    <div class="card item-card">
                        ${item.image ? `<img src="${item.image}" class="card-img-top item-image" alt="${item.name}">` : ''}
                        <div class="card-body">
                            <h5 class="card-title item-name">${item.name}</h5>
                            <p class="card-text item-description">${item.description}</p>
                            ${item.tags?.length ? `
                                <div class="tags-container">
                                    ${item.tags.map(tag => `<span class="badge tag-badge">${tag.name}</span>`).join('')}
                                </div>
                            ` : ''}
                            <p class="card-text item-price">${new Intl.NumberFormat('hu-HU').format(item.price_brutto)} Ft</p>
                        </div>
                    </div>
                </div>
            `);
            
            insertItemInOrder($category.find('.row'), $item);
        }).fail(function(xhr) {
            console.error('Elem betöltési hiba:', xhr.responseText);
            showAlert('error', 'Nem sikerült betölteni az elemet');
        });
    }
    
    // Üres kategóriák eltávolítása
    function removeEmptyCategories() {
        $('.menu-category').each(function() {
            if ($(this).find('.menu-item').length === 0) {
                $(this).remove();
            }
        });
    }
    
    // Kategória beszúrása ABC sorrendbe
    function insertCategoryInOrder($category) {
        const categoryName = $category.find('.category-header').text().toLowerCase();
        let inserted = false;
        
        $('.menu-category').each(function() {
            if ($(this).find('.category-header').text().toLowerCase() > categoryName) {
                $category.insertBefore($(this));
                inserted = true;
                return false;
            }
        });
        
        if (!inserted) $('#menuContainer').append($category);
    }
    
    // Elem beszúrása ABC sorrendbe
    function insertItemInOrder($container, $item) {
        const itemName = $item.find('.item-name').text().toLowerCase();
        let inserted = false;
        
        $container.find('.menu-item').each(function() {
            if ($(this).find('.item-name').text().toLowerCase() > itemName) {
                $item.insertBefore($(this));
                inserted = true;
                return false;
            }
        });
        
        if (!inserted) $container.append($item);
    }
    
    // Értesítés megjelenítése
    function showAlert(type, message) {
        const $alert = $(`
            <div class="alert alert-${type} alert-dismissible fade show" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 1100;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append($alert);
        setTimeout(() => $alert.alert('close'), 3000);
    }
    
    // Keresés az admin panelben
    $('#adminSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('.admin-item').each(function() {
            $(this).toggle($(this).data('name').includes(searchTerm));
        });
        
        $('.category-admin-header').each(function() {
            const $category = $(this);
            $category.toggle($category.next().find('.admin-item:visible').length > 0);
        });
    });
});
    </script>
</body>
</html>