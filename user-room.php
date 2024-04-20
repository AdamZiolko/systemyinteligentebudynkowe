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


function getRoomList($conn) {
    $sql = "SELECT l.id, l.name, 
            (SELECT COUNT(*) FROM Gniazdka g WHERE g.ListaPomieszczen_id = l.id) as total_outlets, 
            (SELECT COUNT(*) FROM Gniazdka g WHERE g.ListaPomieszczen_id = l.id AND g.state = 1) as active_outlets 
            FROM ListaPomieszczen l";
    $result = $conn->query($sql);
    return $result;
}




$sql = "SELECT id, name FROM ListaPomieszczen";
$result = $conn->query($sql);
?>

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'user') {
    include 'user-header.php';
}
?>

<div class="container">

<div class="page-content mt-5 mb-5">
    <h2 class="font-weight-bold text-primary mt-5">Lista Pomieszczeń</h2>
    <table id="roomTable" class="table table-bordered shadow">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Liczba Gniazdek</th>
                <th>Liczba Aktywnych Gniazdek</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $result = getRoomList($conn);

            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row["id"]."</td>";
                    echo "<td>".$row["name"]."</td>";
                    echo "<td>".$row["total_outlets"]."</td>";
                    echo "<td>".$row["active_outlets"]."</td>";
                    echo "<td class='text-center'><a href='gniazdka-user.php?room_id=".$row["id"]."' class='btn btn-light btn-lg rounded-pill'>Pokaż Gniazdka</a></td>";
                    echo "<td class='text-center'><a href='gniazdka.php?room_id=".$row["id"]."' class='btn btn-light btn-lg rounded-pill'>Harmonogram</a></td>";
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
    $('#roomTable').DataTable();
} );
</script>


</div>

</body>
</html>