<?php
	session_start();
#	if( $_SESSION['nick'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../../common_files/connect.php";

	$ptitle = "InVR &#9832; Rendszer adatlap módosítás";
?>

<!DOCTYPE HTML>

<html>
<head>
	<title><?=$ptitle?></title>

	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="hu-hu" />

	<script type="text/javascript" language="javascript" src="lytebox.js"></script>

	<link rel="stylesheet" href="lytebox.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="act_client.css" type="text/css">
	<link rel="icon" href="../../common_files/img/officelink_favicon.png" type="image/png" sizes="16x16">

</head>


<body>
 
<?php

##   ***   POST-ed data    ***
if ($_SERVER["REQUEST_METHOD"] == "POST") { # posted datas?

	# ***   GET id   ***
	$id=$_GET["id"];

		#  ***   part's datas from clients table  ***
		echo "<h3>getting datas for this item from database: ".$id."</h3>";
		$result = mysqli_query($conn,"SELECT * FROM clients6 WHERE id like '$id'");	
		while($row = mysqli_fetch_array($result)) {
	  		$name = $row['name'];
	  	  	$web = $row['web'];
	  	  	$server = $row['server'];
	  	   	$address = $row['address'];
			$GPS = $row['GPS']; 
			$county = $row['county'];
			$del_date = $row['del_date'];
			$comm = $row['comm'];
	  	  	$owner = $row['owner'];
	  	  	$owner2 = $row['owner2'];
			$contact = $row['contact'];
			$cont_phone = $row['cont_phone'];
			$cont_email = $row['cont_email'];
			$note = $row['note'];
			$ip = $row['ip'];
			$vpn_ip = $row['vpn_ip'];
			$OFF = $row['OFF'];

	  	  	$status = $row['status'];
	  	  	$duration = $row['duration'];
	  	  	$avail28days = $row['avail28days'];
	  	  	$last_login = $row['last_login'];
	  	  	$last_status_change = $row['last_status_change'];
			$vpn_ping = $row['vpn_ping'];
			$ConnectedSince = $row['ConnectedSince'];
			$vpn_last = $row['vpn_last'];
			$cl_load = $row['cl_load'];
			$cl_disk = $row['cl_disk'];
			$bearing = $row['bearing'];
			$velocity = $row['velocity'];	
			#echo "from clients id/name: ".$id." / ".$name." / ".$del_date;	# for test
		}
		#  ***   part's datas from clients table  ***


	## ***   called from outside: client_one  
		if ( isset($_POST["edit"]) ) { 
			echo "<h1>Called from client_one.php / id=".$id." / edit=".$_POST['edit']."</h1>";	## check call inv_onv.php


	## ***   submitted data - on / OFF | szünet / on -> write to db
		} elseif ( isset($_POST['onOFF']) ) {	

			## system OFF = OFF
			if ($_POST['onOFF']=="on") { 
				$OFF = "on";
				#  ***   datas to database   ***
				mysqli_query($conn,"UPDATE clients6 SET OFF='on' WHERE id='$id'");

				# ***   display the result on inv_one.php   ***
				echo "<meta http-equiv='Refresh' content='1;url=client_one.php?id=$id'>activity! (".$id.") ".$name." -> move along...<br>";

			## system OFF = on
			} elseif ($_POST['onOFF']=="szünet") { 
				$OFF = "OFF";
				#  ***   datas to database   ***
				mysqli_query($conn,"UPDATE clients6 SET OFF='OFF' WHERE id='$id'");

				# ***   display the result on inv_one.php   ***
				echo "<meta http-equiv='Refresh' content='1;url=client_one.php?id=$id'>activity! (".$id.") ".$name." -> move along...<br>";

			}
			echo "<h1>Filled up?: YES / ".$_POST['onOFF']." - id: ".$id."</h1>"; ## check call self


	## ***   submitted data - check, if ok write to db
		} elseif ( isset($_POST['ok']) AND ($_POST["ok"]=="ok") ) {	

			echo "<h1>Filled up?: YES / ".$_POST["ok"]." submitted data - id: ".$id."</h1>"; ## check call self

			# POSTed datas
			if ( isset($_POST["name_i"]) ) $name = $_POST["name_i"];
			if ( isset($_POST["web_i"]) ) $web = $_POST["web_i"];
			if ( isset($_POST["server_i"]) ) $server = $_POST["server_i"];
			if ( isset($_POST["address_i"]) ) $address = $_POST["address_i"];
			if ( isset($_POST["GPS_i"]) ) $GPS = $_POST["GPS_i"];
			if ( isset($_POST["county_i"]) ) $county = $_POST["county_i"];
			if ( isset($_POST["comm_i"]) ) $comm = $_POST["comm_i"];
			if ( isset($_POST["owner_i"]) ) $owner = $_POST["owner_i"];
			if ( isset($_POST["owner2_i"]) ) $owner2 = $_POST["owner2_i"];
			if ( isset($_POST["contact_i"]) ) $contact = $_POST["contact_i"];	
			if ( isset($_POST["cont_phone_i"]) ) $cont_phone = $_POST["cont_phone_i"];	
			if ( isset($_POST["cont_email_i"]) ) $cont_email = $_POST["cont_email_i"];	
			if ( isset($_POST["note_i"]) ) $note = $_POST["note_i"];	
			if ( isset($_POST["ip_i"]) ) $ip = $_POST["ip_i"];	
			if ( isset($_POST["vpn_ip_i"]) ) $vpn_ip = $_POST["vpn_ip_i"];

			#  ***   test   ***
			echo "<b>Your input from the FORM:</b><br>";
			echo "name: <b>".$name."</b><br>";

			#  ***   datas to database   ***
			mysqli_query($conn,"UPDATE clients6 SET 
				name = '$name',
				web = '$web',
				server = '$server',
				address = '$address',
				GPS = '$GPS',
				county = '$county',
				comm = '$comm',
				owner = '$owner',
				owner2 = '$owner2',
				contact = '$contact',
				cont_phone = '$cont_phone',
				cont_email = '$cont_email',
				note = '$note',
				ip = '$ip',
				vpn_ip = '$vpn_ip' 

			WHERE id='$id'");

			# ***   display the result on inv_one.php   ***
			echo "<meta http-equiv='Refresh' content='5;url=client_one.php?id=$id'>activity! (".$id.") ".$name." -> move along...<br>";

		}
} 
##   ***   POST-ed data    ***
?>













<! ***** head  ***** >
<div class="header">
	<div class="hlogo"><img src="../../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></div>
	<div class="hname">Rendszer adatlap szerkesztés</div>
	<div class="hdate"><?php echo date('Y.m.d. H:i'); ?> </div>
</div>
<! ***** head  ***** >


<! ***** box  ***** >
<div class="box">
<br>


<!-- ***   form   *** -->
<table class=table>

	<?php # set status color
		if($OFF=="OFF") 				/* system OFF - gray */
			$bgc="background-color:Gray;color:Black;";
		elseif($status==1) 			/* OK */
			$bgc="background-color:#cccccc;";
		elseif($status==2) 			/* 15min - yellow */
			$bgc="background-color:Yellow;";
		elseif($status==0) 			/* 24hour - red */
			$bgc="background-color:Tomato;color:Black;";
		elseif($status==3) 			/* no power - blue */
			$bgc="background-color:DeepSkyBlue;color:Black;";
		elseif($status==40) 			/* connection down */
#		elseif($vpn_ping=="no") 		/* vpn exist, but connection down */
			$bgc="background-color:#ff5555;color:Black;";
		elseif($status==41) 			/* fast connection */
#		elseif($vpn_ping<1000) 			/* fast connection */
			$bgc="background-color:#b3d9ff;";
#		elseif($vpn_ping<200) 				/* slow connection */
#			$bgc="background-color:#80bfff;";
#		elseif($vpn_ping<1000) 			/* very slow connection */
#			$bgc="background-color:#1a8cff;color:Black;";
		else
			$bgc="background-color:White;color:Black;";
#		echo "status/bgc= ".$status." / ".$bgc;
	?>

  <tr> 
    <td align="center" style=<?php echo $bgc;?>;><b>

     	<table><tr>
			<td width="370" align="center" style=<?php #echo $bgc;?>>
	  			<?php echo 
					"<h3>(".$id.") ".$name."</h3>
					státusz: ".$status." / utolsó adat: ".$vpn_last."<br>"
					.$vpn_ping."  | load: ".$cl_load."  | disk free: ".$cl_disk
				;?>
			</td><td width="70" align="center" style=<?php #echo $bgc;?>;>
				<img src="../../common_files/img/officelink_logo.png" height="30" border="0" title="OfficeLink logo">
			</td>
		</tr></table>

    </b></td>
  </tr>

  <tr> 
    <td valign="top" style="background-color:Gray">

    <form method="post" action="<?php echo 'act_client.php?id='.$id; ?>"> 
	<table>
		
        <tr><td width="120">
			id: <br>
        </td><td width="320">
			<b>&nbsp;&nbsp;<?php echo $id;?></b>
       	</td></tr>

        <tr><td>
			Név: <br>
        </td><td>
			<input list="name" name="name_i"  value="<?php echo $name; ?>">
       	</td></tr>

        <tr><td>
			Webcím: 
        </td><td>
			<input list="web" name="web_i"  value="<?php echo $web; ?>">
       </td></tr>

       <tr><td>
			Cím: 
        </td><td>
			<input list="address" name="address_i"  value="<?php echo $address; ?>">
        </td></tr>

        <tr><td>
          	GPS (long,lat):
        </td><td>
			<input list="GPS" name="GPS_i"  value="<?php echo $GPS; ?>">
        </td></tr>

		<tr><td>
			Megye: 
        </td><td>
			<input list="county" name="county_i"  value="<?php echo $county; ?>">
        </td></tr>

        <tr><td>
			Üzembe helyezve: 
        </td><td>
			<b>&nbsp;&nbsp;<?php echo $del_date; ?></b>
        </td></tr>

        <tr><td>
			Hálózati kapcsolat: 
        </td><td>
			<input list="comm" name="comm_i"  value="<?php echo $comm; ?>">
        </td></tr>

        <tr><td>
			Tulajdonos: 
        </td><td>
			<input list="owner" name="owner_i"  value="<?php echo $owner; ?>">
        </td></tr>

        <tr><td>
			Kapcsolattartó: 
        </td><td>
			<input list="contact" name="contact_i"  value="<?php echo $contact; ?>">
        </td></tr>

        <tr><td>
			Telefonszám: 
        </td><td>
			<input list="cont_phone" name="cont_phone_i"  value="<?php echo $cont_phone; ?>">
        </td></tr>

        <tr><td>
			Email cím: 
        </td><td>
			<input list="cont_email" name="cont_email_i"  value="<?php echo $cont_email; ?>">
        </td></tr>

        <tr><td>
			Megjegyzés: 
        </td><td>
			<input list="note" name="note_i"  value="<?php echo $note; ?>">
        </td></tr>

        <tr><td>
			IP cím (publikus): 
        </td><td>
			<input list="ip" name="ip_i"  value="<?php echo $ip; ?>">
        </td></tr>

        <tr><td>
			VPN IP cím: 
        </td><td>
			<input list="vpn_ip" name="vpn_ip_i"  value="<?php echo $vpn_ip; ?>">
        </td></tr>


        <tr><td>
			Felügyelet: 
        </td><td>
			<b>&nbsp;&nbsp;<?php if ($OFF=="on") echo "on"; else echo "szünet"; ?></b>
        </td></tr>

		<tr><td>
			Operátor: 
		</td><td>
			<b>&nbsp;&nbsp;<?php echo $_SESSION['nick']; ?></b>
		</td></tr>






		<tr><td colspan=2 style="border:0px;">
			<div style="text-align:right;color:red">
			<?php
				if ( $end_message!="" ) {
					echo $end_message; 				# set button message
				}			

				# OFF = on - system run / OFF system pause
				if ($OFF=="OFF") $pause = "on"; else $pause = "szünet";
				echo '<input style="width:90px;margin:10px;margin-right:30px;padding:5px;font-weight:bold;" type="submit" name="onOFF" value="'.$pause.'">';

				echo '<input style="width:70px;margin:10px;padding:5px;font-weight:bold;" type="submit" name="ok" value="ok">';
			?>

			</div>
		</td></tr>




	</table>
	</form>

	</td>
  </tr>

</table>





</form>
<!-- ***   end of form   *** -->


<br>
</div>
<! ***** box  ***** >






<! ***** impressum  ***** >
<div class="impr">
	<div class="imp1"></div>
	<div class="imp2">2019 @ OfficeLink Kft</div>
</div>
<! ***** impressum  ***** >





<?php
  	mysqli_close($conn);	# ***   closing database connection   ***
?>



</body>
</html>

<?php 
	ob_end_flush();
?>
