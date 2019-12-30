<?php

	session_start();
	# only for kamera, szerver and admin
#	if( ($_SESSION['MKuser']!=="szerver") OR ($_SESSION['MKuser']!=="kamera") OR ($_SESSION['MKuser']!=="admin") ) { header('Location: ../index.php'); exit; }
	ob_start();

	# ***  setup language  ***
	if (isset($_GET['lang'])) { $_SESSION['lang']=$_GET['lang'];; } # get language data
	elseif (!isset($_SESSION['lang'])) { $lang="HU"; } # default language
	include "../common_files/language/lang.php";		# dictionary
	echo "lang: ".$_SESSION['lang']." | ".$n_lang; # check

	$ptitle = $lang_flow[0][$n_lang]; # "InVR &#9832; Rendszertérkép";


	date_default_timezone_set('Europe/Budapest');
	include "../common_files/connect.php";
	include "../common_files/index.php";





## manual and definition
#echo "manual and definition";

#	this is the manual of OfficeLink Kft. "Intelligens vezérlőrendszer kamerarendszer" system

#	units: servers and clients   #
#	server's groups
	$un[0] = "page";
	$un[1] = "officelink.hu";
	$un[2] = "kamera.officelink.hu webszerver";
	$un[3] = "mcs felügyelet";
	$un[4] = "kamera.officelink.hu képszerver";
	$un[5] = "officelink képszerver";
#	client's groups
	$un[6] = "mk_APN kliensek";
	$un[7] = "tk_APN kliensek";
	$un[8] = "officelink kamerák";


#	connection   #
#	transfers
	$trf[0] = "Képátvitel";
	$trf[1] = "Képátvitel officelink.hu szerverre";			$trfp[1] = $un[4]." -> ".$un[1];
	$trf[2] = "Képátvitel kamera.officelink.hu webszerverre";	$trfp[2] = $un[4]." -> ".$un[2];
	$trf[3] = "Képátvitel kamera.officelink.hu képszerverre";	$trfp[3] = $un[5]." -> ".$un[4];
	$trf[4] = "Képátvitel kamera.officelink.hu képszerverre";	$trfp[4] = $un[6]." -> ".$un[4];
	$trf[5] = "Képátvitel kamera.officelink.hu képszerverre";	$trfp[5] = $un[7]." -> ".$un[4];
	$trf[6] = "Képátvitel officelink képszerverre";			$trfp[6] = $un[8]." -> ".$un[5];

#	controls
	$ctr[0] = "Felügyelet";
	$ctr[1] = "officelink.hu felügyelet";						$ctrp[1] = $un[3]." -> ".$un[1];		$ctrl[1] = "wget";
	$ctr[2] = "kamera.officelink.hu webszerver felügyelet";		$ctrp[2] = $un[3]." -> ".$un[2];		$ctrl[2] = "wget";
	$ctr[3] = "kamera.officelink.hu képszerver felügyelet";		$ctrp[3] = $un[3]." -> ".$un[4];		$ctrl[1] = "ping, rsync, load, disk";
	$ctr[4] = "officelink képszerver felügyelet";			$ctrp[4] = $un[4]." -> ".$un[5];		$ctrl[1] = "ping, rsync";
	$ctr[5] = "mk_APN kliens felügyelet";					$ctrp[5] = $un[4]." -> ".$un[6];		$ctrl[1] = "rsnyc from client (load, disk)";
	$ctr[6] = "tk_APN kliens felügyelet";					$ctrp[6] = $un[3]." -> ".$un[7];		$ctrl[1] = "rsnyc from client (load, disk)";
	$ctr[7] = "officelink kamerák kliens felügyelet";		$ctrp[7] = $un[4]." -> ".$un[8];		$ctrl[1] = "ping";



#	status   #
#	|	intensity
#	\/
#
#	s2	color				color code		status name
#		-----------			----------		-----------
#	1	light blue
#	2	light green
#	3	yellow
#	4	orange
#	5	red
#	6	black

