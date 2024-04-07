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

function createUser($username, $firstName, $lastName, $password, $email, $role) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO tbl_member (username, first_name, last_name, password, email, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $firstName, $lastName, $password, $email, $role);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Nowy użytkownik został dodany.";
    } else {
        echo "Nie udało się dodać nowego użytkownika.";
    }
    $stmt->close();
    header("Location: user-list.php");  // Przekierowanie po przetworzeniu formularza
    exit;}

function deleteUser($userId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM tbl_member WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Użytkownik został usunięty.";
    } else {
        echo "Nie udało się usunąć użytkownika.";
    }
    $stmt->close();
}

function updateUserRole($userId, $newRole) {
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

$users = getAllUsers();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_user"])) {
        $username = $_POST["username"];
        $firstName = $_POST["first_name"];
        $lastName = $_POST["last_name"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $role = $_POST["role"];
        createUser($username, $firstName, $lastName, $password, $email, $role);
    } elseif (isset($_POST["delete_user"])) {
        $userId = $_POST["user_id"];
        deleteUser($userId);
    }
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
    <h2>Dodaj nowego użytkownika</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Nazwa użytkownika:</label>
        <input type="text" name="username" required>
        <label for="first_name">Imię:</label>
        <input type="text" name="first_name" required>
        <label for="last_name">Nazwisko:</label>
        <input type="text" name="last_name" required>
        <label for="password">Hasło:</label>
        <input type="password" name="password" required>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <label for="role">Rola:</label>
        <select name="role">
            <option value="user">Użytkownik</option>
            <option value="admin">Admin</option>
        </select>
        <input type="submit" name="create_user" value="Dodaj użytkownika">
    </form>
</div>

<div class="page-content">
    <h2>Usuń użytkownika</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="user_id">ID Użytkownika:</label>
        <input type="number" name="user_id" required>
        <input type="submit" name="delete_user" value="Usuń użytkownika">
    </form>
</div>

<div class="page-content">
    <h2>Zmień uprawnienia użytkownika</h2>
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
    <th>Action</th>
</tr>
<?php foreach ($users as $user): ?>
<tr>
        <td><?php echo $user['id']; ?></td>
        <td><?php echo $user['username']; ?></td>
        <td><?php echo $user['role']; ?></td>
        <td>
            <a href="edit_user.php?user_id=<?php echo $user['id']; ?>">Edit</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
        </div>
    </div>
</BODY>
</HTML>
