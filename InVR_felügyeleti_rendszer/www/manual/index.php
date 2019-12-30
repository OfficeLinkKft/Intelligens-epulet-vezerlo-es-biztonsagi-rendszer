<?php
	session_start();
	ob_start();
	if( $_SESSION['nick'] == "" ) { header('Location: ../index.php'); exit; }

	include "../common_files/connect.php";

	# ***  setup language  ***
	if (isset($_GET['lang'])) { $_SESSION['lang']=$_GET['lang'];; } # get language data
	elseif (!isset($_SESSION['lang'])) { $lang="HU"; } # default language
	include "../common_files/language/lang.php";		# dictionary
	echo "lang: ".$_SESSION['lang']." | ".$n_lang; # check

	$ptitle = $lang_manual[0][$n_lang]; # "InVR &#9832; Manual";

?>

<!DOCTYPE HTML>

<html>
<head>
	<title><?=$ptitle?></title>
	<meta http-equiv="refresh" content="900">

	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="hu-hu" />

	<script type="text/javascript" language="javascript" src="lytebox.js"></script>

	<link rel="stylesheet" href="lytebox.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="index.css" type="text/css">
	<link rel="icon" href="../common_files/img/officelink_favicon.png" type="image/png" sizes="16x16">

</head>






<body>

<! ***** head  *****>
<div class="header">
	<div class="hlogo"><a href="" target='_blank'><img src="../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></a></div>
	<div class="hname"><?=$ptitle?> - <?php echo $lang_manual[1][$n_lang]; # Intelligens Vezérlési Rendszer ?></div>

	<div class="hdate"><?php  echo date('Y.m.d. H:i'); ?> </div>
</div>
<! ***** head  *****>




<! ***** box  *****>
<div class="box">


	<! ***** switches *****>

		<! ***** login box  *****>
			<div class="slogen" style="background-color:#fff;width:400px;text-size:40px;color:#7f1343;">&#9832; <?php echo $lang_manual[2][$n_lang]; # kényelem, takarékosság, presztízs ?></div>

			<div class="login">
				<a href="../common_files/logout.php" title="<?php echo $lang_manual[3][$n_lang]; # logout ?>"><?php echo $_SESSION['nick']; ?> >></a>
			</div>
		<! ***** login box  *****>

	<! ***** switches  *****>




