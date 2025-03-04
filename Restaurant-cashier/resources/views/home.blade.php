@extends('admin.admin_layout')


@section('title', "Dashboard")

@section('content')

    <h1>Üdvözöljük, {{ session('felhasznalo_nev') }}! - Jogosultsága: {{ session('felhasznalo_jogosultsag') }}</h1>
    <div id="success_message"></div>

    <!-- Tartalom -->
    <div class="container mt-4">
      <div id="clientlist" class="row row-cols-1 row-cols-md-3 g-4">
        
        <!-- Kártya, amelyre kattintva új ügyfél jön létre -->
        {{-- <div class="createClientCard" style="cursor: pointer; padding: 20px; border: 1px solid #ccc; border-radius: 10px; text-align: center;">
          <h3>Új Ügyfél</h3>
          <p>Kattints ide egy új ügyfél létrehozásához</p>
        </div> --}}



      </div>
    </div>
    


    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Kijelentkezés</button>
    </form>



    <script>
    $(document).ready(function (){
      
      fetchCashierUsers();
        function fetchCashierUsers(){
            $.ajax({
            type: "GET",
            url: "/clients/fetch-clients",
            dataType: "json",
                success: function (response) {
                    console.log(response.clientUsers);
                    $('#clientlist').html('<div class="createClientCard" style="cursor: pointer; padding: 20px; border: 1px solid #ccc; border-radius: 10px; text-align: center;">\
                        <h3>Új Ügyfél</h3>\
                        <p>Kattints ide egy új ügyfél létrehozásához</p>\
                      </div>'
                      );
                    $.each(response.clientUsers, function (key, item) {
                    $('#clientlist').append(
                        '<div class="col openClientCard" id="'+item.id+'" style="cursor: pointer; text-align: center;">\
                          <div class="card h-100 " >\
                            <div class="card-body">\
                              <h5 class="card-title" style="border-bottom: 3px solid; border-color: '+ item.color +';">'+ item.name + ' #'+ item.id +'</h5>\
                              <p class="card-text">Termékek helye</p>\
                            </div>\
                            <div class="card-footer">\
                              <small class="text-body-secondary">Footer info</small>\
                            </div>\
                          </div>\
                        </div>'
                    )
                    $('#success_message').text("Sikeres frissítés");
                })

                }
            })
        }

        $(document).on('click', '.openClientCard' , function (e) {
          window.location.href = "/cashier/" + this.id;
        })

      
      $(document).on('click', '.createClientCard' , function (e) {
            e.preventDefault();
            var data = {
                /* 'name' : $('.name').val(),
                'pincode' : $('.pincode').val(),
                'role' : $('.role').val(), */

            };

            //console.log(data);
            
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
                if(response.status != 200){
                    $('#saveform_errorList').html('');
                    $('#saveform_errorList').addClass('alert alert-danger');
                    $.each(response.errors, function (key, err_values) {
                        $('#saveform_errorList').append('<li>' + err_values + '</li>');
                    })
                } else {
                  window.location.href = "/cashier/" + response.clientID;
                }
                console.log(response);
            }
          });

      });
    });
    </script>
@endsection