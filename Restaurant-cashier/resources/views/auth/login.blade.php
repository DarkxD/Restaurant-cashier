<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belépés</title>
</head>
<body>
    <h1>Belépés</h1>
    {{$errors}}
    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <label for="pinkod">Pinkód:</label>
        <input type="password" id="pinkod" name="pinkod" required>
        <button type="submit">Belépés</button>
    </form>
</body>
</html>