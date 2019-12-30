<?php
	session_start();
#	if( $_SESSION['MKuser'] == "" ) { header('Location: ../index.php'); exit; }
#	if( $_SESSION['MKuser'] == "" ) { $user ="guest"; } else { $user = $_SESSION['MKuser']; }
	ob_start();

	date_default_timezone_set('Europe/Budapest');
	include "../common_files/connect.php";
	include "../common_files/index.php";


	$ptitle = "s4| MK (s2) flow control list";

	# echo "def status's names | colors<br>";
		$st2n[0] = "OFF"; 		$st2c[0] = "#fff";
		$st2n[1] = "ok"; 		$st2c[1] = "#def";
		$st2n[2] = "warm"; 		$st2c[2] = "#cfc";
		$st2n[3] = "warning"; 	$st2c[3] = "#ffb";
		$st2n[4] = "hot"; 		$st2c[4] = "#fc9";
		$st2n[5] = "no"; 		$st2c[5] = "#f66";
		$st2n[6] = "scrap"; 	$st2c[6] = "#666";
?>

<!DOCTYPE HTML>

<html>
<head>
	<title><?=$ptitle?></title>
	<meta http-equiv="refresh" content="57;url=<?=$adr?>">

	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="hu-hu" />

	<script type="text/javascript" language="javascript" src="../common_files/lytebox.js"></script>

	<link rel="stylesheet" href="../common_files/lytebox.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="flow_list.css" type="text/css">
	<link rel="icon" href="../common_files/img/officelink_logo.png" type="image/png" sizes="20x16">






<?php
	// ******  select clients ******
		#echo $user."<br><br>";



  // ***   select for user and by switches - condition for selection   ***

    // paraméter fogadás url-ben: változó neve, értékadás

	    $cam=$_GET["cam"];          // 1 -> kamera képek látszanak
	    $br=$_GET["br"];            // 1 -> csak a hibás rendszerek látszanak
	    $off=$_GET["off"];          // offline -> OFFline rendszerek

		//owner
	    $usr=$_GET["usr"];       	// vpn users
	    $s=$_GET["s"];              // servers -> InVR servers
	    $dev=$_GET["dev"];          // development -> InVR devel systems
	    $q=$_GET["q"];              // officelink -> officelik systems
		$rd=1;
	    $rd=$_GET["rd"];            // road type -> offficelink kamerák
		$hw=1;
		$hw=$_GET["hw"];            // road type -> officelink kamerák
	    $MKn=$_GET["MKn"];    		// new MK systems

	    $cty=$_GET["cty"];          // megyék: 1,2,3,4,5,6,7,8,9,1,10,11,12,13,14,15,16,17,18,19 - 0 = összes

		//for log status select switches - see statuses up *** only for admin users
		if ( ($_SESSION['MKuser']=="kamera") OR ($_SESSION['MKuser']=="szerver") OR ($_SESSION['MKuser']=="admin") ) {
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
		} else {
			$log = 0;
			$iny = 0;
			$cal = 0;

		}


	$cnd = $cnd." ( owner!='usr' AND owner!='s' AND owner!='dev' AND owner!='q' AND owner!='MK' AND owner!='MKa' AND owner!='MKn'";

	if($usr==1)  { $cnd = $cnd." OR owner='usr' "; }		// vpn users
	if($s==1)  { $cnd = $cnd." OR owner='s' "; }		    // servers
	if($dev==1)  { $cnd = $cnd." OR owner='dev' "; }	    // development systems
	if($q==1)  { $cnd = $cnd." OR owner='q'"; }		        // officelink
	if($rd>=1)  { $cnd = $cnd." OR owner='MK' "; }		    // MK road systems
	if($hw==1)  { $cnd = $cnd." OR owner='MKa' "; }		    // MK highway systems
	if($MKn==1)  { $cnd = $cnd." OR owner='MKn' "; }	  	// new MK systems
	$cnd = $cnd." ) AND ";
	if($rd==2)  { $cnd = $cnd." server LIKE 's0' AND "; }		    // MK road systems - Telekom APN
	if($rd==3)  { $cnd = $cnd." server LIKE 's2' AND "; }		    // MK road systems - MK APN

	if($off==1)  { $cnd = $cnd."OFF LIKE 'OFF'"; } else { $cnd = $cnd."OFF NOT LIKE 'OFF'"; }	// OFFline systems
	if($br==1)  { $cnd = $cnd." AND (status=0 OR status=3)"; }									// broken systems

	// MK users
	if( ($_SESSION['MKuser']=="kamera") OR ($_SESSION['MKuser']=="szerver") OR ($_SESSION['MKuser']=="MK") OR ($_SESSION['MKuser']=="admin") ) { # admin users
		$cnd = $cnd." AND (owner='MK' OR owner='MKa' OR owner='MKn') ";
	} else { # guests
		$cnd = $cnd." AND (owner='MK' OR owner='MKa') AND (status=1 OR status=2) ";
	}

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

	#echo $cnd."<br>".$rd."<br>"; //check


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
	$j=0;	//sum of runtime

  // save the switch's state
	$adr = "index.php?usr=$usr&s=$s&q=$q&rd=$rd&hw=$hw&dev=$dev&MKn=$MKn&cam=$cam&off=$off&br=$br&ord=$ord&cty=$cty&lp=$lp&rcn=$rcn&rcp=$rcp&idl=$idl&log=$log&log=$log&log0=$log0&log1=$log1&log2=$log2&log3=$log3&log40=$log40&log41=$log41&log42=$log42&log43=$log43&";


  // result of system available data
  $result2 = mysqli_query($conn,"SELECT avg(avail28days) FROM clients2 WHERE $cnd"); 	//for available data
  $row2 = mysqli_fetch_array($result2);	//result of "system available" data

