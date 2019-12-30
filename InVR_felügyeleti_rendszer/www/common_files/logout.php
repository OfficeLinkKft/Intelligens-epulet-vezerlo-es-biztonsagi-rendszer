<?php
session_start();
unset($_SESSION['nick']);
header("location: ../index.php");
?>
