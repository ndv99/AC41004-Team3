<?php
    session_start();
    require('db_connect.php');

    //If logged on redirect to dashboard
if (isset($_SESSION['UserID'])) {
    header("Location: dashboard.php");
}

$username_err = $password_err = "";

// Logs in and checks user details to log in
if (isset($_POST['signIn'])) {
    $query = "SELECT * FROM user where username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $username = $_POST['username'];
    $stmt->execute();

    // counts row of statement
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch();
        $id = $row['user_id'];
        $username = $row['username'];
        $password = $row['password'];
        //$hashedpassword = hash('sha256', $_POST['inpassword']);
        $role = $row['role'];
        $name = $row['firstName'];

        // Set sessions variables
        if ($password == $_POST['inpassword']) {
            $_SESSION['loggedIn'] = true;
            $_SESSION['UserID'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $name;

            date_default_timezone_set("Europe/London");
            $currentTime = date_create()->format('Y-m-d H:i:s');


            $query = "UPDATE user SET lastLogin=:currenttime WHERE user_id = :userid;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":currenttime", $currentTime, PDO::PARAM_STR);
            $stmt->bindParam(":userid", $id, PDO::PARAM_STR);
            $stmt->execute();

            header("Location: dashboard.php");
        } else {
            $password_err = "Incorrect password";
        }
    } else {
        $username_err = "No account found with that username";
    }
}

?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous"> -->

    <!-- <link rel="stylesheet" type="text/css" href="../CSS/style.css"> -->

    <link rel="stylesheet" type="text/css" href="./css/login.css" />

    <title>Log In</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico"/>
</head>

<body class="text-center" id="background">

<div class="cont">
    <img class="logo" src="./Style/Images/whiteLogo.png" alt="" width="" height="">

    <form class="form-signin" method="POST">
        <h1 class="h3 mb-3 font-weight-normal"> Log In </h1>
        <input type="text" name="username" id="inputUsername" class="form-control" placeholder="Username" required
               autofocus >
        <span class="help-block"><?php echo $username_err; ?></span> </br>

        <input type="password" name="inpassword" id="inputPassword" class="form-control" placeholder="Password"
               required>
        <span class="help-block"><?php echo $password_err; ?></span> </br>

        <button class="btn btn-lg btn-primary btn-block" type="submit" name="signIn">Sign In</button> </br>

        <p>Don't have an account? <a href="register.php">Register Here</a>.</p>
    </form>
    
</div>

</body>

</html>