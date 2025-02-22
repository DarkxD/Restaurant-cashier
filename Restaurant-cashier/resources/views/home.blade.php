<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kezdőlap</title>
</head>
<body>
    <h1>Üdvözöljük, {{ session('felhasznalo_nev') }}!</h1>
    <p>Jogosultsága: {{ session('felhasznalo_jogosultsag') }}</p>
    <p>Ez a tartalom csak pinkódos hitelesítés után érhető el.</p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Kijelentkezés</button>
    </form>
</body>
</html>