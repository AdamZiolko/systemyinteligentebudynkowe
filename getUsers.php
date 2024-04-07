<?php
require_once 'lib/DataSource.php'; // ścieżka do pliku DataSource.php

$ds = new Phppot\DataSource();
$conn = $ds->getConnection();

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
?>

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