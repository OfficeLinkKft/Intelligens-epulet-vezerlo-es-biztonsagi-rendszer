<?php

	// ***  database connection  ***
	$db_servername = "localhost";
	$db_username = "mcsc6";
	$db_password = "qwe123006";
	$db_name = "mcsc6";
	// Create connection
	$conn = mysqli_connect($db_servername,$db_username,$db_password,$db_name);
	// Check connection
	if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
	mysqli_query($conn,'SET NAMES utf8');

?>
