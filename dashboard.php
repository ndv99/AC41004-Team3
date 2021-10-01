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
	<link rel="stylesheet" href="css/main.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
</head>
<body>

	<h1>Hello <?php echo $_SESSION['name']; ?></h1>

	<?php
		if ($_SESSION['role'] != "athlete") {
			echo "<p>Your clients are:</p>";
			$query = "SELECT `client_id` FROM `physio_athlete` WHERE `staff_id` = ".$_SESSION["UserID"].";";
			$stmt = $pdo->prepare($query);
	    	$stmt->execute();
	    	$row = $stmt->fetchAll();
	    	foreach ($row as $row2) {
			   //echo $row2["clientID"];
			   $clientID = $row2["client_id"];
			   $query2 = "SELECT * FROM `user` WHERE `user_id` = ".$clientID.";";
			   $stmt2 = $pdo->prepare($query2);
	    	   $stmt2->execute();
	    	   $row3 = $stmt2->fetch();

	    	   echo "<p>".$row3["firstName"]." ".$row3["surname"]."</p>";

			}
		}
	?>

	 <div class="page_heading">
      <h1>Your recovery tracker</h1>
    </div>
    <!-- background for the app -->
    <div class="body_background">
      <!-- contains all the main content of the page -->
      <div class="content_container">
        <!-- test form for importing csv using php -->
        <div title="form run on php" class="php_form">
          <form class="row g-3" enctype="multipart/form-data" action="import_csv_test/upload.php" method="post">
          	<div class="col-md-2">
          		<label for="sensor" class="form-label">Choose a sensor:</label>
			    <select id="sensor" name="sensor" class="form-control">
              <option value="">-----Choose Sensor-----</option>
			        <option value="1">Sensor 1</option>
			        <option value="2">Sensor 2</option>
			        <option value="3">Sensor 3</option>
			        <option value="4">Sensor 4</option>
			    </select>
          	</div>
          	<div class="col-12">
          		<label for="file" class="form-label" >Select CSV File:</label>
           		<input type="file" class="form-control" id="file_to_import" name="csv_file" accept=".csv" required>
          	</div>
            <input class="form-control" type="submit" id="submitFile" value="Upload File" name="import">
          </form>
        </div>
      </div>

    </div>

	<script src="js/project.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>


</body>
</html>
