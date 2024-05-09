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

function getRoomName($conn, $room_id) {
    $room_sql = "SELECT name FROM ListaPomieszczen WHERE id = ?";
    $stmt = $conn->prepare($room_sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room_result = $stmt->get_result();
    $room = $room_result->fetch_assoc();
    $stmt->close();
    return $room['name'];
}

function getRoomDetails($conn, $room_id) {
    $sql = "SELECT id, name, description, properties, state FROM Gniazdka WHERE ListaPomieszczen_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $details;
}

$room_id = $_GET['room_id'];
$room_name = getRoomName($conn, $room_id);
$room_details = getRoomDetails($conn, $room_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obsługa zmiany stanu gniazdka
    if (isset($_POST['gniazdko_id'])) {
        $username = $_SESSION["username"];
        $sql = "SELECT id FROM tbl_member WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $user_id = (int) $user['id']; // Fetch the user id as an integer
        
        $gniazdko_id = $_POST['gniazdko_id'];
        $sql = "UPDATE Gniazdka SET state = 1 - state WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $gniazdko_id);
        $stmt->execute();

                // Get the updated state
        $sql = "SELECT state FROM Gniazdka WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $gniazdko_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $state = $row['state'];
                
        $sql = "INSERT INTO `historiauzytkowania`(`Gniazdka_id`, `Data`, `tbl_member_id`, `stan`) VALUES (?, NOW(), ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $gniazdko_id, $user_id, $state);
        $stmt->execute();    

        header("Location: gniazdka-user.php?room_id=$room_id");
        exit();
    }
}

?>
<HTML>
<HEAD>
<TITLE>Lista gniazdek</TITLE>
<!-- <link href="assets/css/phppot-style.css" type="text/css" rel="stylesheet" />
<link href="assets/css/user-registration.css" type="text/css" rel="stylesheet" /> -->
<link rel="stylesheet" href="assets\css\bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="assets\css\bootstrap.min.js"></script>
<script src="assets\css\popper.min.js"></script>
<script src="assets\css\bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

</HEAD>
<BODY>

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'user') {
    include 'user-header.php';
}
?>

<div class="container">
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
                    if (count($room_details) > 0) {
                        // output data of each row
                        foreach($room_details as $row) {
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
</div>
</BODY>
</HTML>