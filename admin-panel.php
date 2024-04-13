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

</HEAD>
<BODY>
<div class="container mt-5">

<?php
if (isset($_SESSION['username']) && $_SESSION['role'] == 'admin') {
    include 'admin-header.php';
}
?>
</BODY>
</HTML>