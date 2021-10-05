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

    <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
--><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!--<link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet"> -->

	<link rel="stylesheet" type="text/css" href="./css/dashboard.css" />
</head>

<body>

	<div class="container">

		<header>
			<img class="logo" src="./Style/Images/fullLogo.png" alt="" width="" height="">
		</header>

		<hr>

		<h1 class="helloName"> Welcome back 
			<span> <?php echo $_SESSION['name']; ?> </span>
		! </h1>

		<hr>


		<div class="clientsList">
		<?php
			if ($_SESSION['role'] != "athlete") {
				echo "<p>Your <span>clients</span> are:</p>";
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

				<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Modal" data-bs-whatever="@getbootstrap">Upload new sensor readings</button>

				<div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="uploadmodal" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="uploadmodal">Upload sensor data</h5>
				        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				      </div>
				      <div class="modal-body">
						<form class="row g-3" enctype="multipart/form-data" action="upload.php" method="post">
							<div class="col-12">
								<label for="file" class="form-label" >Upload Sensor 1 File:</label>
								<input type="file" class="form-control" id="file_to_import" name="csv_file[]" accept=".csv" required>
								<br>
								<label for="file" class="form-label" >Upload Sensor 2 File:</label>
								<input type="file" class="form-control" id="file_to_import" name="csv_file[]" accept=".csv" required>
								<br>
								<label for="file" class="form-label" >Upload Sensor 3 File:</label>
								<input type="file" class="form-control" id="file_to_import" name="csv_file[]" accept=".csv" required>
								<br>
								<label for="file" class="form-label" >Upload Sensor 4 File:</label>
								<input type="file" class="form-control" id="file_to_import" name="csv_file[]" accept=".csv" required>
							</div>
							<input class="form-control" type="submit" id="submitFile" value="Upload File" name="import">
						</form>

				      </div>
				    </div>
				  </div>
				</div>

			<?php
				$query = "SELECT MAX(session_id) FROM sensor_data WHERE user_id =". $_SESSION['UserID']. ";";
				$stmt = $pdo->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch();
				$result = $row["MAX(session_id)"];

				echo $result;
			?>
			<form method="post" action ="3d.php">
				<button value='<?php echo $result ?>' type="submit" name="single_session" class="btn btn-primary">View your last session</button>
			</form>
			</div>
		</div>

		</div>

		<!-- <script src="js/project.js" charset="utf-8"></script> -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>

		</div>
</body>
</html>
