<?php
	session_start();
	ob_start();

	include "common_files/connect.php";

	# ***  setup language  ***
	if (isset($_GET['lang'])) { $_SESSION['lang']=$_GET['lang'];; } # get language data
	elseif (!isset($_SESSION['lang'])) { $lang="HU"; } # default language

	$lang = $_SESSION['lang'];
	include "common_files/language/lang.php";		# dictionary

?>



<!DOCTYPE HTML>
<html>
<head>
	<title><?php echo $lang_login[0][$n_lang]; # InVR &#9832; login ?></title>
	<meta http-equiv="refresh" content="600">
	<meta http-equiv="Content-Language" content="hu">
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

	<link rel="stylesheet" href="index.css" type="text/css">
	<link rel="icon" href="../common_files/img/officelink_favicon.png" type="image/png" sizes="16x16">
</head>




<body>
<div class="loginbox">
	<!-- ***** login ***** -->

		<!-- ***  login form   *** -->
		<div class="login">
			<div class="logo">
				<img src="common_files/img/officelink_logo.png" width="38" style="border:none;" title="officelink logo">
			</div>

			<div class="head">
				<?php echo $lang_login[1][$n_lang]; # Intelligens Vezérlési Rendszer ?><br><br>
				<div id="demo">InVR &#9832;  belépés</div>
			</div>

			<div class="form">
			<form  id="login" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">

				<div class="leftbox">
					<?php echo $lang_login[3][$n_lang]; # Név: ?><br><br>
					<?php echo $lang_login[4][$n_lang]; # Jelszó: ?>
				</div>

				<div class="rightbox">
					<input name="nev" type="text" size="13" maxlength="20" /><br><br>
					<input name="jelszo" type="password" size="13" maxlength="20" />&nbsp;&nbsp;&nbsp;&nbsp;<input name="login" type="submit" value="ok" class="button"/>
				</div>
			</form>


			<form action="index.php" method="get">
				<div class="langbox">
					<input name="lang" title="magyar" type="submit" value="HU" class="button" <?php if ($lang=="HU") echo 'style="background-color:lightgreen"'; ?> >
					<input name="lang" title="english" type="submit" value="EN" class="button" <?php if ($lang=="EN") echo 'style="background-color:lightgreen"'; ?> >
					<input name="lang" title="deutsch" type="submit" value="DE" class="button" <?php if ($lang=="DE") echo 'style="background-color:lightgreen"'; ?> >
					<input name="lang" title="slovinčina" type="submit" value="SI" class="button" <?php if ($lang=="SI") echo 'style="background-color:lightgreen"'; ?> >
					<input name="lang" title="hrvatski" type="submit" value="HR" class="button" <?php if ($lang=="HR") echo 'style="background-color:lightgreen"'; ?> >
				</div>
			</form>
			</div>

		</div>
		<!-- ***  login form   *** -->



		<!-- ***** impressum  ***** -->
		<div class="impr">
			<div class="footer">2019 @ OfficeLink Kft</div>
		</div>
		<!-- ***** impressum  ***** -->

	<!-- ***** login ***** -->





<?php
	# ***  login data received  ***
	if (isset($_POST['login']))
	{ # if datas posted
		$nick = addslashes($_POST['nev']);
		$pass = md5($_POST['jelszo']);
		#echo "a beírt adatok megérkeztek!<br>";
	}

	# ***  database connection  ***
	$sql = "SELECT * FROM users6 WHERE (nick='".$nick."' AND passwd='".$pass."')";
	$result = mysqli_query($conn, $sql);
	$num = mysqli_num_rows($result); 	#echo $num;

	if ($num !== 0)
	{ # nick/pass exists
		echo "<br>megfelelõ név/jelszó!";
		echo "<br>ennyi felhasználó az adatbázisban: ".$num;

		# ***  registering session 'nick'  ***
		$_SESSION['nick'] = addslashes($_POST['nev']);

		while($row = mysqli_fetch_array($result))
		{ # reading fields

			# ***  debug  ***
			echo "<br>*************************************";
			echo "<br>nick: ".$row['nick'];
			echo "<br>email: ".$row['email'];
			echo "<br>owner_of: ".$row['owner_of'];
			echo "<br>jump_to: ".$row['jump_to'];
			echo "<br>*************************************";

			# ***  registering session 'owner'  ***
			$_SESSION['owner'] = $row['owner_of'];

			# ***  jump to  ***
			#$jumpto = "location: ".$row['jump_to'];
			#header($jumpto);
			echo "<meta http-equiv=\"Refresh\" content=\"2;url=".$row['jump_to']."\"><br><br><b>activity! (".$row['owner_of']." / ".$$row['jump_to'].") move on...<br>";
		}
	}

	else
	{ # this nick/pass not exists
		if( $_SESSION['nick'] != "" ) { echo "<br>nem megfelelõ név/jelszó! "; }

		$sql2 = "SELECT * FROM users6 WHERE nick='".$nick."'";
		$result2 = mysqli_query($conn, $sql2);
		$num2 = mysqli_num_rows($result2);

		if ($num2 !== 0);
		{ # nick/pass not exists, but nick exists
			while($row = mysqli_fetch_array($result2))
			{ # reading fields
				/*
				echo "<br>*************************************";
				echo "<br>de van ilyen felhasználó!!";
				echo "   n: ".$num2." db";
				echo "<br>nick: ".$row['nick'];
				echo "<br>email: ".$row['email'];
				echo "<br>owner_of: ".$row['owner_of'];
				echo "<br>jump_to: ".$row['jump_to'];
				echo "<br>*************************************";
				*/
			}
		}
	}


	# closing connection
	mysqli_close($conn);
?>


</div>

</body>


	<script>
		document.getElementById("demo").innerHTML = "<?=$lang_login[2][$n_lang]?>";

	</script>


</html>

<?php
	ob_end_flush();
?>