# echo "def status's names | colors<br>";
		$st2n[0] = $lang_flow_list[1][$n_lang];   	$st2c[0] = "#eee";	# "OFF"
		$st2n[1] = $lang_flow_list[2][$n_lang]; 	$st2c[1] = "#def";	# "ok"
		$st2n[2] = $lang_flow_list[3][$n_lang]; 	$st2c[2] = "#cfc";	# "warm"
		$st2n[3] = $lang_flow_list[4][$n_lang]; 	$st2c[3] = "#ffb";	# "warning"
		$st2n[4] = $lang_flow_list[5][$n_lang]; 	$st2c[4] = "#fc9";	# "hot"
		$st2n[5] = $lang_flow_list[6][$n_lang]; 	$st2c[5] = "#f66";	# "no"
		$st2n[6] = $lang_flow_list[7][$n_lang]; 	$st2c[6] = "#666";	# "scrap"

## end of manual











## read clients table

#echo "*****************************************<br>";

# servers from clients4 table
	$result = mysqli_query($conn,"SELECT * FROM clients4 WHERE owner='s' ");	# for list
	while($row = mysqli_fetch_array($result)) { $i++;

		$idc = $row['id'];
		$name[$idc] = $row['name'];
		$vpn_ip[$idc] = $row['vpn_ip'];

		$vpn_ip[$idc] = $row['vpn_ip'];
		$cl_load[$idc] = $row['cl_load'];
		$vpn_ping[$idc] = $row['vpn_ping'];
		$cl_disk[$idc] = $row['cl_disk'];
		$vpn_last[$idc] = $row['vpn_last'];

		if($row['vpn_ping']=="") 				/* same as s0 status color */
			$bgcv[$idc] = "#567;";
		elseif($row['vpn_ping']=="no") 			/* vpn exist, but connection down */
			$bgcv[$idc] = "#ff5555;";
		elseif($row['vpn_ping']<1000) 			/* fast connection */
			$bgcv[$idc] = "#b3d9ff;";
		else					 				/* same as s0 status color */
			$bgcv[$idc] = "#567;";

/*
		$adridl = '';

		echo $idc."= ".$name[$idc]." | ".$vpn_ip[$idc]." = ";

		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )		// connection, log is ready
			echo " <a href='".$adridl."' target='_blank' title='response time: ".$vpn_ping[$idc]."  | load: ".$cl_load[$idc]."  | disk free: ".$cl_disk[$idc]." - open clients status log'> ".$vpn_ping[$idc]." | load: ".$cl_load[$idc]." | ".$cl_disk[$idc]."</a>";
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	// only connection ready, no log file
			echo "<a href='".$adridl."' target='_blank' title='connection: ".$vpn_ping[$idc]."  - open clients status log'>".$vpn_ping[$idc]."</a>";
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 		// no vpn connection now
			echo "<a href='".$adridl."' target='_blank' title='last vpn connection: ".$vpn_last[$idc]." - open clients status log'>last vpn connection: ".$vpn_last[$idc]."</a>";
		else // no vpn
			echo "never&nbsp;";

		echo " -- ".$bgcv[$idc]."<br>";
*/

	} # end of query

#echo "*****************************************<br>";


#echo "*****************************************<br>";

# mcs249 system from clients4 table
	$result = mysqli_query($conn,"SELECT * FROM clients4 WHERE id LIKE 'mcs249%' ");	# for list
	while($row = mysqli_fetch_array($result)) { $i++;

		$idc = $row['id'];
		$name[$idc] = $row['name'];
		$vpn_ip[$idc] = $row['vpn_ip'];

		$vpn_ip[$idc] = $row['vpn_ip'];
		$cl_load[$idc] = $row['cl_load'];
		$vpn_ping[$idc] = $row['vpn_ping'];
		$cl_disk[$idc] = $row['cl_disk'];
		$vpn_last[$idc] = $row['vpn_last'];

		if($row['vpn_ping']=="") 				/* same as s0 status color */
			$bgcv[$idc] = "#567;";
		elseif($row['vpn_ping']=="no") 			/* vpn exist, but connection down */
			$bgcv[$idc] = "#ff5555;";
		elseif($row['vpn_ping']<1000) 			/* fast connection */
			$bgcv[$idc] = "#b3d9ff;";
		else					 				/* same as s0 status color */
			$bgcv[$idc] = "#567;";

/*
		$adridl = '';

		echo $idc."= ".$name[$idc]." | ".$vpn_ip[$idc]." = ";

		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )		// connection, log is ready
			echo " <a href='".$adridl."' target='_blank' title='response time: ".$vpn_ping[$idc]."  | load: ".$cl_load[$idc]."  | disk free: ".$cl_disk[$idc]." - open clients status log'> ".$vpn_ping[$idc]." | load: ".$cl_load[$idc]." | ".$cl_disk[$idc]."</a>";
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	// only connection ready, no log file
			echo "<a href='".$adridl."' target='_blank' title='connection: ".$vpn_ping[$idc]."  - open clients status log'>".$vpn_ping[$idc]."</a>";
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 		// no vpn connection now
			echo "<a href='".$adridl."' target='_blank' title='last vpn connection: ".$vpn_last[$idc]." - open clients status log'>last vpn connection: ".$vpn_last[$idc]."</a>";
		else // no vpn
			echo "never&nbsp;";

		echo " -- ".$bgcv[$idc]."<br>";
*/

	} # end of query

