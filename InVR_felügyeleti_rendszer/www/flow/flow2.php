<?php

	session_start();
	# only for kamera, szerver and admin
#	if( ($_SESSION['MKuser']!=="szerver") OR ($_SESSION['MKuser']!=="kamera") OR ($_SESSION['MKuser']!=="admin") ) { header('Location: ../index.php'); exit; }
	ob_start();

	date_default_timezone_set('Europe/Budapest');
	include "../common_files/connect.php";
	include "../common_files/index.php";






## manual and definition
#echo "manual and definition";

#	this is the manual of OfficeLink Kft.'s "Intelligens felügyelőrendszer kamerarendszer" system

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
	$st2n[0] = "OFF"; 		$st2c[0] = "#fff";
	$st2n[1] = "ok"; 		$st2c[1] = "#def";
	$st2n[2] = "warm"; 		$st2c[2] = "#cfc";
	$st2n[3] = "warning"; 	$st2c[3] = "#ffb";
	$st2n[4] = "hot"; 		$st2c[4] = "#fc9";
	$st2n[5] = "no"; 		$st2c[5] = "#f66";
	$st2n[6] = "scrap"; 	$st2c[6] = "#666";

## end of manual



## read flow2 table

#echo "*****************************************<br>";

# flows from flow2 table
	$result = mysqli_query($conn,"SELECT * FROM flow2 ORDER BY n");	# for list
	while($row = mysqli_fetch_array($result)) { $i++; $j=$j+$row['run_time'];

		$n = $row['n'];
		$id = $row['id'];
		$name[$id] = $row['name'];
		$name_hu[$id] = $row['name_hu'];
		$host[$id] = $row['host'];
		$type[$id] = $row['type'];	# connection chech: srv, webserver: web, dbase transfer: dbs, file transfer: trf
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


#		echo $n.". ".$id."= ".$name[$id]." | ".$value_description[$id]."= ".$value[$id].$value_unit[$id]."<br>";

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


## read /.mcs/log.log file, made by /.mcs/bin/kame_log.php
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
	<title>Webkamera rendszertérkép</title>
	<meta http-equiv="refresh" content="59;url=flow2.php">

	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="hu-hu" />

	<script type="text/javascript" language="javascript" src="../common_files/lytebox.js"></script>

	<link rel="stylesheet" href="../common_files/lytebox.css" type="text/css" media="screen" />
	<link rel="icon" href="https://mcss.blue/common_files/img/mcs.png" type="image/png" sizes="20x16">

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
	width:1200px;
	border:0px;
	padding-left:10px;
	padding-right:10px;
">


<header style="width:1150px;height:26px;padding-left:14px;padding-top:3px;padding-right:14px;background-color:<?=$st2c[1]?>;border:5px;border-radius:5px;
	font-family:courier;font-style:normal;font-weight:normal;font-size:12px;line-height:1.25;fill:#666">

	<div style="float:left"><a href="" target="_blank"><img src="../common_files/img/officelink_logo.png" height="21" border="0" title="OfficeLink Kft."></a></div>
	<div style="float:left;margin-left:20px"><a href="http://kamera.officelink.hu/flow/flow_list.php" target="_blank"><img src="../common_files/img/officelink_logo.png" height="21" border="0" title="OfficeLink Kft."></a></div>
	<div style="float:left;padding-top:4px;margin-left:40px;"OfficeLink Kft. webkamera rendszertérkép</div>
	<div style="float:left;padding-top:4px;margin-left:100px;"><a href="flow2_list.php" target="_blank">s2 flow_list </a></div>
	<div style="float:left;padding-top:4px;margin-left:100px;"><a href="flow_list.php" target="_blank">s4_flow_list</a></div>
	<div style="float:left;padding-top:4px;margin-left:40px;"><a href="index.php" target="_blank">s4_sysmap</a></div>


	<div style="float:right;padding-top:4px;"><?=date("Y.m.d H:i")?></div>
</header>











<svg height="570" width="1150">

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



<!-- ***   text   *** -->

<!-- <text x="0" y="15" fill="red">I love SVG!</text> -->

<text
   id="public_content"
   x="80"
   y="30"
   style="font-family:courier;font-style:normal;font-weight:normal;font-size:16px;line-height:1.25;fill:#666"
>
	publikus információ
</text>
<text
   id="admin_content"
   x="430"
   y="30"
   style="font-family:courier;font-style:normal;font-weight:normal;font-size:16px;line-height:1.25;fill:#666"
>
	publikus / adminisztrátori információk
</text>
<text
   id="mcs control"
   x="950"
   y="30"
   style="font-family:courier;font-style:normal;font-weight:normal;font-size:16px;line-height:1.25;fill:#666"
>
	felügyelet
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
<text
   id="picture servers"
   x="230"
   y="30"
	transform = "rotate(270 270,270)"
   style="font-family:courier;font-style:normal;font-weight:normal;font-size:16px;line-height:1.25;fill:#666"
>
	képtárolás
</text>
<text
   id="clients"
   x="20"
   y="30"
	transform = "rotate(270 270,270)"
   style="font-family:courier;font-style:normal;font-weight:normal;font-size:16px;line-height:1.25;fill:#666"
>
	kamerák
</text>


















<!-- ***   lines   *** -->
<rect
   id="line_left"
   x="40"
   y="70"
   width="4"
   height="460"
   style="opacity:1;fill:#eee;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
/>
<rect
   id="line_top"
   x="70"
   y="40"
   width="1065"
   height="4"
   style="opacity:1;fill:#eee;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
/>
<rect
   id="line_down"
   x="70"
   y="556"
   width="1065"
   height="4"
   style="opacity:1;fill:#eee;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
/>








<!-- ***   un[1]   *** -->
<rect
   id="officelink.hu_shadow"
   x="75"
   y="75"
   width="300"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="officelink.hu"
   x="70"
   y="70"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr1]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"

	onmouseover="style.color='red'"
	onmouseout="style.color='black'"
