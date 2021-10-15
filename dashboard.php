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

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
	<link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet"> 

	<link rel="stylesheet" type="text/css" href="./css/dashboardLightMode.css">

</head>

<body id="body">
	<div id="content">

	<nav id="nav" class="navbar navbar-dark">
	  <div class="container-fluid">
    	<ul class="nav mr-auto">
            <li class="nav-item">
            	<a class="nav-link" href="account.php">Account</a>
            </li>
            <li class="nav-item">
            	<a class="nav-link" href="logout.php" >Log Out</a>
            </li>
        </ul>
	  </div>

	</nav>

	<header>
		<img id="logo" src="./Style/Images/whiteLogo.png" alt="" width="" height="">
	</header>

	<hr style="clear: both;">


	<div class="container" id="bootstrap_override">

		

		<h1 class="helloName"> Welcome back 
			<span> <?php echo $_SESSION['name']; ?> </span>
		! </h1>

		<hr>


		<div class="clientsList">
		<?php
			if ($_SESSION['role'] != "athlete") :?>
				<p>Your clients are:</p>
				<input type="text" id="myInput" tabindex="1" onkeyup="searchFunction()" placeholder="Search for names..">
				<!-- <hr> -->
				<?php
				$query = "SELECT `client_id` FROM `physio_athlete` WHERE `staff_id` = ".$_SESSION["UserID"].";";
				$stmt = $pdo->prepare($query);
				$stmt->execute();
				$row = $stmt->fetchAll();
				$target = 0;
				foreach ($row as $row2) {
					//echo $row2["clientID"];
					$target = $target + 1;
					$clientID = $row2["client_id"];
					$query2 = "SELECT * FROM `user` WHERE `user_id` = ".$clientID.";";
					$stmt2 = $pdo->prepare($query2);
					$stmt2->execute();
					$row3 = $stmt2->fetch();
					?>
					<div class="row people">
						<div class="col-lg-2 col-md-2 col-sm-12 imageDiv">
							<?php
								$imagepath = null; 
								if(is_null($row3['imagePath'])){
									$imagepath = 'img/profile.png';
								}
								else{
									$imagepath = $row3["imagePath"];
								}
							?>
							<img src="<?php echo($imagepath) ?>">
						</div>
						<div class="col-lg-2 col-md-2 col-sm-12">
							<p class="peoplesName"> <?php echo $row3["firstName"]." ". $row3["surname"]?></p>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-12">
							<p>Last Session Uploaded: <?php echo $row3["lastLogin"] ?></p>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-12">
							<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customsession<?php echo $target ?>" data-bs-whatever="@getbootstrap">View Previous Readings</button>
						</div>
					</div>

					<div class="modal fade" id="customsession<?php echo $target ?>" tabindex="-1" aria-labelledby="customsessionmodal" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="customsessionmodal">View Previous Readings</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						  </div>
						  <div class="modal-body">
							<form class="row g-3" enctype="multipart/form-data" action="3d.php" method="post">
								<div class="col-12">
								<label for="file" class="form-label" >Select Session: </label>
								<select name="custom_session">
									<?php 
									
									$query = "SELECT DISTINCT session_id FROM sensor_data WHERE user_id =". $clientID. ";";
									$stmt = $pdo->prepare($query);
									$stmt->execute();
									$session_no = $stmt->fetchAll();

									// foreach (array_combine($courses, $sections) as $course => $section)
									foreach($session_no as $row){
										echo $row['session_id'];
										$query = "SELECT DISTINCT date FROM sensor_data WHERE session_id =". $row['session_id']. ";";
									$stmt = $pdo->prepare($query);
									$stmt->execute();
									$date = $stmt->fetch();

									echo $date['date'] . "<br>";
									echo "<option value='".$row['session_id']."'> Session #".$row['session_id']." Date: ".$date['date']."</option>";
									}

									?>
									
									</select>

								</div>
								<button class="form-control" type="submit" value="<?php echo $clientID ?>" name="clientid">Submit</button>
							</form>

						  </div>
						</div>
					  </div>
					</div>


				<?php } ?>
			<?php else : ?>

	 <div class="page_heading">
      <h1>Your <span>recovery</span> tracker</h1>
    </div>
   
		<!-- background for the app -->
		<div class="body_background">
		<!-- contains all the main content of the page -->
		<div class="content_container">
			<!-- test form for importing csv using php -->
			<div title="form run on php" class="php_form">

				<button type="button" class="btn btn-primary col-12" data-bs-toggle="modal" data-bs-target="#Modal" data-bs-whatever="@getbootstrap">Upload new sensor readings</button>

				<div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="uploadmodal" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="uploadmodal">Upload sensor data</h5>
				        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				      </div>
				      <div class="modal-body">
						<form class="row g-3" enctype="multipart/form-data" action="upload.php" method="post" onsubmit="showLoader()">
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


				<button type="button" class="btn btn-primary col-12" data-bs-toggle="modal" data-bs-target="#customsession" data-bs-whatever="@getbootstrap">View Previous Readings</button>

				<div class="modal fade" id="customsession" tabindex="-1" aria-labelledby="customsessionmodal" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="customsessionmodal">View Previous Readings</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form class="row g-3" enctype="multipart/form-data" action="3d.php" method="post">
									<div class="col-12">
										<label for="file" class="form-label" >Select Session: </label>
										<select name="custom_session">
											<?php 
											
											$query = "SELECT DISTINCT session_id FROM sensor_data WHERE user_id =". $_SESSION['UserID']. ";";
											$stmt = $pdo->prepare($query);
											$stmt->execute();
											$session_no = $stmt->fetchAll();

											// foreach (array_combine($courses, $sections) as $course => $section)
											foreach($session_no as $row){
												echo $row['session_id'];
												$query = "SELECT DISTINCT date FROM sensor_data WHERE session_id =". $row['session_id']. ";";
											$stmt = $pdo->prepare($query);
											$stmt->execute();
											$date = $stmt->fetch();

											echo $date['date'] . "<br>";
											echo "<option value='".$row['session_id']."'> Session #".$row['session_id']." Date: ".$date['date']."</option>";
											}

											?>
											
										</select>
									</div>
									<input class="form-control" type="submit" value="Check Session" name="submit">
								</form>

							</div>
						</div>
					</div>
				</div>




				<button type="button" class="btn btn-primary col-12" data-bs-toggle="modal" data-bs-target="#comparesession" data-bs-whatever="@getbootstrap">Compare Session Readings</button>

				<div class="modal fade" id="comparesession" tabindex="-1" aria-labelledby="comparesessionmodal" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="comparesessionmodal">Compare Session Readings</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form class="row g-3" enctype="multipart/form-data" action="3d.php" method="post">
									<div class="col-12">
										<label for="file" class="form-label" >Session 1: </label>
										<select name="compare_session_1">
											<?php 
											
											$query = "SELECT DISTINCT session_id FROM sensor_data WHERE user_id =". $_SESSION['UserID']. ";";
											$stmt = $pdo->prepare($query);
											$stmt->execute();
											$session_no = $stmt->fetchAll();

											// foreach (array_combine($courses, $sections) as $course => $section)
											foreach($session_no as $row){
												echo $row['session_id'];
												$query = "SELECT DISTINCT date FROM sensor_data WHERE session_id =". $row['session_id']. ";";
											$stmt = $pdo->prepare($query);
											$stmt->execute();
											$date = $stmt->fetch();

											echo $date['date'] . "<br>";
											echo "<option value='".$row['session_id']."'> Session #".$row['session_id']." Date: ".$date['date']."</option>";
											}

											?>
											
										</select>

										<!-- ---------------------------------------------------- -->

										<br>

										<label for="file" class="form-label" >Session 2: </label>
										<select name="compare_session_2">
										<?php 
										$query = "SELECT DISTINCT session_id FROM sensor_data WHERE user_id =". $_SESSION['UserID']. ";";
										$stmt = $pdo->prepare($query);
										$stmt->execute();
										$session_no = $stmt->fetchAll();

										// foreach (array_combine($courses, $sections) as $course => $section)
										foreach($session_no as $row){
											echo $row['session_id'];
											$query = "SELECT DISTINCT date FROM sensor_data WHERE session_id =". $row['session_id']. ";";
										$stmt = $pdo->prepare($query);
										$stmt->execute();
										$date = $stmt->fetch();

										echo $date['date'] . "<br>";
										echo "<option value='".$row['session_id']."'> Session #".$row['session_id']." Date: ".$date['date']."</option>";
										}
										
										?>
										
										</select>
									</div>
									<input class="form-control" type="submit" value="Check Session" name="submit">
								</form>
							</div>
						</div>
					</div>
				</div>

