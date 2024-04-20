<?php
require_once 'lib/DataSource.php';

$ds = new Phppot\DataSource();
$conn = $ds->getConnection();

session_start();
if (isset($_SESSION["username"]) && $_SESSION["role"] == "user") {
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
        header("Location: gniazdka-user.php?room_id=$room_id");
        exit();
    }
}

?>

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'user') {
    include 'user-header.php';
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
                            <form action='gniazdka-user.php?room_id=".$room_id."' method='post'>
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

