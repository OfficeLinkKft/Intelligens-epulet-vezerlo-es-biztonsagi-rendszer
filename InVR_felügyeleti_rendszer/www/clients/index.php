<?php
	session_start();
	if( $_SESSION['nick'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../common_files/connect.php";

	# ***  setup language  ***
	if (isset($_GET['lang'])) { $_SESSION['lang']=$_GET['lang'];; } # get language data
	elseif (!isset($_SESSION['lang'])) { $lang="HU"; } # default language
	include "../common_files/language/lang.php";		# dictionary
	echo "lang: ".$_SESSION['lang']." | ".$n_lang; # check

	$ptitle = $lang_clients[0][$n_lang]; # "InVR &#9832; Kliens lista"

?>

<!DOCTYPE HTML>

<html>
<head>
	<title><?=$ptitle?></title>
	<meta http-equiv="refresh" content="59;url=<?=$adr?>">

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

	// ******  clients status ******
	/*
				switch ($row['status']) {
					case 0:		# 24hour - red
						$bgc="background-color:Tomato;color:Black;";
						$st="lost (24hour)";
						break;
					case 1: 	# OK - gray
						$bgc="background-color:#cccccc;";
						$st="OK";
						break;
					case 2:		# 15min - yellow
						$bgc="background-color:Yellow;";
						$st="lost (15min)";
						break;
					case 3:		# no power - blue
						$bgc="background-color:DeepSkyBlue;color:Black;";
						$st="no power";
						break;
					case 40:	# vpn user, but its down - other red
						$bgc="background-color:#ff5555;color:Black;";
						$st="no vpn user";
						break;
					case 41: 	# vpn_ping<100 - fast connection
						$bgc="background-color:#b3d9ff;";
						$st="fast connection";
						break;
					case 42: 	# vpn_ping<200 - slow connection
						$bgc="background-color:#80bfff;";
						$st="slow connection";
						break;
					case 43: 	# vpn_ping<1000 - very slow connection
						$bgc="background-color:#1a8cff;color:Black;";
						$st="very slow connection";
						break;
					default:	# default
     					$bgc="background-color:#bbbbbb;";
						$st="default";
				}

		*/




  // ***   select for user and by switches - condition for selection   ***

    // paraméter fogadás url-ben: változó neve, értékadás

	    $cam=$_GET["cam"];          // 1 -> kamera képek látszanak
	    $br=$_GET["br"];            // 1 -> csak a hibás rendszerek látszanak
	    $off=$_GET["off"];          // offline -> OFFline rendszerek

		//owner
	    $usr=$_GET["usr"];       	// vpn users
	    $s=$_GET["s"];              // servers -> mcs servers
	    $dev=$_GET["dev"];          // development -> mcs devel systems
	    $q=$_GET["q"];              // officelink -> officelink
	    $rd=$_GET["rd"];            // road type -> officelink kamerák
	    $hw=$_GET["hw"];            // road type -> officelink kamerák
	    $MKn=$_GET["MKn"];    		// new MK systems

	    $cty=$_GET["cty"];          // megyék: 1,2,3,4,5,6,7,8,9,1,10,11,12,13,14,15,16,17,18,19 - 0 = összes

		//for log status select switches - see statuses up
	    $log=$_GET["log"];          // 1 -> switch to clients log
		$log0=$_GET["log0"];		// 1 : switch on log with this status -
		$log1=$_GET["log1"];		// 1 : switch on log with this status
		$log2=$_GET["log2"];		// 1 : switch on log with this status
		$log3=$_GET["log3"];		// 1 : switch on log with this status
		$log40=$_GET["log40"];		// 1 : switch on log with this status
		$log41=$_GET["log41"];		// 1 : switch on log with this status
		$log42=$_GET["log42"];		// 1 : switch on log with this status
		$log43=$_GET["log43"];		// 1 : switch on log with this status


	    $iny=$_GET["iny"];          // 1 -> switch to inventory
	    $cal=$_GET["cal"];          // 1 -> switch to calendar


	$cnd = $cnd." ( owner!='usr' AND owner!='s' AND owner!='dev' AND owner!='q' AND owner!='MK' AND owner!='MKa' AND owner!='MKn'";

	if($usr==1)  { $cnd = $cnd." OR owner='usr' "; }		// vpn users
	if($s==1)  { $cnd = $cnd." OR owner='s' "; }		    // servers
	if($dev==1)  { $cnd = $cnd." OR owner='dev' "; }	    // development systems
	if($q==1)  { $cnd = $cnd." OR owner='q'"; }		        // officelink
	if($rd==1)  { $cnd = $cnd." OR owner='MK' "; }		    // MK road systems
	if($hw==1)  { $cnd = $cnd." OR owner='MKa' "; }		    // MK highway systems
	if($MKn==1)  { $cnd = $cnd." OR owner='MKn' "; }	  	// new MK systems
	$cnd = $cnd." ) AND ";

	if($off==1)  { $cnd = $cnd."OFF LIKE 'OFF'"; } else { $cnd = $cnd."OFF NOT LIKE 'OFF'"; }	// OFFline systems
	if($br==1)  { $cnd = $cnd." AND (status=0 OR status=3)"; }			// broken systems

	// MK users
	if($user=="MKadmin")  { $cnd = $cnd." AND (owner='MK' OR owner='MKa' OR owner='MKn') "; }	// MK admin user
	if($user=="MK")  { $cnd = $cnd." AND (owner='MK' OR owner='MKa') "; }				// MK user

  // select only for this county
      if ($cty==0) { $cnd = $cnd.""; }     // its Hungary
      if ($cty==1) { $cnd = $cnd." AND county='Bács-Kiskun, C06'"; }                // its Bács-Kiskun, Hungary
      if ($cty==2) { $cnd = $cnd." AND county='Baranya, B06'"; }                    // its Baranya, Hungary
      if ($cty==3) { $cnd = $cnd." AND county='Békés, D06'"; }                      // its Békés, Hungary
      if ($cty==4) { $cnd = $cnd." AND county='Borsod-Abaúj-Zemplén, E06'"; }       // its Borsod-Abaúj-Zemplén, Hungary
      if ($cty==5) { $cnd = $cnd." AND county='Csongrád, F06'"; }                   // its Csongrád, Hungary
      if ($cty==6) { $cnd = $cnd." AND county='Fejér, G06'"; }                      // its Fejér, Hungary
      if ($cty==7) { $cnd = $cnd." AND county='Gy?r-Moson-Sopron, H06'"; }          // its Győr, Hungary
      if ($cty==8) { $cnd = $cnd." AND county='Hajdú-Bihar, I06'"; }                // its Hajdú-Bihar, Hungary
      if ($cty==9) { $cnd = $cnd." AND county='Heves, K06'"; }                      // its Heves, Hungary
      if ($cty==10) { $cnd = $cnd." AND county='Szolnok, L06'"; }                   // its Jász-Nagykun-Szolnok, Hungary
      if ($cty==11) { $cnd = $cnd." AND county='Komárom-Esztergom, M06'"; }         // its Komárom-Esztergom, Hungary
      if ($cty==12) { $cnd = $cnd." AND county='Nógrád, N06'"; }                    // its Nógrád, Hungary
      if ($cty==13) { $cnd = $cnd." AND county='Pest, O06'"; }                      // its Pest, Hungary
      if ($cty==14) { $cnd = $cnd." AND county='Somogy, Q06'"; }                    // its Somogy, Hungary
      if ($cty==15) { $cnd = $cnd." AND county='Szabolcs-Szatmár-Bereg, R06'"; }    // its Szabolcs, Hungary
      if ($cty==16) { $cnd = $cnd." AND county='Tolna, S06'"; }                     // its Tolna, Hungary
      if ($cty==17) { $cnd = $cnd." AND county='Vas, W06'"; }                       // its Vas, Hungary
      if ($cty==18) { $cnd = $cnd." AND county='Veszprém, U06'"; }                  // its Veszprém, Hungary
      if ($cty==19) { $cnd = $cnd." AND county='Zala, V06'"; }                      // its Zala, Hungary
      if ($cty==100) { $cnd = $cnd.""; }                                             // its Europe

//	echo $cnd."<br><br>"; //check


	# log




  // ***   selection ordering - condition for ordering   ***
    // paraméter fogadás url-ben: változó neve, értékadás
	     $ord=$_GET["ord"];               // ordering
       if ($ord=="") { $ord = id; }     // default is ordering by id




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
	$adr = "index.php?usr=$usr&s=$s&q=$q&rd=$rd&hw=$hw&dev=$dev&MKn=$MKn&cam=$cam&off=$off&br=$br&ord=$ord&cty=$cty&lp=$lp&rcn=$rcn&rcp=$rcp&idl=$idl&log=$log&log=$log&log0=$log0&log1=$log1&log2=$log2&log3=$log3&log40=$log40&log41=$log41&log42=$log42&log43=$log43&";


  // result of system available data
  $result2 = mysqli_query($conn,"SELECT avg(avail28days) FROM clients6  WHERE $cnd"); 	//for available data
  $row2 = mysqli_fetch_array($result2);	//result of "system available" data

?>
</head>

















<body>

<! ***** head  *****>
<div class="header">
	<div class="hlogo"><a href="" target="_blank"><img src="../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></a></div>
	<div class="hname"><?=$ptitle?></div>
	<! ***** message box  *****>
	<div class="msg">
		<?php echo "availability: <b>".(floor($row2[0]*100)/100)."% </b>"; ?>
	</div>
	<! ***** message box  *****>
	<div class="hdate"><?php echo date('Y.m.d. H:i'); ?> </div>
</div>
<! ***** head  *****>




<! ***** box  *****>
<div class="box">

	<! ***** switches *****>

		<! ***** flow  *****>
		<div class="sw">
			<a href="../flow/flow_list.php" target="_blank" title="<?php echo $lang_sw[2][$n_lang]; # flow_list ?>"><img src="../common_files/img/flow_tmb.png" height="18"></a>
		</div>


		<! ***** map  *****>
		<div class="sw">
			<a href="map.php?<?php echo $adr; ?>" target="_blank" title="<?php echo $lang_sw[4][$n_lang]; # open map ?>"><img src="../common_files/img/map_tmb.png" height="18"></a>
		</div>

		<! ***** dispach  *****>
		<?php if($log==0) { # when log is not ON ?>
		<div class="sw">
			<a href="dispatch.php" target="_blank" title="<?php echo $lang_sw[5][$n_lang]; # dispatcher screens ?>"><img src="../common_files/img/dispatch.png" height="18"></a>
		</div>
		<?php } ?>

		<div class="swsp"></div>
		<div class="swsp"></div>

		<! ***** VPN users - usr *****>
		<?php
		if($user!=="MK")  {
			if($usr==0)
				echo "<div class='sw0'><a href='".$adr."usr=1' title='".$lang_sw[6][$n_lang]."'><img src='../common_files/img/usr.png' height='16'></a></div>"; # VPN users
			else
				echo "<div class='sw1'><a href='".$adr."usr=0' title='".$lang_sw[6][$n_lang]."'><img src='../common_files/img/usr.png' height='16'></a></div>";
		}
		?>

		<div class="swsp"></div>

		<! ***** Servers  *****>
		<?php

		if($_SESSION['owner']=="admin")  {
			if($s==0)
				echo "<div class='sw0'><a href='".$adr."s=1' title='".$lang_sw[7][$n_lang]."'><img src='../common_files/img/server.png' height='16'></a></div>"; # Server
			else
				echo "<div class='sw1'><a href='".$adr."s=0' title='".$lang_sw[7][$n_lang]."'><img src='../common_files/img/server.png' height='16'></a></div>";
		}
		?>

		<! ***** Development  *****>
		<?php
		if($_SESSION['owner']=="admin")  {
			if($dev==0)
				echo "<div class='sw0' style='font-size:16'><a href='".$adr."dev=1' title='".$lang_sw[8][$n_lang]."'>&#9874;</a></div>"; # Development - Base systems
			else
				echo "<div class='sw1' style='font-size:16'><a href='".$adr."dev=0' title='".$lang_sw[8][$n_lang]."'>&#9874;</a></div>";
		}
		?>

		<! ***** Officelink  *****>
		<?php
		if($_SESSION['owner']=="admin")  {
			if($q==0)
				echo "<div class='sw0' style='font-size:16'><a href='".$adr."q=1' title='".$lang_sw[9][$n_lang]."'>&#9832;</a></div>"; # officelink
			else
				echo "<div class='sw1' style='font-size:16'><a href='".$adr."q=0' title='".$lang_sw[9][$n_lang]."'>&#9832;</a></div>";
		}
		?>



		<! ***** OFFline systems  *****>
		<?php
		if($user!=="MK")  {
			if($off==0)
				echo "<div class='sw0'><a href='".$adr."off=1' title='".$lang_sw[10][$n_lang]."'><img src='../common_files/img/offline.png' height='16'></a></div>"; # OFFline systems
			else
				echo "<div class='sw1'><a href='".$adr."off=0' title='".$lang_sw[10][$n_lang]."'><img src='../common_files/img/offline.png' height='16'></a></div>";
		}
		?>

		<! ***** missing systems  *****>
		<?php
			if($br==0)
				echo "<div class='sw0'><a href='".$adr."br=1' title='".$lang_sw[11][$n_lang]."'><img src='../common_files/img/broken.png' height='16'></a></div>"; # only broken systems of the owner
			else
				echo "<div class='sw1'><a href='".$adr."br=0' title='".$lang_sw[11][$n_lang]."'><img src='../common_files/img/broken.png' height='16'></a></div>";
		?>

		<! ***** camera pics on/off  *****>
		<?php
			if($log==0) { # when log is not ON
			if($cam==0)
				echo "<div class='sw0'><a href='".$adr."cam=1' title='".$lang_sw[12][$n_lang]."'><img src='../common_files/img/camera.png' height='16'></a></div>"; # camera pics ON / OFF
			else
				echo "<div class='sw1'><a href='".$adr."cam=0' title='".$lang_sw[12][$n_lang]."'><img src='../common_files/img/camera.png' height='16'></a></div>";
			}
		?>

		<div class="swsp"></div>
		<div class="swsp"></div>

		<! ***** clients log on/off  *****>
		<?php
			if($log==0)
				echo "<div class='sw0'><a href='".$adr."log=1&idl=%&log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&' title='".$lang_sw[13][$n_lang]."'><img src='../common_files/img/log.jpg' width='24' height='18'></a></div>"; # clients log ON / OFF
			else
				echo "<div class='sw1'><a href='".$adr."log=0&idl=%&' title='".$lang_sw[13][$n_lang]."'><img src='../common_files/img/log.jpg' width='24' height='18'></a></div>";
		?>
		<! ***** status selector - if clients log on  *****>
		<?php
			if($log==1) {

/*
					case 0:		# lost (24hour) - red
						$bgc="background-color:Tomato;color:Black;";
						$st="lost (24hour)";
					case 1: 	# OK - gray
						$bgc="background-color:#cccccc;";
						$st="OK";
					case 2:		# lost (15min) - yellow
						$bgc="background-color:Yellow;";
						$st="lost (15min)";
					case 3:		# no power - blue
						$bgc="background-color:DeepSkyBlue;color:Black;";
						$st="no power";
					case 40:	# vpn user, but its down - other red
						$bgc="background-color:#ff5555;color:Black;";
						$st="lost vpn user";
					case 41: 	# vpn_ping<100 - fast connection
						$bgc="background-color:#b3d9ff;";
						$st="fast connection";
					case 42: 	# vpn_ping<200 - slow connection
						$bgc="background-color:#80bfff;";
						$st="slow connection";
					case 43: 	# vpn_ping<1000 - very slow connection
						$bgc="background-color:#1a8cff;color:Black;";
						$st="very slow connection";
					default:	# default
     					$bgc="background-color:#bbbbbb;";
						$st="default";
*/
				# all status ON switch
				echo "<div class='sw1' style='background-color:White;'><a href='".$adr."log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&' title='".$lang_sw[14][$n_lang]."'>all</a></div>"; # all status log -> ON
				# all status OFF switch
				echo "<div class='sw1' style='background-color:#666666;color:White;'><a href='".$adr."log0=0&log1=0&log2=0&log3=0&log40=0&log41=0&log42=0&log43=0&' title='".$lang_sw[15][$n_lang]."'>no</a></div>"; # all status log -> OFF


				if($log0==0)	# status=0
					echo "<div class='sw0' style='background-color:Tomato;color:Black;'><a href='".$adr."log0=1' title='".$lang_sw[16][$n_lang]."'>0</a></div>"; # lost (24hour) | status=0 -> ON
				else
					echo "<div class='sw1' style='background-color:Tomato;color:Black;'><a href='".$adr."log0=0' title='".$lang_sw[17][$n_lang]."'>0</a></div>"; # lost (24hour) | status=0 -> OFF

				if($log1==0)	# status=1
					echo "<div class='sw0' style='background-color:#cccccc;'><a href='".$adr."log1=1' title='".$lang_sw[18][$n_lang]."'>1</a></div>"; # OK | status=1 -> ON
				else
					echo "<div class='sw1' style='background-color:#cccccc;'><a href='".$adr."log1=0' title='".$lang_sw[19][$n_lang]."'>1</a></div>"; # OK | status=1 -> OFF

				if($log2==0)	# status=2
					echo "<div class='sw0' style='background-color:Yellow;'><a href='".$adr."log2=1' title='".$lang_sw[20][$n_lang]."'>2</a></div>"; # lost (15min) | status=2 -> ON
				else
					echo "<div class='sw1' style='background-color:Yellow;'><a href='".$adr."log2=0' title='".$lang_sw[21][$n_lang]."'>2</a></div>"; # lost (15min) | status=2 -> OFF

				if($log3==0)	# status=3
					echo "<div class='sw0' style='background-color:DeepSkyBlue;color:Black;'><a href='".$adr."log3=1' title='".$lang_sw[22][$n_lang]."'>3</a></div>"; # no power | status=3 -> ON
				else
					echo "<div class='sw1' style='background-color:DeepSkyBlue;color:Black;'><a href='".$adr."log3=0' title='".$lang_sw[23][$n_lang]."'>3</a></div>"; # no power | status=3 -> OFF

				if($log40==0)	# status=40
					echo "<div class='sw0' style='background-color:#ff5555;color:Black;'><a href='".$adr."log40=1' title='".$lang_sw[24][$n_lang]."'>40</a></div>"; # lost vpn user | status=40 -> ON
				else
					echo "<div class='sw1' style='background-color:#ff5555;color:Black;'><a href='".$adr."log40=0' title='".$lang_sw[25][$n_lang]."'>40</a></div>"; # lost vpn user | status=40 -> OFF

				if($log41==0)	# status=41
					echo "<div class='sw0' style='background-color:#b3d9ff;'><a href='".$adr."log41=1' title='".$lang_sw[26][$n_lang]."'>41</a></div>"; # lost vpn user | status=41 -> ON
				else
					echo "<div class='sw1' style='background-color:#b3d9ff;'><a href='".$adr."log41=0' title='".$lang_sw[27][$n_lang]."'>41</a></div>"; # lost vpn user | status=41 -> OFF

				if($log42==0)	# status=42
					echo "<div class='sw0' style='background-color:#80bfff;'><a href='".$adr."log42=1' title='".$lang_sw[28][$n_lang]."'>42</a></div>"; # lost vpn user | status=42 -> ON
				else
					echo "<div class='sw1' style='background-color:#80bfff;'><a href='".$adr."log42=0' title='".$lang_sw[29][$n_lang]."'>42</a></div>"; # lost vpn user | status=42 -> OFF

				if($log43==0)	# status=43
					echo "<div class='sw0' style='background-color:#1a8cff;color:Black;'><a href='".$adr."log43=1' title='".$lang_sw[30][$n_lang]."'>43</a></div>"; # lost vpn user | status=43 -> ON
				else
					echo "<div class='sw1' style='background-color:#1a8cff;color:Black;'><a href='".$adr."log43=0' title='".$lang_sw[31][$n_lang]."'>43</a></div>"; # lost vpn user | status=43 -> OFF

				# echo idl, if exist
				echo "<div class='sw0'><a href='".$adr."idl=%' title='".$lang_sw[32][$n_lang]."'>$idl</a></div>"; # idl

			}
		?>







		<?php
			if($log==0) { # when log is not ON
		?>

		<! ***** calendar on/off  *****>
		<?php
			if($cal==0)
				echo "<div class='sw0'><a href='".$adr."cal=1' title='".$lang_sw[33][$n_lang]."'><img src='../common_files/img/calendar.png' width='24' height='18'></a></div>"; # calendar ON/OFF
			else
				echo "<div class='sw1'><a href='".$adr."cal=0' title='".$lang_sw[33][$n_lang]."'><img src='../common_files/img/calendar.png' width='24' height='18'></a></div>";
		?>

		<div class="swsp"></div>
		<div class="swsp"></div>

		<! ***** openvpn-status.log  *****>
		<div class="sw0" style="background-color:#b3d9ff;">
			<a href="ovpn_status.php" target="_blank" title="openvpn-status.log">ovpn</a>
		</div>

		<div class="swsp"></div>
		<div class="swsp"></div>

		<! ***** new system  *****>
		<div class="sw0" style="background-color:#00cc00;">
			<a href="ds/act_client_new.php" target="_blank" title="<?php echo $lang_sw[34][$n_lang]; # Új rendszer felvétele ?>"><?php echo $lang_sw[35][$n_lang]; # Új rendszer ?></a>
		</div>

		<?php
			}
		?>


		<! ***** login box  *****>
			<div class="login">
				<a href="../common_files/logout.php" title="<?php echo $lang_sw[36][$n_lang]; # logout ?>"><?php echo $_SESSION['nick']; ?> >></a>
			</div>
		<! ***** login box  *****>

		<! ***** manual box  *****>
			<div class="swm">
				<a href="../manual/index.php" target="_blank" title="<?php echo $lang_sw[37][$n_lang]; # Manual ?>"><img src="../common_files/img/manual.png" height="18"></a>
			</div>
		<! *****  manual box  *****>

	<! ***** switches  *****>
































	<! ***** list of clients *****>
	<?php if( ($log==0) AND ($iny==0) AND ($cal==0) ) { // only when log and inventory and calendar are OFF ?>

	<div class="list">

		<! ***** list header  *****>
			<p>
				<div class="own" style="background-color:#eeeeee;height:21px;padding-top:6px;text-align:center;">&nbsp;</div>

				<div class="deliv" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
				<?php echo $lang_clients[1][$n_lang]; # CPU ?>
				<?php
/*					if($ord=="del_date")
						echo "<a href='".$adr."ord=del_date DESC' title='order by delivery date - last will be first'>delivery &nbsp&nbsp\/</a>";
					elseif($ord=="del_date DESC")
						echo "<a href='".$adr."ord=del_date' title='order by delivery date'>delivery &nbsp&nbsp/\</a>";
					else
						echo "<a href='".$adr."ord=del_date' title='order by delivery date'>delivery</a>";
*/				?>
				</div>

				<div class="id" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="id")
						echo "<a href='".$adr."ord=id DESC' title='".$lang_clients[2][$n_lang]."'>id &nbsp&nbsp\/</a>"; # order by id - last will be first
					elseif($ord=="id DESC")
						echo "<a href='".$adr."ord=id' title='".$lang_clients[3][$n_lang]."'>id &nbsp&nbsp/\</a>"; # order by id
					else
						echo "<a href='".$adr."ord=id' title='".$lang_clients[3][$n_lang]."'>id</a>"; # order by id
				?>

				</div>

				<div class="name" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="name")
						echo "<a href='".$adr."ord=name DESC' title='".$lang_clients[4][$n_lang]."'>".$lang_clients[5][$n_lang]." &nbsp&nbsp\/</a>"; # order by name - last will be first | name
					elseif($ord=="name DESC")
						echo "<a href='".$adr."ord=name' title='".$lang_clients[6][$n_lang]."'>".$lang_clients[5][$n_lang]." &nbsp&nbsp/\</a>"; # order by name | name
					else
						echo "<a href='".$adr."ord=name' title='".$lang_clients[6][$n_lang]."'>".$lang_clients[5][$n_lang]."</a>"; # order by name | name
				?>
				</div>

				<div class="lastlogin" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="last_login")
						echo "<a href='".$adr."ord=last_login DESC' title='".$lang_clients[8][$n_lang]."'>".$lang_clients[7][$n_lang]." &nbsp&nbsp\/</a>"; # order by last login - last will be first | last login
					elseif($ord=="last_login DESC")
						echo "<a href='".$adr."ord=last_login' title='".$lang_clients[9][$n_lang]."'>".$lang_clients[7][$n_lang]." &nbsp&nbsp/\</a>"; # order by last login | last login
					else
						echo "<a href='".$adr."ord=last_login' title='".$lang_clients[9][$n_lang]."'>".$lang_clients[7][$n_lang]."</a>"; # order by last login | last login
				?>
				</div>

				<div class="avail" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="avail28days")
						echo "<a href='".$adr."ord=avail28days DESC' title='".$lang_clients[10][$n_lang]."'>SLA &nbsp&nbsp\/</a>"; # order by SLA - last will be first |
					elseif($ord=="avail28days DESC")
						echo "<a href='".$adr."ord=avail28days' title='".$lang_clients[11][$n_lang]."'>SLA &nbsp&nbsp/\</a>"; # order by SLA
					else
						echo "<a href='".$adr."ord=avail28days' title='".$lang_clients[11][$n_lang]."'>SLA</a>"; # order by SLA
				?>
				</div>



				<div class="vpn" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="vpn_ip DESC")
						echo "<a href='".$adr."ord=vpn_ip' title='".$lang_clients[12][$n_lang]."'>vpn ip &nbsp&nbsp/\</a>"; # order by vpn ip
					elseif($ord=="vpn_ip")
						echo "<a href='".$adr."ord=vpn_ip DESC' title='".$lang_clients[13][$n_lang]."'>vpn ip &nbsp&nbsp\/</a>"; # order by vpn ip - last will be first
					else
						echo "<a href='".$adr."ord=vpn_ip DESC' title='".$lang_clients[12][$n_lang]."'>vpn ip</a>"; # order by vpn ip
				?>
				</div>

				<div class="vpnst" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="vpn_ping")
						echo "<a href='".$adr."ord=vpn_ping DESC' title='".$lang_clients[14][$n_lang]."'>vpn stat &nbsp&nbsp\/</a>"; # order by response time - last will be first
					elseif($ord=="vpn_ping DESC")
						echo "<a href='".$adr."ord=vpn_ping' title='".$lang_clients[15][$n_lang]."'>vpn stat &nbsp&nbsp/\</a>"; # order by response time
					else
						echo "<a href='".$adr."ord=vpn_ping' title='".$lang_clients[15][$n_lang]."'>vpn stat</a>"; # order by response time
				?>
				</div>

				<div class="loc" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="velocity")
						echo "<a href='".$adr."ord=velocity DESC' title='".$lang_clients[16][$n_lang]."'>loc / mov &nbsp&nbsp\/</a>"; # order by velocity - last will be first
					elseif($ord=="velocity DESC")
						echo "<a href='".$adr."ord=velocity' title='".$lang_clients[17][$n_lang]."'>loc / mov &nbsp&nbsp/\</a>"; # order by velocity
					else
						echo "<a href='".$adr."ord=velocity' title='".$lang_clients[17][$n_lang]."'>loc / mov</a>"; # order by velocity
				?>
				</div>


				<div class="note" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">note</div>
				<div class="dsheet" style="background-color:#eeeeee;height:22px;padding-top:6px;text-align:center;">DS</div>
			</p>

		<! ***** list  *****>
		<?php

     $result = mysqli_query($conn,"SELECT * FROM clients6  WHERE $cnd ORDER BY $ord");	//for list
     while($row = mysqli_fetch_array($result)) { $i++; ?>

		<?php
		## collect datas for this client from inventory
			$CPU = "&nbsp;";
			$id = $row['id'];	# id
			#echo "id: ".$id."\n";		# check

			$result_inv = mysqli_query($conn,"SELECT * FROM inv_log WHERE location LIKE '$id' AND last=1");
			while($row_inv = mysqli_fetch_array($result_inv)) {

				$PN = $row_inv['PN'];
				#echo "PN: ".$PN."\n";		# check

				$result_prod = mysqli_query($conn,"SELECT * FROM products WHERE TOP LIKE 'CPU' AND PN LIKE '$PN'");
				while($row_prod = mysqli_fetch_array($result_prod)) {

					$CPU = $PN;
					$CPUdscr = $row_prod['dscr'];
					#echo "CPU: ".$CPU."\n";		# check

				}
			}

		?>




			<! ***** client  *****>
			<div class="client">

				<?php
#					if($row['server']=="s4") 			/* s4 server | vpn - white */
					if( ($row['status']>4) OR ($row['server']=="s4") ) 				/* status from s4 | vpn - white */
						$bgc="background-color:#eeeeee;color:Black;";
					elseif($row['status']==1) 			/* OK */
						$bgc="background-color:#cccccc;";
					elseif($row['status']==2) 			/* 15min - yellow */
						$bgc="background-color:Yellow;";
					elseif($row['status']==0) 			/* 24hour - red */
						$bgc="background-color:Tomato;color:Black;";
					elseif($row['status']==3) 			/* no power - blue */
						$bgc="background-color:DeepSkyBlue;color:Black;";
				?>

				<div class="own" style=<?php echo $bgc;?>;>
				<?php
					if($row['owner']=='usr') 			/* vpn user */
						$png="usr.png";
					elseif($row['owner']=='s') 			/* server */
						$png="server.png";
					elseif($row['owner']=='dev') 		/* devel */
						$png="dev.png";
					elseif($row['owner']=='q') 			/* officelink */
						$png="officelink_logo.png";
					elseif($row['owner']=='MK') 		/* MK road */
						$png="road.png";
					elseif($row['owner']=='MKa') 		/* MK highway */
						$png="highway.png";
					else						 		/* more systems */
						$png="dev.png";
				?>
					<img src="../common_files/img/<?php echo $png; ?>" width="12" height="12">
				</div>
				<div class="deliv" style=<?php echo $bgc;?>;>
					<?php echo "<a href='' title='".$CPUdscr."'>".$CPU."</a>"; ?>
				</div>
				<div class="id" style=<?php echo $bgc;?>;><?php echo "<a href='http://".$row['web']."' target='_blank' title='<?php echo $lang_clients[18][$n_lang]; # jump to client ?>'><b>".$row['id']."</b></a>"; ?></div>
				<div class="name" style=<?php echo $bgc;?>;><?php echo "<a href='http://".$row['web']."' target='_blank' title='<?php echo $lang_clients[18][$n_lang]; # jump to client ?>'><b>".$row['name']."</b></a>"; ?></div>

				<div class="lastlogin" style=<?php echo $bgc;?>;>
				<?php
					$adridl = $adr."log=1&log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&idl=".$row['id'];
					if ($row['last_login']=='1970-01-01 00:00:00') { # no mcsc-login to s0
						echo "no";
					} else {
						echo "<a href='".$adridl."' title='".$lang_clients[19][$n_lang]."'><b>".$row['last_login']."</b></a>"; # log of the client
					}
				?>
				</div>

				<div class="avail" style=<?php echo $bgc;?>;><?php echo $row['avail28days']."%"; ?></div>


				<?php
					if($row['vpn_ping']=="") 				/* same as s0 status color */
						$bgcv=$bgc;
					elseif($row['vpn_ping']=="no") 		/* vpn exist, but connection down */
						$bgcv="background-color:#ff5555;color:Black;";
					elseif($row['vpn_ping']<1000) 			/* fast connection */
						$bgcv="background-color:#b3d9ff;";
#					elseif($row['vpn_ping']<200) 				/* slow connection */
#						$bgcv="background-color:#80bfff;";
#					elseif($row['vpn_ping']<1000) 			/* very slow connection */
#						$bgcv="background-color:#1a8cff;color:Black;";
					else					 				/* same as s0 status color */
						$bgcv=$bgc;
				?>


				<div class="vpn" style=<?php echo $bgcv;?>;>&nbsp;<?php echo "<a href='' title='".$lang_clients[20][$n_lang]."'><b>".$row['vpn_ip']."</b></a>"; # open terminal ?></div>

				<div class="vpnst" style=<?php echo $bgcv;?>;>
				<?php
					if ( ($row['cl_load']!='no') && ($row['cl_load']!='') )		// connection, log is ready
						echo " <a href='".$adridl."' target='_blank' title='response time: ".$row['vpn_ping']."  | load: ".$row['cl_load']."  | disk free: ".$row['cl_disk']." - open clients status log'> ".$row['vpn_ping']." | load: ".$row['cl_load']." | ".$row['cl_disk']."</a>";

					elseif ( ($row['vpn_ping'] != 'no') && ($row['vpn_ping'] != '') ) 	// only connection ready, no log file
						echo "<a href='".$adridl."' target='_blank' title='connection: ".$row['vpn_ping']."  - open clients status log'>".$row['vpn_ping']."</a>";

					elseif ( $row['vpn_last'] != '1970-01-01 00:00:00') 		// no vpn connection now
//						echo "last vpn connection: <b>".$row['vpn_last']."&nbsp;</b>";
						echo "<a href='".$adridl."' target='_blank' title='last vpn connection: ".$row['vpn_last']." - open clients status log'>last vpn connection: ".$row['vpn_last']."</a>";

					else // no vpn
						echo "never&nbsp;";
				?>
				</div>

				<div class="loc" style=<?php echo $bgcv;?>;>
					<a href='' title='open map: <?php echo $row['GPS']; ?>'>

					<?php
					if ( ($row['velocity']!=0) AND ($row['bearing']!=0) ) {

						echo $row['velocity']."km/h - ".$row['bearing']."°";

					} else {

						echo $row['GPS']."&nbsp;";

					}
					?>
					</a>
				</div>

				<div class="note" style=<?php echo $bgc;?>;>
				<?php
					if(strlen($row[note])>12) { $note_S = substr($row[note],0,10); $note_S=$note_S."..."; } else $note_S=$row[note];
					echo "<a href='' title='".$row[note]."'>".$note_S."&nbsp;</a>";
				?>
				</div>

				<div class="dsheet" style=<?php echo $bgc;?>;>
					<a href="ds/ds.php?id=<?php echo $row['id']; ?>" target="_blank" title="<?php echo $row['name']." (".$row['id'].")"; ?>" rev="width:720px; height:500px; scrolling:yes;"><img src="images/dsheet.png" width="8" height="10"></a>
				</div>

			</div>

			<! ***** pix  *****>
			<?php
				if($cam==0)
				{
					if($row['cam1']!="no") {$j++;}
					if($row['cam2']!="no") {$j++;}
					if($row['cam3']!="no") {$j++;}
					if($row['cam4']!="no") {$j++;}
				}
				else
				{
					echo "<div class='pix'>";
						if($row['cam1']!="no")
							{$j++; echo "<a href='".substr($row[web],strpos($row[web],'/')). "/webcam1.jpg' width='400' height='300' rel='lytebox[cam]' title='Kamera'><img class='thumbnail' src='".substr($row[web],strpos($row[web],'/'))."/webcam1.jpg' width='40' height='30'></a>";}
						if($row['cam2']!="no")
							{$j++; echo "<a href='".substr($row[web],strpos($row[web],'/')). "/webcam2.jpg' width='400' height='300' rel='lytebox[cam]' title='Kamera'><img class='thumbnail' src='".substr($row[web],strpos($row[web],'/'))."/webcam2.jpg' width='40' height='30'></a>";}
						if($row['cam3']!="no")
							{$j++; echo "<a href='".substr($row[web],strpos($row[web],'/')). "/webcam3.jpg' width='400' height='300' rel='lytebox[cam]' title='Kamera'><img class='thumbnail' src='".substr($row[web],strpos($row[web],'/'))."/webcam3.jpg' width='40' height='30'></a>";}
						if($row['cam4']!="no")
							{$j++; echo "<a href='".substr($row[web],strpos($row[web],'/')). "/webcam4.jpg' width='400' height='300' rel='lytebox[cam]' title='Kamera'><img class='thumbnail' src='".substr($row[web],strpos($row[web],'/'))."/webcam4.jpg' width='40' height='30'></a>";}
					echo "</div>";
				}
		 } //end of query ?>
	</div>

  <?php } // only when log and inventory and calendar are OFF ?>
	<! ***** list of clients *****>
















































	<! ***** clients status log *****>
	<?php if ($log==1)  { // when log is ON ?>

	<div class="list">

		<! ***** list header  *****>
		<p>
			<div class="own" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">&nbsp;</div>
			<div class="note" style="background-color:#eeeeee;width:110px;height:20px;padding-top:6px;text-align:center;">
			<?php
				if($lp==1) {   // log page counter
					echo "";
				}
				else { if($lp==2) {   // log page counter
            		$lpp=$lp-1;$rcp=1;
					echo "<a href='".$adr."log=1&lp=$lpp&rcn=$rcp' title='previous page'> prev. page /\\ </a>".$rcp;
          				}
          			else { if($lp>2) {   // log page counter
					$lpp=$lp-1;$rcp=$rcn;
					echo "<a href='".$adr."log=1&lp=$lpp&rcn=$rcp' title='previous page'> prev. page /\\ </a>".$rcp;
    				}}}
			?>
			</div>
			<div class="avail" style="background-color:#eeeeee;width:25px;height:20px;margin-right:5px;padding-top:6px;text-align:center;">
    				<?php echo "<b>".$lp."</b>"; ?>
			</div>

			<div class="lastlogin" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
          		status change
			</div>
			<div class="avail" style="background-color:#eeeeee;width:120px;height:20px;padding-top:6px;text-align:center;">
				prev. status
			</div>
			<div class="avail" style="background-color:#eeeeee;width:120px;height:20px;margin-right:5px;padding-top:6px;text-align:center;">
          		new status
			</div>

			<div class="avail" style="background-color:#eeeeee;width:70px;height:20px;padding-top:6px;text-align:center;">
          		vpn ping
			</div>
			<div class="avail" style="background-color:#eeeeee;width:55px;height:20px;padding-top:6px;text-align:center;">
				cl.load
			</div>
			<div class="avail" style="background-color:#eeeeee;width:110px;height:20px;margin-right:5px;padding-top:6px;text-align:center;">
          		cl.disk
			</div>

			<div class="id" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
          			id
			</div>
			<div class="name" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
          			name
			</div>

			<div class="dsheet" style="background-color:#eeeeee;height:22px;padding-top:6px;text-align:center;">DS</div>
		</p>
		<! ***** end of list header  *****>



		<! ***** clients log list  *****>
    		<?php
			## for lines in a page and counter
      		$ord = "date DESC";
      		$logl = 100;				# line in a page
      		$ll = ($lp-1)*$logl;		# log line counter


			#echo "<div>".$idl. "</div>";	# check


			## status selector, new status = 0,1,2,3,  40,41,42,43
			$cndlog = "( 0";

			if($log0==1)  { $cndlog = $cndlog." OR newstatus=0 "; }
			if($log1==1)  { $cndlog = $cndlog." OR newstatus=1 "; }
			if($log2==1)  { $cndlog = $cndlog." OR newstatus=2 "; }
			if($log3==1)  { $cndlog = $cndlog." OR newstatus=3 "; }

			if($log40==1)  { $cndlog = $cndlog." OR newstatus=40 "; }
			if($log41==1)  { $cndlog = $cndlog." OR newstatus=41 "; }
			if($log42==1)  { $cndlog = $cndlog." OR newstatus=42 "; }
			if($log43==1)  { $cndlog = $cndlog." OR newstatus=43 "; }

			$cndlog = $cndlog." OR 0 )";


			$cndlog = $cndlog." AND id LIKE '".$idl."'"; 	#for one client
//			$cndlog = "id LIKE '".$idl."'"; 	#for one client

			#echo $cndlog."\n";	# check


//		   		$result_log = mysqli_query($conn,"SELECT * FROM clients_status_log4 WHERE id LIKE 'mcs069' ORDER BY date DESC LIMIT $rcn,1000000");	// for clients log
	    		$result_log = mysqli_query($conn,"SELECT * FROM clients_status_log4 WHERE $cndlog ORDER BY date DESC LIMIT $rcn,1000000");	// for clients log

				# counting row numbers for this selection
	    		$rowcount=mysqli_num_rows($result_log);
#	      		echo "<p>rowcount: ".$rowcount.".</p>";	# check

				#selecting rows for this pages
	    		while(($row = mysqli_fetch_array($result_log)) && (($logl*$lp) > $ll)) { $rcn++;


        		$id = $row['id'];
#	      		echo "<p>".$id.".</p>";	# check
        		$cnd1 = $cnd." AND id LIKE '$id'";
     	  		$result_log3 = mysqli_query($conn,"SELECT * FROM clients6  WHERE $cnd1"); 	// clients name
  	    		while($row3 = mysqli_fetch_array($result_log3)) { $ll++;

  /*
          		echo $row3['owner'];
          		echo " - ";
          		echo $row['id']." _ ";
          		echo $row3['name'];
          		echo " - ";
          		echo $row['date'];
          		echo " - ";
          		echo $row3['web'];
          		echo "<br>";
  */
    		?>

		<div class="client">

			<! ***** client  *****>
			<?php
				## status
				# select old status color and name

				switch ($row['oldstatus']) {
					case 0:		# 24hour - red
						$bgcp="background-color:Tomato;color:Black;";
						$st_p="lost (24hour)";
						break;
					case 1: 	# OK - gray
						$bgcp="background-color:#cccccc;";
						$st_p="OK";
						break;
					case 2:		# 15min - yellow
						$bgcp="background-color:Yellow;";
						$st_p="lost (15min)";
						break;
					case 3:		# no power - blue
						$bgcp="background-color:DeepSkyBlue;color:Black;";
						$st_p="no power";
						break;
					case 40:	# vpn user, but its down - other red
						$bgcp="background-color:#ff5555;color:Black;";
						$st_p="lost vpn conn.";
						break;
					case 41: 	# vpn_ping<100 - fast connection
						$bgcp="background-color:#b3d9ff;";
						$st_p="fast conn.";
						break;
					case 42: 	# vpn_ping<200 - slow connection
						$bgcp="background-color:#80bfff;";
						$st_p="slow conn.";
						break;
					case 43: 	# vpn_ping<1000 - very slow connection
						$bgcp="background-color:#1a8cff;color:Black;";
						$st_p="vpn status ready";
						break;
					case 49: 	# exist in openvpn-status.log - no ping - android
						$bgcp="background-color:#b3d9ff;";
						$st_p="very slow conn.";
						break;
					default:	# default
     					$bgcp="background-color:#bbbbbb;";
						$st_p="default";
				}

				switch ($row['newstatus']) {
					case 0:		# 24hour - red
						$bgc="background-color:Tomato;color:Black;";
						$st="lost (24hour)";
						break;
					case 1: 	# OK - gray
						$bgc="background-color:#cccccc;";
						$st="OK";
						break;
					case 2:		# 15min - yellow
						$bgc="background-color:Yellow;";
						$st="lost (15min)";
						break;
					case 3:		# no power - blue
						$bgc="background-color:DeepSkyBlue;color:Black;";
						$st="no power";
						break;
					case 40:	# vpn user, but its down - other red
						$bgc="background-color:#ff5555;color:Black;";
						$st="lost vpn conn.";
						break;
					case 41: 	# vpn_ping<100 - fast connection
						$bgc="background-color:#b3d9ff;";
						$st="fast conn.";
						break;
					case 42: 	# vpn_ping<200 - slow connection
						$bgc="background-color:#80bfff;";
						$st="slow conn.";
						break;
					case 43: 	# vpn_ping<1000 - very slow connection
						$bgc="background-color:#1a8cff;color:Black;";
						$st="vpn status ready";
						break;
					case 49: 	# exist in openvpn-status.log - no ping - android
						$bgc="background-color:#b3d9ff;";
						$st="very slow conn.";
						break;
					default:	# default
     					$bgc="background-color:#bbbbbb;";
						$st="default";
				}

				# select client group icon
				switch ($row3['owner']) {
					case 's':		/* server */
						$png="server.png"; break;
					case 'dev': 	/* devel */
						$png="dev.png"; break;
					case 'q':		/* officelink */
						$png="officelink_logo.png"; break;
					case 'MK':		/* MK road */
						$png="road.png"; break;
					case 'MKa':		/* MK highway */
						$png="highway.png"; break;
					default:		/* more systems */
     						$png="dev.png";
				}
			?>

			<div class="own" style=background-color:#cccccc;>
				<img src="../common_files/img/<?php echo $png; ?>" width="12" height="12">
			</div>
			<div class="note" style="background-color:#cccccc;width:110px;">&nbsp;<?php echo $rcn; ?></div>
			<div class="avail" style="background-color:#cccccc;width:25px;margin-right:5px;text-align:right;padding-right:5px;">&nbsp;<?php echo $ll; ?></div>

			<div class="lastlogin" style=<?php echo $bgc;?>;><?php echo $row['date']; ?></div>
			<div class="avail" style="width:120px;<?php echo $bgcp;?>;"><?php echo $st_p;?></div>
			<div class="avail" style="width:120px;margin-right:5px;<?php echo $bgc;?>;"><?php echo $st;?></div>

			<div class="avail" style=width:70px;<?php echo $bgc;?>;><?php echo $row['vpn_ping']; ?>&nbsp;</div>
			<div class="avail" style="width:55px;<?php echo $bgc;?>;"><?php echo $row['cl_load'];?>&nbsp;</div>
			<div class="avail" style="width:110px;margin-right:5px;<?php echo $bgc;?>;"><?php echo $row['cl_disk'];?>&nbsp;</div>

			<div class="id" style="text-align:center;<?php echo $bgc;?>";>
			<?php

				if ($idl=="%") { # if there is no selected clients, list this client's log
					$adridl = $adr."log0=$log0&log1=$log1&log2=$log2&log3=$log3&log40=$log40&log41=$log41&log42=1&log43=$log43&idl=$row[id]&lp=1";
				} else {	# if there is one selected client, list all client's log
					$adridl = $adr."log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&idl=%&lp=1";
				}

				echo "<a href='".$adridl."' title='List only this client'><b>".$row['id']."</b></a>";

			?>
			</div>
			<div class="name" style=<?php echo $bgc;?>;>
			<?php
				echo "<a href='http://".$row3['web']."' target='_blank' title='Weblap'>&nbsp;<b>".$row3['name']."</b></a>";
			?>
			</div>
			<div class="dsheet" style="background-color:#cccccc;">
				<?php if(($user!=="MK")||($user=="admin"))  { ?>
					<a href="ds/ds.php?id=<?php echo $row['id']; ?>" target="_blank" title="Adatlap" rev="width: 720px; height: 500px; scrolling: no;">
						<img src="../common_files/img/dsheet.png" width="10" height="12">
					</a>
				<?php } ?>
			</div>

		</div>
		<?php		}
	   		}        //end of query ?>
		<! ***** end of clients log list  *****>



		<! ***** list footer  *****>
		<div class="client">
 			<div class="own" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">&nbsp;</div>
			<div class="note" style="background-color:#eeeeee;width:110px;height:20px;padding-top:6px;text-align:center;">
			<?php
          			$lpn=$lp+1;   // log page counter plus 1 is the next page
				echo "<a href='".$adr."log=1&lp=$lpn&rcn=$rcn&rcp=$rcp' title='next page'>next page \\/ </a>".$rcn;
			?>
			</div>
			<div class="avail" style="width:25px;height:20px;margin-right:5px;padding-top:6px;text-align:center;">
          			<?php echo "<b>".$lp."</b>"; ?>
			</div>

			<div class="lastlogin" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
          		status change
			</div>
			<div class="avail" style="background-color:#eeeeee;width:120px;height:20px;padding-top:6px;text-align:center;">
				prev. status
			</div>
			<div class="avail" style="background-color:#eeeeee;width:120px;height:20px;margin-right:5px;padding-top:6px;text-align:center;">
          		new status
			</div>

			<div class="avail" style="background-color:#eeeeee;width:70px;height:20px;padding-top:6px;text-align:center;">
          		vpn ping
			</div>
			<div class="avail" style="background-color:#eeeeee;width:55px;height:20px;padding-top:6px;text-align:center;">
				cl.load
			</div>
			<div class="avail" style="background-color:#eeeeee;width:110px;height:20px;margin-right:5px;padding-top:6px;text-align:center;">
          		cl.disk
			</div>

			<div class="id" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
          			id
			</div>
			<div class="name" style="background-color:#eeeeee;height:20px;padding-top:6px;text-align:center;">
          			name
			</div>

			<div class="dsheet" style="background-color:#eeeeee;height:22px;padding-top:6px;text-align:center;">DS</div>
		</div>
		<! ***** end of list footer  *****>

	</div>
  	<?php } // when log is ON ?>
	<! ***** clients status log *****>
































	<! ***** calendar  *****>
	<?php if($cal==1) { // only when calendar is ON ?>
    <div class="calendar">


    </div>
  <?php } // only when calendar is ON ?>
	<! ***** calendar  *****>












	<! ***** footer  *****>
	<div style="margin-top:10px;text-align:center;font-weight:bold;clear:both;">
	<br>
	<?php if( ($cal==0) AND ($log==0) ) { // only when calendar and log are OFF
		      echo "Rendszerek száma: ".$i." | Kamerák száma: ".$j.""; }
       else { echo "&nbsp;...thanks for waiting"; }
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


</body>
</html>

<?php
	// closing connection
	mysqli_close($conn);

	ob_end_flush();
?>
