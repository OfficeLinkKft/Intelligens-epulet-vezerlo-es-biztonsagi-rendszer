<?php
	session_start();
	ob_start();
#	if( $_SESSION['MKuser'] == "" ) { header('Location: ../index.php'); exit; }
#	if( $_SESSION['MKuser'] == "" ) { $user ="guest"; } else { $user = $_SESSION['MKuser']; }

	# ***  setup language  ***
	if (isset($_GET['lang'])) { $_SESSION['lang']=$_GET['lang'];; } # get language data
	elseif (!isset($_SESSION['lang'])) { $lang="HU"; } # default language
	include "../common_files/language/lang.php";		# dictionary
	echo "lang: ".$_SESSION['lang']." | ".$n_lang; # check

	$ptitle = $lang_flow_list[0][$n_lang]; # "InVR &#9832; Rendszerfolyamatok";

	date_default_timezone_set('Europe/Budapest');
	include "../common_files/connect.php";
	include "../common_files/index.php";

	# echo "def status's names | colors<br>";
		$st2n[0] = $lang_flow_list[1][$n_lang];   	$st2c[0] = "#eee";	# "OFF"
		$st2n[1] = $lang_flow_list[2][$n_lang]; 	$st2c[1] = "#def";	# "ok"
		$st2n[2] = $lang_flow_list[3][$n_lang]; 	$st2c[2] = "#cfc";	# "warm"
		$st2n[3] = $lang_flow_list[4][$n_lang]; 	$st2c[3] = "#ffb";	# "warning"
		$st2n[4] = $lang_flow_list[5][$n_lang]; 	$st2c[4] = "#fc9";	# "hot"
		$st2n[5] = $lang_flow_list[6][$n_lang]; 	$st2c[5] = "#f66";	# "no"
		$st2n[6] = $lang_flow_list[7][$n_lang]; 	$st2c[6] = "#666";	# "scrap"
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
	<link rel="stylesheet" href="../common_files/common.css" type="text/css">
	<link rel="stylesheet" href="flow_list.css" type="text/css">
	<link rel="icon" href="../common_files/img/officelink_favicon.png" type="image/png" sizes="16x16">






<?php
	// ******  select clients ******
		#echo $user."<br><br>";



  // ***   select for user and by switches - condition for selection   ***

    // paraméter fogadás url-ben: változó neve, értékadás

		//owner
	    $usr=$_GET["usr"];       	// vpn users

		# flow id type for switch - ctr<>
		$idt_ctr=1;
	    $idt_ctr=$_GET["idt_ctr"];
		# flow id type for switch - dbs<>
		$idt_dbs=1;
	    $idt_dbs=$_GET["idt_dbs"];
		# flow id type for switch - trf<>
		$idt_trf=1;
	    $idt_trf=$_GET["idt_trf"];



		//for log status select switches - see statuses up *** only for admin users
		if ( ($_SESSION['user']=="admin") ) {
			$log=$_GET["log"];          // 1 -> switch to clients log
		    $cal=$_GET["cal"];          // 1 -> switch to calendar
		} else {
			$log = 0;
			$cal = 0;
		}


#	$cnd = $cnd." (( n < 100 ) AND ";
	$cnd = $cnd." ( id NOT LIKE 'ctr%' AND id NOT LIKE 'dbs%' AND id NOT LIKE 'trf%' ";

	# id like ctr<>
#	if($idt_ctr==1)  { $cnd = $cnd." OR id LIKE 'ctr%'"; } else { $cnd = $cnd." id NOT LIKE 'ctr%'"; }
	if($idt_ctr==1)  { $cnd = $cnd." OR id LIKE 'ctr%'"; }
	# id like dbs<>
	if($idt_dbs==1)  { $cnd = $cnd." OR id LIKE 'dbs%'"; }
	# id like trf<>
	if($idt_trf==1)  { $cnd = $cnd." OR id LIKE 'trf%'"; }

	$cnd = $cnd." ) ";

	#echo $cnd."<br>"; //check


	# log




  // ***   selection ordering - condition for ordering   ***
    // paraméter fogadás url-ben: változó neve, értékadás
	     $ord=$_GET["ord"];               // ordering
       if ($ord=="") { $ord = 'n'; }     // default is ordering by id




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
	$adr = "flow_list.php?idt_ctr=$idt_ctr&idt_dbs=$idt_dbs&idt_trf=$idt_trf&log=$log&ord=$ord&";
	# echo $adr;


?>
</head>

















<body>

