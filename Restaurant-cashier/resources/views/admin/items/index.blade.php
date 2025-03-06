@extends('admin.admin_layout')

@section('title', "Tételek kezelése")

@section('content')

<!-- Add Item User Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true" enctype="multipart/form-data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addItemModalLabel">Új tétel hozzáadása</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul id="saveform_errorList" style="padding-left:3rem;"></ul>
                <div class="form-group mb-3">
                    <label for="name">Tétel neve</label>
                    <input type="text" id="name" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label for="description">Leírás</label>
                    <textarea id="description" class="form-control"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="short_name">Rövid név</label>
                    <input type="text" id="short_name" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label for="image">Főkép</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    <div id="uploadedImage">
                        <!-- Ide kerül a főkép -->
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="album">Album képek</label>
                    <input type="file" id="album" name="album[]" class="form-control" accept="image/*" multiple>
                    <div id="uploadedImages">
                        <!-- Ide kerülnek az album képek -->
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label>Kategóriák</label>
                    <select id="categories" class="form-control">
                        {{-- <option value="category1">Kategória 1</option>
                        <option value="category2">Kategória 2</option>
                        <option value="category3">Kategória 3</option> --}}
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>Tag-ek</label>
                    <div id="tagButtons" class="d-flex flex-wrap gap-2">
                        <!-- Ide kerülnek a tag gombok -->
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="price_netto">Nettó ár</label>
                    <input type="number" id="price_netto" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label for="price_brutto">Bruttó ár</label>
                    <input type="number" id="price_brutto" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label for="default_vat">ÁFA</label>
                    <input type="number" id="default_vat" class="form-control">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="show_cashier" checked>
                    <label class="form-check-label" for="show_cashier">Megjelenjen a pénztárban</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="show_menu">
                    <label class="form-check-label" for="show_menu">Megjelenjen az étlapon</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                <button type="button" class="btn btn-primary" id="saveItem">Mentés</button>
            </div>
        </div>
    </div>
</div>
{{-- END MODAL --}}

