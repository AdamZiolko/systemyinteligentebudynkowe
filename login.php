<?php
use Phppot\Member;
if (! empty($_POST["login-btn"])) {
    require_once __DIR__ . '/Model/Member.php';
    $member = new Member();
    $loginResult = $member->loginMember();

    if ($loginResult === true) {
        // Assuming the getMemberByUsername function returns an associative array of the user data
        $memberData = $member->getMemberByUsername($_POST["username"]);

        if ($memberData["role"] == "admin") {
            header("Location: admin-panel.php");
        } else {
            header("Location: user-panel.php");
        }
    }
}
?>
<HTML>
<HEAD>
<TITLE>Login</TITLE>

	<link rel="stylesheet" href="assets\css\bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="assets\css\bootstrap.min.js"></script>
<script src="assets\css\popper.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<script src="vendor/jquery/jquery-3.3.1.js" type="text/javascript"></script>
</HEAD>
<BODY>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header text-center">
                    <h4>Login</h4>
                    <a href="user-registration.php" class="btn btn-link">Rejestracja</a>
                </div>
                <div class="card-body">
                    <form name="login" action="" method="post" onsubmit="return loginValidation()">
                        <?php if(!empty($loginResult)){?>
                        <div class="alert alert-danger"><?php echo $loginResult;?></div>
                        <?php }?>
                        <div class="form-group">
                            <label for="username">Login<span class="required error" id="username-info"></span></label>
                            <input class="form-control" type="text" name="username" id="username">
                        </div>
                        <div class="form-group">
                            <label for="login-password">Hasło<span class="required error" id="login-password-info"></span></label>
                            <input class="form-control" type="password" name="login-password" id="login-password">
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary btn-block" type="submit" name="login-btn" id="login-btn" value="Zaloguj">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

	<script>
function loginValidation() {
	var valid = true;
	$("#username").removeClass("error-field");
	$("#password").removeClass("error-field");

	var UserName = $("#username").val();
	var Password = $('#login-password').val();

	$("#username-info").html("").hide();

	if (UserName.trim() == "") {
		$("#username-info").html("required.").css("color", "#ee0000").show();
		$("#username").addClass("error-field");
		valid = false;
	}
	if (Password.trim() == "") {
		$("#login-password-info").html("required.").css("color", "#ee0000").show();
		$("#login-password").addClass("error-field");
		valid = false;
	}
	if (valid == false) {
		$('.error-field').first().focus();
		valid = false;
	}
	return valid;
}
</script>
</BODY>
</HTML>
