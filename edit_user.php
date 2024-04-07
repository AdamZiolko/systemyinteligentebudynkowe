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

function getUserId($username) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM tbl_member WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user['id'];
}

function getUser($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM tbl_member WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function editUser($userId, $username, $firstName, $lastName, $password, $email, $role) {
    global $conn;
    $stmt = $conn->prepare("UPDATE tbl_member SET username = ?, first_name = ?, last_name = ?, password = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $username, $firstName, $lastName, $password, $email, $role, $userId);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Dane użytkownika zostały zaktualizowane.";
    } else {
        echo "Nie udało się zaktualizować danych użytkownika.";
    }
    $stmt->close();
}

if (isset($_SESSION['username'])) {
    $userId = getUserId($_SESSION['username']);
    error_log("User ID: " . $userId);  // Log the user ID
    $user = getUser($userId);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["edit_user"])) {
        $username = $_POST["username"];
        $firstName = $_POST["first_name"];
        $lastName = $_POST["last_name"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $role = $_POST["role"];
        editUser($userId, $username, $firstName, $lastName, $password, $email, $role);
    }
}
?>

<div class="page-content">
    <h2>Edytuj użytkownika</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Nazwa użytkownika:</label>
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
        <label for="first_name">Imię:</label>
        <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required>
        <label for="last_name">Nazwisko:</label>
        <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required>
        <label for="password">Hasło:</label>
        <input type="password" name="password" value="<?php echo $user['password']; ?>" required>
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
        <label for="role">Rola:</label>
        <select name="role">
            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Użytkownik</option>
            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <input type="submit" name="edit_user" value="Edytuj użytkownika">
    </form>
</div>