#echo "*****************************************<br>";





# s0_clients
		$idc = 's0_clients';
		$name[$idc] = $row['name'];
		$vpn_ip[$idc] = $row['vpn_ip'];

		$vpn_ip[$idc] = $row['vpn_ip'];
		$cl_load[$idc] = $row['cl_load'];
		$vpn_ping[$idc] = $row['vpn_ping'];
		$cl_disk[$idc] = $row['cl_disk'];
		$vpn_last[$idc] = $row['vpn_last'];

		if($row['vpn_ping']=="") 				/* same as s0 status color */
			$bgcv[$idc] = "#eee;";
		elseif($row['vpn_ping']=="no") 		/* vpn exist, but connection down */
			$bgcv[$idc] = "#ff5555;";
		elseif($row['vpn_ping']<1000) 			/* fast connection */
			$bgcv[$idc] = "#b3d9ff;";
		else					 				/* same as s0 status color */
			$bgcv[$idc] = "#abc;";
/*
		$adridl = '';

		echo $idc."= ".$name[$idc]." | ".$vpn_ip[$idc]." = ";

		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )		// connection, log is ready
			echo " <a href='".$adridl."' target='_blank' title='response time: ".$vpn_ping[$idc]."  | load: ".$cl_load[$idc]."  | disk free: ".$cl_disk[$idc]." - open clients status log'> ".$vpn_ping[$idc]." | load: ".$cl_load[$idc]." | ".$cl_disk[$idc]."</a>";
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	// only connection ready, no log file
			echo "<a href='".$adridl."' target='_blank' title='connection: ".$vpn_ping[$idc]."  - open clients status log'>".$vpn_ping[$idc]."</a>";
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 		// no vpn connection now
			echo "<a href='".$adridl."' target='_blank' title='last vpn connection: ".$vpn_last[$idc]." - open clients status log'>last vpn connection: ".$vpn_last[$idc]."</a>";
		else // no vpn
			echo "never&nbsp;";

		echo " -- ".$bgcv[$idc]."<br>";
*/
#echo "*****************************************<br>";


# s2_clients
		$idc = 's2_clients';
		$name[$idc] = $row['name'];
		$vpn_ip[$idc] = $row['vpn_ip'];

		$vpn_ip[$idc] = $row['vpn_ip'];
		$cl_load[$idc] = $row['cl_load'];
		$vpn_ping[$idc] = $row['vpn_ping'];
		$cl_disk[$idc] = $row['cl_disk'];
		$vpn_last[$idc] = $row['vpn_last'];

		if($row['vpn_ping']=="") 				/* same as s0 status color */
			$bgcv[$idc] = "#eee;";
		elseif($row['vpn_ping']=="no") 		/* vpn exist, but connection down */
			$bgcv[$idc] = "#ff5555;";
		elseif($row['vpn_ping']<1000) 			/* fast connection */
			$bgcv[$idc] = "#b3d9ff;";
		else					 				/* same as s0 status color */
			$bgcv[$idc] = "#abc;";
