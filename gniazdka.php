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

$room_sql = "SELECT name FROM ListaPomieszczen WHERE id = $room_id";
$room_result = $conn->query($room_sql);
$room = $room_result->fetch_assoc();
$room_name = $room['name'];

$sql = "SELECT id, name, description, properties, state FROM Gniazdka WHERE ListaPomieszczen_id = $room_id";
$result = $conn->query($sql);
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
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Brak wyników</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready( function () {
    $('#gniazdkaTable').DataTable();
} );
</script>

<?php
$conn->close();
?>