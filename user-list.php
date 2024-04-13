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
<div class="container mt-5">

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'admin') {
    include 'admin-header.php';
}
?>


    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card p-4 border border-primary shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h2 class="mb-0 font-weight-bold">Dodaj nowego użytkownika</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group row mb-3">
                            <label for="username" class="col-sm-3 col-form-label text-right font-weight-bold">Nazwa użytkownika</label>
                            <div class="col-sm-9">
                                <input type="text" id="username" name="username" required class="form-control py-2">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name" class="col-sm-3 col-form-label text-right">Imię</label>
                            <div class="col-sm-9">
                                <input type="text" id="first_name" name="first_name" required class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="last_name" class="col-sm-3 col-form-label text-right">Nazwisko</label>
                            <div class="col-sm-9">
                                <input type="text" id="last_name" name="last_name" required class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-sm-3 col-form-label text-right">Hasło</label>
                            <div class="col-sm-9">
                                <input type="password" id="password" name="password" required class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-3 col-form-label text-right">Email</label>
                            <div class="col-sm-9">
                                <input type="email" id="email" name="email" required class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="role" class="col-sm-3 col-form-label text-right">Rola:</label>
                            <div class="col-sm-9">
                                <select id="role" name="role" class="form-control">
                                    <option value="user">Użytkownik</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <input type="submit" name="create_user" value="Dodaj użytkownika" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="card p-4 border border-danger shadow">
                            <div class="card-header bg-danger text-white py-3">
                                <h2 class="mb-0 font-weight-bold">Usuń użytkownika</h2>
                            </div>
                            <div class="card-body">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="form-group row mb-3">
                                        <label for="user_id" class="col-sm-2 col-form-label text-right font-weight-bold">ID Użytkownika</label>
                                        <div class="col-sm-10">
                                            <input type="number" id="user_id" name="user_id" required class="form-control py-2">
                                        </div>
                                    </div>
                                    <input type="submit" name="delete_user" value="Usuń użytkownika" class="btn btn-danger btn-lg btn-block rounded-pill">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card p-4 border border-warning shadow">
                <div class="card-header bg-warning text-white py-3">
                    <h2 class="mb-0 font-weight-bold">Zmień uprawnienia użytkownika</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group row mb-3">
                            <label for="user_id" class="col-sm-2 col-form-label text-right font-weight-bold">ID Użytkownika</label>
                            <div class="col-sm-10">
                                <input type="number" id="user_id" name="user_id" required class="form-control py-2">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                        <label for="user_role" class="col-sm-2 col-form-label text-right font-weight-bold">Nowa Rola</label>
                        <div class="col-sm-10">
                            <select name="user_role" class="form-control py-2">
                                <option value="user">Użytkownik</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <input type="submit" name="edit_user" value="Aktualizuj" class="btn btn-warning btn-lg btn-block rounded-pill">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content mt-4">
    <h2 class="font-weight-bold text-primary">Lista użytkowników</h2>
    <table id="userTable" class="table table-bordered shadow">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Nick</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>email</th>
                <th>ID mieszkania</th>
                <th>Uprawnienia</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['first_name']; ?></td>
                <td><?php echo $user['last_name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['apartment_id']; ?></td>
                <td><?php echo $user['role']; ?></td>
                <td>
                    <a href="edit_user.php?user_id=<?php echo $user['id']; ?>" class="btn btn-light btn-lg rounded-pill">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

        </div>
        <script>
$(document).ready( function () {
    $('#userTable').DataTable();
} );
</script>
    </BODY>
</HTML>
