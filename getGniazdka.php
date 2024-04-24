<?php
require_once 'lib/DataSource.php';

$ds = new Phppot\DataSource();
$conn = $ds->getConnection();

if(isset($_POST['room_id']) && isset($_POST['date'])) {
    $selectedRoomId = $_POST["room_id"];
    $selectedDate = $_POST["date"];

    // Pobieramy dane z bazy danych dla wybranego dnia
    $query = "SELECT g.id, g.name, h.godzina, h.stan
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
        $gniazdkaData = array();
    
        // Przechodzimy przez wyniki z bazy danych i grupujemy je według gniazdka
      // Przechodzimy przez wyniki z bazy danych i grupujemy je według gniazdka
while($row = $result->fetch_assoc()) {
    $gniazdkaId = $row["id"];
    if (!isset($gniazdkaData[$gniazdkaId])) {
        $gniazdkaData[$gniazdkaId] = array(
            'name' => $row['name'],
            'stan' => array() // Zamiast inicjować tablicę dla wszystkich godzin, inicjujemy ją pustą
        );
    }
    
    $hour = isset($row['godzina']) ? date('H', strtotime($row['godzina'])) : null; // Pobieramy godzinę z formatu czasu
    if ($hour !== null) {
        $gniazdkaData[$gniazdkaId]['stan'][(int)$hour] = $row['stan']; // Ustawiamy stan tylko dla istniejącej godziny
    }
}

        // Generujemy wiersze tabeli dla każdego gniazdka
        foreach ($gniazdkaData as $gniazdkoId => $gniazdka) {
            $rowContent = "<tr><td>ID: " . $gniazdkoId . "</td><td>Nazwa: " . $gniazdka['name'] . "</td>";
            for ($i = 1; $i <= 24; $i++) { // Poprawiamy zakres iteracji na $i = 1; $i <= 24;
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