/>
<text
   id="officelink_text1"
   x="80"
   y="90"
   	style="font-family:courier;font-size:13;stroke:#222;fill:#222"
>
	(u1) <?=$un[1]?>
</text>


<!-- ***   un[2]   *** -->
<rect
   id="kamera.officelink.hu_shadow"
   x="425"
   y="75"
   width="300"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="kamera.officelink.hu"
   x="420"
   y="70"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr2]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="kamera.officelink.hu_text1"
   x="430"
   y="90"
   	style="font-family:courier;font-size:13;stroke:#222;fill:#222"
>
	(u2) <?=$un[2]?>
</text>


<!-- ***   un[3]   *** -->
<rect
   id="mcss.blue_shadow"
   x="945"
   y="75"
   width="190"
   height="170"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="mcss.blue"
   x="940"
   y="70"
   width="190"
   height="170"
   style="opacity:1;fill:<?=$st2c[1]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="mcss.blue_text1"
   x="950"
   y="90"
   	style="font-family:courier;font-size:13;stroke:#222;fill:#222"
>
	(u3) <?=$un[3]?>
</text>





<!-- ***   un[4]   *** -->
<rect
   id="pict_server"
   x="75"
   y="225"
   width="500"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="pict_server"
   x="70"
   y="220"
   width="500"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr0]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="pict_server_text1"
   x="80"
   y="240"
   	style="font-family:courier;font-size:13px;stroke:#222;fill:#222"
>
	(u4) <?=$un[4]?>
</text>

<?php $id = "ctr0"; ?>
<text
   id="pict_server_load-disk_line1"
   x="264"
   y="267"
   	style="font-family:courier;font-size:11px"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?=$s2_log_lines[0]?>
</text>
<text
   id="pict_server_load-disk_line2"
   x="264"
   y="282"
   	style="font-family:courier;font-size:11px"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?=$s2_log_lines[1]?>
</text>

<?php $id = "dbs1"; ?>
<text
   id="pict_server_database check"
   x="450"
   y="240"
   	style="font-family:courier;font-size:11px"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	Adatbázis: <?php echo $value[$id].$value_unit[$id]; ?>
