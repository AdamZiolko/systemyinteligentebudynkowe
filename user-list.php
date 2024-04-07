<?php
require_once 'lib/DataSource.php'; // ścieżka do pliku DataSource.php

$ds = new Phppot\DataSource();
$conn = $ds->getConnection();

session_start();
if (isset($_SESSION["username"]) && $_SESSION["role"] == "admin") {
    $username = $_SESSION["username"];
    session_write_close();
} else {
    // if user is not an admin, redirect them to the index page
    session_unset();
    session_write_close();
    $url = "./index.php";
    header("Location: $url");
}



// Funkcja do aktualizacji roli użytkownika
function updateUserRole($userId, $newRole) {
    // Zakładając, że $conn to Twoje połączenie z bazą danych
    global $conn;
    $stmt = $conn->prepare("UPDATE tbl_member SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $newRole, $userId);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Rola użytkownika została zaktualizowana.";
    } else {
        echo "Nie udało się zaktualizować roli użytkownika.";
    }
    $stmt->close();
}

function getAllUsers() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM tbl_member");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $users;
}

// Pobierz wszystkich użytkowników
$users = getAllUsers();

// Obsługa żądania POST do edycji użytkownika
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_user"])) {
    $userId = $_POST["user_id"];
    $newRole = $_POST["user_role"];
    updateUserRole($userId, $newRole);
}
?>
<HTML>
<HEAD>
<TITLE>Admin Panel</TITLE>
<link href="assets/css/phppot-style.css" type="text/css" rel="stylesheet" />
<link href="assets/css/user-registration.css" type="text/css" rel="stylesheet" />
</HEAD>
<BODY>
    <div class="phppot-container">
        <div class="page-header">
            <span class="login-signup"><a href="logout.php">Logout</a></span>
        </div>
        <div class="page-content">Welcome <?php echo $username;?></div>
        <div class="page-content">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="user-list.php">User List</a></li>
                <li><a href="apartment-list.php">Apartment List</a></li>
                <li><a href="change-history.php">Change History</a></li>
                <li><a href="room-list.php">Room List</a></li>
            </ul>
        </div>
        <div class="page-content">
            <h2>Edytuj Listę Użytkowników</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="user_id">ID Użytkownika:</label>
                <input type="number" name="user_id" required>
                <label for="user_role">Nowa Rola:</label>
                <select name="user_role">
                    <option value="user">Użytkownik</option>
                    <option value="admin">Admin</option>
                </select>
                <input type="submit" name="edit_user" value="Aktualizuj">
            </form>
            <!-- Tutaj można dodać tabelę z listą użytkowników i opcjami edycji -->
            <table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo $user['id']; ?></td>
        <td><?php echo $user['username']; ?></td>
        <td><?php echo $user['role']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
        </div>
    </div>
    
</BODY>
</HTML>