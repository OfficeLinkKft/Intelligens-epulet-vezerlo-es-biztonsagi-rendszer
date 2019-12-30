<?
	session_start();
	if( $_SESSION['owner'] == "" ) { header('Location: ../../index.php'); exit; }
		else { header('Location: ../products.php'); exit; }
	ob_start();


	ob_end_flush();
?>

