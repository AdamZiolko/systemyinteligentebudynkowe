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



function getAllHistory() {
    global $conn;
    $stmt = $conn->prepare("SELECT `id`, `Gniazdka_id`, `Data`, `tbl_member_id` FROM `historiauzytkowania`");
    $stmt->execute();
    $result = $stmt->get_result();
    $history = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $history;
}

// Get all history
$history = getAllHistory();
?>

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'admin') {
    include 'admin-header.php';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">

<div class="page-content mt-4 mb-4">
    <h2 class="font-weight-bold text-primary">Historia zmian</h2>
    <table id="historyTable" class="table table-bordered shadow">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>ID Gniazdka</th>
                <th>Data</th>
                <th>ID uzytkownika</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $record): ?>
            <tr>
                <td><?php echo $record['id']; ?></td>
                <td><?php echo $record['Gniazdka_id']; ?></td>
                <td><?php echo $record['Data']; ?></td>
                <td><?php echo $record['tbl_member_id']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready( function () {
    $('#historyTable').DataTable();
} );
</script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>