/*
		$adridl = '';

		echo $idc."= ".$name[$idc]." | ".$vpn_ip[$idc]." = ";

		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )		// connection, log is ready
			echo " <a href='".$adridl."' target='_blank' title='response time: ".$vpn_ping[$idc]."  | load: ".$cl_load[$idc]."  | disk free: ".$cl_disk[$idc]." - open clients status log'> ".$vpn_ping[$idc]." | load: ".$cl_load[$idc]." | ".$cl_disk[$idc]."</a>";
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	// only connection ready, no log file
			echo "<a href='".$adridl."' target='_blank' title='connection: ".$vpn_ping[$idc]."  - open clients status log'>".$vpn_ping[$idc]."</a>";
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 		// no vpn connection now
			echo "<a href='".$adridl."' target='_blank' title='last vpn connection: ".$vpn_last[$idc]." - open clients status log'>last vpn connection: ".$vpn_last[$idc]."</a>";
		else // no vpn
			echo "never&nbsp;";

		echo " -- ".$bgcv[$idc]."<br>";
*/
#echo "*****************************************<br>";


# users
		$idc = 'users';
		$name[$idc] = $row['name'];
		$vpn_ip[$idc] = $row['vpn_ip'];

		$vpn_ip[$idc] = $row['vpn_ip'];
		$cl_load[$idc] = $row['cl_load'];
		$vpn_ping[$idc] = $row['vpn_ping'];
		$cl_disk[$idc] = $row['cl_disk'];
		$vpn_last[$idc] = $row['vpn_last'];

		if($row['vpn_ping']=="") 				/* same as s0 status color */
			$bgcv[$idc] = "#eee;";
		elseif($row['vpn_ping']=="no") 		/* vpn exist, but connection down */
			$bgcv[$idc] = "#ff5555;";
		elseif($row['vpn_ping']<1000) 			/* fast connection */
			$bgcv[$idc] = "#b3d9ff;";
		else					 				/* same as s0 status color */
			$bgcv[$idc] = "#abc;";
/*
		$adridl = '';

		echo $idc."= ".$name[$idc]." | ".$vpn_ip[$idc]." = ";

		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )		// connection, log is ready
			echo " <a href='".$adridl."' target='_blank' title='response time: ".$vpn_ping[$idc]."  | load: ".$cl_load[$idc]."  | disk free: ".$cl_disk[$idc]." - open clients status log'> ".$vpn_ping[$idc]." | load: ".$cl_load[$idc]." | ".$cl_disk[$idc]."</a>";
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	// only connection ready, no log file
			echo "<a href='".$adridl."' target='_blank' title='connection: ".$vpn_ping[$idc]."  - open clients status log'>".$vpn_ping[$idc]."</a>";
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 		// no vpn connection now
			echo "<a href='".$adridl."' target='_blank' title='last vpn connection: ".$vpn_last[$idc]." - open clients status log'>last vpn connection: ".$vpn_last[$idc]."</a>";
		else // no vpn
			echo "never&nbsp;";

		echo " -- ".$bgcv[$idc]."<br>";
*/
#echo "*****************************************<br>";

# q_clients
		$idc = 'q_clients';
		$name[$idc] = $row['name'];
		$vpn_ip[$idc] = $row['vpn_ip'];

		$vpn_ip[$idc] = $row['vpn_ip'];
		$cl_load[$idc] = $row['cl_load'];
		$vpn_ping[$idc] = $row['vpn_ping'];
		$cl_disk[$idc] = $row['cl_disk'];
		$vpn_last[$idc] = $row['vpn_last'];

		if($row['vpn_ping']=="") 				/* same as s0 status color */
			$bgcv[$idc] = "#eee;";
		elseif($row['vpn_ping']=="no") 		/* vpn exist, but connection down */
			$bgcv[$idc] = "#ff5555;";
		elseif($row['vpn_ping']<1000) 			/* fast connection */
			$bgcv[$idc] = "#b3d9ff;";
		else					 				/* same as s0 status color */
			$bgcv[$idc] = "#abc;";
/*
		$adridl = '';

		echo $idc."= ".$name[$idc]." | ".$vpn_ip[$idc]." = ";

		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )		// connection, log is ready
			echo " <a href='".$adridl."' target='_blank' title='response time: ".$vpn_ping[$idc]."  | load: ".$cl_load[$idc]."  | disk free: ".$cl_disk[$idc]." - open clients status log'> ".$vpn_ping[$idc]." | load: ".$cl_load[$idc]." | ".$cl_disk[$idc]."</a>";
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	// only connection ready, no log file
			echo "<a href='".$adridl."' target='_blank' title='connection: ".$vpn_ping[$idc]."  - open clients status log'>".$vpn_ping[$idc]."</a>";
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 		// no vpn connection now
			echo "<a href='".$adridl."' target='_blank' title='last vpn connection: ".$vpn_last[$idc]." - open clients status log'>last vpn connection: ".$vpn_last[$idc]."</a>";
		else // no vpn
			echo "never&nbsp;";

		echo " -- ".$bgcv[$idc]."<br>";
*/
#echo "*****************************************<br>";














