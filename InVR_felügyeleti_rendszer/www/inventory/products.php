<?php
	session_start();
	if( $_SESSION['owner'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../common_files/connect.php";

	$ptitle = "InVR &#9832; - Rendszerelemek";
?>

<!DOCTYPE HTML>

<html>
<head>
	<title><?=$ptitle?></title>
	<meta http-equiv="refresh" content="600">

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

  // ***   select for user and by switches - condition for selection   ***

  // ***   selection ordering - condition for ordering   ***
    // paraméter fogadás url-ben: változó neve, értékadás
	     $ord=$_GET["ord"];               // ordering
       if ($ord=="") { $ord = N; }     // default is ordering by id

  // ***   log list page - condition for listing   ***
    // paraméter fogadás url-ben: változó neve, értékadás
	     $lp=$_GET["lp"];                   // log page counter 
       if ($lp=="") { $lp = 1; }          // default is first
  // ***   log list row counter   ***
    // paraméter fogadás url-ben: változó neve, értékadás
	     $rcn=$_GET["rcn"];                   // log row counter - next record
	     $rcp=$_GET["rcp"];                   // log row counter - previous record
       if ($rcn=="") { $rcn = 0; }          // default is first
  // ***   log list for one client   ***
    // paraméter fogadás url-ben: változó neve, értékadás
	     $idl=$_GET["idl"];                   // log for one client
       if ($idl=="") { $idl = "%"; }          // default is first

	$i=0;	//number of clients - for counters to footer
	$j=0;	//number of cameras

  // save the switch's state
  $adr = "products.php?ord=$ord&cty=$cty&lp=$lp&rcn=$rcn&rcp=$rcp&idl=$idl&asld=$asld&";

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

		<! ***** manual box  *****>
			<div class="swm">
				<a href="/manual/" target="_blank" title="Manuál"><img src="../common_files/img/manual.png" height="18"></a>
			</div>
		<! *****  manual box  *****>

		<! ***** login box  *****>
			<div class="login">
				<a href="../common_files/logout.php" title="logout"><?php echo $_SESSION['nick']; ?> >></a>
			</div>
		<! ***** login box  *****>

	<! ***** switches  *****>



















	<! ***** list of products *****>
	<div class="list">

		<! ***** list header  *****>
			<p>
				<div class="icon" style="background-color:#bbbbbb;height:21px;padding-top:6px;text-align:center;">&nbsp;</div>
				<div class="n" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="N")
						echo "<a href='".$adr."ord=N DESC' title='Rendezés sorszám szerint - visszafelé'>Ssz &nbsp\/</a>";
					else
					if($ord=="N DESC")
						echo "<a href='".$adr."ord=N' title='Rendezés sorszám szerint'>Ssz &nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=N' title='Rendezés sorszám szerint'>Ssz</a>";
				?>

				</div>

				<div class="top" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="TOP")
						echo "<a href='".$adr."ord=TOP DESC' title='Rendezés típus szerint - visszafelé'>Típus &nbsp&nbsp\/</a>";
					else
					if($ord=="TOP DESC")
						echo "<a href='".$adr."ord=TOP' title='Rendezés típus szerint'>Típus &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=TOP' title='Rendezés típus szerint'>Típus</a>";
				?>
				</div>

				<div class="pn" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="PN")
						echo "<a href='".$adr."ord=PN DESC' title='Rendezés PartNumber szerint - visszafelé'>PN &nbsp&nbsp\/</a>";
					else
					if($ord=="PN DESC")
						echo "<a href='".$adr."ord=PN' title='Rendezés PartNumber szerint'>PN &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=PN' title='Rendezés PartNumber szerint'>PN</a>";
				?>
				</div>

				<div class="dscr_p" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">Leírás</div>

				<div class="orig" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="orig")
						echo "<a href='".$adr."ord=orig DESC' title='Rendezés származás szerint - visszafelé'>Származás &nbsp&nbsp\/</a>";
					else
					if($ord=="orig DESC")
						echo "<a href='".$adr."ord=orig' title='Rendezés származás szerint'>Származás &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=orig' title='Rendezés származás szerint'>Származás</a>";
				?>

				</div>

				<div class="dob" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="DOB")
						echo "<a href='".$adr."ord=DOB DESC' title='Rendezés kiadás szerint - visszafelé'>DOB &nbsp&nbsp\/</a>";
					else
					if($ord=="DOB DESC")
						echo "<a href='".$adr."ord=DOB' title='Rendezés kiadás szerint'>Kiadás &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=DOB' title='Rendezés kiadás szerint'>Kiadás</a>";
				?>
				</div>

				<div class="dss" style="background-color:#bbbbbb;height:21px;padding-top:6px;text-align:center;">Adatlapok</div>
			</p>

		<! ***** list  *****>
		<?php 

