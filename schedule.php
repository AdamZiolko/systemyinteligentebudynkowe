<?php
require_once 'lib/DataSource.php';
require_once 'Model/member.php';

$ds = new Phppot\DataSource();
$conn = $ds->getConnection();
$gniazdkaData = array();

session_start();
if (isset($_SESSION["username"]) && $_SESSION["role"] == "admin") {
    $username = $_SESSION["username"];
    session_write_close();
} else {
    session_unset();
    session_write_close();
    $url = "./index.php";
    header("Location: $url");
}

function getRoomList($conn) {
    $sql = "SELECT id, name FROM ListaPomieszczen";
    $result = $conn->query($sql);
    return $result;
}

if (isset($_POST['room_id']) && isset($_POST['date'])) {
    $selectedRoomId = $_POST["room_id"];
    $selectedDate = $_POST["date"];

    // Pobierz dane z bazy danych dla wybranego pomieszczenia i daty
    $query = "SELECT g.id, g.name, h.stan, h.godzina
              FROM Gniazdka g
              LEFT JOIN harmonogram h ON g.id = h.id_gniazdka AND h.data = ? 
              WHERE g.ListaPomieszczen_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $selectedDate, $selectedRoomId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Inicjujemy zmienną, która będzie przechowywać treść tabeli
        $tableContent = "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        $tableContent .= "<tr><th style='min-width: 25px;'>ID</th><th style='min-width: 100px;'>Nazwa</th>"; // Otwieramy tabelę i nagłówek
        for ($i = 1; $i <= 24; $i++) {
            $tableContent .= "<th style='min-width: 25px;'>" . $i . "</th>"; // Dodajemy kolumny z godzinami
        }
        $tableContent .= "</tr>";

        // Tablica do przechowywania danych na temat stanu gniazdek
    

        // Przechodzimy przez wyniki z bazy danych i grupujemy je według gniazdka
        while($row = $result->fetch_assoc()) {
            $gniazdkaId = $row["id"];
            if (!isset($gniazdkaData[$gniazdkaId])) {
                $gniazdkaData[$gniazdkaId] = array(
                    'name' => $row['name'],
                    'stan' => array_fill(1, 24, 0) // Inicjujemy tablicę stanów dla każdej godziny dla danego gniazdka jako 0
                );
            }
            if (isset($row['godzina']) && $row['stan'] == 1) {
                $hour = date('H', strtotime($row['godzina'])); // Pobieramy godzinę z formatu czasu
                $gniazdkaData[$gniazdkaId]['stan'][$hour] = 1;
            }
            
        }

        // Generujemy wiersze tabeli dla każdego gniazdka
        foreach ($gniazdkaData as $gniazdkoId => $gniazdka) {
            $rowContent = "<tr><td>ID: " . $gniazdkoId . "</td><td>Nazwa: " . $gniazdka['name'] . "</td>";
            for ($i = 1; $i <= 24; $i++) {
                $stan = isset($gniazdka['stan'][$i]) && $gniazdka['stan'][$i] == 1 ? 'green-background' : ''; // Ustawiamy klasę green-background dla zielonego tła, jeśli stan = 1
                $rowContent .= "<td style='min-width: 25px;' class='extra-column " . $stan . "' data-id='" . $gniazdkoId . "-" . $i . "'></td>";
            }
            $rowContent .= "</tr>";
            // Dodajemy wiersz dla każdego gniazdka
            $tableContent .= $rowContent;
        }

        // Zamykamy tabelę
        $tableContent .= "</table>";
        // Zwracamy treść tabeli
        echo $tableContent;
    } else {
        // Jeśli nie znaleziono gniazdek, zwracamy komunikat
        echo "Brak gniazdek w wybranym pomieszczeniu.";
    }
}
?>

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'admin') {
    include 'admin-header.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmonogram</title>
    <style>
        .green-background {
            background-color: green !important; /* Ustawiamy !important, aby przeważyć styl wewnątrz tagu */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-content mt-5 mb-5">
            <h2 class="font-weight-bold text-primary mt-5">Harmonogram</h2>
            <form id="roomForm">
                <div class="form-group">
                    <label for="room_id">Wybierz pomieszczenie:</label>
                    <select name="room_id" id="room_id" class="form-control" required>
                        <option value="">Wybierz...</option>
                        <?php 
                        $roomList = getRoomList($conn);
                        if ($roomList->num_rows > 0) {
                            while($row = $roomList->fetch_assoc()) {
                                echo "<option value='".$row["id"]."'>".$row["name"]."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Wybierz datę:</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
            </form>

            <div id="gniazdkaContainer"></div>
            <button id="saveButton" class="btn btn-primary mt-3">Zapisz</button>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function(){
    // Obsługa zmiany daty
    $('#date').change(function() {
        var roomId = $('#room_id').val();
        var date = $(this).val();
        $.ajax({
            url: 'getGniazdka.php',
            type: 'POST',
            data: {room_id: roomId, date: date},
            success: function(response){
                console.log("success");
                console.log(response); // Wyświetlamy otrzymane dane
                $('#gniazdkaContainer').html(response);
                // Po pobraniu danych, ustawiamy kolorowanie komórek
                colorizeCells();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Wystąpił błąd podczas pobierania danych.');
            }
            });
        });
        $('#room_id').change(function() {
    var roomId = $(this).val();
    var date = $('#date').val();
    $.ajax({
        url: 'getGniazdka.php',
        type: 'POST',
        data: {room_id: roomId, date: date},
        success: function(response){
            console.log("success");
            console.log(response); // Wyświetlamy otrzymane dane
            $('#gniazdkaContainer').html(response);
            // Po pobraniu danych, ustawiamy kolorowanie komórek
            colorizeCells();
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('Wystąpił błąd podczas pobierania danych.');
        }
    });
});
        function colorizeCells() {
    $('td.extra-column').each(function() {
        var id = $(this).attr('data-id').split("-"); // Pobieramy ID gniazdka i godzinę
        var gniazdkoId = id[0];
        var godzina = id[1];
        var stan = gniazdkaData[gniazdkoId]['stan'][godzina]; // Pobieramy stan gniazdka dla danej godziny

        if (stan == 1) {
            $(this).addClass('green-background');
        } else {
            $(this).removeClass('green-background');
        }
    });
}
    // Obsługa kliknięcia na komórkę tabeli
    $('#gniazdkaContainer').on('click', 'td.extra-column', function() {
        $(this).toggleClass('green-background'); // Toggle dodaje klasę, jeśli jej nie ma, i usuwa, jeśli jest obecna
    });

    // Obsługa kliknięcia przycisku "Zapisz"
    $('#saveButton').click(function() {
        var roomId = $('#room_id').val();
        var date = $('#date').val();
        var dataToSave = [];

        $('td.extra-column').each(function() {
            var gniazdkoId = $(this).attr('data-id').split("-")[0]; // Pobieramy ID gniazdka
            var godzinaId = $(this).attr('data-id').split("-")[1]; // Pobieramy ID godziny
            var godzina = godzinaId + ':00:00'; // Tworzymy pełną godzinę w formacie czasu
            var stan = $(this).hasClass('green-background') ? 1 : 0; // Sprawdzamy, czy komórka ma zielone tło
            dataToSave.push({id_gniazdka: gniazdkoId, godzina: godzina, data: date, stan: stan});
        });

        // Zapisujemy dane do bazy danych
        $.ajax({
            url: '/saveData.php',
            type: 'POST',
            data: {room_id: roomId, date: date, data: JSON.stringify(dataToSave)},
            success: function(response){
                alert('Zapisano zmiany!');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Wystąpił błąd podczas zapisywania danych.');
            }
        });
    });
});
</script>
</body>
</html>