</text>



<!-- ***   un[5]   *** -->
<rect
   id="officelink_ftp_szerver"
   x="655"
   y="225"
   width="240"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="officelink_ftp_szerver"
   x="650"
   y="220"
   width="240"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr3]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="officelink_ftp_text1"
   x="660"
   y="240"
   	style="font-family:courier;font-size:13;stroke:#222;fill:#222"
>
	(u5) <?=$un[5]?>
</text>










<!-- ***   un[6] - clients mk_APN   *** -->
<rect
   id="mk_APN_a_shadow"
   y="435"
   x="95"
   height="80"
   width="300"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="mk_APN_b_shadow"
   y="445"
   x="85"
   height="80"
   width="300"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="mk_APN_c_shadow"
   y="455"
   x="75"
   height="80"
   width="300"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>

<rect
   id="mk_APN_a"
   x="90"
   y="430"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr4]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="mk_APN_b"
   x="80"
   y="440"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr4]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="mk_APN_c"
   x="70"
   y="450"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr4]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="mk_APN_text1"
   x="80"
   y="470"
   	style="font-family:courier;font-size:13;stroke:#222;fill:#222"
>
	(u6) <?=$un[6]?>
</text>


<!-- ***   un[7] - clients tkom_APN   *** -->
<rect
   id="tkom_APN_a_shadow"
   x="455"
   y="435"
   width="300"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="tkom_APN_b_shadow"
   x="445"
   y="445"
   width="300"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="tkom_APN_c_shadow"
   x="435"
   y="455"
   width="300"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>

<rect
   id="tkom_APN_a"
   x="450"
   y="430"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr5]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="tkom_APN_b"
   x="440"
   y="440"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr5]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="tkom_APN_c"
   x="430"
   y="450"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[ctr5]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<text
   id="tkom_APN_text1"
   x="440"
   y="470"
   	style="font-family:courier;font-size:13;stroke:#222;fill:#222"
>
	(u7) <?=$un[7]?>
</text>


<!-- ***   un[8] - clients hw_ftp   *** -->
<rect
   id="highway_a_shadow"
   x="835"
   y="435"
   width="300"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="highway_b_shadow"
   x="825"
   y="445"
   width="300"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>
<rect
   id="highway_c_shadow"
   x="815"
   y="455"
   width="300"
   height="80"
   style="opacity:1;fill:#aaa;fill-opacity:1;stroke:#aaa;stroke-width:1;stroke-opacity:1;filter:url(#shadow)"
   ry="<?=$ry?>"
/>

<rect
   id="highway_a"
   x="830"
   y="430"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[trf6]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="highway_b"
   x="820"
   y="440"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[trf6]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>
<rect
   id="highway_c"
   x="810"
   y="450"
   width="300"
   height="80"
   style="opacity:1;fill:<?=$status_color[trf6]?>;fill-opacity:1;stroke:#666;stroke-width:1;stroke-opacity:1"
   ry="<?=$ry?>"
/>

<text
   id="highway_text1"
   x="820"
   y="470"
   	style="font-family:courier;font-size:13;stroke:#222;fill:#222"
>
	(u8) <?=$un[8]?>
</text>






<!-- ***   connections   *** -->




<!-- *** templates
	<path
		id="elbows"
		style="fill:#ccc;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
		d="
			m 1100,250
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
		id="conn"
		style="fill:#ccc;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
		d="
			m 1000,300
			l -5,0 8,-10 8,10 -5,0 0,20 100,0
			c 3,0 6,3 6,6
			l 0,20 -6,0 0,-20 -100,0
			c -3,0 -6,-3 -6,-6
			z
		 "
		 onmouseover="on mouse over"
		 onclick="on click"
	/>
-->








<!-- ***   ctr[0] - shadows   *** -->
<path
	id="ctr1_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 205,175
		l -5,0 8,-10 8,10 -5,0 0,40 -6,0
		z
	"
/>
<path
	id="ctr2_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 465,175
		l -5,0 8,-10 8,10 -5,0 0,40 -6,0
		z
	"
