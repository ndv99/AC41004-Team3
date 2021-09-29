<!DOCTYPE html>
<?php
    session_start();
    require('db_connect.php');

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

	<?php 
		if ($_SESSION['role'] != "athlete") {
			echo "<p>Your clients are:</p>";
			$query = "SELECT `clientID` FROM `relationship` WHERE `staffID` = ".$_SESSION["UserID"].";";
			$stmt = $pdo->prepare($query);
	    	$stmt->execute();
	    	$row = $stmt->fetchAll();
	    	foreach ($row as $row2) {
			   //echo $row2["clientID"];
			   $clientID = $row2["clientID"];
			   $query2 = "SELECT * FROM `user` WHERE `user_id` = ".$clientID.";";
			   $stmt2 = $pdo->prepare($query2);
	    	   $stmt2->execute();
	    	   $row3 = $stmt2->fetch();

	    	   echo "<p>".$row3["firstName"]." ".$row3["surname"]."</p>"; 

			}
		}
	?>

</body>
</html>