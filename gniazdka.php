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

$room_id = $_GET['room_id'];
$room_sql = "SELECT name FROM ListaPomieszczen WHERE id = ?";
$stmt = $conn->prepare($room_sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room_result = $stmt->get_result();
$room = $room_result->fetch_assoc();
$room_name = $room['name'];
$stmt->close();

$sql = "SELECT id, name, description, properties, state FROM Gniazdka WHERE ListaPomieszczen_id = $room_id";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obsługa zmiany stanu gniazdka
    if (isset($_POST['gniazdko_id'])) {
        $gniazdko_id = $_POST['gniazdko_id'];
        $sql = "UPDATE Gniazdka SET state = 1 - state WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $gniazdko_id);
        $stmt->execute();
        header("Location: gniazdka.php?room_id=$room_id");
        exit();
    }

    // Obsługa dodawania gniazdka
    if (isset($_POST["add_gniazdko"])) {
        $gniazdko_name = $_POST["gniazdko_name"];
        $gniazdko_description = $_POST["gniazdko_description"];
        $gniazdko_properties = $_POST["gniazdko_properties"];
        $gniazdko_state = 0;
    
        $sql = "INSERT INTO Gniazdka (name, description, properties, state, ListaPomieszczen_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $gniazdko_name, $gniazdko_description, $gniazdko_properties, $gniazdko_state, $room_id);
        $stmt->execute();
        header("Location: gniazdka.php?room_id=$room_id");
        exit();
    }

    // Obsługa usuwania gniazdka
    if (isset($_POST["delete_gniazdko"])) {
        $gniazdko_id_to_delete = $_POST["gniazdko_id_to_delete"];
        $sql = "DELETE FROM Gniazdka WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $gniazdko_id_to_delete);
        $stmt->execute();
        header("Location: gniazdka.php?room_id=$room_id");
        exit();
    }
}
?>

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'admin') {
    include 'admin-header.php';
}
?>
<div class="container pd-5">
    <div class="page-content mt-5 mb-4">
        <h2 class="font-weight-bold text-primary mt-5">Lista gniazdek dla pokoju <?php echo $room_name; ?></h2>
        <table id="gniazdkaTable" class="table table-bordered shadow">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Opis</th>
                    <th>Właściwości</th>
                    <th>Stan</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row["id"]."</td>";
                        echo "<td>".$row["name"]."</td>";
                        echo "<td>".$row["description"]."</td>";
                        echo "<td>".$row["properties"]."</td>";
                        echo "<td>".$row["state"]."</td>";
                        echo "<td>
                            <form action='gniazdka.php?room_id=".$room_id."' method='post'>
                                <input type='hidden' name='gniazdko_id' value='".$row["id"]."'>
                                <input type='submit' value='Zmień stan' class='btn btn-primary'>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Brak wyników</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Formularz do dodawania gniazdka -->
    <div class="page-content mt-5 mb-4">
        <h2 class="font-weight-bold text-primary mt-5">Dodaj gniazdko</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?room_id=<?php echo $room_id; ?>" method="post">
            <div class="form-group">
                <label for="gniazdko_name">Nazwa gniazdka:</label>
                <input type="text" name="gniazdko_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="gniazdko_description">Opis gniazdka:</label>
                <input type="text" name="gniazdko_description" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="gniazdko_properties">Właściwości gniazdka:</label>
                <input type="text" name="gniazdko_properties" class="form-control" required>
            </div>
            <input type="submit" name="add_gniazdko" class="btn btn-primary" value="Dodaj gniazdko">
        </form>
    </div>

    <!-- Formularz do usuwania gniazdka -->
    <div class="page-content mt-5 mb-4">
        <h2 class="font-weight-bold text-primary mt-5">Usuń gniazdko</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?room_id=<?php echo $room_id; ?>" method="post">
            <div class="form-group">
                <label for="gniazdko_id_to_delete">ID gniazdka do usunięcia:</label>
                <input type="text" name="gniazdko_id_to_delete" class="form-control" required>
            </div>
            <input type="submit" name="delete_gniazdko" class="btn btn-danger" value="Usuń gniazdko">
        </form>
    </div>
</div>
