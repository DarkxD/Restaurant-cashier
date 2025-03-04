@extends('admin.admin_layout')

@section('title', "Tételek kezelése")

@section('content')


<!-- Add Item User Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
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
                    <label for="image">Kép URL</label>
                    <input type="text" id="image" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label for="album">Album (JSON formátum)</label>
                    <textarea id="album" class="form-control"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Kategóriák</label>
                    <select id="categories" class="form-control" multiple>
                        <option value="category1">Kategória 1</option>
                        <option value="category2">Kategória 2</option>
                        <option value="category3">Kategória 3</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label>Tag-ek</label>
                    <select id="tags" class="form-control" multiple>
                        <option value="tag1">Tag 1</option>
                        <option value="tag2">Tag 2</option>
                        <option value="tag3">Tag 3</option>
                    </select>
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
                                <th>ID</th>
                                <th>KÉP</th>
                                <th>Név</th>
                                <th>Kategória</th>
                                <th>Tag-ek</th>
                                <th>Szerkesztés</th>
                                <th>Törlés</th>
                            </tr>
                        </thead>
                        <tbody>
                             {{-- Some magic here --}}
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>













<script>
    $(document).ready(function (){

        fetchCashierUsers();
        function fetchCashierUsers(){
            $.ajax({
            type: "GET",
            url: "/admin/fetch-items",
            dataType: "json",
                success: function (response) {
                    //console.log(response.cashierUsers);
                    $('tbody').html("");
                    $.each(response.cashierUsers, function (key, item) {
                        $('tbody').append(
                            '<tr>\
                                <td>'+item.id+'</td>\
                                <td>'+item.kep+'</td>\
                                <td>'+item.nev+'</td>\
                                <td>'+item.category+'</td>\
                                <td>'+item.tag+'</td>\
                                <td><button type="button" value="'+item.id+'" class="edit_cashieruser btn btn-primary btn-sm">Szerkesztés</button></td>\
                                <td><button type="button" value="'+item.id+'" class="delete_cashieruser btn btn-danger btn-sm">Törlés</button></td>\
                            </tr>'
                        )
                        $('#saveform_errorList').append('<li>' + item + '</li>');
                        $('#success_message').text("Sikeres frissítés");
                    })

                }
            })
        }




        $(document).on('click', '.delete_cashieruser', function (e){
            e.preventDefault(); // Hogy ne töltődjön újra
            var cashierUser_id = $(this).val();
            alert(cashierUser_id);

             $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
            type: "DELETE",
            url: "/admin/delete-item/"+cashierUser_id,
                success: function (response) {
                    if(response.status != 200){
                        $('#success_message').addClass('alert alert-danger');
                        $('#success_message').text("Sikertelen törlés");
                        fetchCashierUsers();
                    } else {
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        fetchCashierUsers();
                    }
                }


             })
            });


        $(document).on('click', '#saveItem' , function (e) {
            e.preventDefault();
            var data = {
                'name' : $('#name').val(),
                'description' : $('#description').val(),
                'short_name' : $('#short_name').val(),
                'image' : $('#image').val(),
                'album' : $('#album').val(),
                'categories' : $('#categories').val(),
                'tags' : $('#tags').val(),
                'price_netto' : $('#price_netto').val(),
                'price_brutto' : $('#price_brutto').val(),
                'default_vat' : $('#default_vat').val(),
                'show_cashier' : $('#show_cashier').is(':checked')? 1 : 0,
                'show_menu' : $('#show_menu').is(':checked')? 1 : 0,

                
            };

            console.log(data);
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
            type: "POST",
            url: "/admin/items",
            data: data,
            dataType: "json",
            success: function (response) {
                if(response.status != 200){
                //if(response.message != "Kassza létrehozva"){
                    $('#saveform_errorList').html('');
                    $('#saveform_errorList').addClass('alert alert-danger');
                    $.each(response.errors, function (key, err_values) {
                        $('#saveform_errorList').append('<li>' + err_values + '</li>');
                    })
                } else {
                    $('#saveform_errorList').html('');
                    $('#success_message').addClass('alert alert-success');
                    $('#success_message').text(response.message);
                    $('#addItemModal').modal('hide');
                    $('#addItemModal').find('input').val("");
                    fetchCashierUsers();
                }
                console.log(response);
            }

        });
    });

        
    });

</script>

@endsection