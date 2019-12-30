<?
		$con = mysql_connect("localhost","mcsc","qwe1230");
		if (!$con)
			{ die('Could not connect: ' . mysql_error()); }
		mysql_select_db("mcsc", $con);

	/**************************************************
	* Ha még nem lépett be
	***************************************************/

	if ($_SESSION['belepett']!== true)
	{
		$hbel = " kint";
	  	if (isset($_POST['login']))
	  	{ //Ha postolt adatokat
    		$nick = addslashes($_POST['nev']);
    		$pass = md5($_POST['jelszo']);

    		$sql = "SELECT * FROM users ";
    		$sql.= "WHERE (nick='".$nick."'";
    		$sql.= " AND jelszo='".$pass."')";

    		$query = mysql_query($sql);
			$num = mysql_num_rows($query);

    		if (mysql_num_rows($query) !== 0)
    		{ //Helyes nick+pass
				/*  $hbel = "Lépj be, kérlek!"; */
	  			$_SESSION['nick'] = addslashes($_POST['nev']);
      			$_SESSION['belepett'] = true;

      			header("Location: ".$_SERVER['PHP_SELF']);
    		}
	    else
    		{//Hibás nick+pass
       			$hbel = "hibás adatok!";
    		}
  		}
?>

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post"></form>

<?
	}//Nem lépett be

	/********************************
	* Ha már belépett
	*********************************/
	else
  		{//Be van lépve
 ?>

			<div class="login" id="login">
				<a href="login/logout.php">User:&nbsp;<? print $_SESSION['nick']; ?></a>
			</div>

<?
  		}
?>
