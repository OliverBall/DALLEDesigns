<?php
session_start();


if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    header("Location: account.php");
    exit();
}
?>