<
<! ***** manual  *****>
<div class="manual">

	<h2 style="margin-top:30px;padding-left:50px;"><?php echo $lang_manual[4][$n_lang]; # I. OfficeLink Intelligens Vezérlési Rendszer | InVR &#9832; ?></h2>

	<h3>1) <b><?php echo $lang_manual[5][$n_lang]; # Felügyeleti rendszer?></b></h3>
		<p>
			<?php echo $lang_manual[6][$n_lang]; # A felügyeleti rendszer nyilvántartja a vezérlési rendszereket és azok felépítését, naplózza a rendszerek elérhetőségét. ?><br>
			<?php echo $lang_manual[7][$n_lang]; # Nyilvántartja a felhasználókat, rendszerelérési jogosultságaikat, az értesítési rendszeren keresztül az eseményekről téjákoztatja őket. ?> 
		</p>
		<br>

	<h3>2) <b><?php echo $lang_manual[8][$n_lang]; # Vezérlési rendszer ?></b></h3>
		<p>
			<?php echo $lang_manual[9][$n_lang]; # A felhasználó számára a vezérlőprogram nyújta a rendszerhez kapcsolt eszközök közötti logikai kapcsolatokon keresztül a rendszer alapvető feladatait: <b>Kényelem, takarékosság, presztízs. ?></b>.
		</p>
		<br>

	<h3>3) <b><?php echo $lang_manual[10][$n_lang]; # Vezérlőmodulok ?></b></h3>
		<p>
			<?php echo $lang_manual[11][$n_lang]; # A rendszer fizikai részegységei a vezérlőmodulok, amelyek elérhetővé teszik a rendszerhez kapcsolt környezeti eszközöket. ?>
		</p>

		<br><br><br><br>







	<h2 style="padding-left:50px;"><?php echo $lang_manual[12][$n_lang]; # II. Felügyeleti rendszer ?></h2>
	
	<h3>1) <a href="../clients/" target='_blank'><?php echo $lang_manual[13][$n_lang]; # Kliens lista ?></a></h3>
		<p>
			<?php echo $lang_manual[14][$n_lang]; # <b>Kliens lista: </b>A kliens lista elemei a szerverek, a vezérlési rendszerek és a felhasználók. ?><br>
			<?php echo $lang_manual[15][$n_lang]; # Egy adott rendszer adatlapja <a href="../clients/ds_form.php?id=mcs001" target='_blank'>itt elérhető</a>. ?>
		</p>
		<br>


	<h3>2) <a href="../flow/flow_list.php" target='_blank'><?php echo $lang_manual[16][$n_lang]; # Rendszerfolyamatok - flow ?></a></h3>
		<p>
			<?php echo $lang_manual[17][$n_lang]; # <b>rendszerfolyamatok: </b>Ez a <a href='../flow/flow_list.php' target='_blank'>rendszerfolyamatok és eljárások listája</a>, amelyek a szerveren vagy a klienseken futnak. ?><br>
			<?php echo $lang_manual[18][$n_lang]; # A rendszerfolyamatok <a href='../flow/index.php' target='_blank'>térképe</a>. ?>
		</p>
		<br>


	<h3>3) <a href="../inventory/" target='_blank'><?php echo $lang_manual[19][$n_lang]; # Leltár ?></a></h3>
		<p>
			<?php echo $lang_manual[20][$n_lang]; # <b>Leltár: </b>A vezérlési rendszerek alkatrészei, hardver és szoftver komponensei, amelyek a működést biztosítják. <a href='../inventory' target='_blank'>Teljes elemlista</a>, naplószerűen rögzítve, azaz elemek és rendszer életciklusa követhető, a gyártástól vagy vásárlástól kezdve a beállításon, összeszerelésen keresztül,  a rendszertelepítésig, valamint a javításig és a selejtezésig. ?><br>
			<?php echo $lang_manual[21][$n_lang]; # Az elemek azonosítása a part numberrel (PN) és a serial numberrel (SN) együttesen történik. ?><br>
			<?php echo $lang_manual[22][$n_lang]; # Vezérlési rendszer alkatrészek, elemek <a href='../inventory/act_inv.php' target='_blank'>hozzáadása, szerkesztése, törlése, állapotváltása</a>. Elemek listája elérhető <a href='../inventory/products.php' target='_blank'>itt</a>. ?>
  		</p>
		<br>


	<h3>4) <a href='../privileges/' target='_blank'><?php echo $lang_manual[23][$n_lang]; # Felhasználók, jogosultságok ?></a></h3>
		<p>
			<?php echo $lang_manual[24][$n_lang]; # <b>felhasználók, jogosultságok: </b>A vezérlőrendszerek tulajdonosai, felhasználói hozzáféréssel rendelkeznek a rendszerekhez: értesítések fogadása, elérés, beállítás.</a> ?>
  		</p>

		<br><br><br><br>






	<h2 style="padding-left:50px;">III. <?php echo $lang_manual[25][$n_lang]; # Felügyeleti rendszer tevékenységek ?></h2>

		<p>
				<a href='../clients/act_base.php' target='_blank'><?php echo $lang_manual[26][$n_lang]; # Rendszerfelügyelet ?></a>

				<p>
					<?php echo $lang_manual[27][$n_lang]; # A rendszerfelügyeletet a rendszerfolyamatok (flow) végzik, amely a <a href='../flow/flow_list.php' target='_blank'>rendszerfolyamatok listájában</a> és a <a href='../flow/index.php' target='_blank'>rendszertérképen</a> követhető. ?>
				</p>
				<br>

				<a href='../inventory/inv.php' target='_blank'><?php echo $lang_manual[28][$n_lang]; # Rendszer elem tevékenységek ?></a>
				<ul>
				  <li><a href='../inventory/act_inv.php' target='_blank'><?php echo $lang_manual[29][$n_lang]; # gyártás / vásárlás ?></a></li>
				  <li><a href='../inventory/act_inv.php' target='_blank'><?php echo $lang_manual[30][$n_lang]; # beállítás / rendszerbe kapcsolás / értékesítés / javítás / selejtezés ?></a></li>
				</ul> 					 
				<br>

				<a href="" target='_blank'><?php echo $lang_manual[31][$n_lang]; # Rendszer összeállítás, üzemeltetés ?></a>
				<ul>
				  <li><?php echo $lang_manual[32][$n_lang]; # új rendszer ?></li>
				  <li><?php echo $lang_manual[33][$n_lang]; # elem rendszerhez adása ?></li>
				  <li><?php echo $lang_manual[34][$n_lang]; # rendszer karbantartás ?></li>
				  <li><?php echo $lang_manual[35][$n_lang]; # rendszerelem cseréje ?></li>
				  <li><?php echo $lang_manual[36][$n_lang]; # rendszer értékesítése ?></li>
				  <li><?php echo $lang_manual[37][$n_lang]; # rendszer bérbeadása ?></li>
				  <li><?php echo $lang_manual[38][$n_lang]; # rendszer szüneteltetése, leállítása ?></li>
				</ul> 					 
				<br>

  		</p>
		<br><br><br><br>





	<h2 style="padding-left:50px;">IV. <?php echo $lang_manual[39][$n_lang]; # Adatlista ?></h2>

		<?php echo $lang_manual[40][$n_lang]; # <b>adatlista: </b>Alapadatok felügyeleti rendszerhez: ?><br><br>

		<table style="margin-left:20px;margin-right:50px;border:2px solid #ddd;font-size:10px;">
		<?php
			echo "<tr style=\"font-weight:bold;\">";
				echo "<td style=\"width:65px;\"><?php echo $lang_manual[41][$n_lang]; # name ?></td>";
				echo "<td style=\"width:65px;\">1</td>"; 
				echo "<td style=\"width:65px;\">2</td>"; 
				echo "<td style=\"width:65px;\">3</td>"; 
				echo "<td style=\"width:65px;\">4</td>"; 
				echo "<td style=\"width:65px;\">5</td>";
				echo "<td style=\"width:65px;\">6</td>"; 
				echo "<td style=\"width:65px;\">7</td>";
				echo "<td style=\"width:65px;\">8</td>"; 
			echo "</tr>";

			$result = mysqli_query($conn,"SELECT * FROM datalist");	
			while($row = mysqli_fetch_array($result))
			{
				echo "<tr>";
				echo "<td>".$row['Name']."</td>";
				echo "<td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td>";
				echo "<td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td>";
				echo "</tr>";
			}
		?>
		</table>

		<br><br><br><br>






	<h2 style="padding-left:50px;">V. <?php echo $lang_manual[42][$n_lang]; # Felügyeleti rendszer fejlesztési napló ?> (s6:/.mcs/devel.log)</h2>

		<p>
			<b>client_supervisor_system.txt: </b><br><br>

			<div style="padding-left:20px;">
			<?php	

				## read manual text
				$source_file = "client_supervisor_system.txt";

				$file = fopen($source_file, "r") or die("Unable to open file!");
				$text = fread($file,filesize($source_file));
				echo "<PRE>" . $text . "</PRE>";
				fclose($file);

			?>
			</div>

  		</p>
		<br><br>






</div>
<! ***** manual  *****>

</div>
<! ***** box  *****>




<! ***** impressum  *****>
<div class="impr">
	<div class="imp1"></div>
	<div class="imp2">2019 @ OfficeLink Kft</div>
</div>
<! ***** impressum  *****>





</body>
</html>



<?php
	// closing connection
	mysqli_close($conn);

	ob_end_flush();
?>

