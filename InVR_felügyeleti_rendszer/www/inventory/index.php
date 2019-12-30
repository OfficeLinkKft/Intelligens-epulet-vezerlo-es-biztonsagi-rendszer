<?php
	session_start();
	if( $_SESSION['nick'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../common_files/connect.php";

	$ptitle = "InVR &#9832; Leltár";
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
	# ******  select clients ******
	$user = $_SESSION['nick'];
#	echo $user."<br><br>";

  # ***   select for user and by switches - condition for selection   ***

    # paraméter fogadás url-ben: változó neve, értékadás

	# for clients list

	    $cam=$_GET["cam"];          # 1 -> kamera képek látszanak
	    $br=$_GET["br"];            # 1 -> csak a hibás rendszerek látszanak
	    $off=$_GET["off"];          # offline -> OFFline rendszerek


	    $asld=$_GET["asld"];       # assembled for install

	    $s=$_GET["s"];              # servers -> mcs servers
	    $dev=$_GET["dev"];          # development -> mcs devel systems
	    $q=$_GET["q"];              # officelink -> officelink
	    $rd=$_GET["rd"];            # road type -> officelink kamerák
	    $hw=$_GET["hw"];            # road type -> officelink kamerák

	    $mk_new=$_GET["mk_new"];    # road type -> new road cameras

	    $cty=$_GET["cty"];          # megyék: 1,2,3,4,5,6,7,8,9,1,10,11,12,13,14,15,16,17,18,19 - 0 = összes

	    $log=$_GET["log"];          # 1 -> switch to clients log
	    $iny=$_GET["iny"];          # 1 -> switch to inventory
	    $cal=$_GET["cal"];          # 1 -> switch to calendar



/*
	# conditions for clients list
	$cnd = $cnd." ( owner!='s' AND owner!='dev' AND owner!='q' AND owner!='MK' AND owner!='MKa' AND owner!='MKn'";

	if($asld==1)  { $cnd = $cnd." OR owner='asld' "; }		# assembled for install

	if($s==1)  { $cnd = $cnd." OR owner='s' "; }		      # servers
	if($dev==1)  { $cnd = $cnd." OR owner='dev' "; }	    # development systems
	if($q==1)  { $cnd = $cnd." OR owner='q'"; }		        # officelink
	if($rd==1)  { $cnd = $cnd." OR owner='MK' "; }		    # MK road systems
	if($hw==1)  { $cnd = $cnd." OR owner='MKa' "; }		    # MK highway systems
	if($mk_new==1)  { $cnd = $cnd." OR owner='MKn' "; }	  # new MK systems
	$cnd = $cnd." ) AND ";

	if($off==1)  { $cnd = $cnd."OFF='OFF'"; } else { $cnd = $cnd."OFF!='OFF'"; }	# OFFline systems
	if($br==1)  { $cnd = $cnd." AND (status=0 OR status=3)"; }			# broken systems

	# MK users
	if($user=="MKadmin")  { $cnd = $cnd." AND (owner='MK' OR owner='MKa' OR owner='MKn') "; }	# MK admin user
	if($user=="MK")  { $cnd = $cnd." AND (owner='MK' OR owner='MKa') "; }				# MK user

  	# select only for this county
      	if ($cty==0) { $cnd = $cnd.""; }     # its Hungary
      	if ($cty==1) { $cnd = $cnd." AND county='Bács-Kiskun, C06'"; }                # its Bács-Kiskun, Hungary
      	if ($cty==2) { $cnd = $cnd." AND county='Baranya, B06'"; }                    # its Baranya, Hungary
      	if ($cty==3) { $cnd = $cnd." AND county='Békés, D06'"; }                      # its Békés, Hungary
      	if ($cty==4) { $cnd = $cnd." AND county='Borsod-Abaúj-Zemplén, E06'"; }       # its Borsod-Abaúj-Zemplén, Hungary
      	if ($cty==5) { $cnd = $cnd." AND county='Csongrád, F06'"; }                   # its Csongrád, Hungary
      	if ($cty==6) { $cnd = $cnd." AND county='Fejér, G06'"; }                      # its Fejér, Hungary
      	if ($cty==7) { $cnd = $cnd." AND county='Gy?r-Moson-Sopron, H06'"; }          # its Győr, Hungary
      	if ($cty==8) { $cnd = $cnd." AND county='Hajdú-Bihar, I06'"; }                # its Hajdú-Bihar, Hungary
      	if ($cty==9) { $cnd = $cnd." AND county='Heves, K06'"; }                      # its Heves, Hungary
      	if ($cty==10) { $cnd = $cnd." AND county='Szolnok, L06'"; }                   # its Jász-Nagykun-Szolnok, Hungary
      	if ($cty==11) { $cnd = $cnd." AND county='Komárom-Esztergom, M06'"; }         # its Komárom-Esztergom, Hungary
      	if ($cty==12) { $cnd = $cnd." AND county='Nógrád, N06'"; }                    # its Nógrád, Hungary
      	if ($cty==13) { $cnd = $cnd." AND county='Pest, O06'"; }                      # its Pest, Hungary
      	if ($cty==14) { $cnd = $cnd." AND county='Somogy, Q06'"; }                    # its Somogy, Hungary
      	if ($cty==15) { $cnd = $cnd." AND county='Szabolcs-Szatmár-Bereg, R06'"; }    # its Szabolcs, Hungary
      	if ($cty==16) { $cnd = $cnd." AND county='Tolna, S06'"; }                     # its Tolna, Hungary
      	if ($cty==17) { $cnd = $cnd." AND county='Vas, W06'"; }                       # its Vas, Hungary
      	if ($cty==18) { $cnd = $cnd." AND county='Veszprém, U06'"; }                  # its Veszprém, Hungary
      	if ($cty==19) { $cnd = $cnd." AND county='Zala, V06'"; }                      # its Zala, Hungary
      	if ($cty==100) { $cnd = $cnd.""; }                                             # its Europe
*/









	# for inventory list

	# stocks or mcs<xxx>
	$lctl=$_GET["lctl"]; 				# paraméter fogadás url-ben: változó neve, értékadás
       		if ($lctl=="") {								# if there is nothing, list all
				$lctl = "%";
				$cndi = "location like '$lctl'";			# conditions for inventory log

			} elseif ($lctl=="inSys") {						# inSystem parts
				$lctl = "inSys";
				$cndi = "systempart like ''";				# conditions for inventory log
			} else { 										# select mcs249% all
				$lctl = $lctl;
				$cndi = "location like '".$lctl."%'";			# conditions for inventory log
			}


    	# list of this partnumber
	$pnr=$_GET["pnr"];                   		# paraméter fogadás url-ben: változó neve, értékadás
       		if ($pnr=="") { $pnr = "%"; }          	# if there is nothing, list all

	$cndi = $cndi." AND PN like '$pnr'";		# conditions for inventory log

    	# list of this serial number
	$snr=$_GET["snr"];                   		# paraméter fogadás url-ben: változó neve, értékadás
       		if ($snr=="") { $snr = "%"; }          	# if there is nothing, list all

	$cndi = $cndi." AND SN like '$snr'";		# conditions for inventory log


    	# list of this inventory activity
	$inv_act=$_GET["inv_act"];                   			# paraméter fogadás url-ben: változó neve, értékadás
       		if ($inv_act=="") { $inv_act = "%"; }          	# if there is nothing, list all

	$cndi = $cndi." AND inv_act like '$inv_act'";	# conditions for inventory log

    	# list of this status
	$sts=$_GET["sts"];                   		# paraméter fogadás url-ben: változó neve, értékadás
       		if ($sts=="") { $sts = "%"; }          	# if there is nothing, list all

	$cndi = $cndi." AND status like '$sts'";	# conditions for inventory log

    	# list of this set_for
	$stf=$_GET["stf"];                   		# paraméter fogadás url-ben: változó neve, értékadás
       		if ($stf=="") { $stf = "%"; }          	# if there is nothing, list all

	$cndi = $cndi." AND set_for like '$stf'";	# conditions for inventory log

    	# list of this date
	$dte=$_GET["dte"];                   		# paraméter fogadás url-ben: változó neve, értékadás
       		if ($dte=="") { $dte = "%"; }          	# if there is nothing, list all

	$cndi = $cndi." AND date like '$dte'";		# conditions for inventory log

    	# list of this operator
	$opr=$_GET["opr"];                   		# paraméter fogadás url-ben: változó neve, értékadás
       		if ($opr=="") { $opr = "%"; }          	# if there is nothing, list all

	$cndi = $cndi." AND operator like '$opr'";	# conditions for inventory log

    	# list only last state of this parts
	$lasts=$_GET["lasts"];                   		# paraméter fogadás url-ben: változó neve, értékadás
       		if ($lasts=="") { $lasts = 1; }          	# if there is nothing, list all

	if ($lasts==1) {
		$cndi = $cndi." AND last like '$lasts'";	# conditions for inventory log
	}

	#echo "<h4>cndi: ".$cndi."</h4>"; #for test








  	# ***   selection ordering - condition for ordering   ***
    	# paraméter fogadás url-ben: változó neve, értékadás
	     	$ord=$_GET["ord"];               # ordering
       		if ($ord=="") { $ord = N; }     # default is ordering by id




  	# ***   log list page - condition for listing   ***
    	# paraméter fogadás url-ben: változó neve, értékadás
	     $lp=$_GET["lp"];                   # log page counter
       	if ($lp=="") { $lp = 1; }          # default is first
  	# ***   log list row counter   ***
    	# paraméter fogadás url-ben: változó neve, értékadás
	     $rcn=$_GET["rcn"];                   # log row counter - next record
	     $rcp=$_GET["rcp"];                   # log row counter - previous record
       	if ($rcn=="") { $rcn = 0; }          # default is first



	$i=0;	#number of clients - for counters to footer
	$j=0;	#number of cameras




  	# save the switch's state - for inventory lists
  	$adr = "index.php?lctl=$lctl&pnr=$pnr&snr=$snr&inv_act=$inv_act&sts=$sts&stf=$stf&dte=$dte&";





  # result of system available data
  $result2 = mysqli_query($conn,"SELECT avg(avail28days) FROM clients6 WHERE $cnd"); 	#for available data
  $row2 = mysqli_fetch_array($result2);	#result of system available data

?>

<?php
  # read product's datas for inventory list
     	#echo "PRODUCTS:<br>"; #for check

	$result = mysqli_query($conn,"SELECT * FROM products6");
	while($row = mysqli_fetch_array($result))
	{
    		$N = $row[N];
    		$TOP[$N] = $row[TOP];
    		$PN[$N] = $row[PN];
    		$dscr[$N] = $row[dscr];
    		$DOB[$N] = $row[DOB];
    		$orig[$N] = $row[orig];

    	#echo $N.": ".$TOP[$N]." / ".$PN[$N]." / ".$dscr[$N]." / ".$DOB[$N]." / ".$orig[$N]."<br>"; # for check
	}
  	#echo "<br>";

  # read datas from datalist table
#	echo "Datalist - location:<br>";
#	$result = mysql_query("SELECT * FROM datalist WHERE Name like 'location'");
#	while($row = mysql_fetch_array($result))
#	{
#		echo $row[0].": ";

#		$Nd = 1;
#		while($row[$Nd])
#		{
#	    	$location[$Nd] = $row[$Nd];
#		    echo $location[$Nd]."; ";
#			$Nd++;
#		}
#		echo "<br>";
#	}


	$inSys_color = "#bbf";		# part is inSystem - implemented
	$outSys_color = "#bfb";		# part is outSystem - not implemented
  	#echo "<br>";

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
		<?php  #echo "availability: <b>".(floor($row2[0]*100)/100)."% </b>"; ?>
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
	<! ***** switches / first line - for clients6 *****>
	<div>

		<! ***** map  *****>
		<div class="sw">
			<a href="map.php?<?php  echo $adr; ?>" target="_blank" title="Open Map"><img src="../common_files/img/map_tmb.png" height="18"></a>
		</div>


		<div class="swsp"></div>

		<! ***** assembled systems - for sale, for install  *****>
		<?php
		if(($user=="admin") OR ($user=="root"))  {
			if($asld==0)
				echo "<div class='sw0'><a href='".$adr."asld=1' title='Assembled for install'>new</a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."asld=0' title='Assembled for install'>new</a></div>";
		}
		?>

		<! ***** Servers  *****>
		<?php

		if(($user=="admin") OR ($user=="root"))  {
			if($s==0)
				echo "<div class='sw0'><a href='".$adr."s=1' title='Servers'><img src='../common_files/img/server.png' height='16'></a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."s=0' title='Servers'><img src='../common_files/img/server.png' height='16'></a></div>";
		}
		?>

		<! ***** Development  *****>
		<?php
		if(($user=="admin") OR ($user=="root"))  {
			if($dev==0)
				echo "<div class='sw0'><a href='".$adr."dev=1' title='Development'><img src='../common_files/img/dev.png' height='16'></a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."dev=0' title='Development'><img src='../common_files/img/dev.png' height='16'></a></div>";
		}
		?>

		<! ***** InVR - Officelink *****>
		<?php
		if(($user=="admin") OR ($user=="root"))  {
			if($q==0)
				echo "<div class='sw0'><a href='".$adr."q=1' title='InVR - Officelink'><img src='../common_files/img/officelink_logo.png' height='16'></a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."q=0' title='InVR - OfficeLink'><img src='../common_files/img/officelink_logo.png' height='16'></a></div>";
		}
		?>


		<div class="swsp"></div>

		<! ***** OFFline systems  *****>
		<?php
		if($user!=="MK")  {
			if($off==0)
				echo "<div class='sw0'><a href='".$adr."off=1' title='OFFline systems'><img src='../common_files/img/offline.png' height='16'></a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."off=0' title='OFFline systems'><img src='../common_files/img/offline.png' height='16'></a></div>";
		}
		?>

		<! ***** missing systems  *****>
		<?php
			if($br==0)
				echo "<div class='sw0'><a href='".$adr."br=1' title='only broken systems of the owner'><img src='../common_files/img/broken.png' height='16'></a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."br=0' title='all systems of the owner'><img src='../common_files/img/broken.png' height='16'></a></div>";
		?>

		<! ***** camera pics on/off  *****>
		<?php
			if($cam==0)
				echo "<div class='sw0'><a href='".$adr."cam=1' title='camera pics ON'><img src='../common_files/img/camera.png' height='16'></a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."cam=0' title='camera pics OFF'><img src='../common_files/img/camera.png' height='16'></a></div>";
		?>

		<div class="swsp"></div>


		<! ***** clients log on/off  *****>
		<?php
			if($log==0)
				echo "<div class='sw0'><a href='".$adr."log=1' title='clients log ON'><img src='../common_files/img/log.jpg' width='24' height='18'></a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."log=0' title='clients log OFF'><img src='../common_files/img/log.jpg' width='24' height='18'></a></div>";
		?>


		<! ***** calendar on/off  *****>
		<?php
			if($cal==0)
				echo "<div class='sw0'><a href='".$adr."cal=1' title='calendar ON'><img src='../common_files/img/calendar.png' width='24' height='18'></a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."cal=0' title='calendar OFF'><img src='../common_files/img/calendar.png' width='24' height='18'></a></div>";
		?>

		<div class="swsp"></div>

		<! ***** manual box  *****>
			<div class="swm">
				<a href="/manual/" target="_blank" title="Manuál"><img src="../common_files/img/manual.png" height="18"></a>
			</div>
		<! *****  manual box  *****>

		<! ***** login box  *****>
			<div class="login">
				<a href="../common_files/logout.php" title="logout"><?php  echo $_SESSION['nick']; ?> >></a>
			</div>
		<! ***** login box  *****>

	</div>
	<! ***** switches / first line - for clients *****>

		<br><br><br>

-->







	<! ***** switches / second line - for inventory *****>
	<div>

		<! ***** products  *****>
		<div class="sw" style="width:65px;padding: 7px 5px 3px 5px;">
			<a href="products.php" target="_blank" title="Elem lista megjeneítése">Elem lista</a>
		</div>

		<div class="swsp"></div>
		<div class="swsp"></div>

		<! ***** all parts of inventory  *****>
		<div class="sww0">
			<a href="index.php" title="Minden elem megjelenítése">Minden elem</a>
		</div>

		<! ***** locations: stocks / inSystem  *****>
		<?php
			$result = mysqli_query($conn,"SELECT * FROM datalist WHERE Name like 'location'");
			while($row = mysqli_fetch_array($result))
			{
				$Nd = 1;
				while($row[$Nd])
				{
					$location[$Nd] = $row[$Nd]; #echo $lctl;
					if ($lctl==$location[$Nd]) {
						echo "<div class='sww1'><a href='index.php' title='$location[$Nd] helyszínen lévő elemek megjelenítése'>$location[$Nd]</a></div>";
					} else {
						echo "<div class='sww0' style='background-color:$outSys_color'><a href='".$adr."lctl=$location[$Nd]' title='$location[$Nd] helyszínen lévő elemek megjelenítése'>$location[$Nd]</a></div>";
					}
					$Nd++;
				}
			}

			if($lctl=='inSys') {
				echo "<div class='sww1'><a href='index.php' title='Rendszerbe épített elemek megjelenítése'>inSystem</a></div>";
			} else {
				echo "<div class='sww0' style='background-color:$inSys_color'><a href='".$adr."lctl=inSys' title='Rendszerbe épített elemek megjelenítése'>inSystem</a></div>";
			}
		?>

		<div class="swsp"></div>
		<div class="swsp"></div>

		<! ***** only actual state of parts  *****>
		<?php
			if($lasts==0)
				echo "<div class='sww0'><a href='".$adr."lasts=1' title='Elem(ek) története'>Élettörténet</a></div>";
			else
				echo "<div class='sww1'><a href='".$adr."lasts=0' title='Elem(ek) aktuális státusza'>Aktuális</a></div>";
		?>

		<div class="swsp"></div>
		<div class="swsp"></div>
		<div class="swsp"></div>
		<div class="swsp"></div>

		<! ***** new part to Inventory  *****>
		<div class="sww0" style="background-color:#00cc00;">
			<a href="act_inv_new.php" target="_blank" title="Új elem felvétele">Új elem</a>
		</div>





		<! ***** manual box  *****>
			<div class="swm">
				<a href="/manual/" target="_blank" title="Manuál"><img src="../common_files/img/manual.png" height="18"></a>
			</div>
		<! *****  manual box  *****>

		<! ***** login box  *****>
			<div class="login">
				<a href="../common_files/logout.php" title="logout"><?php  echo $_SESSION['nick']; ?> >></a>
			</div>
		<! ***** login box  *****>


	</div>
	<! ***** switches / second line - for inventory *****>
	<! ***** switches  *****>



















	<! ***** list of inventory *****>
	<?php  if( ($log==0) AND ($iny==0) AND ($cal==0) ) { # only when log and inventory and calendar are OFF ?>

	<div class="list">

		<! ***** list header  *****>
			<p>
				<div class="own" style="background-color:#bbbbbb;height:21px;padding-top:6px;text-align:center;">&nbsp;</div>

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

				<div class="dscr" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
					Leírás
				</div>

				<div class="sn" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="SN")
						echo "<a href='".$adr."ord=SN DESC' title='Rendezés SerialNumber szerint - visszafelé'>SN &nbsp&nbsp\/</a>";
					else
					if($ord=="SN DESC")
						echo "<a href='".$adr."ord=SN' title='Rendezés SerialNumber szerint'>SN &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=SN' title='Rendezés SerialNumber szerint'>SN</a>";
				?>

				</div>

				<div class="port" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
					port
				</div>
				<div class="ip" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
					ip
				</div>



				<div class="date" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="date")
						echo "<a href='".$adr."ord=date DESC' title='Rendezés kiadás dátum szerint - visszafelé'>Kiadás &nbsp&nbsp\/</a>";
					else
					if($ord=="date DESC")
						echo "<a href='".$adr."ord=date' title='Rendezés kiadás dátum szerint'>Kiadás &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=date' title='Rendezés kiadás dátum szerint'>Kiadás</a>";
				?>
				</div>

				<div class="oper" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="operator")
						echo "<a href='".$adr."ord=operator DESC' title='Rendezés operátor szerint - visszafelé'>Operátor &nbsp&nbsp\/</a>";
					else
					if($ord=="operator DESC")
						echo "<a href='".$adr."ord=operator' title='Rendezés operátor szerint'>Operátor &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=operator' title='Rendezés operátor szerint'>Operátor</a>";
				?>
				</div>

				<div class="loc" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="location")
						echo "<a href='".$adr."ord=location DESC' title='Rendezés helyszín szerint - visszafelé'>Helyszín &nbsp&nbsp\/</a>";
					else
					if($ord=="location DESC")
						echo "<a href='".$adr."ord=location' title='Rendezés helyszín szerint'>Helyszín &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=location' title='Rendezés helyszín szerint'>Helyszín</a>";
				?>
				</div>

				<div class="reas" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="inv_act")
						echo "<a href='".$adr."ord=inv_act DESC' title='Rendezés módosítás szerint - visszafelé'>Módosítás &nbsp&nbsp\/</a>";
					else
					if($ord=="inv_act DESC")
						echo "<a href='".$adr."ord=inv_act' title='Rendezés módosítás szerint'>Módosítás &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=inv_act' title='Rendezés módosítás szerint'>Módosítás</a>";

 					echo " / ";

					if($ord=="status")
						echo "<a href='".$adr."ord=status DESC' title='Rendezés státusz szerint - visszafelé'>Státusz &nbsp&nbsp\/</a>";
					else
					if($ord=="status DESC")
						echo "<a href='".$adr."ord=status' title='Rendezés státusz szerint'>Státusz &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=status' title='Rendezés státusz szerint'>Státusz</a>";
				?>
				</div>

				<div class="top" style="background-color:#bbbbbb;height:20px;padding-top:6px;text-align:center;">
					beállítás
				</div>

				<div class="dsheet" style="background-color:#bbbbbb;height:21px;padding-top:6px;text-align:center;">
					DS
				</div>
			</p>

		<! ***** list  *****>
		<?php