## read flow2 table

#echo "*****************************************<br>";

# flows from flow2 table
	$result = mysqli_query($conn,"SELECT * FROM flow6 ORDER BY n");	# for list
	while($row = mysqli_fetch_array($result)) { $i++; $j=$j+$row['run_time'];

		$n = $row['n'];
		$id = $row['id'];
		$name[$id] = $row['name'];
		$name_hu[$id] = $row['name_hu'];
		$host[$id] = $row['host'];
		$type[$id] = $row['type'];	# connection check: srv, webserver: web, dbase transfer: dbs, file transfer: trf
		$path_source[$id] = $row['path_source'];
		$path_dest[$id] = $row['path_dest'];

		$run_time[$id] = $row['run_time'];
		$last_run[$id] = $row['last_run'];

		$value_description[$id] = $row['value_description'];
		$value_description_hu[$id] = $row['value_description_hu'];
		$value[$id] = $row['value'];
		$value_unit[$id] = $row['value_unit'];
		$value_h[$id] = $row['value_h'];
		$value_d[$id] = $row['value_d'];
		$value_w[$id] = $row['value_w'];
		$flow_status[$id] = $row['flow_status'];

#		$status_color[$id] = $st2c[rand(0,6)]; echo $status_color[$id];
		$status_color[$id] = $st2c[$flow_status[$id]]; #echo $status_color[$id];


		#echo $n.". ".$id."= ".$name[$id]." | ".$value_description[$id]."= ".$value[$id].$value_unit[$id]."<br>";

		# count age of the last_run
			$last_run_age = strtotime(date('Y-m-d H:i:s')) - strtotime($row['last_run']);
			#echo strtotime($row['last_run'])." | ".strtotime(date('Y-m-d H:i:s'))." = ".$last_run_age; # check
			if ($last_run_age>120) {
				$last_run_aged[$id] = 1;
				$last_run_color[$id] = "red";
			} else {
				$last_run_aged[$id] = 0;
				$last_run_color[$id] = "black";
			}
			#echo $last_run_aged[$id]; # check



	} # end of query

#echo "*****************************************<br>";




## read /.mcs/log.log file, made by /.mcs/bin/make_log.php
	$filename = "/.mcs/log.log";
	$handle = fopen($filename, "r");
	$s2_log = fread($handle, filesize($filename));
	fclose($handle);

	$s2_log_lines = explode("&", $s2_log);
#	echo $s2_log_lines[0]."<br>"; # check
#	echo $s2_log_lines[1]."<br>"; # check

?>





























<!DOCTYPE html>
<html>
<head>
	<title><?=$ptitle?></title>
	<meta http-equiv="refresh" content="59;url=index.php">

	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="hu-hu" />

	<script type="text/javascript" language="javascript" src="../common_files/lytebox.js"></script>

	<link rel="stylesheet" href="../common_files/lytebox.css" type="text/css" media="screen" />
	<link rel="icon" href="../common_files/img/officelink_favicon.png" type="image/png" sizes="16x16">

	<style>
		A {
			color: #222;
			text-decoration:none;
			font-weight:bold;
		  }
		A:hover {
			color: #004;
			text-decoration:none;
		}
	</style>


</head>
<body>
<div style="
	margin:auto;
	width:1240px;
	border:0px;
	padding-left:10px;
	padding-right:10px;
">


<header style="width:1220px;height:26px;padding-left:14px;padding-top:3px;padding-right:14px;background-color:#424244;border:5px;
	font-family:courier;font-size:14px;color:#eeeeee">

	<div style="float:left"><a href="" target="_blank"><img src="../common_files/img/officelink_logo.png" height="21" border="0" title="OfficeLink logo"></a></div>
	<div style="float:left;padding-top:4px;margin-left:40px;"><?php echo $lang_flow[0][$n_lang]; # InVR &#9832; Rendszertérkép ?></div>
	<div style="float:left;padding-top:4px;margin-left:100px;"><a href="flow_list.php" target="_blank" style="color:#eeeeee"><?php echo $lang_flow[1][$n_lang]; # Rendszerfolyamatok ?></a></div>


	<div style="float:right;padding-top:4px;"><?=date("Y.m.d H:i")?></div>
