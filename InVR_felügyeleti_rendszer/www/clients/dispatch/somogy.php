<!DOCTYPE HTML>

<html>
<head>
<title>Webkamer�k - Somogy megye</title>
<meta http-equiv="refresh" content="580;url=somogy.php">
<meta http-equiv="Content-Language" content="hu">
<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-2" />

<script type="text/javascript" language="javascript" src="lytebox.js"></script>

<link rel="stylesheet" href="lytebox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="county.css" type="text/css">
<link rel="shortcut icon" href="../favicon.ico" />

<?
	$con = mysql_connect("localhost","mcsc","qwe1230");
	if (!$con)
		{ die('Could not connect: ' . mysql_error()); }

	mysql_select_db("mcsc", $con);

	// ******  select clients ******
//	$n=$_SESSION['nick'];
	$n='somogy';
		if($n =='admin')  {
				$result = mysql_query("SELECT * FROM clients WHERE OFF!='OFF' ORDER BY id");			//for list
				$result2 = mysql_query("SELECT avg(avail28days) FROM clients WHERE OFF!='OFF'"); }		//for available data
		elseif($n =='MK') {
				$result = mysql_query("SELECT * FROM clients WHERE owner LIKE 'MK' AND OFF!='OFF' ORDER BY id");
				$result2 = mysql_query("SELECT avg(avail28days) FROM clients WHERE owner LIKE 'MK'"); }
		elseif($n =='somogy') {
				$result = mysql_query("SELECT * FROM clients WHERE owner LIKE 'MK' AND county LIKE 'Somogy, Q06' AND OFF!='OFF' ORDER BY id");
				$result2 = mysql_query("SELECT avg(avail28days) FROM clients WHERE owner LIKE 'MK' AND county LIKE 'Somogy, Q06' AND OFF!='OFF'"); }

	$row2 = mysql_fetch_array($result2);	//result of system available data

	$nam='Somogy megye';

	$i=0;		//number of clients
	$j=0;		//number of cameras

	$pw=180;	//pix size
	$ph=140;
?>

</head>

