<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Cashier</title>

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
        
        <button class="button">Új ügyfél</button>
        {{--  <button>Rendelések</button> --}}
    </div>

    <div class="main">
        <div class="sidebar">
            {{-- <button class="button">Új ügyfél</button> --}}
            {{-- <button class="button">Új rendelés</button> --}}
            {{-- <button class="button">Lezárt ügyfelek</button> --}}
            <button class="button red">Ügyfél zárása</button>
            
            <form action="{{ route('logout') }}" method="POST"> 
                @csrf
                <button class ="button red" type="submit">Kijelentkezés</button>
            </form>
        </div>

        <div class="content">
            <div class="orders">
                <h2>{{ $client->name }} #{{ $client->id }}</h2> <p><b></b><p>
                <ul>
                    <li><input type="checkbox">Capuccino: 630 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                    <li><input type="checkbox">Kisscsillag pizza: 1730 Ft <button>&#10005;</button></li>
                </ul>
                <div>
                    <button class="button green">Nyomtatás</button>
                    <button class="button green">Fizetés</button>
                </div>
            </div>

            <div class="menu">
                <h2>Étlap</h2>
                <div class="product">
                    <div>
                        <img src="https://via.placeholder.com/100" alt="Pepperoni">
                        <p>Pepperoni</p>
                    </div>
                    <div>
                        <img src="https://via.placeholder.com/100" alt="Húsimádó">
                        <p>Húsimádó</p>
                    </div>
                    <div>
                        <img src="https://via.placeholder.com/100" alt="Magyaros">
                        <p>Magyaros</p>
                    </div>
                    <div>
                        <img src="https://via.placeholder.com/100" alt="Pacal">
                        <p>Pacal</p>
                    </div>
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
        <button>Pizzák</button>
        <button>Italok</button>
        <button>Egyebek</button>
    </div>

    
    <script>
        const toggleButton = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });



    </script>
</body>
</html>