</header>






































<!--   ************************************************   svg   ********************************************************   -->

<svg height="600" width="1230">

<defs>
    <filter
       	id="shadow"
       	style="color-interpolation-filters:sRGB"
       	x="-0.018"
       	width="1.036"
       	y="-0.036"
       	height="1.072"
		>
    	<feGaussianBlur
         	stdDeviation="1.8"
         	id="feGaussianBlur4399"
		/>
    </filter>

</defs>




<!-- ***   variable   *** -->

<?php

	$ry=5;		# round edge


?>

<!-- ***   lines   *** -->
<rect
   id="line_left"
   x="40"
   y="70"
   width="4"
   height="480"
   style="opacity:1;fill:#eee;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
/>
<rect
   id="line_top"
   x="70"
   y="40"
   width="1150"
   height="4"
   style="opacity:1;fill:#eee;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
/>
<rect
   id="line_down"
   x="70"
   y="576"
   width="1150"
   height="4"
   style="opacity:1;fill:#eee;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
/>


<!-- ***   text   *** -->

<!-- <text x="0" y="15" fill="red">I love SVG!</text> -->

<text
   id="public_content"
   x="80"
   y="30"
   style="font-family:courier;font-style:normal;font-weight:normal;font-size:16px;line-height:1.25;fill:#666"
>
	public information
</text>

<text
   id="webservers"
   x="410"
   y="30"
	transform = "rotate(270 270,270)"
   style="font-family:courier;font-style:normal;font-weight:normal;font-size:16px;line-height:1.25;fill:#666"
>
	web
</text>


















<!-- ***   s0   *** -->
<?php $idc = "s0"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="75" y="275"
   width="140" height="100"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="70" y="270"
   width="140" height="100"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$id?>_text1"
	x="80" y="290"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$id?>_text2"
	x="80" y="305"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>











<!-- ***   s0 clients   *** -->
<?php $idc = "s0_clients"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="95" y="455"
   width="120" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>_shadow"
   x="85" y="445"
   width="120" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>_shadow"
   x="75" y="435"
   width="120" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="90" y="450"
   width="120" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="80" y="440"
   width="120" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="70" y="430"
   width="120" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="80" y="450"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$id?>_text2"
	x="80" y="465"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>




<!-- ***   s2   *** -->
<?php $idc = "s2"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="275" y="275"
   width="140" height="100"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="270" y="270"
   width="140" height="100"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="280" y="290"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$id?>_text2"
	x="280" y="305"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>


<!-- ***   s2 clients   *** -->
<?php $idc = "s2_clients"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="295" y="455"
   width="120" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>_shadow"
   x="285" y="445"
   width="120" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>_shadow"
   x="275" y="435"
   width="120" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="290" y="450"
   width="120" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="280" y="440"
   width="120" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="270" y="430"
   width="120" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="280" y="450"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="280" y="465"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>



<!-- ***   s20   *** -->
<?php $idc = "s20"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="445" y="275"
   width="120" height="100"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>"
   x="440" y="270"
   width="120" height="100"
   style="opacity:1;fill:#eee;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="450" y="290"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="450" y="305"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>








<!-- ***   s4   *** -->
<?php $idc = "s4"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="605" y="75"
   width="140" height="300"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="600" y="70"
   width="140" height="300"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="610" y="90"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="610" y="105"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>










<!-- ***   users   *** -->
<?php $idc = "users"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="495" y="455"
   width="180" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>_shadow"
   x="485" y="445"
   width="180" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>_shadow"
   x="475" y="435"
   width="180" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="490" y="450"
   width="180" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="480" y="440"
   width="180" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="470" y="430"
   width="180" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="480" y="450"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="480" y="465"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>





