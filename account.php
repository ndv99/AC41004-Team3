<?php
    
    //start session and connect to database
    session_start();
    require('db_connect.php');

    //if not logged in redirect to log in page!
    if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {

    header("location: index.php");
    exit;
}

//if form is submitted then attempt to change password
if (isset($_POST['submitDetails'])) {
    $id = $_SESSION['UserID'];
    $query = "SELECT * FROM user WHERE `user_id` = $id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    unset($stmt);


    $oldpassword = $_POST['oldpassword'];

// Compare old password typed in
    if ($oldpassword == $result["password"]) {
        $query = "UPDATE user SET `password` = :password WHERE `user_id` = $id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":password", $password);
        $password = $_POST['newpassword'];
        $stmt->execute();
        unset($stmt);
        echo "<div class='alert alert-warning' role='alert' style='margin-bottom: 0px;padding-bottom: 5px;padding-top: 5px;'>";
        echo "Details Updated!";
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert' style='margin-bottom: 0px;padding-bottom: 5px;padding-top: 5px;'>";
        echo "Details failed to update, Password incorrect?";
        echo "</div>";
    }
    unset($result);
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="./css/account.css">

</head>

<body id="body">

    <nav id="nav" class="navbar navbar-dark">

      <div class="container-fluid">
        <ul class="nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
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

    <div class="container">
        
        <div class="row">
            <div class="col-12">
                <h1 id="your_account">Your <span>Account </span>:</h1>
            </div>
        </div>

        <div class="row">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Details</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row">Username</th>
                    <td><?php echo $_SESSION['username']; ?></td>
                </tr>
                <tr>
                    <th scope="row">Password</th>
                    <td colspan="2"><em>hidden</em></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end">
            <!-- Button trigger modal -->
            <div id="edit_button" class="button-holder" style="justify-content:flex-end;display:flex;">
                <button  type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
                    Edit
                </button>
            </div>
        </div>

        <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Edit Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form Body -->
                        <form class="row g-3" style="padding-top: 0px;" method="post">
                            <div class="col-md-4">
                                <label for="inputoldpass" class="form-label">Old Password *</label>
                                <input type="password" class="form-control" maxlength="20" id="inputoldpass"
                                       name="oldpassword" required>
                            </div>
                            <div class="col-md-4">
                                <label for="inputnewpass" class="form-label">New Password</label>
                                <input type="password" class="form-control" maxlength="20" id="inputnewpass"
                                       name="newpassword" onkeyup='check();' required>
                            </div>
                            <div class="col-md-4">
                                <label for="inputnewpass" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" maxlength="20" id="inputnewpass2"
                                       name="newpassword2" onkeyup='check();' required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button id="submitButton" type="submit" name="submitDetails" method="post" class="btn btn-primary">Save
                                    changes
                                </button>
                            </div>
                        </form>
                        <!-- Form Body End -->
                    </div>
                </div>
            </div>
        </div>

        <!--If role is anything other then athlete then show part for adding users -->
        <?php if ($_SESSION['role'] != "athlete") :?>
            <?php 
                $username = "";
                $username_err = "";

                // when the user wants to make a new account, run a query that adds them to the database as a researcher
                if (isset($_POST['submitAccount'])) {
                    $query = "SELECT user_id FROM user WHERE user.`username` = :username;";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                    $username = $_POST['inputUsername'];
                    $stmt->execute();
                    // if the username is already found in the database, tell the user that this account already exists in the database
                    if ($stmt->rowCount() == 1) {
                        $username_err = "This username is already taken";
                    } else {
                        $username = $_POST['inputUsername'];
                    }

                    if (empty($username_err))   // insert the account into the database
                    {
                        $query = "INSERT INTO user (`firstName`,`username`,`password`,`role`, `surname`) VALUES (:name,:username,'Password123','athlete',:lastname)";

                        $stmt = $pdo->prepare($query);

                        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
                        $stmt->bindParam(":lastname", $lastname, PDO::PARAM_STR);
                        $stmt->bindParam(":username", $username, PDO::PARAM_STR);

                        $name = $_POST['inputName'];
                        $username = $_POST['inputUsername'];
                        $lastname = $_POST['inputSurname'];

                        $stmt->execute();
                        unset($stmt);

                        echo "<div class='alert alert-success' role='alert' style='margin-bottom: 0px;padding-bottom: 5px;padding-top: 5px;'>";
                        echo "<p>Account Created!</p>";
                        echo "<p>username: $username</p>";
                        echo "<p>password: Password123</p>";
                        echo "</div>";

                        //Now that the account has been created need to create link in physio_athlete table
                        $query = "SELECT user_id FROM user WHERE user.`username` = :username;";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                        $stmt->execute();
                        $result = $stmt->fetch();

                        $newAccountID = $result["user_id"];
                        unset($stmt);

                        $query = "INSERT INTO `physio_athlete`(`client_id`, `staff_id`) VALUES (:clientID, :staffID)";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":clientID", $newAccountID, PDO::PARAM_STR);
                        $stmt->bindParam(":staffID", $_SESSION['UserID'], PDO::PARAM_STR);

                        $stmt->execute();
                        unset($stmt);

                    }
                }

            ?>
            <div class="row">
                <div class="col-12 create_account">
                    <h1>Create an account for a client:</h1>
                    <form id="create_account_form" method="POST">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="inputName">Forename</label>
                                <input type="text" class="form-control" id="inputName" name="inputName" placeholder="Forename">
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="inputSurname">Surname</label>
                                <input type="text" class="form-control" id="inputSurname" name="inputSurname" placeholder="Surname">
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="inputUsername">Username</label>
                                <input type="text" class="form-control" name="inputUsername" id="inputUsername" placeholder="Username">
                                <span class="help-block"><?php echo $username_err; ?></span>
                            </div>
                            <div class="col-12">
                                <button id="button_submit" type="submit" class="btn btn-primary" name="submitAccount">Create Account</button>
                            </div>
                        </div>
                    </form>                                   
                </div>
            </div>

        <?php endif; ?>

    </div>

    <script type="text/javascript">
    var check = function() {
        //Javascript for making sure new password and confirm password have same text. 
      if (document.getElementById('inputnewpass').value ==
          document.getElementById('inputnewpass2').value) {
          document.getElementById('submitButton').disabled = false;
      } else {
          document.getElementById('submitButton').disabled = true;
      }
  }
    </script>
</body>
</html>