<div id="success_message"></div>
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tételek kezelése</h4>
                    <a href="#" class="btn btn-primary float-end btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">Létrehozás</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>KÉP</th>
                                <th>Album</th>
                                <th>Név ~ rövidnév</th>
                                <th>Kategória, címkék</th>
                                <th></th>
                                <th>Megjelenés</th>
                                <th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody id="itemRows">
                            {{-- Item rows will be loaded here --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let selectedTags = []; // Tömb a kiválasztott tag-ek tárolására

        fetchItems();

        function fetchItems() {
            $.ajax({
                type: "GET",
                url: "/admin/fetch-items",
                dataType: "json",
                success: function (response) {
                    $('#itemRows').html("");
                    
                    $('#categories').html('<option value="">Válassz kategóriát</option>');
                    $.each(response.categories, function (key, category) {
                        $('#categories').append('<option value="' + category.id + '">' + category.name + '</option>');
                    });

                    // Tag gombok létrehozása
                    $('#tagButtons').html('');
                    $.each(response.tags, function (key, tag) {
                        $('#tagButtons').append(`
                            <button type="button" class="btn btn-outline-secondary tagBtn" data-id="${tag.id}">${tag.name}</button>
                        `);
                    });

                    // Tag gombok eseménykezelője
                    $('.tagBtn').click(function () {
                        const tagId = $(this).data('id');
                        if (selectedTags.includes(tagId)) {
                            selectedTags = selectedTags.filter(id => id !== tagId); // Távolítsd el a tag-et
                            $(this).removeClass('btn-primary').addClass('btn-outline-secondary');
                        } else {
                            selectedTags.push(tagId); // Add hozzá a tag-et
                            $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
                        }
                    });

                    $.each(response.items, function (key, item) {
                        var mainImage = item.image
                            ? '<img src="' + item.image + '" alt="Főkép" style="width:50px; height:50px;">'
                            : 'Nincs kép';

                        var albumImages = item.album && item.album.length > 0
                            ? item.album.map(function(image) {
                                return image
                                    ? '<img src="' + image + '" alt="Album kép" style="width:50px; height:50px; margin-right:5px;">'
                                    : '';
                            }).join('')
                            : 'Nincs albumban kép';

                        var tagNames = item.tags
                            ? item.tags.map(function(tag) {
                                return '<span class="badge bg-secondary">' + tag.name + '</span>';
                            }).join(' ')
                            : 'Nincs címke';

                        var displayLocations = [];
                        if (item.show_cashier) displayLocations.push('<span class="badge bg-success">Kassza</span>');
                        if (item.show_menu) displayLocations.push('<span class="badge bg-info">Étlap</span>');
                        var displayLocationsHtml = displayLocations.join(' ');

                        $('#itemRows').append(
                            '<tr>\
                                <td>' + mainImage + '</td>\
                                <td>' + albumImages + '</td>\
                                <td>' + item.name + '<br><em>'+ item.short_name +'</em></td>\
                                <td><span class="badge bg-primary">' + item.category_name +'</span><br>'+ tagNames + '</td>\
                                <td>\
                                    <strong>Nettó:</strong> ' + item.price_netto + ' Ft<br>\
                                    <strong>Bruttó:</strong> ' + item.price_brutto + ' Ft<br>\
                                    <strong>Áfakulcs:</strong> ' + item.default_vat + ' %\
                                </td>\
                                <td>' + displayLocationsHtml + '</td>\
                                <td>\
                                    <button type="button" value="' + item.id + '" class="edit_cashieruser btn btn-warning btn-sm">Szerkesztés</button>\
                                    <button type="button" value="' + item.id + '" class="delete_cashieruser btn btn-danger btn-sm">Törlés</button>\
                                </td>\
                            </tr>'
                        );
                    });
                }
            });
        }

        $(document).on('click', '#addItemModal', function (e) {
            $('#addItemModalLabel').text("Létrehozás");
        });

        $(document).on('click', '.delete_cashieruser', function (e) {
            e.preventDefault();
            var itemId = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "DELETE",
                url: "/admin/delete-item/" + itemId,
                success: function (response) {
                    if (response.status != 200) {
                        $('#success_message').addClass('alert alert-danger');
                        $('#success_message').text("Sikertelen törlés");
                    } else {
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        fetchItems();
                    }
                }
            });
        });

        $(document).on('click', '.edit_cashieruser', function (e) {
            e.preventDefault();
            var itemId = $(this).val();
            $('#addItemModalLabel').text("Szerkesztés");

            $.ajax({
                type: "GET",
                url: "/admin/edit-item/" + itemId,
                success: function (response) {
                    if (response.status == 200) {
                        $('#addItemModal').modal('show');
                        $('#name').val(response.item.name);
                        $('#description').val(response.item.description);
                        $('#short_name').val(response.item.short_name);
                        $('#categories').val(response.item.category_id);
                        $('#price_netto').val(response.item.price_netto);
                        $('#price_brutto').val(response.item.price_brutto);
                        $('#default_vat').val(response.item.default_vat);
                        $('#show_cashier').prop('checked', response.item.show_cashier);
                        $('#show_menu').prop('checked', response.item.show_menu);

                        // Tag gombok frissítése
                        selectedTags = response.item.tags.map(tag => tag.id);
                        $('.tagBtn').each(function () {
                            const tagId = $(this).data('id');
                            if (selectedTags.includes(tagId)) {
                                $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
                            } else {
                                $(this).removeClass('btn-primary').addClass('btn-outline-secondary');
                            }
                        });

                        // Feltöltött képek megjelenítése
                        $('#uploadedImage').html('');
                        if (response.item.image) {
                            $('#uploadedImage').append(`
                                <div class="image-container mb-2">
                                    <img src="${response.item.image}" alt="Főkép" style="width: 100px; height: auto;">
                                </div>
                            `);
                        }

                        // Album képek megjelenítése
                        $('#uploadedImages').html('');
                        if (response.item.album && response.item.album.length > 0) {
                            response.item.album.forEach((url, index) => {
                                $('#uploadedImages').append(`
                                    <div class="image-container mb-2">
                                        <img src="${url}" alt="Album kép" style="width: 100px; height: auto;">
                                    </div>
                                `);
                            });
                        }

                        $('#saveItem').val(itemId).text('Frissítés');
                    }
                }
            });
        });

        $(document).on('click', '#saveItem', function (e) {
            e.preventDefault();
            var itemId = $(this).val();

            var formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('name', $('#name').val());
            formData.append('description', $('#description').val());
            formData.append('short_name', $('#short_name').val());
            formData.append('category_id', $('#categories').val());
            formData.append('price_netto', $('#price_netto').val());
            formData.append('price_brutto', $('#price_brutto').val());
            formData.append('default_vat', $('#default_vat').val());
            formData.append('show_cashier', $('#show_cashier').is(':checked') ? 1 : 0);
            formData.append('show_menu', $('#show_menu').is(':checked') ? 1 : 0);

            // Ha nincs címke kiválasztva, akkor üres tömböt küldünk
            if (selectedTags.length > 0) {
                selectedTags.forEach(tagId => formData.append('tags[]', tagId));
            }

            var imageFile = $('#image')[0].files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            var albumFiles = $('#album')[0].files;
            if (albumFiles && albumFiles.length > 0) {
                for (var i = 0; i < albumFiles.length; i++) {
                    formData.append('album[]', albumFiles[i]);
                }
            }
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var url = itemId ? "/admin/update-item/" + itemId : "/admin/items";
            var method = itemId ? "POST" : "POST";

            $.ajax({
                type: method,
                url: url,
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status != 200) {
                        $('#saveform_errorList').html('');
                        $('#saveform_errorList').addClass('alert alert-danger');
                        $.each(response.errors, function (key, err_values) {
                            $('#saveform_errorList').append('<li>' + err_values + '</li>');
                        });
                    } else {
                        $('#saveform_errorList').html('');
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('#addItemModal').modal('hide');
                        $('#addItemModal').find('input').val("");
                        fetchItems();
                    }
                }
            });
        });
    });
</script>

@endsection