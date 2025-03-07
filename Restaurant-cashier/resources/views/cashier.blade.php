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
                    @foreach($categories as $category)
                        @foreach($category->items as $item)
                            <div data-category-id="{{ $category->id }}">
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
                {{-- <p>{{ $category->name }}</p> --}}
            </div>
        @endforeach
    </div>

    
    <script>
        const toggleButton = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });



    </script>
    <script>
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
    </script>
</body>
</html>