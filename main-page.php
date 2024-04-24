<!DOCTYPE html>
<html>
<head>
    <title>Main Page</title>
    <style>
        /* Add your CSS styles here */
        .banner {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-image: url('banner-main-page.png');
            background-repeat: no-repeat;
            background-size: cover;
            padding: 10px;
            text-align: center;
            height: 70vh;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); /* Add a box shadow to the banner */

        }

        .banner h1, .banner p {
            color: white;
            text-shadow: 0px 0px 16px rgba(0, 0, 0, 1);
        }

        body {
            background: #B24592;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #F15F79, #B24592);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #F15F79, #B24592); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
}

        .card {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
            width: 100%;
            margin-bottom: 20px;
        }

        .card:hover {
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        }

        .card-body {
            padding: 20px;
        }
    </style>
    <link rel="stylesheet" href="assets\css\bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets\css\bootstrap.min.js"></script>
    <script src="assets\css\popper.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
</head>
<body style="background-color: pink;">
    <div class="container">
        <div class="banner jumbotron text-center bg-dark text-white">
            <h1 class="display-4 font-weight-bold">Układ Sterowania Obecnością Napięcia Gniazd 1-fazowych</h1>
        </div>
        <div class="card mb-3">
            <div class="card-body bg-light">
                <p class="lead text-black font-weight-bold">Układ Sterowania Obecnością Napięcia Gniazd 1-fazowych to zaawansowane rozwiązanie zaprojektowane do monitorowania i sterowania napięciem w gniazdkach elektrycznych jednofazowych. Jest to niezwykle przydatne narzędzie, które umożliwia zarządzanie energią elektryczną w inteligentny i efektywny sposób.</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body bg-light">
                <h5 class="card-title text-dark font-weight-bold">Jak Działa:</h5>
                <ol>
                    <li class="mb-2">
                        <h6 class="text-black font-weight-bold">Monitorowanie Stanu Gniazdek:</h6>
                        <p>Nasz system pozwala na bieżące monitorowanie stanu każdego gniazdka. Dzięki temu możesz szybko sprawdzić, czy jest ono włączone czy wyłączone.</p>
                    </li>
                    <li class="mb-2">
                        <h6 class="text-black font-weight-bold">Sterowanie Włączaniem i Wyłączaniem:</h6>
                        <p>Za pomocą naszej aplikacji możesz zdalnie włączać i wyłączać gniazdka elektryczne. Niezależnie od tego, gdzie jesteś, masz pełną kontrolę nad zasilaniem.</p>
                    </li>
                    <li class="mb-2">
                        <h6 class="text-black font-weight-bold">Harmonogramowanie Pracy Gniazdek:</h6>
                        <p>Nasz system umożliwia również zaplanowanie harmonogramu pracy gniazdek. Możesz ustawić godziny, w których mają być włączane lub wyłączane, co pozwala na oszczędność energii i automatyzację codziennych zadań.</p>
                    </li>
                </ol>
            </div>
        </div>

    </div>
</body>
</html>