<?php
require_once 'lib/DataSource.php';

$ds = new Phppot\DataSource();
$conn = $ds->getConnection();

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

$sql = "SELECT id, name FROM ListaPomieszczen";
$result = $conn->query($sql);
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
            </form>

            <div id="gniazdkaContainer"></div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function(){
        $('#room_id').change(function(){
            var roomId = $(this).val();
            $.ajax({
                url: 'getGniazdka.php',
                type: 'POST',
                data: {room_id: roomId},
                success: function(response){
                    $('#gniazdkaContainer').html(response);
                }
            });
        });

        // Obsługa kliknięcia na komórkę tabeli
        $('#gniazdkaContainer').on('click', 'td.extra-column', function() {
            $(this).toggleClass('green-background'); // Toggle dodaje klasę, jeśli jej nie ma, i usuwa, jeśli jest obecna
        });
    });
    </script>
</body>
</html>
