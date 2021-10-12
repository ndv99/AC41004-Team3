<?php
    session_start();
    require('db_connect.php');

    if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {

    header("location: index.php");
    exit;
}

if (isset($_POST['submitDetails'])) {
    $id = $_SESSION['UserID'];
    $query = "SELECT * FROM user WHERE `user_id` = $id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();
    unset($stmt);


    $oldpassword = $_POST['oldpassword'];
    //$oldpassword = hash('sha256', $oldpassword);

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

</head>
<body>
    <nav class="navbar navbar-dark">
      <div class="container-fluid">
        <ul class="nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php" >Log Out</a>
            </li>
        </ul>
      </div>

    </nav>

    <div class="container">
        
        <div class="row">
            <div class="col-12">
                <h1>Your Account:</h1>
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
            <div class="button-holder" style="justify-content:flex-end;display:flex;">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
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
                            <div class="col-md-6">
                                <label for="inputoldpass" class="form-label">Old Password *</label>
                                <input type="password" class="form-control" maxlength="20" id="inputoldpass"
                                       name="oldpassword" required>
                            </div>
                            <div class="col-md-6">
                                <label for="inputnewpass" class="form-label">New Password</label>
                                <input type="password" class="form-control" maxlength="20" id="inputnewpass"
                                       name="newpassword" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="submitDetails" method="post" class="btn btn-primary">Save
                                    changes
                                </button>
                            </div>
                        </form>
                        <!-- Form Body End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>