<?php
require_once 'lib/DataSource.php';

$ds = new Phppot\DataSource();
$conn = $ds->getConnection();

if(isset($_POST['room_id'])) {
    $selectedRoomId = $_POST["room_id"];
    $sql = "SELECT id, name FROM Gniazdka WHERE ListaPomieszczen_id = $selectedRoomId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Inicjujemy zmienną, która będzie przechowywać treść tabeli
        $tableContent = "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        $tableContent .= "<tr><th style='min-width: 25px;'>ID</th><th style='min-width: 100px;'>Nazwa</th>"; // Otwieramy tabelę i nagłówek
        for ($i = 1; $i <= 24; $i++) {
            $tableContent .= "<th style='min-width: 25px;'>" . $i . "</th>"; // Dodajemy kolumny z godzinami
        }
        $tableContent .= "</tr>";
        while($row = $result->fetch_assoc()) {
            // Łączymy nazwę i ID w jednym polu
            $rowContent = "<tr><td>ID: " . $row["id"] . "</td><td>Nazwa: " . $row["name"] . "</td>";
            // Dodajemy kolumny dla każdej godziny
            for ($i = 1; $i <= 24; $i++) {
                if ($i > 0) {
                    $rowContent .= "<td style='min-width: 25px;' class='extra-column'>   </td>"; // Dodajemy klasę extra-column
                } else {
                    $rowContent .= "<td style='min-width: 25px;' class='extra-column'>   </td>"; // Dodajemy klasę extra-column
                }
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