<! ***** head  *****>
<div class="header">
	<div class="hlogo"><a href="" target="_blank"><img src="../common_files/img/officelink_logo.png" height="21" border="0" title="OfficeLink Logo"></a></div>
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
			<a href="../clients/index.php" target="_blank" title="<?php echo $lang_sw[1][$n_lang]; # Helyszín lista ?>"><img src="../common_files/img/clients_tmb.png" width="27" height="18"></a>
		</div>

		<! ***** flow  *****>
		<div class="sw">
			<a href="../flow/index.php" target="_blank" title="<?php echo $lang_sw[3][$n_lang]; # flow_list ?>"><img src="../common_files/img/flow_tmb.png" height="18"></a>
		</div>

<!--
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

		<! ***** MRTG  *****>
		<div class="sw0" style="background-color:#b3d9ff;">
			<a href="../../mrtg/index.html" target="_blank" title="MRTG">MRTG</a>
		</div>
-->

		<div class="swsp"></div>

		<! ***** id_type  *****>
		<?php
		#if($_SESSION['owner']=="admin")  { # idt
			if($idt_ctr==0) # id = ctr<>
				echo "<div class='sw0'><a href='".$adr."idt_ctr=1' title='control flows'>ctr</a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."idt_ctr=0' title='control flows'>ctr</a></div>";

			if($idt_dbs==0) # id = dbs<>
				echo "<div class='sw0'><a href='".$adr."idt_dbs=1' title='database flows'>db</a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."idt_dbs=0' title='database flows'>db</a></div>";

			if($idt_trf==0) # id = trf<>
				echo "<div class='sw0'><a href='".$adr."idt_trf=1' title='file transfer flows'>trf</a></div>";
			else
				echo "<div class='sw1'><a href='".$adr."idt_trf=0' title='file transfer flows'>trf</a></div>";
		#}
		?>













		<! ***** manual box  *****>
			<div class="swm">
				<a href="../manual/index.php" target="_blank" title="Manual"><img src="../common_files/img/manual.png" height="18"></a>
			</div>
		<! *****  manual box  *****>

		<! ***** login box  *****>
			<div class="login">
				<a href="../common_files/logout.php" title="logout"><?php echo $_SESSION['nick']; ?> >></a>
			</div>
		<! ***** login box  *****>

	<! ***** switches  *****>
































	<! ***** list of clients *****>
	<?php if( ($log==0) AND ($cal==0) ) { // only when log and calendar are OFF ?>

	<div class="list">

		<! ***** list header  *****>
			<p>
				<div class="host" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">host</div>

				<div class="type" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="type")
						echo "<a href='".$adr."ord=type DESC' title='order by type - last will be first'>tp \/</a>";
					elseif($ord=="type DESC")
						echo "<a href='".$adr."ord=type' title='order by type'>tp /\</a>";
					else
						echo "<a href='".$adr."ord=type' title='order by type'>tp</a>";
				?>
				</div>

				<div class="id" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="id")
						echo "<a href='".$adr."ord=id DESC' title='order by id - last will be first'>id \/</a>";
					elseif($ord=="id DESC")
						echo "<a href='".$adr."ord=id' title='order by id'>id /\</a>";
					else
						echo "<a href='".$adr."ord=id' title='order by id'>id</a>";
				?>
				</div>

				<div class="name" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">name</div>

				<div class="pathsource" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">source</div>
				<div class="pathdest" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">target</div>

				<div class="lastrun" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="last_run")
						echo "<a href='".$adr."ord=last_run DESC' title='order by lastrun - last will be first'>lastrun \/</a>";
					elseif($ord=="last_run DESC")
						echo "<a href='".$adr."ord=last_run' title='order by lastrun'>lastrun /\</a>";
					else
						echo "<a href='".$adr."ord=last_run' title='order by lastrun'>lastrun</a>";
				?>
				</div>

				<div class="result" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">value</div>
				<div class="result" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">value h</div>
				<div class="result" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">value d</div>
				<div class="result" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">value w</div>

				<div class="runtime" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">
				<?php
					if($ord=="run_time")
						echo "<a href='".$adr."ord=run_time DESC' title='order by runtime - last will be first'>runtime \/</a>";
					elseif($ord=="run_time DESC")
						echo "<a href='".$adr."ord=run_time' title='order by runtime'>runtime /\</a>";
					else
						echo "<a href='".$adr."ord=run_time' title='order by runtime'>runtime</a>";
				?>
				</div>

				<div class="dsheet" style="background-color:#eee;height:18px;padding-top:6px;text-align:center;">DS</div>
			</p>
		<! ***** end of list header  *****>



		<! ***** list  *****>
		<?php

	$result = mysqli_query($conn,"SELECT * FROM flow6 WHERE $cnd ORDER BY $ord ");	# for list