?>
</head>

















<body>

<! ***** head  *****>
<div class="header">
	<div style="float:left"><a href="" target="_blank"><img src="../common_files/img/officelink_logo.png" height="21" border="0" title="OfficeLink Kft."></a></div>
	<div style="float:left;margin-left:20px;margin-right:50px"><a href="http://kamera.officelink.hu/flow/flow_list.php" target="_blank"><img src="../common_files/img/officelink_logo.png" height="21" border="0" title="Officelink Kft."></a></div>
	<div class="hname"><?=$ptitle?></div>
	<! ***** message box  *****>
<!--
	<div class="msg">
		<?php echo "availability: <b>".(floor($row2[0]*100)/100)."% </b>"; ?>
	</div>
-->
	<! ***** message box  *****>
	<div class="hdate"><?php echo date('Y.m.d. H:i'); ?> </div>
</div>
<! ***** head  *****>




<! ***** box  *****>
<div class="box">

	<! ***** switches *****>

		<! ***** clients list  *****>
		<div class="sw">
			<?php $address="../clients/index.php?rd=1&hw=1&cam=1&cty=".$cty ?>
			<a href=<?php echo $address; ?> target="_blank" title="Helyszín lista"><img src="../common_files/img/clients_tmb.png" width="27" height="18"></a>
		</div>

		<! ***** map  *****>
		<div class="sw">
			<?php $address="../clients/map.php?cty=0" ?>
			<a href=<?php echo $address; ?> target="_blank" title="Térkép"><img src="../common_files/img/map_tmb.png" width="27" height="18"></a>
		</div>

		<! ***** dispatch  *****>
		<div class="sw">
			<?php $address="../clients/dispatch.php?cty=".$cty ?>
			<a href=<?php echo $address; ?> target="_blank" title="Diszpécser képernyő"><img src="../common_files/img/dispatch.png" height="18"></a>
		</div>

		<div class="swsp"></div>

		<div class="swsp"></div>
		<div class="swsp"></div>

		<! ***** flow - system map  *****>
		<div class="sw0" style="background-color:#b3d9ff;">
			<a href="flow2.php" target="_blank" title="s2 sysytem map">s2 sysmap</a>
		</div>

		<div class="swsp"></div>

		<! ***** flow s4  *****>
		<div class="sw0" style="background-color:#b3d9ff;">
			<a href="index.php" target="_blank" title="s4 system map">s4 sysmap</a>
		</div>

		<div class="swsp"></div>

		<! ***** flow_list s4  *****>
		<div class="sw0" style="background-color:#b3d9ff;">
			<a href="flow_list.php" target="_blank" title="s4 flow list">s4 flow list</a>
		</div>



		<! ***** manual box  *****>
			<div class="swm">
				<a href="../manual/index.php" target="_blank" title="Kézikönyv"><img src="../common_files/img/manual.png" height="18"></a>
			</div>
		<! *****  manual box  *****>

		<! ***** login box  *****>
			<div class="login">
				<?php
					if ( $_SESSION['MKuser'] == "" ) { #echo "login";
						echo '<a href="../login/index.php" title="Kezelői bejelentkezés">login&nbsp; >>></a>';
					} else { #echo "logout";
						echo '<a href="../login/logout.php" title="Kilépés">'.$_SESSION['MKuser'].' >></a>';
					}
				?>
			</div>
		<! ***** login box  *****>

	<! ***** switches  *****>
































	<! ***** list of clients *****>
	<?php if( ($log==0) AND ($iny==0) AND ($cal==0) ) { // only when log and inventory and calendar are OFF ?>

	<div class="list">

		<! ***** list header  *****>
			<p>
				<div class="type" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">tip</div>
				<div class="id" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">id</div>
				<div class="name" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">Név</div>

				<div class="pathsource" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">forrás</div>
				<div class="pathdest" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">cél</div>

				<div class="lastrun" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">Utolsó adat</div>

				<div class="result" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">value</div>
				<div class="result" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">value h</div>
				<div class="result" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">value d</div>
				<div class="result" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">value w</div>

				<div class="runtime" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">runtime</div>
				<div class="dsheet" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">DS</div>
			</p>
		<! ***** end of list header  *****>



		<! ***** list  *****>
		<?php

     $result = mysqli_query($conn,"SELECT * FROM flow2 ORDER BY n");	# for list
     while($row = mysqli_fetch_array($result)) { $i++; $j=$j+$row['run_time']; ?>

			<! ***** client  *****>
			<div class="client">

				<?php

					$flow_status = $row['flow_status'];
					$status_color = $st2c[$flow_status]; #echo $status_color;
					$status_name = $st2n[$flow_status]; #echo $status_name;

					$bgc = "background-color:".$st2c[0].";";
					$bgc_value = "background-color:".$status_color.";";

					if( ($row['value']!=='') AND ($row['value']!=='no') ) { #
						$bgc_value="background-color:$status_color";
					} elseif( ($row['value_h']!=='') AND ($row['value_h']!=='no') ) { #
						$bgc_value_h="background-color:$st2c[1]";
					} elseif( ($row['value_d']!=='') AND ($row['value_d']!=='no') ) { #
						$bgc_value_d="background-color:$st2c[1]";
					} elseif( ($row['value_w']!=='') AND ($row['value_w']!=='no') ) { #
						$bgc_value_w="background-color:$st2c[1]";
					}
				?>

				<div class="type" style=<?php echo $bgc;?>;>
					<?php echo $row['type'] ?>
				</div>

				<div class="id" style=<?php echo $bgc_value;?>;>
					<?php echo "<a href='' target='_blank' title='".$row['id']." | status: ".$flow_status." (".$status_name.")'>".$row['id']."</a>"; ?>
				</div>

				<div class="name" style=<?php echo $bgc_value;?>;>
				<?php
					if (strlen($row['name_hu'])>55) {
						echo "<a href='' target='_blank' title='Host/script:".$row['host']."/".$row['script']." - ".$row['name_hu']."'>".substr($row['name_hu'],0,55)."...</a>";
					} else {
						echo "<a href='' target='_blank' title='Host/script:".$row['host']."/".$row['script']." - ".$row['name_hu']."'>".$row['name_hu']."</a>";
					}
				?>
				</div>

				<div class="pathsource" style=<?php echo $bgc;?>;>
				<?php
					if (strlen($row['path_source'])>7) {
						echo "<a href='' target='_blank' title='".$row['path_source']."'>".substr($row['path_source'],0,7)."...</a>";
					} else {
						echo "<a href='' target='_blank' title='".$row['path_source']."'>".$row['path_source']."</a>";
					}
				?>
				</div>

				<div class="pathdest" style=<?php echo $bgc;?>;>
				<?php
					if (strlen($row['path_dest'])>7) {
						echo "<a href='' target='_blank' title='".$row['path_dest']."'>".substr($row['path_dest'],0,7)."...</a>";
					} else {
						echo "<a href='' target='_blank' title='".$row['path_dest']."'>".$row['path_dest']."</a>";
					}
				?>
				</div>

				<?php # if last_run if older, than 120sec -> background is red!

					# count age of the last_run
					$last_run_age = strtotime(date('Y-m-d H:i:s')) - strtotime($row['last_run']);
					#echo strtotime($row['last_run'])." | ".strtotime(date('Y-m-d H:i:s'))." = ".$last_run_age; # check
					if ($last_run_age>120) {
						$bgc_lastrun = "background-color:red;";
					} else {
						$bgc_lastrun = $bgc;
					}
				?>
				<div class="lastrun" style=<?php echo $bgc_lastrun;?>;>
				<?php
					$adridl = $adr."log=1&log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&idl=".$row['id'];
					if ( ($row['last_run']=='') OR ($row['last_run']=='0000-00-00 00:00:00') ) { # no InVRc-login to s0
						echo "not yet";
					} else {
						echo "<a href='".$adridl."' title='log of this flow'>".$row['last_run']."</a>";
					}
				?>
				</div>

				<div class="result" style=<?php echo $bgc_value;?>;>&nbsp;
					<?php echo "<a href='' title='".$row['value_description_hu'].": ".$row['value'].$row['value_unit']."'>".$row['value'].$row['value_unit']."</a>"; ?>
				</div>
				<div class="result" style=<?php echo $bgc;?>;>&nbsp;
					<?php echo "<a href='' title='".$row['value_description_hu'].": ".$row['value'].$row['value_unit']."'>".$row['value_h']."</a>"; ?>
				</div>
				<div class="result" style=<?php echo $bgc;?>;>&nbsp;
					<?php echo "<a href='' title='".$row['value_description_hu'].": ".$row['value'].$row['value_unit']."'>".$row['value_d']."</a>"; ?>
				</div>
				<div class="result" style=<?php echo $bgc;?>;>&nbsp;
					<?php echo "<a href='' title='".$row['value_description_hu'].": ".$row['value'].$row['value_unit']."'>".$row['value_w']."</a>"; ?>
				</div>


				<div class="runtime" style=<?php echo $bgc;?>;>
				<?php
					if ($row['run_time']!='') {
						echo "<a href='' target='_blank' title='runtime=".$row['run_time']."'>".$row['run_time']."s</a>";
					} else {
						echo "&nbsp;";
					}
				?>
				</div>

				<div class="dsheet" style=<?php echo $bgc;?>;>
					<a href="ds.php?id=<?php echo $row['id']; ?>" target="_blank" title="<?php echo $row['name']." (".$row['id'].")"; ?>" rev="width:720px; height:500px; scrolling:yes;"><img src="../common_files/img/dsheet.png" width="8" height="10"></a>
				</div>

			</div>

		<?php
		 } //end of query ?>
	</div>

  <?php } // only when log and inventory and calendar are OFF ?>
	<! ***** list of clients *****>
















































	<! ***** clients status log *****>
	<?php if ($log==1)  { // when log is ON ?>

	<div class="list">

		<! ***** list header  *****>
		<p>
			<div class="own" style="background-color:#eee;height:20px;padding-top:6px;text-align:center;">&nbsp;</div>
			<div class="name" style="background-color:#eee;width:110px;height:20px;padding-top:6px;text-align:center;">
			<?php
				if($lp==1) {   // log page counter
					echo "";
				}
				else { if($lp==2) {   // log page counter
            		$lpp=$lp-1;$rcp=1;
					echo "<a href='".$adr."log=1&lp=$lpp&rcn=$rcp' title='előző lap'> előző /\\ </a>".$rcp;
          				}
          			else { if($lp>2) {   // log page counter
					$lpp=$lp-1;$rcp=$rcn;
					echo "<a href='".$adr."log=1&lp=$lpp&rcn=$rcp' title='előző lap'> előző /\\ </a>".$rcp;
    				}}}
			?>
			</div>
			<div class="name" style="background-color:#eee;width:30px;height:20px;margin-right:5px;padding-top:6px;padding-right:5px;text-align:center;">
    				<?php echo "<b>".$lp."</b>"; ?>
			</div>

			<div class="name" style="background-color:#eee;width:120px;height:20px;padding-top:6px;text-align:center;">
          		időpont
			</div>
			<div class="name" style="background-color:#eee;width:100px;height:20px;padding-top:6px;text-align:center;">
				előző státusz
			</div>
			<div class="name" style="background-color:#eee;width:100px;height:20px;margin-right:5px;padding-top:6px;text-align:center;">
          		új státusz
			</div>

			<div class="name" style="background-color:#eee;width:70px;height:20px;padding-top:6px;text-align:center;">
          			id
			</div>
			<div class="name" style="background-color:#eee;width:205px;height:20px;padding-top:6px;text-align:center;">
          			név
			</div>

			<div class="dsheet" style="background-color:#eee;height:22px;padding-top:6px;text-align:center;">&nbsp;</div>
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


//		   		$result_log = mysqli_query($conn,"SELECT * FROM clients_status_log4 WHERE id LIKE 'InVR069' ORDER BY date DESC LIMIT $rcn,1000000");	// for clients log
	    		$result_log = mysqli_query($conn,"SELECT * FROM clients_status_log4 WHERE $cndlog ORDER BY date DESC LIMIT $rcn,1000000");	// for clients log

				# counting row numbers for this selection
	    		$rowcount=mysqli_num_rows($result_log);
#	      		echo "<p>rowcount: ".$rowcount.".</p>";	# check

				#selecting rows for this pages
	    		while(($row = mysqli_fetch_array($result_log)) && (($logl*$lp) > $ll)) { $rcn++;


        		$id = $row['id'];
#	      		echo "<p>".$id.".</p>";	# check
        		$cnd1 = $cnd." AND id LIKE \"$id\"";
     	  		$result_log3 = mysqli_query($conn,"SELECT * FROM clients2 WHERE $cnd1"); 	// clients name
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
			<div class="name" style="background-color:#cccccc;width:110px;">&nbsp;<?php echo $rcn; ?></div>
			<div class="name" style="background-color:#cccccc;width:30px;margin-right:5px;text-align:right;padding-right:5px;">&nbsp;<?php echo $ll; ?></div>

			<div class="name" style="width:120px;<?php echo $bgc;?>";><?php echo $row['date']; ?></div>
			<div class="name" style="width:100px;<?php echo $bgcp;?>;"><?php echo $st_p;?></div>
			<div class="name" style="width:100px;margin-right:5px;<?php echo $bgc;?>;"><?php echo $st;?></div>

			<div class="name" style="width:70px;text-align:center;<?php echo $bgc;?>";>
			<?php

				if ($idl=="%") { # if there is no selected clients, list this client's log
					$adridl = $adr."log0=$log0&log1=$log1&log2=$log2&log3=$log3&log40=$log40&log41=$log41&log42=1&log43=$log43&idl=$row[id]&lp=1";
				} else {	# if there is one selected client, list all client's log
					$adridl = $adr."log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&idl=%&lp=1";
				}

				echo "<a href='".$adridl."' title='Csak ennek a helyszínnek a bejegyzései'><b>".$row['id']."</b></a>";

			?>
			</div>
			<div class="name" style="width:205px;<?php echo $bgc;?>;">
			<?php
#				echo "<a href='http://".$row3['web']."' target='_blank' title='Weblap'>&nbsp;<b>".$row3['name']."</b></a>";

				if ($idl=="%") { # if there is no selected clients, list this client's log
					$adridl = $adr."log0=$log0&log1=$log1&log2=$log2&log3=$log3&log40=$log40&log41=$log41&log42=1&log43=$log43&idl=$row[id]&lp=1";
				} else {	# if there is one selected client, list all client's log
					$adridl = $adr."log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&idl=%&lp=1";
				}

				echo "<a href='".$adridl."' title='Csak ennek a helyszínnek a bejegyzései'><b>".$row3['name']."</b></a>";

			?>
			</div>
			<div class="dsheet" style="background-color:#cccccc;">
				<?php if(($user!=="MK")||($user=="MKadmin")||($user=="admin"))  { ?>
					<a href="ds.php?id=<?php echo $row['id']; ?>" target="_blank" title="Adatlap" rev="width: 720px; height: 500px; scrolling: no;">
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
			<div class="own" style="background-color:#eee;height:20px;padding-top:6px;text-align:center;">&nbsp;</div>
			<div class="name" style="background-color:#eee;width:110px;height:20px;padding-top:6px;text-align:center;">
			<?php
          		$lpn=$lp+1;   // log page counter plus 1 is the next page
				echo "<a href='".$adr."log=1&lp=$lpn&rcn=$rcn&rcp=$rcp' title='következő lap'>következő \\/ </a>".$rcn;
			?>
			</div>
			<div class="name" style="background-color:#eee;width:30px;height:20px;margin-right:5px;padding-top:6px;padding-right:5px;text-align:center;">
    				<?php echo "<b>".$lp."</b>"; ?>
			</div>

			<div class="name" style="background-color:#eee;width:120px;height:20px;padding-top:6px;text-align:center;">
          		status change
			</div>
			<div class="name" style="background-color:#eee;width:100px;height:20px;padding-top:6px;text-align:center;">
				prev. status
			</div>
			<div class="name" style="background-color:#eee;width:100px;height:20px;margin-right:5px;padding-top:6px;text-align:center;">
          		new status
			</div>

			<div class="name" style="background-color:#eee;width:70px;height:20px;padding-top:6px;text-align:center;">
          			id
			</div>
			<div class="name" style="background-color:#eee;width:205px;height:20px;padding-top:6px;text-align:center;">
          			name
			</div>

			<div class="dsheet" style="background-color:#eee;height:22px;padding-top:6px;text-align:center;">&nbsp;</div>
		</div>
		<! ***** end of list footer  *****>

	</div>
  	<?php } // when log is ON ?>
	<! ***** clients status log *****>






























	<! ***** footer  *****>
	<div style="margin-top:10px;text-align:center;font-weight:bold;clear:both;">
	<br>
	<?php if( ($cal==0) AND ($log==0) ) { // only when calendar and log are OFF
		      echo "Ellenőrző rutinok száma: ".$i." | Teljes futásidő: ".$j."s (percenként)<br><br>...thanks for waiting"; }
       else { echo "&nbsp;...thanks for waiting"; }
    ?>
	</div>
  <! ***** footer  *****>

</div>
<! ***** box  *****>


<! ***** impressum  *****>
<div class="impr">
	<div class="imp1">Minden információ a <a href="http://officelink.hu/" target="_blank"> OfficeLink Kft. </a> tulajdona.</div>
	<div class="imp2">2019 @ OfficeLink Kft.</div>
</div>
<! ***** impressum  *****>



<br><br>
<div class="impr">
<?php

	echo "Status and colours:<br>";
	for ($st2 = 0; $st2 <= 6; $st2++) {
		echo '<div style="width:150px;height:15px;float:left;margin:2px;margin-top:8px;padding:8px;background-color:'.$st2c[$st2].'">Status (st2): ('.$st2.') '.$st2n[$st2].' </div>';
	}

?>
</div>












<?php
	// closing connection
	mysqli_close($conn);
?>
</body>
</html>

<?php
	ob_end_flush();
?>
