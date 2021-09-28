<!DOCTYPE html>
<?php
    session_start();

    if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {

    header("location: index.php");
    exit;
}

?>
<html>
<head>
	<meta charset="utf-8">
	<title>Dashboard</title>
</head>
<body>
	
	<h1>Hello <?php echo $_SESSION['name']; ?></h1>
</body>
</html>