#$cndi = "location like '%'";
#echo "<div style='float:left;'>cndi: ".$cndi."</div>";
#echo "<div>ord: ".$ord."</div>";

#     $result = mysql_query("SELECT * FROM inv_log6");								#for list
#     $result = mysql_query("SELECT * FROM inv_log6 WHERE location='mcs202'");		#for list
     $result = mysqli_query($conn,"SELECT * FROM inv_log6 WHERE $cndi ORDER BY $ord");		#for list
     while($row = mysqli_fetch_array($result)) { $i++; ?>

			<! ***** inventory  *****>
			<div class="client">

				<?php  # set product"s datas

				  $Ni = $N;
				  #echo " / Ni: ".$Ni."<br>";		# for test

				  while ( ($row[PN] != $PN[$Ni]) and ($Ni > 0) ) {
				    $Ni--; #echo "... Ni: ".$Ni."<br>";		# for test
				  }

				  #echo $Ni.": ".$PN[$Ni]." <br>";	# for test
				?>



				<?php  # set status color
					$status_i=$row['status'];
					switch ($status_i) {
					    case "empty":
							$bgc="background-color:LightGreen;"; break;
						case "ok":
							$bgc="background-color:Cyan;color:Black;"; break;
						case "mcs":
							$bgc="background-color:RoyalBlue;color:Black;"; break;
						case "fail":
							$bgc="background-color:Coral;color:Black;"; break;
						case "set":
							$bgc="background-color:SkyBlue;color:Black;"; break;
						case "scrap":
							$bgc="background-color:Black;color:White;"; break;

					    case "üres":
							$bgc="background-color:LightGreen;"; break;
						case "ok":
							$bgc="background-color:Cyan;color:Black;"; break;
						case "hiba":
							$bgc="background-color:Coral;color:Black;"; break;
						case "beállítva":
							$bgc="background-color:SkyBlue;color:Black;"; break;
						case "selejt":
							$bgc="background-color:Gray;color:White;"; break;


						default:
							$bgc="background-color:White;color:Black;";
					}
				?>

			<div class="own" style=<?php  echo $bgc;?>;>
				<?php
					switch ($TOP[$Ni]) {
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
					<img src="../common_files/img/<?php  echo $png; ?>" title="Típus: <?php  echo $ttl; ?>"  width="12" height="12">
				</div>

				<div class="pn" style=<?php  echo $bgc;?>;>&nbsp;
				<?php
					if($pnr!=$row[PN])
						echo "<a href='".$adr."pnr=$row[PN]' title='Csak az ilyen PN listázása'><b>".$row[PN]."</b></a>";
					else
						echo "<a href='".$adr."pnr=%' title='Minden egység listázása'><b>".$row[PN]."</b></a>";
				?>
				</div>
				<div class="dscr" style=<?php  echo $bgc;?>;>&nbsp;
				<?php
					if(strlen($dscr[$Ni])>32) { $dscro = substr($dscr[$Ni],0,29); $dscro=$dscro."..."; } else $dscro=$dscr[$Ni];
					echo "<a href='inv_one.php?PN=".$row[PN]."&SN=".$row[SN]."&location=".$row[location]."' target='_blank' title='Elem szerkesztése: $dscr[$Ni]'><b>".$dscro."</b></a>";
				?>
				</div>

				<div class="sn" style=<?php  echo $bgc;?>;>
				<?php
					if(strlen($row[SN])>15) { $SNo = substr($row[SN],0,13); $SNo=$SNo."..."; } else $SNo=$row[SN];
					if($snr!=$row[SN])
						echo "<a href='index.php?snr=$row[SN]&pnr=$row[PN]&lasts=0' title='Az elem története SN: $row[SN] | Megjegyzés: ".$row['note']."'><b># ".$SNo."</b></a>";
					else
						echo "<a href='index.php?snr=%&pnr=%&lasts=1' title='Minden elem. SN: $row[SN]'><b># ".$SNo."</b></a>";

					#echo "<a href='inv_form.php?snr=".$row[SN]."&pnr=".$row[PN]."' target='_blank' title='edit this item'><b># ".$row[SN]."</b></a>";
				?>
				</div>

				<div class="port" style=<?php  echo $bgc;?>;><?php  echo $row[port]; ?>&nbsp;</div>
				<div class="ip" style=<?php  echo $bgc;?>;>
					<?php  echo "<a href='' title='id / name in location: ".$row[id]." / ".$row[name]."'><b>".$row[ip]."</b>&nbsp;</a>"; ?>
				</div>

				<div class="date" style=<?php  echo $bgc;?>;>
				<?php
					if($dte!=$row[date])
						echo "<a href='".$adr."dte=$row[date]' title='Azonos dátumú elemek listázása / Garancia: ".$row[warranty]."'><b>".$row[date]."</b></a>";
					else
						echo "<a href='".$adr."dte=%' title='Minden elem listázása / Garancia: ".$row[warranty]."'><b>".$row[date]."</b></a>";
				?>
				</div>

				<div class="oper" style=<?php  echo $bgc;?>;>&nbsp;
				<?php
					if($opr!=$row[operator])
						echo "<a href='".$adr."opr=$row[operator]' title='Az operátor minden művelete'><b>".$row[operator]."</b></a>";
					else
						echo "<a href='".$adr."opr=%' title='Minden operátor művelete'><b>".$row[operator]."</b></a>";
				?>
				</div>

				<div class="loc" style=<?php if ($row[systempart]=="no") echo "background-color:$outSys_color"; else echo "background-color:$inSys_color"; ?>;>&nbsp;
				<?php
					if($lctl!=$row[location])
						echo "<a href='".$adr."lctl=$row[location]' title='Helyszín elemeinek megjelenítése. &nbsp; id / név: ".$row[id]." / ".$row[name]."'><b>".$row[location]."</b></a>";
					else
						echo "<a href='".$adr."lctl=%' title='Minden helyszín elemeinek megjelenítése. &nbsp; id / név: ".$row[id]." / ".$row[name]."'><b>".$row[location]."</b></a>";
				?>
				</div>

				<div class="reas" style=<?php  echo $bgc;?>;>&nbsp;
				<?php
					if($rsn!=$row[inv_act])
						echo "<a href='".$adr."rsn=$row[inv_act]' title='Azonos művelettel mozgatott elemek listázása'><b>".$row[inv_act]."</b></a>";
					else
						echo "<a href='".$adr."rsn=%' title='Minden művelet'><b>".$row[inv_act]."</b></a>";

					echo " / ";

					if($sts!=$row[status])
						echo "<a href='".$adr."sts=$row[status]' title='Azonos státuszú elemek listázása'><b>".$row[status]."</b></a>";
					else
						echo "<a href='".$adr."sts=%' title='Minden státusz listázása'><b>".$row[status]."</b></a>";
				?>
				</div>

				<div class="top" style=<?php  echo $bgc;?>;>
				<?php
					if($stf!=$row[set_for])
						echo "<a href='".$adr."stf=$row[set_for]' title='Minde elem listázása ezzel a beállítással'>&nbsp;<b>".$row[set_for]."</b></a>";
					else
						echo "<a href='".$adr."stf=%' title='Minden elem listázása'>&nbsp;<b>".$row[set_for]."</b></a>";
				?>
				</div>

				<div class="dsheet" style=<?php  echo $bgc;?>;>
					<a href="<?php  echo 'ds/ds.php?pn='.$row[PN]; ?>" target="_blank" title="<?php echo $row[PN]." adatlap"; ?>"><img src="../common_files/img/dsheet.png" width="10" height="12"></a>
				</div>
			</div>

			<?php
		 } #end of query ?>
	</div>

  <?php  } # only when log and inventory and calendar are OFF ?>
	<! ***** list of clients *****>
















	<! ***** footer  *****>
	<div style="margin-top:10px;text-align:center;font-weight:bold;clear:both;">
    <?php  if( ($cal==0) AND ($log==0) ) { # only when calendar and log are OFF
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
	# closing connection
	mysqli_close($conn);
?>
</body>
</html>

<?php
	ob_end_flush();
?>
