@extends('admin.admin_layout')

@section('title', "Kassza kezelése")

@section('content')


<!-- Add Cashier User Modal -->
<div class="modal fade" id="addCashier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Kassza kezelése</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul style="padding-left:3rem;" id="saveform_errorList"></ul>
                <div class="form-group mb-3">
                    <label for="name">Kassza neve</label>
                    <input type="text" id="name" class="name form-control">

                    <label for="pincode">Pinkód</label>
                    <input type="text" id="pincode" class="pincode form-control">

                    <label for="role">Jogosultság</label>
                    <input type="text" id="role" class="role form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                <button type="button" class="btn btn-primary add-cashier">Mentés</button>
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
                    <h4>Kassza kezelése</h4>
                    <a href="#" class="btn btn-primary float-end btn-sm" data-bs-toggle="modal" data-bs-target="#addCashier">Létrehozás</a>
                </div>
                <div class="card-body">
                    
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Név</th>
                                <th>Jogosultság</th>
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
            url: "/admin/fetch-cashierusers",
            dataType: "json",
                success: function (response) {
                    //console.log(response.cashierUsers);
                    $('tbody').html("");
                    $.each(response.cashierUsers, function (key, item) {
                        $('tbody').append(
                            '<tr>\
                                <td>'+item.id+'</td>\
                                <td>'+item.nev+'</td>\
                                <td>'+item.jogosultsag+'</td>\
                                <td><button type="button" value="'+item.id+'" class="edit_cashieruser btn btn-primary btn-sm">Szerkesztés</button></td>\
                                <td><button type="button" value="'+item.id+'" class="delete_cashieruser btn btn-danger btn-sm">Törlés</button></td>\
                            </tr>'
                        )
                        $('#saveform_errorList').append('<li>' + item + '</li>');
                        //$('#success_message').text("Sikeres frissítés");
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
            url: "/admin/cashieruser-delete/"+cashierUser_id,
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


        $(document).on('click', '.add-cashier' , function (e) {
            e.preventDefault();
            var data = {
                'name' : $('.name').val(),
                'pincode' : $('.pincode').val(),
                'role' : $('.role').val(),

            };

            //console.log(data);
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
            type: "POST",
            url: "/admin/cashierusers",
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
                    $('#addCashier').modal('hide');
                    $('#addCashier').find('input').val("");
                    fetchCashierUsers();
                }
                console.log(response);
            }

        });
    });

        
    });

</script>

@endsection