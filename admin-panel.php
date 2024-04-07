<?php
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
    </div>
</BODY>
</HTML>