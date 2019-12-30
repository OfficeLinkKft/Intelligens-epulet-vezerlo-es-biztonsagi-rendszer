<!DOCTYPE HTML>

<html>
<head>
<title>Webkamerák</title>
<meta http-equiv="refresh" content="55;url=all.php">
<meta http-equiv="Content-Language" content="hu">
<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-2" />

<script type="text/javascript" language="javascript" src="../lytebox.js"></script>

<link rel="stylesheet" href="../lytebox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="county.css" type="text/css">
<link rel="shortcut icon" href="../favicon.ico" />

<?
	$con = mysql_connect("localhost","mcsc","qwe1230");
	if (!$con)
		{ die('Could not connect: ' . mysql_error()); }

	mysql_select_db("mcsc", $con);
	mysql_query('SET NAMES utf8');

	// ******  select clients ******
//	$n=$_SESSION['nick'];
	$n='MK';
		if($n =='admin')  {
				$result = mysql_query("SELECT * FROM clients WHERE OFF!='OFF' ORDER BY id");			//for list
				$result2 = mysql_query("SELECT avg(avail28days) FROM clients WHERE OFF!='OFF'"); }		//for available data
		elseif($n =='MK') {
				$result = mysql_query("SELECT * FROM clients WHERE owner LIKE 'MK' AND OFF!='OFF' ORDER BY id");
				$result2 = mysql_query("SELECT avg(avail28days) FROM clients WHERE owner LIKE 'MK' OFF!='OFF'"); }

	$row2 = mysql_fetch_array($result2);	//result of system available data

	$nam='Magyaroszág';

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
			<b>Adatok, képek, weblap: <a href="http://officelink.hu/" target="_blank"> OfficeLink Kft. </a> </b>
			<? echo " | availability: ".(floor($row2[0]*100)/100)."%"; ?>
		</div>
		<! ***** avail  *****>

		<! ***** name *****>
		<div class="sw1">
			<a href="../dispatch.php" target="_blank">megyék</a>
		</div>
		<div class="nam">
			<b><? echo $nam; ?></b>
		</div>
		<div class="sw1">
			<a href="../clients.php" target="_blank">kliensek</a>
		</div>
		<div class="sw1">
			<a href="../map.php" target="_blank" title="térkép"><img src="../images/map_tmb.png" width="27" height="18"></a>
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

				$query4=mysql_query('SELECT ADAT FROM '.$id.'.msvtolin WHERE NEV="port.ite_levego"') or $dmis4=1;
        if($dmis4!=1) {
          $leker4=mysql_fetch_object($query4);
				  $adat4=explode('.',$leker4->ADAT);
				  $data=$adat4[0].'.'.substr($adat4[1],1,1).'oC'; }
        else { $data=" no"; }

				$query5=mysql_query('SELECT ADAT FROM '.$id.'.msvtolin WHERE NEV="port.ite_aszfalt"') or $dmis5=1;
        if($dmis5!=1) {
				  $leker5=mysql_fetch_object($query5);
				  $adat5=explode('.',$leker5->ADAT);
				  $data=$adat5[0].'.'.substr($adat5[1],1,1).'/'.$data; }

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
							<a href=\"".substr($row[web],strpos($row[web],'/')). "/webcam1.jpg\" width=\"600\" height=\"400\" rel=\"lytebox[cam]\" title='Hely: ".$row[address]." - Kamerairány: ".$row[cam1]." - Levegõ/aszfalt hõmérséklet: ".$data."'><img src=\"".substr($row[web],strpos($row[web],'/'))."/webcam1.jpg\" width=".$pw." height=".$ph."></a>
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
							(".$row[id].")<div style='float:right;padding-right:4px;'>".$row[cam1]."</div></a>
						</div>";
						echo "<div class='thumbnail'>
							<a href=\"".substr($row[web],strpos($row[web],'/')). "/webcam2.jpg\" width=\"600\" height=\"400\" rel=\"lytebox[cam]\" title='Hely: ".$row[address]." - Kamerairány: ".$row[cam2]." - Levegõ/aszfalt hõmérséklet: ".$data."'><img src=\"".substr($row[web],strpos($row[web],'/'))."/webcam2.jpg\" width=".$pw." height=".$ph."></a>
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
							(".$row[id].")<div style='float:right;padding-right:4px;'>".$row[cam1]."</div></a>
						</div>";
						echo "<div class='thumbnail'>
							<a href=\"".substr($row[web],strpos($row[web],'/')). "/webcam3.jpg\" width=\"600\" height=\"400\" rel=\"lytebox[cam]\" title='Hely: ".$row[address]." - Kamerairány: ".$row[cam3]." - Levegõ/aszfalt hõmérséklet: ".$data."'><img src=\"".substr($row[web],strpos($row[web],'/'))."/webcam3.jpg\" width=".$pw." height=".$ph."></a>
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
							(".$row[id].")<div style='float:right;padding-right:4px;'>".$row[cam1]."</div></a>
						</div>";
						echo "<div class='thumbnail'>
							<a href=\"".substr($row[web],strpos($row[web],'/')). "/webcam4.jpg\" width=\"600\" height=\"400\" rel=\"lytebox[cam]\" title='Hely: ".$row[address]." - Kamerairány: ".$row[cam4]." - Levegõ/aszfalt hõmérséklet: ".$data."'><img src=\"".substr($row[web],strpos($row[web],'/'))."/webcam4.jpg\" width=".$pw." height=".$ph."></a>
						</div>";
					echo "</div>";
				}

		 } //end of query ?>
	</div>
	<! ***** list  *****>

	<! ***** footer  *****>
		<div class="footer"><? echo "Rendszerek száma: ".$i." | Kamerák száma: ".$j.""; ?></div>
	<! ***** footer  *****>

</div>
<! *****  box  *****>

<? mysql_close($con); ?>
</body>
</html>
