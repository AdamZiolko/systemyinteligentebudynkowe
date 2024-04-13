<link rel="stylesheet" href="assets\css\bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="assets\css\bootstrap.min.js"></script>
<script src="assets\css\popper.min.js"></script>
<script src="assets\css\bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="phppot-container p-4 col-12">
    <div class="page-header py-3 bg-primary text-white text-center">
        <span class="login-signup"><a href="logout.php" class="btn btn-light btn-lg rounded-pill">Logout</a></span>
    </div>
    <div class="page-content py-3 text-center">
        <h2>Welcome <?php echo $username;?></h2>
    </div>
    <div class="page-content">
        <h2 class="font-weight-bold text-primary text-center">Admin Panel</h2>
        <ul class="nav nav-pills mb-3 bg-light p-3 rounded shadow justify-content-center">
            <li class="nav-item p-2">
                <a class="nav-link text-dark" href="user-list.php">User List</a>
            </li>
            <li class="nav-item p-2">
                <a class="nav-link text-dark" href="apartment-list.php">Apartment List</a>
            </li>
            <li class="nav-item p-2">
                <a class="nav-link text-dark" href="change-history.php">Change History</a>
            </li>
            <li class="nav-item p-2">
                <a class="nav-link text-dark" href="room-list.php">Room List</a>
            </li>
            <li class="nav-item p-2">
                <a class="nav-link text-dark" href="admin-panel.php">Główna Strona</a>
            </li>
        </ul>
    </div>