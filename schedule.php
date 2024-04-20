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

<div class="container">
    <div class="page-content mt-5 mb-5">
        <h2 class="font-weight-bold text-primary mt-5">Harmonogram</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="room_id">Wybierz pomieszczenie:</label>
                <select name="room_id" class="form-control" required>
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
            <input type="submit" name="show_schedule" class="btn btn-primary" value="Pokaż harmonogram">
        </form>

        <?php 
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["show_schedule"])) {
            $selectedRoomId = $_POST["room_id"];
            $sql = "SELECT id, name FROM Gniazdka WHERE ListaPomieszczen_id = $selectedRoomId";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<h3>Gniazdka w wybranym pomieszczeniu:</h3>";
                echo "<ul>";
                while($row = $result->fetch_assoc()) {
                    echo "<li>ID: " . $row["id"] . ", Nazwa: " . $row["name"] . "</li>";
                }
                echo "</ul>";
            } else {
                echo "Brak gniazdek w wybranym pomieszczeniu.";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
