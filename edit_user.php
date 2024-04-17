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
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hash the password
    $stmt = $conn->prepare("UPDATE tbl_member SET username = ?, first_name = ?, last_name = ?, password = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $username, $firstName, $lastName, $hashed_password, $email, $role, $userId);  // Use the hashed password
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Dane użytkownika zostały zaktualizowane.";
    } else {
        echo "Nie udało się zaktualizować danych użytkownika.";
    }
    $stmt->close();
    header("Location: user-list.php");  // Przekierowanie po przetworzeniu formularza
    exit;
}

if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
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
<link rel="stylesheet" href="assets/css/bootstrap.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="assets\css\bootstrap.min.js"></script>
<script src="assets\css\popper.min.js"></script>
<script src="assets\css\bootstrap.min.js"></script>

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'admin') {
    include 'admin-header.php';
}
?>

<div class="page-content container mt-5 pd-6">
    <h2 class="mb-3 mt-5">Edytuj użytkownika</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="username">Nazwa użytkownika:</label>
            <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="first_name">Imię:</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo $user['first_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Nazwisko:</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo $user['last_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Hasło:</label>
            <input type="password" name="password" class="form-control" value="<?php echo $user['password']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="role">Rola:</label>
            <select name="role" class="form-control">
                <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Użytkownik</option>
                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <input type="submit" name="edit_user" class="btn btn-primary" value="Edytuj użytkownika">
    </form>
</div>