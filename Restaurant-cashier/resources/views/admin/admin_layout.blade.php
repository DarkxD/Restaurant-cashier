<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield( 'title')</title>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Cashier</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link @if(Route::currentRouteName() == 'home') active @endif" href="{{ route('home') }}" aria-current="page" >Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Étlep</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Admin beállítások
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item @if(Route::currentRouteName() == 'cashierusers') active @endif" href="{{ route('cashierusers') }}" >Kasszák</a></li>
                <li><a class="dropdown-item @if(Route::currentRouteName() == 'admin.items') active @endif" href="{{ route('admin.items') }}">Termékek</a></li>
                <li><a class="dropdown-item @if(Route::currentRouteName() == 'admin.categories') active @endif" href="{{ route('admin.categories') }}">Kategóriák</a></li>
                <li><a class="dropdown-item @if(Route::currentRouteName() == 'admin.tags') active @endif" href="{{ route('admin.tags') }}">Címkék</a></li>
                <li><a class="dropdown-item @if(Route::currentRouteName() == 'admin.images') active @endif" href="{{ route('admin.images') }}">Képek</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Beállítások</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    @yield('content')

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</body>
</html>
