@extends('admin.admin_layout')

@section('title', 'Kategóriák')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Kategóriák</h4>
                    <button id="createCategoryBtn" class="btn btn-primary float-end btn-sm">Új kategória létrehozása</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Név</th>
                                <th>Leírás</th>
                                <th>Kép</th>
                                <th>Kasszában mutatva</th>
                                <th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr id="categoryRow{{ $category->id }}">
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description }}</td>
                                    <td>
                                        <img src="{{ asset('/storage/' . $category->image) }}" alt="{{ $category->image }}" style="width: auto; height: 50px;" title="{{ $category->image }}">
                                    </td>
                                    <td>{{ $category->show_cashier ? 'Igen' : 'Nem' }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm editCategoryBtn" data-id="{{ $category->id }}">Szerkesztés</button>
                                        <button class="btn btn-danger btn-sm deleteCategoryBtn" data-id="{{ $category->id }}">Törlés</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Create/Edit Category -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Kategória létrehozása/szerkesztése</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    @csrf
                    <input type="hidden" id="categoryId">
                    <div class="mb-3">
                        <label for="name" class="form-label">Név</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Leírás</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Kép</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="show_cashier" name="show_cashier">
                        <label class="form-check-label" for="show_cashier">Kasszában mutatva</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">Mentés</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#createCategoryBtn').click(function() {
            $('#categoryForm')[0].reset();
            $('#categoryId').val('');
            $('#categoryModalLabel').text('Kategória létrehozása');
            $('#categoryModal').modal('show');
        });

        $('.editCategoryBtn').click(function() {
            var categoryId = $(this).data('id');
            $.get('/admin/categories/' + categoryId, function(data) {
                $('#categoryId').val(data.id);
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#show_cashier').prop('checked', data.show_cashier);
                $('#categoryModalLabel').text('Kategória szerkesztése');
                $('#categoryModal').modal('show');
            });
        });

        $('#saveCategoryBtn').click(function() {
            var categoryId = $('#categoryId').val();
            var url = categoryId ? '/admin/categories/' + categoryId : '/admin/categories';
            var method = categoryId ? 'PUT' : 'POST';

            var formData = new FormData($('#categoryForm')[0]);

            if (!formData.get('name')) {
                alert('A név mező kitöltése kötelező!');
                return;
            }

            // Ha szerkesztés történik, adjuk hozzá a _method mezőt
            if (categoryId) {
                formData.append('_method', 'PUT');
            }
            // Ellenőrizzük a checkbox értékét
            var showCashier = $('#show_cashier').is(':checked') ? 1 : 0;
            formData.set('show_cashier', showCashier); // Beállítjuk a FormData-ban

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: url,
                method: 'POST', // Mindig POST-ot használunk, mert a _method mező fogja szimulálni a PUT-ot
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#categoryModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Hiba történt a mentés során: ' + xhr.responseText);
                }
            });
        });

        $('.deleteCategoryBtn').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var categoryId = $(this).data('id');
            if (confirm('Biztosan törölni szeretnéd ezt a kategóriát?')) {
                $.ajax({
                    url: '/admin/categories/' + categoryId,
                    method: 'DELETE',
                    success: function(response) {
                        $('#categoryRow' + categoryId).remove();
                    }
                });
            }
        });
    });
</script>
@endsection