<body>
<! *****  box  *****>
<div class="box">

	<! ***** header  *****>
	<div class="header">

		<! ***** avail  *****>
		<div class="avail">
		</div>
		<! ***** avail  *****>

		<! ***** name *****>
		<div class="sw1">
			<a href="../dispatch.php" target="_blank">megy�k</a>
		</div>
		<div class="nam">
			<b><? echo $nam; ?></b>
		</div>
		<div class="sw1">
			<a href="../clients_somogy.php" target="_blank">kliensek</a>
		</div>
		<div class="sw1">
			<a href="../map_somogy.php" target="_blank" title="t�rk�p"><img src="../images/map_tmb.png" width="27" height="18"></a>
		</div>
		<! ***** name  *****>

	</div>
	<! ***** header  *****>

	<! ***** list  *****>
	<div class="list">

		<! ***** list  *****>
		<? while($row = mysql_fetch_array($result)) { $i++; ?>

			<! ***** client status - color  *****>
			<?
				if($row['status']==1) /* OK */
					$bgc="background-color:#cccccc;";
				elseif($row['status']==2) /* 15min - yellow */
					$bgc="background-color:Yellow;";
				else /* 24hour - red */
					$bgc="background-color:Tomato;color:Black;";

			// ***** temperatures  *****
				$id=$row['id'];
			    mysql_connect('localhost','mcs0','qwe1230');


				$query4=mysql_query('SELECT ADAT FROM '.$id.'.msvtolin WHERE NEV="port.ite_levego"') or die('Hiba t�rt�nt az adatok lek�r�s�ben! Hibak�d: JDATA4');
				$leker4=mysql_fetch_object($query4);
				$adat4=explode('.',$leker4->ADAT);
				$data=$adat4[0].'.'.substr($adat4[1],1,1).'�C';

			// ***** pix  *****
				if($row[cam1]!="no")
				{
					$j++; 	//camera sum
					echo "<div class='pix'>";
						echo "<div class='pixtitle' style=".$bgc.">
							<a href='http://kamera.officelink.hu/".$row[id]."' target='_blank' title='Weblap'><b>".$row[name]."</b>
							<div style='float:right;padding-right:4px;'>".$data."</div>
							<br>
							(".$row[id].")<div style='float:right;padding-right:4px;'>".$row[cam1]."</div></a>
						</div>";
						echo "<div class='thumbnail'>
							<a href=\"".substr($row[web],strpos($row[web],'/')). "/webcam1.jpg\" width=\"400\" height=\"300\" rel=\"lytebox[cam]\" title='Hely: ".$row[address]." - Kamerair�ny: ".$row[cam1]." - Leveg� h�m�rs�klet: ".$data."'><img src=\"".substr($row[web],strpos($row[web],'/'))."/webcam1.jpg\" width=".$pw." height=".$ph."></a>
						</div>";
					echo "</div>";
				}
				if($row[cam2]!="no")
				{
					$j++; 	//camera sum
					echo "<div class='pix'>";
						echo "<div class='pixtitle' style=".$bgc.">
							<a href='http://kamera.officelink.hu/".$row[id]."' target='_blank' title='Weblap'><b>".$row[name]."</b>
							<div style='float:right;padding-right:4px;'>".$data."</div>
							<br>
							(".$row[id].")<div style='float:right;padding-right:4px;'>".$row[cam2]."</div></a>
						</div>";
						echo "<div class='thumbnail'>
							<a href=\"".substr($row[web],strpos($row[web],'/')). "/webcam2.jpg\" width=\"400\" height=\"300\" rel=\"lytebox[cam]\" title='Hely: ".$row[address]." - Kamerair�ny: ".$row[cam2]." - Leveg� h�m�rs�klet: ".$data."'><img src=\"".substr($row[web],strpos($row[web],'/'))."/webcam2.jpg\" width=".$pw." height=".$ph."></a>
						</div>";
					echo "</div>";
				}
				if($row[cam3]!="no")
				{
					$j++; 	//camera sum
					echo "<div class='pix'>";
						echo "<div class='pixtitle' style=".$bgc.">
							<a href='http://kamera.officelink.hu/".$row[id]."' target='_blank' title='Weblap'><b>".$row[name]."</b>
							<div style='float:right;padding-right:4px;'>".$data."</div>
							<br>
							(".$row[id].")<div style='float:right;padding-right:4px;'>".$row[cam3]."</div></a>
						</div>";
						echo "<div class='thumbnail'>
							<a href=\"".substr($row[web],strpos($row[web],'/')). "/webcam3.jpg\" width=\"600\" height=\"400\" rel=\"lytebox[cam]\" title='Hely: ".$row[address]." - Kamerair�ny: ".$row[cam3]." - Leveg� h�m�rs�klet: ".$data."'><img src=\"".substr($row[web],strpos($row[web],'/'))."/webcam3.jpg\" width=".$pw." height=".$ph."></a>
						</div>";
					echo "</div>";
				}
				if($row[cam4]!="no")
				{
					$j++; 	//camera sum
					echo "<div class='pix'>";
						echo "<div class='pixtitle' style=".$bgc.">
							<a href='http://kamera.officelink.hu/".$row[id]."' target='_blank' title='Weblap'><b>".$row[name]."</b>
							<div style='float:right;padding-right:4px;'>".$data."</div>
							<br>
							(".$row[id].")<div style='float:right;padding-right:4px;'>".$row[cam4]."</div></a>
						</div>";
						echo "<div class='thumbnail'>
							<a href=\"".substr($row[web],strpos($row[web],'/')). "/webcam4.jpg\" width=\"600\" height=\"400\" rel=\"lytebox[cam]\" title='Hely: ".$row[address]." - Kamerair�ny: ".$row[cam4]." - Leveg� h�m�rs�klet: ".$data."'><img src=\"".substr($row[web],strpos($row[web],'/'))."/webcam4.jpg\" width=".$pw." height=".$ph."></a>
						</div>";
					echo "</div>";
				}

		 } //end of query ?>
	</div>
	<! ***** list  *****>

	<! ***** footer  *****>
		<div class="footer"><? echo "Rendszerek sz�ma: ".$i." | Kamer�k sz�ma: ".$j.""; ?></div>
	<! ***** footer  *****>

</div>
<! *****  box  *****>

<? mysql_close($con); ?>
</body>
</html>