#	$result = mysqli_query($conn,"SELECT * FROM flow6 WHERE id LIKE 'ctr%' ORDER BY n");	# for list
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

				<div class="host" style=<?php echo $bgc;?>;>
					<?php echo $row['host'] ?>
				</div>

				<div class="type" style=<?php echo $bgc;?>;>
					<?php echo $row['type'] ?>
				</div>

				<div class="id" style=<?php echo $bgc_value;?>;>
					<?php echo "<a href='' target='_blank' title='".$row['id']." | status: ".$flow_status." (".$status_name.")'>".$row['id']."</a>"; ?>
				</div>

				<div class="name" style=<?php echo $bgc_value;?>;>
				<?php
					if (strlen($row['name_hu'])>55) {
						echo "<a href='' target='_blank' title='Host/script:".$row['host']."/".$row['script']." - ".$row['name']."'>".substr($row['name'],0,55)."...</a>";
					} else {
						echo "<a href='' target='_blank' title='Host/script:".$row['host']."/".$row['script']." - ".$row['name']."'>".$row['name']."</a>";
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
					if ($last_run_age<120) { # data is fresh
						$bgc_lastrun = $bgc;
					} else { # data is old, but maybe its periodic saver
#						if ( ($last_run_age<3600) AND ( ($id=='trf9')OR($id=='dbs9')OR($id=='trf29')OR($id=='dbs29') ) ) { # yellow
						if ( ($last_run_age<100000) AND ( (1) ) ) { # less then one day then yellow
							$bgc_lastrun = "background-color:#ff0;";
						} else { # red
							$bgc_lastrun = "background-color:#f88;";
						}
					}
				?>
				<div class="lastrun" style=<?php echo $bgc_lastrun;?>;>
				<?php
					$adridl = $adr."log=1&log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&idl=".$row['id'];
					if ( ($row['last_run']=='') OR ($row['last_run']=='0000-00-00 00:00:00') OR ($row['last_run']=='1970-01-01 00:00:00') ) { # no mcsc-login to s0
						echo "not yet";
					} else {
						echo "<a href='".$adridl."' title='log of this flow'>".$row['last_run']."</a>";
					}
				?>
				</div>

				<div class="result" style=<?php echo $bgc_value;?>;>&nbsp;
					<?php echo "<a href='' title='".$row['value_description'].": ".$row['value'].$row['value_unit']."'>".$row['value'].$row['value_unit']."</a>"; ?>
				</div>
				<div class="result" style=<?php echo $bgc;?>;>&nbsp;
					<?php echo "<a href='' title='".$row['value_description'].": ".$row['value'].$row['value_unit']."'>".$row['value_h']."</a>"; ?>
				</div>
				<div class="result" style=<?php echo $bgc;?>;>&nbsp;
					<?php echo "<a href='' title='".$row['value_description'].": ".$row['value'].$row['value_unit']."'>".$row['value_d']."</a>"; ?>
				</div>
				<div class="result" style=<?php echo $bgc;?>;>&nbsp;
					<?php echo "<a href='' title='".$row['value_description'].": ".$row['value'].$row['value_unit']."'>".$row['value_w']."</a>"; ?>
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

  <?php } // only when log and calendar are OFF ?>
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


//		   		$result_log = mysqli_query($conn,"SELECT * FROM clients_status_log4 WHERE id LIKE 'mcs069' ORDER BY date DESC LIMIT $rcn,1000000");	// for clients log
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
				## status - see datalist table !
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
	<?php
		echo "all flow: ".$i." | Total runtime: ".$j."s /minute<br><br>...thanks for waiting";
    ?>
	</div>
  <! ***** footer  *****>

</div>
<! ***** box  *****>


<! ***** impressum  *****>
<div class="impr">
	<div class="imp1"></div>
	<div class="imp2">2019 @ OfficeLink Kft.</div>
</div>
<! ***** impressum  *****>



<br><br>
<div class="stsl">
<?php

	echo "<div style='color:#eee'>Status and colours:</div>";
	for ($st2 = 0; $st2 <= 6; $st2++) {
		echo '<div style="width:155px;height:12px;float:left;margin:3px;margin-top:8px;padding:8px;background-color:'.$st2c[$st2].'">Status (st2): ('.$st2.') '.$st2n[$st2].' </div>';
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