/>
<path
	id="ctr3_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 585,295
		l 0,6 50,0 0,5 10,-8 -10,-8 0,5
		z
	"
/>
<path
	id="ctr4_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 205,415
		l -5,0 8,10 8,-10 -5,0 0,-100 -6,0
		z
	"
/>
<path
	id="ctr5_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 465,415
		l -5,0 8,10 8,-10 -5,0 0,-100 -6,0
		z
	"
/>
<path
	id="ctr6_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 555,325
		l -5,0 8,-10 8,10 -5,0 0,20 400,0 0,-86 6,0 0,86
		c 0,3 -3,6 -6,6
		l -400,0
		c -3,0 -6,-3 -6,-6
		z
	"
/>


<!-- ***   trf[0] - shadows   *** -->
<path
	id="trf1_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 235,175
		l -10,0 25,-10 25,10 -10,0 0,40 -30,0
		z
	"
/>
<path
	id="trf2_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 495,175
		l -10,0 25,-10 25,10 -10,0 0,40 -30,0
		z
	"
/>
<path
	id="trf3_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#a;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 595,240
		l 0,-10 -10,25 10,25 0,-10 50,0 0,-30
		z
	"
/>
<path
	id="trf4_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 235,325
		l -10,0 25,-10 25,10 -10,0 0,100 -30,0
		z
	"
/>
<path
	id="trf5_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 495,325
		l -10,0 25,-10 25,10 -10,0 0,100 -30,0
		z
	"
/>
<path
	id="trf6_shadow"
	style="fill:#aaa;fill-rule:evenodd;stroke:#aaa;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1;filter:url(#shadow)"
	d="
		m 845,325
		l -10,0 25,-10 25,10 -10,0 0,100 -30,0
		z
	"
/>




<!-- ***   ctr[1] - officelink.hu szerver elérés   *** -->
<?php $id = "ctr1"; ?>
<path
	title="ctr1"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 200,170
		l -5,0 8,-10 8,10 -5,0 0,40 -6,0
		z
	"
/>
<text
   id="ctr1_text1"
   x="190"
   y="207"
	text-anchor="end"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>






<!-- ***   ctr[2] - kamera.officelink.hu webszerver felügyelet   *** -->
<?php $id = "ctr2"; ?>
<path
	id="ctr2"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 460,170
		l -5,0 8,-10 8,10 -5,0 0,40 -6,0
		z
	"
/>
<text
   id="ctr2_text1"
   x="450"
   y="207"
	text-anchor="end"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>








<!-- ***   ctr[3] - officelink képszerver felügyelet   *** -->
<?php $id = "ctr3"; ?>
<path
	id="ctr3"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 580,290
		l 0,6 50,0 0,5 10,-8 -10,-8 0,5
		z
	"
/>
<text
   id="ctr3_text1"
   x="640"
   y="316"
	text-anchor="end"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>




<!-- ***   ctr[4] - mk_APN kliens felügyelet   *** -->
<?php $id = "ctr4"; ?>
<path
	id="ctr4"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 200,410
		l -5,0 8,10 8,-10 -5,0 0,-100 -6,0
		z
	"
/>
<text
   id="ctr4_text1"
   x="190"
   y="322"
	text-anchor="end"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>





<!-- ***   ctr[5] - tk_APN kliens felügyelet   *** -->
<?php $id = "ctr5"; ?>
<path
	id="ctr5"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 460,410
		l -5,0 8,10 8,-10 -5,0 0,-100 -6,0
		z
	"
/>
<text
   id="ctr5_text1"
   x="450"
   y="322"
	text-anchor="end"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>



<!-- ***   ctr[6] - Felügyeleti szerver kapcsolat   *** -->
<?php $id = "ctr6"; ?>
<path
	id="ctr6"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 550,320
		l -5,0 8,-10 8,10 -5,0 0,20 400,0 0,-86 6,0 0,86
		c 0,3 -3,6 -6,6
		l -400,0
		c -3,0 -6,-3 -6,-6
		z
	"
/>
<text
   id="ctr6_text1"
   x="972"
   y="266"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>





