@extends('admin.admin_layout')

@section('title', 'Címkék')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Címkék</h4>
                    <button id="createTagBtn" class="btn btn-primary float-end btn-sm">Új címke létrehozása</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Név</th>
                                <th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tags as $tag)
                                <tr id="tagRow{{ $tag->id }}">
                                    <td>{{ $tag->id }}</td>
                                    <td>{{ $tag->name }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm editTagBtn" data-id="{{ $tag->id }}">Szerkesztés</button>
                                        <button class="btn btn-danger btn-sm deleteTagBtn" data-id="{{ $tag->id }}">Törlés</button>
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

<!-- Modal for Create/Edit Tag -->
<div class="modal fade" id="tagModal" tabindex="-1" aria-labelledby="tagModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tagModalLabel">Címke létrehozása/szerkesztése</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tagForm">
                    @csrf
                    <input type="hidden" id="tagId">
                    <div class="mb-3">
                        <label for="name" class="form-label">Név</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                <button type="button" class="btn btn-primary" id="saveTagBtn">Mentés</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#createTagBtn').click(function() {
            $('#tagForm')[0].reset();
            $('#tagId').val('');
            $('#tagModalLabel').text('Címke létrehozása');
            $('#tagModal').modal('show');
        });

        $('.editTagBtn').click(function() {
            var tagId = $(this).data('id');
            $.get('/admin/tags/' + tagId, function(data) {
                $('#tagId').val(data.id);
                $('#name').val(data.name);
                $('#tagModalLabel').text('Címke szerkesztése');
                $('#tagModal').modal('show');
            });
        });

        $('#saveTagBtn').click(function() {
            var tagId = $('#tagId').val();
            var url = tagId ? '/admin/tags/' + tagId : '/admin/tags';
            var method = tagId ? 'PUT' : 'POST';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: url,
                method: 'POST', // Mindig POST-ot használunk
                data: {
                    _method: method, // _method mezővel jelöljük a tényleges metódust
                    name: $('#name').val(),
                    _token: $('input[name="_token"]').val() // CSRF token hozzáadása
                },
                success: function(response) {
                    $('#tagModal').modal('hide');
                    location.reload();
                }
            });
        });

        $('.deleteTagBtn').click(function() {
            var tagId = $(this).data('id');
            if (confirm('Biztosan törölni szeretnéd ezt a címkét?')) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/admin/tags/' + tagId,
                    method: 'DELETE',
                    success: function(response) {
                        $('#tagRow' + tagId).remove();
                    }
                });
            }
        });
    });
</script>
@endsection