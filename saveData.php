<?php
require_once 'Model/member.php'; // Poprawna ścieżka do pliku member.php
$member = new Phppot\Member();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["data"])) {
    $date = $_POST["date"];
    $dataToSave = json_decode($_POST["data"], true); // Dekodowanie danych z JSON

    // Iteracja przez dane do zapisania
    foreach ($dataToSave as $data) {
        $gniazdkoId = $data["id_gniazdka"];
        $godzina = $data["godzina"];
        $stan = $data["stan"]; // Ustawiamy wartość stanu na podstawie przesłanych danych

        // Wykonanie zapytania w celu zapisania danych do bazy danych
        $query = "INSERT INTO harmonogram (id_gniazdka, data, godzina, stan) 
                  VALUES (?, ?, ?, ?) 
                  ON DUPLICATE KEY UPDATE stan = VALUES(stan)";
        $paramType = "issi";
        $paramValue = array($gniazdkoId, $date, $godzina, $stan);
        $member->execute($query, $paramType, $paramValue);
    }
    // Zwrócenie odpowiedzi po zapisaniu danych
    echo "Dane zostały pomyślnie zapisane!";
} else {
    // Jeśli dane nie zostały przesłane poprawnie, zwróć błąd
    echo "Wystąpił błąd podczas zapisywania danych!";
}
?>