<!-- ***   officelink clients   *** -->
<?php $idc = "q_clients"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="715" y="455"
   width="180" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>_shadow"
   x="705" y="445"
   width="180" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="<?=$idc?>_shadow"
   x="695" y="435"
   width="180" height="60"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="710" y="450"
   width="180" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="700" y="440"
   width="180" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="690" y="430"
   width="180" height="60"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="710" y="450"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="710" y="465"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>







<!-- ***   s5   *** -->
<?php $idc = "s5"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="845" y="75"
   width="140" height="100"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="840" y="70"
   width="140" height="100"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="850" y="90"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="850" y="105"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>








<!-- ***   mcs249   *** -->
<?php $idc = "mcs249"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="865" y="235"
   width="140" height="100"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="860" y="230"
   width="140" height="100"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$id?>_text1"
	x="870" y="250"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="870" y="265"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>



<!-- ***   mcs249 clients  *** -->
<?php $idc = "mcs249av"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="1085" y="75"
   width="140" height="40"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="1080" y="70"
   width="140" height="40"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="1090" y="85"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="1090" y="100"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>

<?php $idc = "mcs249g"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="1085" y="135"
   width="140" height="40"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="1080" y="130"
   width="140" height="40"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="1090" y="145"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="1090" y="160"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>

<?php $idc = "mcs249m"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="1085" y="195"
   width="140" height="40"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="1080" y="190"
   width="140" height="40"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="1090" y="205"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="1090" y="220"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>

<?php $idc = "mcs249p"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="1085" y="255"
   width="140" height="40"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="1080" y="250"
   width="140" height="40"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="1090" y="265"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="1090" y="280"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>

<?php $idc = "mcs249s"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="1085" y="315"
   width="140" height="40"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="1080" y="310"
   width="140" height="40"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="1090" y="325"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="1090" y="340"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>

<?php $idc = "mcs249u"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="1085" y="375"
   width="140" height="40"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="1080" y="370"
   width="140" height="40"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="1090" y="385"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="1090" y="400"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>

<?php $idc = "mcs249u1"; ?>
<rect
   id="<?=$idc?>_shadow"
   x="1085" y="435"
   width="140" height="40"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
	id="<?=$idc?>"
   x="1080" y="430"
   width="140" height="40"
   style="opacity:1;fill:<?=$bgcv[$idc]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="<?=$idc?>_text1"
	x="1090" y="445"
   	style="font-family:courier;font-size:10px;fill:#222"
>
	<title><?php echo $idc." (".$name[$idc].") - ping time: ".$vpn_ping[$idc]; ?></title>
	<?php echo $idc." - ".$vpn_ping[$idc]; ?>
</text>
<text
   id="<?=$idc?>_text2"
	x="1090" y="460"
   	style="font-family:courier;font-size:9px;fill:#222"
>
	<?php
		echo "<title>load | disk</title>";
		if ( ($cl_load[$idc]!='no') && ($cl_load[$idc]!='') )				# connection, log is ready
			echo $cl_load[$idc]." | ".$cl_disk[$idc];
		elseif ( ($vpn_ping[$idc] != 'no') && ($vpn_ping[$idc] != '') ) 	# only connection ready, no log file
			echo $vpn_ping[$idc];
		elseif ( $vpn_last[$idc] != '1970-01-01 00:00:00') 					# no vpn connection now
			echo " ".$vpn_last[$idc];
		else 																# no vpn
			echo "never";
	?>
</text>



















<!--
	<path
		id="elbows"
		style="fill:#ccc;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
		d="
			m 1100,350
			c 3,0 6,3 6,6
			l 0,20
			c 0,3 -3,6 -6,6
			l -20,0
			c -3,0 -6,-3 -6,-6
			l 0,-20
			c 0,-3 3,-6 6,-6
			z
		"
	/>
	<path
		style="fill:#ccc;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
		d="
			m 1100,250
			l 0,-20
			c 0,-3 3,-6 6,-6
		"
	/>

-->









<!-- ***   ctr10 -> s0   *** -->
<?php $id = "ctr10"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 590,100
		l -480,0 0,150
		l 5,0 -10,10 -10,-10 5,0
		l 0,-150
		c 0,-5 5,-10 10,-10
		l 480,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="580" y="85"
	text-anchor="end"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>

<!-- ***   dbs10 -> s0   *** -->
<?php $id = "dbs10"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 590,130
		l -440,0 0,120
		l 10,0 -20,10 -20,-10 10,0
		l 0,-130
		c 0,-5 5,-10 10,-10
		l 450,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="580" y="145"
	text-anchor="end"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>