<!------------------------------------------------------------- End of Jordan changes ------------------------------------------------------------------>

				<?php
					$query = "SELECT MAX(session_id) FROM sensor_data WHERE user_id =". $_SESSION['UserID']. ";";
					$stmt = $pdo->prepare($query);
					$stmt->execute();
					$row = $stmt->fetch();
					$result = $row["MAX(session_id)"];

					//echo $result;
				?>

				<form method="post" action ="3d.php">
					<button value='<?php echo $result ?>' type="submit" name="single_session" class="btn btn-primary col-12">View your last session</button>
				</form>
						

				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
</div>
</div>

<div id="loader"></div>

<script>
function showLoader() {
  document.getElementById("loader").style.display = "block";
  document.getElementById("content").style.display = "none";
  document.querySelector("#body > div.modal-backdrop.fade.show").remove();
}

//https://www.w3schools.com/howto/howto_js_filter_lists.asp Followed and edited this tutorial to get this to work. Has been changed but bones are similiar still
function searchFunction() {
  // Declare variables
  var input, filter, row, p, txtValue;

  input = document.getElementById('myInput');
  filter = input.value.toUpperCase();
  row = document.getElementsByClassName("people");

  for (var i = 0; i < row.length; i++) {
  	p = row[i].getElementsByClassName("peoplesName");
  	txtValue = p[0].innerText;
  	console.log(txtValue);
  	if (txtValue.toUpperCase().indexOf(filter) > -1) {
      row[i].style.display = "";
    } else {
      row[i].style.display = "none";
    }
  }
}
</script>			
</body>
</html>
