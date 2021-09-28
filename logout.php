<?php
session_start();

if (!empty($_SESSION)) {
    // Unset all of the session variables.
    $_SESSION = array();

    // Finally, destroy the session.
    session_destroy();
}
// Redirect to index
header("location: index.php");
?>