<?php
	session_start();
	if( $_SESSION['owner'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../common_files/connect.php";

	$ptitle = "InVR &#9832; Jogosultságok";
?>

<!DOCTYPE HTML>

<html>
<head>
	<title><?=$ptitle?></title>
	<meta http-equiv="refresh" content="8000;url=index.php">

	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="hu-hu" />

	<script type="text/javascript" language="javascript" src="lytebox.js"></script>

	<link rel="stylesheet" href="lytebox.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="../common_files/common.css" type="text/css">
	<link rel="stylesheet" href="index.css" type="text/css">
	<link rel="icon" href="../common_files/img/officelink_favicon.png" type="image/png" sizes="16x16">




<?php
	// ******  select clients ******
	$user = $_SESSION['nick'];
//	echo $user."<br><br>";

?>
</head>








<body>
<! ***** head  *****>
<div class="header">
	<div class="hlogo"><a href="" target="_blank"><img src="../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></a></div>
	<div class="hname"><?=$ptitle?></div>
<!--
	<! ***** message box  *****>
	<div class="msg">
    ... message
		<?php  //echo "availability: <b>".(floor($row2[0]*100)/100)."% </b>"; ?>
	</div>
	<! ***** message box  *****>
-->
	<div class="hdate"><?php  echo date('Y.m.d. H:i'); ?> </div>
</div>
<! ***** head  *****>




<! ***** box  *****>
<div class="box">

	<! ***** switches *****>

<!--

		<! ***** map  *****>
		<div class="sw">
			<a href="map.php?<?php echo $adr; ?>" target="_blank" title="Open Map"><img src="../common_files/img/map_tmb.png" height="18"></a>
		</div>

		<! ***** Admin  *****>
		<?php

		if($_SESSION['owner']=="admin")  {
			if($s==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."s=1\" title=\"Servers\"><img src=\"../common_files/img/server.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."s=0\" title=\"Servers\"><img src=\"../common_files/img/server.png\" height=\"16\"></a></div>";
		}
		?>

		<! ***** Development  *****>
		<?php
		if($_SESSION['owner']=="admin")  {
			if($dev==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."dev=1\" title=\"Development\"><img src=\"../common_files/img/dev.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."dev=0\" title=\"Development\"><img src=\"../common_files/img/dev.png\" height=\"16\"></a></div>";
		}
		?>

		<! ***** Users  *****>
		<?php
		if($_SESSION['owner']=="admin")  {
			if($q==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."q=1\" title=\"InVR - OfficeLink\"><img src=\"../common_files/img/officelink_logo.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."q=0\" title=\"InVR - OfficeLink\"><img src=\"../common_files/img/officelink_logo.png\" height=\"16\"></a></div>";
		}
		?>



		<div class="swsp"></div>

		<! ***** OFFline  *****>
		<?php
		if($user!=="MK")  {
			if($off==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."off=1\" title=\"OFFline systems\"><img src=\"../common_files/img/offline.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."off=0\" title=\"OFFline systems\"><img src=\"../common_files/img/offline.png\" height=\"16\"></a></div>";
		}
		?>

		<! ***** camera pics on/off  *****>
		<?php
			if($cam==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."cam=1\" title=\"camera pics ON\"><img src=\"../common_files/img/camera.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."cam=0\" title=\"camera pics OFF\"><img src=\"../common_files/img/camera.png\" height=\"16\"></a></div>";
		?>

		<div class="swsp"></div>

		<! ***** clients log on/off  *****>
		<?php
			if($log==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."log=1\" title=\"clients log ON\"><img src=\"../common_files/img/log.jpg\" width=\"24\" height=\"18\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."log=0\" title=\"clients log OFF\"><img src=\"../common_files/img/log.jpg\" width=\"24\" height=\"18\"></a></div>";
		?>

		<! ***** calendar on/off  *****>
		<?php
			if($cal==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."cal=1\" title=\"calendar ON\"><img src=\"../common_files/img/calendar.png\" width=\"24\" height=\"18\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."cal=0\" title=\"calendar OFF\"><img src=\"../common_files/img/calendar.png\" width=\"24\" height=\"18\"></a></div>";
		?>

		<div class="swsp"></div>

-->

		<! ***** manual box  *****>
			<div class="swm">
				<a href="inv_manual.php" target="_blank" title="Manuál"><img src="../common_files/img/manual.png" height="18"></a>
			</div>
		<! *****  manual box  *****>

		<! ***** login box  *****>
			<div class="login">
				<a href="../logout.php" title="logout"><?php echo $_SESSION['nick']; ?> >></a>
			</div>
		<! ***** login box  *****>

	<! ***** switches  *****>



















	<! ***** list of products *****>
	<?php if( ($log==0) AND ($iny==0) AND ($cal==0) ) { // only when log and inventory and calendar are OFF ?>

	<div class="list">

		<! ***** list header  *****>
			<p>
				<div class="own">&nbsp;</div>
				<div class="n" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="N")
						echo "<a href=\"".$adr."ord=N DESC\" title=\"order by Number - last will be first\">n &nbsp&nbsp\/</a>";
					else
					if($ord=="N DESC")
						echo "<a href=\"".$adr."ord=N\" title=\"order by Number\">n &nbsp&nbsp/\</a>";
					else
						echo "<a href=\"".$adr."ord=N\" title=\"order by Number\">n</a>";
				?>
				</div>

				<div class="group" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="group")
						echo "<a href=\"".$adr."ord=group DESC\" title=\"order by group - last will be first\">group &nbsp&nbsp\/</a>";
					else
					if($ord=="group DESC")
						echo "<a href=\"".$adr."ord=group\" title=\"order by group\">group &nbsp&nbsp/\</a>";
					else
						echo "<a href=\"".$adr."ord=group\" title=\"order by group\">group</a>";
				?>
				</div>

				<div class="nick" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="nick")
						echo "<a href=\"".$adr."ord=nick DESC\" title=\"order by nick - last will be first\">nick &nbsp&nbsp\/</a>";
					else
					if($ord=="nick DESC")
						echo "<a href=\"".$adr."ord=nick\" title=\"order by nick\">nick &nbsp&nbsp/\</a>";
					else
						echo "<a href=\"".$adr."ord=nick\" title=\"order by nick\">nick</a>";
				?>
				</div>

				<div class="owner_of" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="owner_of")
						echo "<a href=\"".$adr."ord=owner_of DESC\" title=\"order by owner_of - last will be first\">owner_of &nbsp&nbsp\/</a>";
					else
					if($ord=="owner_of DESC")
						echo "<a href=\"".$adr."ord=owner_of\" title=\"order by owner_of\">owner_of &nbsp&nbsp/\</a>";
					else
						echo "<a href=\"".$adr."ord=owner_of\" title=\"order by owner_of\">owner_of</a>";
				?>
				</div>

				<div class="jump_to" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="jump_to")
						echo "<a href=\"".$adr."ord=jump_to DESC\" title=\"order by jump_to - last will be first\">jump_to &nbsp&nbsp\/</a>";
					else
					if($ord=="jump_to DESC")
						echo "<a href=\"".$adr."ord=jump_to\" title=\"order by jump_to\">jump_to &nbsp&nbsp/\</a>";
					else
						echo "<a href=\"".$adr."ord=jump_to\" title=\"order by jump_to\">jump_to</a>";
				?>
				</div>

				<div class="firstname" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="firstname")
						echo "<a href=\"".$adr."ord=firstname DESC\" title=\"order by firstname - last will be first\">firstname &nbsp&nbsp\/</a>";
					else
					if($ord=="firstname DESC")
						echo "<a href=\"".$adr."ord=firstname\" title=\"order by firstname\">firstname &nbsp&nbsp/\</a>";
					else
						echo "<a href=\"".$adr."ord=firstname\" title=\"order by firstname\">firstname</a>";
				?>
				</div>

				<div class="familyname" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="familyname")
						echo "<a href=\"".$adr."ord=familyname DESC\" title=\"order by familyname - last will be first\">familyname &nbsp&nbsp\/</a>";
					else
					if($ord=="familyname DESC")
						echo "<a href=\"".$adr."ord=familyname\" title=\"order by familyname\">familyname &nbsp&nbsp/\</a>";
					else
						echo "<a href=\"".$adr."ord=familyname\" title=\"order by familyname\">familyname</a>";
				?>
				</div>

				<div class="dsheet" style="background-color:#eeeeee;height:21px;padding-top:6px;text-align:center;">&nbsp;DS</div>

			</p>

		<! ***** list  *****>
		<?php

     $result = mysqli_query($conn,"SELECT * FROM users6");	//for list
     while($row = mysqli_fetch_array($result)) { $i++; ?>

			<! ***** products  *****>
			<div class="client">

        <?php // set product"s datas
          $Ni = $N;
//          echo " / Ni: ".$Ni."<br>";

          if ($Ni > 0) {
          while ($row['PN'] != $PN[$Ni]) {
            $Ni--;
          }}

//          $Ni = 4;
//          echo $Ni.": ".$PN[$Ni]." <br>";
        ?>

				<?php // set status color - new	set 	ok  fail	repaired	scrap
					if($row['status']=="empty") /* new */
						$bgc="background-color:LightGreen;";
					elseif($row['status']=="ok") /* ok */
						$bgc="background-color:Cyan;color:Black;";
					elseif($row['status']=="fail") /* fail */
						$bgc="background-color:Tomato;color:Black;";
					elseif($row['status']=="scrap") /* scrap */
						$bgc="background-color:Black;color:White;";
					else /* else */
						$bgc="background-color:#eee;color:Black;";

					//$bgc="background-color:lightgray;";
				?>


				<div class="own" style=<?php echo $bgc;?>;>
				<?php
					if($row['owner']=='s') /* server */
						$png="server.png";
					elseif($row['owner']=='dev') /* devel */
						$png="dev.png";
					elseif($row['owner']=='q') /* Officelink */
						$png="officelink_logo.png";
					elseif($row['owner']=='MK') /* MK road */
						$png="road.png";
					elseif($row['owner']=='MKa') /* MK highway */
						$png="highway.png";
					else						 /* more systems */
						$png="dev.png";
				?>
					<img src="../common_files/img/<?php echo $png; ?>" width="12" height="12">
				</div>
				<div class="n" style=<?php echo $bgc;?>;>#<?php echo $row['id']; ?></div>
				<div class="group" style=<?php echo $bgc;?>;>&nbsp;<?php echo $row['group']; ?></div>
				<div class="nick" style=<?php echo $bgc;?>;>&nbsp;
					<a href="mailto:<?php echo $row['email']; ?>?Subject=from_mcs_systems" target="_top"><?php echo $row['nick']; ?></a>
				</div>
				<div class="owner_of" style=<?php echo $bgc;?>;>&nbsp;<?php echo $row['owner_of']; ?></div>
				<div class="jump_to" style=<?php echo $bgc;?>;>&nbsp;<?php echo "<a href='http://".$web."' target='_blank' title='jump to client'><b>".$row['jump_to']."</b></a>"; ?></div>
				<div class="firstname" style=<?php echo $bgc;?>;><?php echo $row['firstname']; ?>&nbsp;</div>
				<div class="familyname" style=<?php echo $bgc;?>;><?php echo $row['familyname']; ?>&nbsp;</div>

				<div class="dsheet" style=<?php echo $bgc;?>;>
					<a href="ds.php?id=<?php echo $row['id']; ?>" rel="lyteframe[datasheet]" title="<?php echo $row['name']." (".$row['id'].")"; ?>" rev="width:720px; height:500px; scrolling:yes;"><img src="../common_files/img/dsheet.png" width="10" height="12"></a>
				</div>

			</div>

			<?php
		 } //end of query ?>
	</div>

  <?php } // only when log and inventory and calendar are OFF ?>
	<! ***** list of clients *****>
















	<! ***** footer  *****>
	<div style="margin-top:10px;text-align:center;font-weight:bold;clear:both;">
    <?php if( ($cal==0) AND ($log==0) ) { // only when calendar and log are OFF
		      echo "<br>Elemek száma: ".$i." db"; }
       else { echo "<br>&nbsp;...thanks for waiting"; }
    ?>
	</div>
  <! ***** footer  *****>

</div>
<! ***** box  *****>


<! ***** impressum  *****>
<div class="impr">
	<div class="imp1"></div>
	<div class="imp2">2019 @ OfficeLink Kft</div>
</div>
<! ***** impressum  *****>


<?php
	// closing connection
	mysqli_close($conn);
?>
</body>
</html>

<?php
	ob_end_flush();
?>
