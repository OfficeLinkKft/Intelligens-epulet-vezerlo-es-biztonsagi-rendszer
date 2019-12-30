<?php
	session_start();
	if( $_SESSION['owner'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../../common_files/connect.php";

	$ptitle = "InVR &#9832; Kliens adatlap";
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
	<link rel="stylesheet" href="ds.css" type="text/css">
	<link rel="stylesheet" href="../../common_files/common.css" type="text/css">
	<link rel="icon" href="../../common_files/img/officelink_favicon.png" type="image/png" sizes="16x16">

<?php
	// ******  user ******
	$user = $_SESSION['nick'];
	//	echo $user."<br><br>";


    // ***   paraméter fogadás url-ben: változó neve, értékadás   ***
    $ids=$_GET["id"];          // clients id
	$id = $ids."%";			# select group of client
	//echo "id: ".$id; //for test

	// ***   client's datasheet   ***
//	$result = mysqli_query("SELECT * FROM clients6 WHERE id like '$id'");
//	while($row = mysqli_fetch_array($result))
//	{
//    		$id = $row['id'];
//    		$name = $row['name'];
//     		$web = $row['web'];
//    		$server = $row['server'];
//    		$status = $row['status'];
//    		$last_login = $row['last_login'];
//    		$address = $row['address'];
//    		$address_hu = $row['address_hu'];
//    		$address_en = $row['address_en'];
//    		$address_de = $row['address_de'];
//    		$GPS = $row['GPS'];
//    		$owner = $row['owner'];
//    		$owner2 = $row['owner2'];
//    		$county = $row['county'];
//    		$del_date = $row['del_date'];
//    		$contact = $row['contact'];
//    		$note = $row['note'];
//    		$OFF = $row['OFF'];
//    		$ip = $row['ip'];

    //echo "<br>FROM clients6 (".$id."): ".$name.": ".$status." / ".$owner." / ".$ip." / ".$OFF; // for test
//	}




	// ***   read product's datas   ***
     	//echo "PRODUCTS:<br>"; //for check

	$result = mysqli_query($conn,"SELECT * FROM products6");
	while($row = mysqli_fetch_array($result))
	{
    		$N = $row[N];
    		$TOP[$N] = $row[TOP];
    		$PN[$N] = $row[PN];
    		$dscr[$N] = $row[dscr];
    		$DOB[$N] = $row[DOB];
    		$orig[$N] = $row[orig];

    	//echo $N.": ".$TOP[$N]." / ".$PN[$N]." / ".$dscr[$N]." / ".$DOB[$N]." / ".$orig[$N]."<br>"; // for check
	}
  	//echo "<br>";

  	// read datas from datalist table
//	echo "Datalist - location:<br>";
//	$result = mysqli_query($conn,"SELECT * FROM datalist WHERE Name like 'location'");
//	while($row = mysqli_fetch_array($result))
//	{
//		echo $row[0].": ";

//		$Nd = 1;
//		while($row[$Nd])
//		{
//	    	$location[$Nd] = $row[$Nd];
//		    echo $location[$Nd]."; ";
//			$Nd++;
//		}
//		echo "<br>";
//	}

?>

</head>

<body>
<! ***** head  *****>
<div class="header">
	<div class="hlogo"><a href="" target="_blank"><img src="../../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></a></div>
	<div class="hname"><?=$ptitle?></div>
	<div class="hdate"><?php echo date('Y.m.d. H:i'); ?> </div>


	<! ***** inventory log  *****>
		<div class="msg">
			<a href="../../inventory/index.php?lctl=<?php echo $ids; ?>" target="_blank" title="A rendszer elemei a leltárban">Leltár</a>
		</div>
	<! ***** products list  *****>
		<div class="msg">
			<a href="client_one.php?id=<?php echo $ids; ?>" target="_blank" title="Rendszer adatlapja">Adatlap</a>
		</div>

</div>
<! ***** head  *****>


<! ***** box  *****>
<div class="box">




	<! ***** client's data *****>

		<! ***** header  *****>
			<div class="own" style="background-color:#eee;margin-top:8px;text-align:center;">&nbsp;</div>
			<div class="deliv" style="background-color:#eee;margin-top:8px;">delivery</div>
			<div class="id" style="background-color:#eee;margin-top:8px;text-align:center;">id</div>
			<div class="name" style="background-color:#eee;margin-top:8px;text-align:center;">name</div>
			<div class="lastlogin" style="background-color:#eee;margin-top:8px;text-align:center;">last_login</div>
			<div class="avail" style="background-color:#eee;margin-top:8px;text-align:center;">SLA</div>

			<div class="vpn" style="background-color:#eee;margin-top:8px;text-align:center;">vpn ip</div>
			<div class="vpnst" style="background-color:#eee;margin-top:8px;text-align:center;">vpn stat</div>
			<div class="loc" style="background-color:#eee;margin-top:8px;text-align:center;">location</div>

			<div class="note" style="background-color:#eee;margin-top:8px;text-align:center;">note</div>
			<div class="dsheet" style="background-color:#eee;margin-top:8px;text-align:center;">inv</div>
		<! ***** header  *****>

		<! ***** client  *****>
		<?php
			$result2 = mysqli_query($conn,"SELECT * FROM clients6 WHERE id like '$id'");	//for list
			while($row = mysqli_fetch_array($result2))
			{
		?>
			<! ***** client  *****>
			<div class="client">

				<?php
					if($row['server']=="s4") 			/* s4 server | vpn - white */
						$bgc="background-color:White;color:Black;";
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
					elseif($row['owner']=='q') 			/* OL */
						$png="officelink_logo.png";
					elseif($row['owner']=='MK') 		/* MK road */
						$png="road.png";
					elseif($row['owner']=='MKa') 		/* MK highway */
						$png="highway.png";
					else						 		/* more systems */
						$png="dev.png";
				?>
					<img src="../../common_files/img/<?php echo $png; ?>" width="12" height="11">
				</div>
				<div class="deliv" style=<?php echo $bgc;?>;><?php echo $row['del_date']; ?></div>
				<div class="id" style=<?php echo $bgc;?>;><?php echo "<a href='http://".$row['web']."' target='_blank' title='jump to client'><b>".$row['id']."</b></a>"; ?></div>
				<div class="name" style=<?php echo $bgc;?>;><?php echo "<a href='http://".$row['web']."' target='_blank' title='jump to client'><b>".$row['name']."</b></a>"; ?></div>

				<div class="lastlogin" style=<?php echo $bgc;?>;>
				<?php
					$adridl = "../index.php?usr=$usr&s=1&q=1&rd=1&hw=1&dev=1&MKn=1&log=1&log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&idl=".$row['id'];
					echo "<a href='".$adridl."' target='_blank' title='log of the client'><b>".$row['last_login']."</b></a>";
				?>
				</div>
				<div class="avail" style=<?php echo $bgc;?>;><?php echo $row['avail28days']."%"; ?></div>


				<?php
					if($row['vpn_ping']=="") 				/* same as s0 status color */
						$bgcv=$bgc;
					elseif($row['vpn_ping']=="down") 		/* vpn exist, but connection down */
						$bgcv="background-color:#ff5555;color:Black;";
					elseif($row['vpn_ping']<100) 			/* fast connection */
						$bgcv="background-color:#b3d9ff;";
					elseif($row['status']<200) 				/* slow connection */
						$bgcv="background-color:#80bfff;";
					elseif($row['status']<1000) 			/* very slow connection */
						$bgcv="background-color:#1a8cff;color:Black;";
					else					 				/* same as s0 status color */
						$bgcv=$bgc;
				?>


				<div class="vpn" style=<?php echo $bgcv;?>;>&nbsp;<?php echo "<a href='' title='open terminal'><b>".$row['vpn_ip']."</b></a>"; ?></div>

				<div class="vpnst" style=<?php echo $bgcv;?>;>
				<?php

					$adridl = "../index.php?usr=$usr&s=1&q=1&rd=1&hw=1&dev=1&MKn=1&log=1&log0=1&log1=1&log2=1&log3=1&log40=1&log41=1&log42=1&log43=1&idl=".$row['id'];

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
					&nbsp;<?php echo "<a href='' title='open map'><b>".$row['GPS']."</b></a>"; ?>
				</div>



				<div class="note" style=<?php echo $bgc;?>;>
				<?php
					if(strlen($row[note])>12) { $note_S = substr($row[note],0,10); $note_S=$note_S."..."; } else $note_S=$row[note];
					echo "<a href='' title='".$row[note]."'>".$note_S."&nbsp;</a>";
				?>
				</div>

				<div class="dsheet" style=<?php echo $bgc;?>;>&nbsp;
					<a href="../../inventory/index.php?lctl=<?php echo $row['id']; ?>" target="_blank" title="<?php echo "inventory for this client: ".$row['id']; ?>"><img src="../../common_files/img/dsheet.png" width="10" height="12"></a>
				</div>

			</div>













			<! ***** name, web, owner  *****>
			<div class="col2a" style="width:240px";>
				Name: <b><?php echo $row['name']; ?></b><br>
				Web: <b><?php echo $row['web']; ?></b><br>
				Server: <b><?php echo $row['server']; ?></b><br>
				Owner: <b><?php echo $row['owner']; ?></b><br>
				Owner2: <b><?php echo $row['owner2']; ?></b><br>
				Contact: <b><?php echo $row['contact']; ?></b><br>
				Email: <b><?php echo $row['cont_email']; ?></b><br>
				Contact phone: <b><?php echo $row['cont_phone']; ?></b><br>
			</div>

			<! ***** vpn and client's log  *****>
			<div class="col2" style="width:300px";>
				VPN IP: <b><?php echo $row['vpn_ip']; ?></b><br>
				VPN response time: <b><?php echo $row['vpn_ping']; ?></b><br>
				Client's CPU load: <b><?php echo $row['cl_load']; ?></b><br>
				Client's hardware info: <b><?php echo $row['cl_hw_info']; ?></b><br>
				<br>
				Location: <b><?php echo $row['GPS']; ?></b><br>
				Bearing: <b><?php echo $row['bearing']; ?></b><br>
				Velocity: <b><?php echo $row['velocity']; ?></b><br>
			</div>

			<! ***** address, IP, pics  *****>
			<div class="col2" style="width:624px";>

				<div class="ppict">
				<?php
				for ($i=1; $i<=6; $i++) {
					$px = "../pics_of_clients/".$row['id']."/".$i.".jpg";
					$pxt = "../pics_of_clients/".$row['id']."/".$i."t.jpg";
					if ( file_exists ( $px ) ) {
				?>
						<a href="<?php echo $px; ?>" width="400" height="300" rel="lytebox[cam]" title="helyszín"><img src="<?php echo $pxt; ?>" width="60" height="40" title="helyszín"></a>

				<?php
					} else {
				?>
						<img src="../images/place_gra.png" width="60" height="40" title="helyszín">
				<?php
					}
				}
				?>

				</div>

				Address: <b><?php echo $row['address']; ?></b><br>
				HU Address Name: <b><?php echo $row['address_hu']; ?></b><br>
				EN Address Name: <b><?php echo $row['address_en']; ?></b><br>
				DE Address Name: <b><?php echo $row['address_de']; ?></b><br>
				County: <b><?php echo $row['county']; ?></b><br>
				GPS: <b><?php echo $row['GPS']; ?></b><br>
				Delivery Date: <b><?php echo $row['del_date']; ?></b><br>
				IP: <b><?php echo $row['ip']; ?></b><br>
			</div>

			<! ***** note  *****>
			<div class="col1">
				Note: <b><?php echo $row['note']; ?></b><br>
			</div>

		<?php
		} //end of query
		?>
		<! ***** client's data *****>


















		<! ***** client's parts  *****>

		<?php
			// set counters
			$iap = 0; 	//numbers of parts
			$n_CPU = 0;
			$n_enclosure = 0;
			$n_net = 0;
			$n_camera = 0;
			$n_mcs = 0;
			$n_another = 0;


			$result3 = mysqli_query($conn,"SELECT * FROM inv_log6 WHERE location like '$id' AND last=1");	//for list
			while($row = mysqli_fetch_array($result3))
			{ $iap++;
		?>


			<?php
			// set product's datas
				$Ni = $N;
			  	//echo " / Ni: ".$Ni."<br>";		// for test
			  	while ( ($row[PN] != $PN[$Ni]) and ($Ni > 0) ) {
			    	$Ni--; //echo "... Ni: ".$Ni."<br>";		// for test
			  	}
			  	//echo $Ni.": (".$TOP[$Ni].") ".$PN[$Ni]." - ".$dscr[$Ni]."<br>";	// for test



			// ***   set product's type and icon   ***
			switch ($TOP[$Ni]) {

			case "enclosure": $n_enclosure++;
				//$enclosure["png"][$n_enclosure] = "enclosure.png";
				$enclosure["ttl"][$n_enclosure] = "enclosure";

				$enclosure["PN"][$n_enclosure] = $row['PN'];
				$enclosure["dscr"][$n_enclosure] = $dscr[$Ni];
				$enclosure["SN"][$n_enclosure] = $row['SN'];
				$enclosure["id"][$n_enclosure] = $row['id'];
				$enclosure["name"][$n_enclosure] = $row['name'];
				$enclosure["date"][$n_enclosure] = $row['date'];
				$enclosure["operator"][$n_enclosure] = $row['operator'];
				$enclosure["status"][$n_enclosure] = $row['status'];
				$enclosure["port"][$n_enclosure] = $row['port'];
				$enclosure["ip"][$n_enclosure] = $row['ip'];
				$enclosure["warranty"][$n_enclosure] = $row['warranty'];

				// set product's status color
				switch ($enclosure["status"][$n_enclosure]) {
				    case "empty":
						$enclosure["bgc"][$n_enclosure] = "background-color:LightGreen;"; break;
					case "ok":
						$enclosure["bgc"][$n_enclosure] = "background-color:Cyan;color:Black;"; break;
					case "mcs":
						$enclosure["bgc"][$n_enclosure] = "background-color:RoyalBlue;color:Black;"; break;
					case "fail":
						$enclosure["bgc"][$n_enclosure] = "background-color:Coral;color:Black;"; break;
					case "set":
						$enclosure["bgc"][$n_enclosure] = "background-color:SkyBlue;color:Black;"; break;
					case "scrap":
						$enclosure["bgc"][$n_enclosure] = "background-color:Black;color:White;"; break;
					default:
						$enclosure["bgc"][$n_enclosure] = "background-color:White;color:Black;";
				}
				break;

			case "CPU": $n_CPU++;
				//$CPU["png"][$n_CPU] = "CPU.png";
				//$CPU["ttl"][$n_CPU] = "CPU";

				$CPU["PN"][$n_CPU] = $row['PN'];
				$CPU["dscr"][$n_CPU] = $dscr[$Ni];
				$CPU["SN"][$n_CPU] = $row['SN'];
				$CPU["id"][$n_CPU] = $row['id'];
				$CPU["name"][$n_CPU] = $row['name'];
				$CPU["date"][$n_CPU] = $row['date'];
				$CPU["operator"][$n_CPU] = $row['operator'];
				$CPU["status"][$n_CPU] = $row['status'];
				$CPU["port"][$n_CPU] = $row['port'];
				$CPU["ip"][$n_CPU] = $row['ip'];
				$CPU["warranty"][$n_CPU] = $row['warranty'];

				// set product's status color
				switch ($CPU["status"][$n_CPU]) {
				    case "empty":
						$CPU["bgc"][$n_CPU] = "background-color:LightGreen;"; break;
					case "ok":
						$CPU["bgc"][$n_CPU] = "background-color:Cyan;color:Black;"; break;
					case "mcs":
						$CPU["bgc"][$n_CPU] = "background-color:RoyalBlue;color:Black;"; break;
					case "fail":
						$CPU["bgc"][$n_CPU] = "background-color:Coral;color:Black;"; break;
					case "set":
						$CPU["bgc"][$n_CPU] = "background-color:SkyBlue;color:Black;"; break;
					case "scrap":
						$CPU["bgc"][$n_CPU] = "background-color:Black;color:White;"; break;
					default:
						$CPU["bgc"][$n_CPU] = "background-color:White;color:Black;";
				}
				break;

			case "net": $n_net++;
				//$net["png"][$n_net] = "net.png";
				//$net["ttl"][$n_net] = "net";

				$net["PN"][$n_net] = $row['PN'];
				$net["dscr"][$n_net] = $dscr[$Ni];
				$net["SN"][$n_net] = $row['SN'];
				$net["id"][$n_net] = $row['id'];
				$net["name"][$n_net] = $row['name'];
				$net["date"][$n_net] = $row['date'];
				$net["operator"][$n_net] = $row['operator'];
				$net["status"][$n_net] = $row['status'];
				$net["port"][$n_net] = $row['port'];
				$net["ip"][$n_net] = $row['ip'];
				$net["warranty"][$n_net] = $row['warranty'];

				// set product's status color
				switch ($net["status"][$n_net]) {
				    case "empty":
						$net["bgc"][$n_net] = "background-color:LightGreen;"; break;
					case "ok":
						$net["bgc"][$n_net] = "background-color:Cyan;color:Black;"; break;
					case "mcs":
						$net["bgc"][$n_net] = "background-color:RoyalBlue;color:Black;"; break;
					case "fail":
						$net["bgc"][$n_net] = "background-color:Coral;color:Black;"; break;
					case "set":
						$net["bgc"][$n_net] = "background-color:SkyBlue;color:Black;"; break;
					case "scrap":
						$net["bgc"][$n_net] = "background-color:Black;color:White;"; break;
					default:
						$net["bgc"][$n_net] = "background-color:White;color:Black;";
				}
				break;

			case "camera": $n_camera++;
				//$camera["png"][$n_camera] = "camera.png";
				//$camera["ttl"][$n_camera] = "camera";

				$camera["PN"][$n_camera] = $row['PN'];
				$camera["dscr"][$n_camera] = $dscr[$Ni];
				$camera["SN"][$n_camera] = $row['SN'];
				$camera["id"][$n_camera] = $row['id'];
				$camera["name"][$n_camera] = $row['name'];
				$camera["date"][$n_camera] = $row['date'];
				$camera["operator"][$n_camera] = $row['operator'];
				$camera["status"][$n_camera] = $row['status'];
				$camera["port"][$n_camera] = $row['port'];
				$camera["ip"][$n_camera] = $row['ip'];
				$camera["warranty"][$n_camera] = $row['warranty'];

				// set product's status color
				switch ($camera["status"][$n_camera]) {
				    case "empty":
						$camera["bgc"][$n_camera] = "background-color:LightGreen;"; break;
					case "ok":
						$camera["bgc"][$n_camera] = "background-color:Cyan;color:Black;"; break;
					case "mcs":
						$camera["bgc"][$n_camera] = "background-color:RoyalBlue;color:Black;"; break;
					case "fail":
						$camera["bgc"][$n_camera] = "background-color:Coral;color:Black;"; break;
					case "set":
						$camera["bgc"][$n_camera] = "background-color:SkyBlue;color:Black;"; break;
					case "scrap":
						$camera["bgc"][$n_camera] = "background-color:Black;color:White;"; break;
					default:
						$camera["bgc"][$n_camera] = "background-color:White;color:Black;";
				}
				break;


			case "mcs": $n_mcs++;
				//$mcs["png"][$n_mcs] = "mcs.png";
				//$mcs["ttl"][$n_mcs] = "mcs";

				$mcs["PN"][$n_mcs] = $row['PN'];
				$mcs["dscr"][$n_mcs] = $dscr[$Ni];
				$mcs["SN"][$n_mcs] = $row['SN'];
				$mcs["id"][$n_mcs] = $row['id'];
				$mcs["name"][$n_mcs] = $row['name'];
				$mcs["date"][$n_mcs] = $row['date'];
				$mcs["operator"][$n_mcs] = $row['operator'];
				$mcs["status"][$n_mcs] = $row['status'];
				$mcs["port"][$n_mcs] = $row['port'];
				$mcs["ip"][$n_mcs] = $row['ip'];
				$mcs["warranty"][$n_mcs] = $row['warranty'];

				// set product's status color
				switch ($mcs["status"][$n_mcs]) {
				    case "empty":
						$mcs["bgc"][$n_mcs] = "background-color:LightGreen;"; break;
					case "ok":
						$mcs["bgc"][$n_mcs] = "background-color:Cyan;color:Black;"; break;
					case "mcs":
						$mcs["bgc"][$n_mcs] = "background-color:RoyalBlue;color:Black;"; break;
					case "fail":
						$mcs["bgc"][$n_mcs] = "background-color:Coral;color:Black;"; break;
					case "set":
						$mcs["bgc"][$n_mcs] = "background-color:SkyBlue;color:Black;"; break;
					case "scrap":
						$mcs["bgc"][$n_mcs] = "background-color:Black;color:White;"; break;
					default:
						$mcs["bgc"][$n_mcs] = "background-color:White;color:Black;";
				}
				break;

			default: $n_another++;
				//$another["png"][$n_another] = "broken.png";
				//$another["ttl"][$n_another] = "another";

				$another["PN"][$n_another] = $row['PN'];
				$another["dscr"][$n_another] = $dscr[$Ni];
				$another["SN"][$n_another] = $row['SN'];
				$another["id"][$n_another] = $row['id'];
				$another["name"][$n_another] = $row['name'];
				$another["date"][$n_another] = $row['date'];
				$another["operator"][$n_another] = $row['operator'];
				$another["status"][$n_another] = $row['status'];
				$another["port"][$n_another] = $row['port'];
				$another["ip"][$n_another] = $row['ip'];
				$another["warranty"][$n_another] = $row['warranty'];

				// set product's status color
				switch ($another["status"][$n_another]) {
				    case "empty":
						$another["bgc"][$n_another] = "background-color:LightGreen;"; break;
					case "ok":
						$another["bgc"][$n_another] = "background-color:Cyan;color:Black;"; break;
					case "another":
						$another["bgc"][$n_another] = "background-color:RoyalBlue;color:Black;"; break;
					case "fail":
						$another["bgc"][$n_another] = "background-color:Coral;color:Black;"; break;
					case "set":
						$another["bgc"][$n_another] = "background-color:SkyBlue;color:Black;"; break;
					case "scrap":
						$another["bgc"][$n_another] = "background-color:Black;color:White;"; break;
					default:
						$another["bgc"][$n_another] = "background-color:White;color:Black;";
				}
			}
			?>
		<?php
		} //end of query ?>




		<!-- ***   display it - first raw  *** -->

			<! ***** CPU  *****>
			<div class="col3a" style="<?php echo $CPU["bgc"][1];?>";>
				<img src="../../common_files/img/CPU.png" title="Type Of Product: CPU"  width="12" height="12"> - <?php echo $CPU["dscr"][1]; ?>
				<div style="float:right;";><?php echo $i."/".$n_CPU; ?></div><br>

				CPU: <b><?php echo $CPU["PN"][1]; ?></b><br>
				CPU_SN: <b><?php echo $CPU["SN"][1]; ?></b><br>
				CPU delivery: <b><?php echo $CPU["date"][1]; ?></b><br>
				CPU warranty: <b><?php echo $CPU["warranty"][1]; ?></b><br>
				CPU id: <b><?php echo $CPU["id"][1]; ?></b><br>
				CPU name: <b><?php echo $CPU["name"][1]; ?></b><br>
				CPU port: <b><?php echo $CPU["port"][1]; ?></b> / ip: <b><?php echo $CPU["ip"][1]; ?></b><br>

				CPU status: <b><?php echo $CPU["status"][1]; ?></b> / operator: <b><?php echo $CPU["operator"][1]; ?></b><br>
			</div>

			<! ***** net, that is no SIM *****>
			<?php
			$i = 1;
			while ($n_net>=$i) {
			if ($net["PN"][$i]=="SIM") {
			} else {
			?>
			<! ***** net, without SIM *****>
			<div class="col3" style="<?php echo $net["bgc"][$i];?>";>
				<img src="../../common_files/img/net.png" title="Type Of Product: net"  width="12" height="12"> - <?php echo $net["dscr"][$i]; ?>
				<div style="float:right;";><?php echo $i."/".$n_net; ?></div><br>

				net: <b><?php echo $net["PN"][$i]; ?></b><br>
				net_SN: <b><?php echo $net["SN"][$i]; ?></b><br>
				net delivery: <b><?php echo $net["date"][$i]; ?></b><br>
				net warranty: <b><?php echo $net["warranty"][$i]; ?></b><br>
				net id: <b><?php echo $net["id"][$i]; ?></b><br>
				net name: <b><?php echo $net["name"][$i]; ?></b><br>
				net ip: <b><?php echo $net["ip"][$i]; ?></b><br>

				net status: <b><?php echo $net["status"][$i]; ?></b> / operator: <b><?php echo $net["operator"][$i]; ?></b><br>
			</div>
			<?php
			} $i++; }
			?>

			<! ***** net, only SIM *****>
			<?php
			$i = 1;
			while ($n_net>=$i) {
			if ($net["PN"][$i]=="SIM") {
			?>
			<! ***** net, only SIM *****>
			<div class="col3" style="<?php echo $net["bgc"][$i];?>";>
				<img src="../../common_files/img/net.png" title="Type Of Product: net"  width="12" height="12"> - <?php echo $net["dscr"][$i]; ?>
				<div style="float:right;";><?php echo $i."/".$n_net; ?></div><br>

				net: <b><?php echo $net["PN"][$i]; ?></b><br>
				net_SN: <b><?php echo $net["SN"][$i]; ?></b><br>
				net delivery: <b><?php echo $net["date"][$i]; ?></b><br>
				net warranty: <b><?php echo $net["warranty"][$i]; ?></b><br>
				net id: <b><?php echo $net["id"][$i]; ?></b><br>
				net name: <b><?php echo $net["name"][$i]; ?></b><br>
				net ip: <b><?php echo $net["ip"][$i]; ?></b><br>

				net status: <b><?php echo $net["status"][$i]; ?></b> / operator: <b><?php echo $net["operator"][$i]; ?></b><br>
			</div>
			<?php
			} $i++; }
			?>

			<! ***** enclosures  *****>
			<div class="col3" style="<?php echo $enclosure["bgc"][1];?>";>
				<img src="../../common_files/img/enclosure.png" title="Type Of Product: enclosure"  width="12" height="12"> - <?php echo $enclosure["dscr"][1]; ?>
				<div style="float:right;";><?php echo $i."/".$n_enclosure; ?></div><br>

				enclosure: <b><?php echo $enclosure["PN"][1]; ?></b><br>
				enclosure_SN: <b><?php echo $enclosure["SN"][1]; ?></b><br>
				enclosure delivery: <b><?php echo $enclosure["date"][1]; ?></b><br>
				enclosure warranty: <b><?php echo $enclosure["warranty"][1]; ?></b><br>
				enclosure id: <b><?php echo $enclosure["id"][1]; ?></b><br>
				enclosure name: <b><?php echo $enclosure["name"][1]; ?></b><br>

				enclosure status: <b><?php echo $enclosure["status"][1]; ?></b> / operator: <b><?php echo $enclosure["operator"][1]; ?></b><br>
			</div>



		<!-- ***   display it - raws  *** -->

			<! ***** mcs modules  *****>
		<?php
			$i = 1;
			while ($n_mcs>=$i) {
		?>
			<div class="col1" style="<?php echo $mcs["bgc"][$i];?>";>
				<img src="../../common_files/img/mcs.png" title="Type Of Product: mcs"  width="12" height="12">
				<b><?php echo $mcs["PN"][$i]; ?></b> (<?php echo $mcs["dscr"][$i]; ?>) / SN: <b><?php echo $mcs["SN"][$i]; ?></b> / port: <b><?php echo $mcs["port"][$i]; ?></b> / ip: <b><?php echo $mcs["ip"][$i]; ?></b> / id: <b><?php echo $mcs["id"][$i]; ?></b> / name: <b><?php echo $mcs["name"][$i]; ?></b>
				<div style="float:right;";><?php echo $i."/".$n_mcs; ?></div><br>
				status: <b><?php echo $mcs["status"][$i]; ?></b> / operator: <b><?php echo $mcs["operator"][$i]; ?></b> / delivery: <b><?php echo $mcs["date"][$i]; ?></b> / warranty: <b><?php echo $mcs["warranty"][$i]; ?></b><br>
			</div>
		<?php
			$i++; }
		?>
			<! ***** mcs modules  *****>


			<! ***** camera  *****>
		<?php
			$i = 1;
			while ($n_camera>=$i) {
		?>
			<div class="col1" style="<?php echo $camera["bgc"][$i];?>";>

				<img src="../../common_files/img/camera.png" title="Type Of Product: camera"  width="12" height="12">
				<b><?php echo $camera["id"][$i]; ?></b> direction: <b><?php echo $camera["name"][$i]; ?></b> / <?php echo $camera["PN"][$i]; ?> (<?php echo $camera["SN"][$i]; ?>) - ip: <b><?php echo $camera["ip"][$i]; ?></b>
				<div style="float:right;";><?php echo $i."/".$n_camera; ?></div><br>
				status: <b><?php echo $camera["status"][$i]; ?></b> / operator: <b><?php echo $camera["operator"][$i]; ?></b> / delivery: <b><?php echo $camera["date"][$i]; ?></b> / warranty: <b><?php echo $camera["warranty"][$i]; ?></b><br>
			</div>
		<?php
			$i++; }
		?>

			<! ***** camera  *****>


			<! ***** another modules  *****>
		<?php
			$i = 1;
			while ($n_another>=$i) {
		?>
			<div class="col1" style="<?php echo $another["bgc"][$i];?>";>
				<img src="../../common_files/img/broken.png" title="Type Of Product: another parts"  width="12" height="12">
				<b><?php echo $another["PN"][$i]; ?></b> (<?php echo $another["dscr"][$i]; ?>) / SN: <b><?php echo $another["SN"][$i]; ?></b> / port: <b><?php echo $another["port"][$i]; ?></b> / ip: <b><?php echo $another["ip"][$i]; ?></b> / id: <b><?php echo $another["id"][$i]; ?></b> / name: <b><?php echo $another["name"][$i]; ?></b>
				<div style="float:right;";><?php echo $i."/".$n_another; ?></div><br>
				status: <b><?php echo $another["status"][$i]; ?></b> / operator: <b><?php echo $another["operator"][$i]; ?></b> / delivery: <b><?php echo $another["date"][$i]; ?></b> / warranty: <b><?php echo $another["warranty"][$i]; ?></b><br>
			</div>
		<?php
			$i++; }
		?>
			<! ***** another modules  *****>






		<! ***   display it   *** >
		<! ***** client's parts *****>

















	<! ***** datasheet *****>

	<! ***** footer  *****>
	<div style="margin-top:2px;text-align:center;font-weight:bold;color:#2d6f8d;clear:both;">
		<?php echo "<br>Elemek száma: ".$iap." db"; ?>
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
