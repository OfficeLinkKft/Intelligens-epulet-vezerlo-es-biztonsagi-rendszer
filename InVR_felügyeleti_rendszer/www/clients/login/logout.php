<?php
session_start();
unset($_SESSION['belepett']);
unset($_SESSION['nick']);
header("location: ../index.php");
?>