<!-- ***   ctr<> / dbs<> | s0 -> s0 clients   *** -->
<?php $id = "ctr10"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 110,380
		l 0,30
		l 5,0 -10,10 -10,-10 5,0
		l 0,-30
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="125" y="395"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>
<?php $id = "dbs10"; ?>
<text
   id="<?=$id?>_text1"
	x="125" y="415"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>




<!-- ***   ctr21 -> s2   *** -->
<?php $id = "ctr21"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 590,200
		l -280,0 0,50
		l 5,0 -10,10 -10,-10 5,0
		l 0,-50
		c 0,-5 5,-10 10,-10
		l 280,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="580" y="185"
	text-anchor="end"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>

<!-- ***   dbs20, trf20 -> s2   *** -->
<?php $id = "dbs20"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 590,230
		l -240,0 0,20
		l 10,0 -20,10 -20,-10 10,0
		l 0,-30
		c 0,-5 5,-10 10,-10
		l 250,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="580" y="245"
	text-anchor="end"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>


<!-- ***   ctr<> / dbs<> | s2 -> s2 clients   *** -->
<?php $id = "ctr10"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 310,380
		l 0,30
		l 5,0 -10,10 -10,-10 5,0
		l 0,-30
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="325" y="395"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>
<?php $id = "dbs10"; ?>
<text
   id="<?=$id?>_text1"
	x="325" y="415"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>









<!-- ***   ctr<> | s4 -> s4 users   *** -->
<?php $id = "ctr10"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 630,380
		l 0,30
		l 5,0 -10,10 -10,-10 5,0
		l 0,-30
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="610" y="395"
	text-anchor="end"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>



<!-- ***   ctr<> / dbs<> | s4 -> officelink clients   *** -->
<?php $id = "ctr10"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 720,380
		l 0,30
		l 5,0 -10,10 -10,-10 5,0
		l 0,-30
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="730" y="395"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>
<?php $id = "dbs10"; ?>
<text
   id="<?=$id?>_text1"
	x="730" y="415"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>









<!-- ***   ctr50 -> s5   *** -->
<?php $id = "ctr50"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 750,100
		l 70,0
		l 0,5 10,-10 -10,-10 0,5
		l -70,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="750" y="85"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>

<!-- ***   dbs50 -> s5   *** -->
<?php $id = "dbs50"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 750,140
		l 70,0
		l 0,10 10,-20 -10,-20 0,10
		l -70,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="750" y="155"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>



<!-- ***   ctr249 -> mcs249   *** -->
<?php $id = "ctr249"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 750,250
		l 90,0
		l 0,5 10,-10 -10,-10 0,5
		l -90,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="750" y="235"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>

<!-- ***   dbs249 -> mcs249   *** -->
<?php $id = "dbs249"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 750,290
		l 90,0
		l 0,10 10,-20 -10,-20 0,10
		l -90,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="750" y="305"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>




<!-- ***   ctr249clients | mcs249 -> mcs249clients   *** -->
<?php $id = "dbs249"; ?>
<path
	id="<?=$id?>"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 1010,290
		l 50,0
		l 0,10 10,-20 -10,-20 0,10
		l -50,0
		z
	"
/>
<text
   id="<?=$id?>_text1"
	x="1010" y="310"
   	style="font-family:courier;font-size:10px;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $id.": ".$value[$id].$value_unit[$id]; ?>
</text>











	Sorry, your browser does not support inline SVG.

</svg>







<footer style="width:1220px;height:26px;padding-left:14px;padding-top:7px;padding-right:14px;background-color:#424244;
	font-family:courier;font-size:14px;color:#eeeeee">
	<div style="float:left"></div>
	<div style="float:right">2019 @ OfficeLink Kft.</div>
</footer>

<br><br>
<?php

#echo "Status, colour:<br>";
for ($st2 = 0; $st2 <= 6; $st2++) {
	echo '<div style="width:153px;height:12px;float:left;margin:4px;padding:8px;background-color:'.$st2c[$st2].';font-family:courier;font-size:10px">Status: ('.$st2.') '.$st2n[$st2].' </div>';
}

?>

</div>
</body>
</html>