//     $result = mysqli_query("SELECT * FROM inventory WHERE $cnd ORDER BY $ord");	//for list
     $result = mysqli_query($conn,"SELECT * FROM products6 ORDER BY $ord");	//for list
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


				<div class="icon" style=<?php echo $bgc;?>;>
				<?php
					switch ($row['TOP']) {
					    case "mcs":
							$png="mcs.png"; $ttl="mcs modul"; break;
						case "CPU":
							$png="CPU.png"; $ttl="CPU"; break;
						case "net":
							$png="net.png"; $ttl="hálózat"; break;
						case "camera":
							$png="cam.png"; $ttl="kamera"; break;
						case "enclosure":
							$png="enclosure.png"; $ttl="doboz"; break;
						case "ctr":
							$png="officelink_favicon.png"; $ttl="InVR modul"; break;
						case "sw":
							$png="sw.png"; $ttl="szoftver"; break;
						default:
							$png="broken.png";
					}
				?>
					<img src="../common_files/img/<?php echo $png; ?>" title="Típus: <?php echo $ttl; ?>" width="12" height="12">
				</div>
				<div class="n" style=<?php echo $bgc;?>;>#<?php echo $row['N']; ?></div>

				<div class="top" style=<?php echo $bgc;?>;><?php echo $row['TOP']; ?>&nbsp;</div>
				<div class="pn" style=<?php echo $bgc;?>;><?php echo "<a href='/inventory/index.php?pnr=".$row['PN']."' target='_blank' title='show this product in inventory'><b>".$row['PN']."</b></a>"; ?>&nbsp;</div>

				<div class="dscr_p" style=<?php echo $bgc;?>;>
				<?php
					$file_doc = "ds/".$row['path']."/".$row['doc']; # check doc file
					if(strlen($row['dscr'])>102) { $dscr_S = substr($row['dscr'],0,100); $dscr_S=$dscr_S."..."; } else $dscr_S=$row['dscr']; # cut description, if too long

					if( ($row['doc']!="")  AND file_exists( $file_doc ) ) {
						echo "<a href='".$file_doc."' target='_blank' title='Leírás kinyitása'>".$dscr_S."</a>";
					} else {
						echo $dscr_S;
					}
				?>&nbsp;
				</div>

				<div class="orig" style=<?php echo $bgc;?>;><?php echo $row['orig']; ?>&nbsp;</div>
				<div class="dob" style=<?php echo $bgc;?>;><?php echo $row['DOB']; ?>&nbsp;</div>

				<div class="dss" style=<?php echo $bgc;?>>
				<?php
					$file_doc = "ds/".$row['path']."/".$row['doc'];	# doc file

					if( ($row['doc']!="")  AND file_exists( $file_doc ) ) {
						echo "<a href='".$file_doc."' target='_blank' title='Leírás'><img src='../common_files/img/dsheet.png' width='10' height='12'></a>"; 



					} else {
						echo "&nbsp;";
					}
				?>
				</div>
			</div>

			<?php
		 } //end of query ?>
	</div>
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