<!-- ***   transfers arrows - wide: 30px  *** -->

<!-- ***   trf[1] - Képátvitel officelink.hu szerverre   *** -->
<?php $id = "trf1"; ?>
<path
	id="trf1"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 230,170
		l -10,0 25,-10 25,10 -10,0 0,40 -30,0
		z
	"
/>
<text
   id="trf1_text1"
   x="270"
   y="207"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>

<!-- ***   trf[2] - Képfeldolgozás kamera.officelink.hu szerveren   *** -->
<?php $id = "trf2"; ?>
<path
	id="trf2"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 490,170
		l -10,0 25,-10 25,10 -10,0 0,40 -30,0
		z
	"
/>
<text
   id="trf2_text1"
   x="530"
   y="207"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>
<!-- ***   trf[7] - Képarchiválás kamera.officelink.hu szerveren   - trf2 arrow  *** -->
<?php $id = "trf7"; ?>
<text
   id="trf2_text1"
   x="530"
   y="187"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>


<!-- ***   trf[3] - officelink képek átvitele kamera.officelink.hu képszerve   *** -->
<?php $id = "trf3"; ?>
<path
	id="trf3"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 590,235
		l 0,-10 -10,25 10,25 0,-10 50,0 0,-30
		z
	"
/>
<text
   id="trf3_text1"
   x="640"
   y="225"
	text-anchor="end"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>

<!-- ***   trf[4] - Képfeltöltés ellenőrzése (mk_APN kliensek) kamera....   *** -->
<?php $id = "trf4"; ?>
<path
	id="trf4"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 230,320
		l -10,0 25,-10 25,10 -10,0 0,100 -30,0
		z
	"
/>
<text
   id="trf4_text1"
   x="270"
   y="415"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>

<!-- ***   trf[5] - Képfeltöltés ellenőrzése (telekom_APN kliensek) kamerák   *** -->
<?php $id = "trf5"; ?>
<path
	id="trf5"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 490,320
		l -10,0 25,-10 25,10 -10,0 0,100 -30,0
		z
	"
/>
<text
   id="trf5_text1"
   x="530"
   y="415"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>

<!-- ***   trf[6] - Képfeltöltés ellenőrzése (officelink kamerák) kamerák   *** -->
<?php $id = "trf6"; ?>
<path
	id="trf6"
	style="fill:<?=$status_color[$id]?>;fill-rule:evenodd;stroke:#666;stroke-width:1;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
	d="
		m 840,320
		l -10,0 25,-10 25,10 -10,0 0,100 -30,0
		z
	"
/>
<text
   id="trf6_text1"
   x="880"
   y="415"
   	style="font-family:courier;font-size:13;stroke:<?=$last_run_color[$id]?>;stroke-width:0.6"
>
	<title><?php echo "(".$id.") ".$name_hu[$id]."<br>".$value_description_hu[$id]."<br>".$value[$id].$value_unit[$id]; ?></title>
	<?php echo $value[$id].$value_unit[$id]; ?>
</text>









<!-- ***   end of connections   *** -->




	Sorry, your browser does not support inline SVG.

</svg>




<footer style="width:1150px;height:20px;padding-left:14px;padding-top:7px;padding-right:14px;background-color:<?=$st2c[1]?>;border:5px;border-radius:5px;
	font-family:courier;font-style:normal;font-weight:normal;font-size:10px;line-height:1.25;fill:#666">
	<div style="float:left">Minden információ a <a href="http://officelink.hu/" target="_blank"> OfficeLink Kft. </a> tulajdona.</div>
	<div style="float:right">2019 @ OfficeLink Kft.</div>
</footer>

<br><br>
<?php

#echo "Status, colour:<br>";
for ($st2 = 0; $st2 <= 6; $st2++) {
	echo '<div style="width:146px;height:15px;float:left;margin-bottom:20px;padding:11px;background-color:'.$st2c[$st2].';font-family:courier;font-size:10px">Status: ('.$st2.') '.$st2n[$st2].' </div>';
}

?>

</div>
</body>
</html>
