<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('/images/restaurant-bg.jpg');
            background-size: cover;
            background-position: center;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .login-container h1 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .pin-display {
            width: 100%;
            padding: 0.8rem;
            margin: 1rem 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1.2rem;
            text-align: center;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pin-display input {
            border: none;
            outline: none;
            font-size: 1.5rem;
            text-align: center;
            width: 80%;
            position: relative;
            right: -1.2rem;
            background: transparent;
        }

        .pin-display button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            color: #007bff;
        }

        .pin-pad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .pin-pad button {
            padding: 1rem;
            font-size: 1.2rem;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .pin-pad button:hover {
            background-color: #0056b3;
        }

        .pin-pad button:active {
            background-color: #004080;
        }
        .pin-pad #enter {
            background-color: #0ba02b;
        }
        .pin-pad #enter:hover {
            background-color: #00771a;
        }
        .pin-pad #enter:active {
            background-color: #00771a;
        }


        .error-message {
            color: red;
            margin-top: 0.5rem;
            font-size: 1rem;
            align-content: center;

        }

        #toggle-visibility {
            font-size: 1.8rem;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Bejelentkezés</h1>
        <div class="pin-display">
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

            
            <input type="password" id="pinkod"  name="pinkod" value="" readonly>
            <button type="button" id="toggle-visibility">👁️</button>
        </div>
        <div class="pin-pad">
            <button type="button" data-number="1">1</button>
            <button type="button" data-number="2">2</button>
            <button type="button" data-number="3">3</button>
            <button type="button" data-number="4">4</button>
            <button type="button" data-number="5">5</button>
            <button type="button" data-number="6">6</button>
            <button type="button" data-number="7">7</button>
            <button type="button" data-number="8">8</button>
            <button type="button" data-number="9">9</button>
            <button type="button" data-number="0">0</button>
            <button type="button" id="clear">C</button>
            <button id="enter" type="submit">↵</button>
        </div>
        </form>
        <div id="error-message" class="error-message" style="display: none;">{{ $errors }}</div>

        @if (count($errors) > 0)
            <div class="row">
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <label class="error-message">{{ $error }}</label>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <script>
        const pinInput = document.getElementById('pinkod');
        const pinPad = document.querySelector('.pin-pad');
        const errorMessage = document.getElementById('error-message');
        const toggleVisibilityButton = document.getElementById('toggle-visibility');
        let pinInputValue = '';

        pinPad.addEventListener('click', function (e) {
            if (e.target.tagName === 'BUTTON') {
                const button = e.target;
                if (button.id === 'clear') {
                    pinInputValue = '';
                    pinInput.value = '';
                    errorMessage.style.display = 'none';
                }

                  



                if (pinInputValue.length < 10) {
                    if(button.getAttribute('data-number') != null ){
                        pinInputValue += button.getAttribute('data-number');
                        pinInput.value = pinInputValue;
                    }
                    
                }
            }
        });

        toggleVisibilityButton.addEventListener('click', function () {
            if (pinInput.type === 'password') {
                pinInput.type = 'text';
                toggleVisibilityButton.textContent = '👁️';
            } else {
                pinInput.type = 'password';
                toggleVisibilityButton.textContent = '👁️';
            }
        });
    </script>
</body>
</html>




{{-- <!DOCTYPE html>
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
</html> --}}