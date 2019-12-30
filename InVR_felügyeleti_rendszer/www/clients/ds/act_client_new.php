<?php
	session_start();
#	if( $_SESSION['nick'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../../common_files/connect.php";

	$ptitle = "InVR &#9832; Új rendszer";
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

	## ***   submitted data - check, if ok write to db
		if ( isset($_POST['ok']) AND ($_POST['ok']=="mehet") ) {	

			#echo "<h3>submitted: ".$_POST['ok']."! | ".$_POST['id_i']."</h3>";

			## check system id
			if ( $_POST['id_i'] != "" ) { ## id not empty
				$id_i = $_POST['id_i'];
			
				## system id exist?
				$result = mysqli_query($conn,"SELECT * FROM clients6 WHERE id LIKE '$id_i'");	
				while($row = mysqli_fetch_array($result)) { # check all system id

					$id_exist = $row['id'];
					$name_exist = $row['name'];
					#echo "<h3>".$id_exist." | ".$name_exist." exist! </h3r>";	#check

					$end_message = "Rendszer id (<b>".$id_exist."</b>) már létezik, válassz másikat!";
				} 

				## system id not exist
				if ($id_i!=$id_exist) { 

					# POSTed datas
					$id = $_POST["id_i"];
					if ( isset($_POST["name_i"]) ) $name = $_POST["name_i"];
					if ( isset($_POST["web_i"]) ) $web = $_POST["web_i"];
					if ( isset($_POST["server_i"]) ) $server = $_POST["server_i"];
					if ( isset($_POST["address_i"]) ) $address = $_POST["address_i"];
					if ( isset($_POST["GPS_i"]) ) $GPS = $_POST["GPS_i"];
					if ( isset($_POST["county_i"]) ) $county = $_POST["county_i"];
					if ( isset($_POST["del_date_i"]) ) $del_date = $_POST["del_date_i"];
					if ( isset($_POST["comm_i"]) ) $comm = $_POST["comm_i"];
					if ( isset($_POST["owner_i"]) ) $owner = $_POST["owner_i"];
					if ( isset($_POST["owner2_i"]) ) $owner2 = $_POST["owner2_i"];
					if ( isset($_POST["contact_i"]) ) $contact = $_POST["contact_i"];	
					if ( isset($_POST["cont_phone_i"]) ) $cont_phone = $_POST["cont_phone_i"];	
					if ( isset($_POST["cont_email_i"]) ) $cont_email = $_POST["cont_email_i"];	
					if ( isset($_POST["note_i"]) ) $note = $_POST["note_i"];	
					if ( isset($_POST["ip_i"]) ) $ip = $_POST["ip_i"];	
					if ( isset($_POST["vpn_ip_i"]) ) $vpn_ip = $_POST["vpn_ip_i"];

					#  ***   datas to database   ***
					mysqli_query($conn,"INSERT INTO clients6
						(id,name,web,server,status,duration,avail28days,last_login,last_status_change,address,GPS,owner,owner2,county,del_date,comm,contact,cont_phone,cont_email,note,OFF,ip,vpn_ip,ConnectedSince,vpn_ping,vpn_last,cl_load,cl_disk) 
						VALUES ('$id','$name','$web','$server','','','','','','$address','$GPS','$owner','$owner2','$county','$del_date','$comm','$contact','$cont_phone','$cont_email','$note','','$ip','$vpn_ip','','','','','')
					");


					# ***   display the result on inv_one.php   ***
					echo "<meta http-equiv='Refresh' content='5;url=client_one.php?id=$id'>activity! (".$id.") ".$name." -> move along...<br>";


	
				} ## system id not exist


			} else {
				echo "<h3>Rendszer id hiányzik!</h3>";
				$end_message = "Rendszer id hiányzik!";
			}


		}
} 
##   ***   POST-ed data    ***
?>












<! ***** head  ***** >
<div class="header">
	<div class="hlogo"><img src="../../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></div>
	<div class="hname">Új rendszer felvétele</div>
	<div class="hdate"><?php echo date('Y.m.d. H:i'); ?> </div>
</div>
<! ***** head  ***** >


<! ***** box  ***** >
<div class="box">
<br>


<!-- ***   form   *** -->
<table class=table>

  <tr> 
    <td align="center"  style="background-color:#00cc00;padding:15px"><b>
	  	Új rendszer felvétele
    </b></td>
  </tr>

  <tr> 
    <td valign="top" style="background-color:#00cc00">

    <form method="post" action="act_client_new.php"> 
	<table>
		
        <tr><td width="120">
			id (egyedi): <br>
        </td><td width="320">
			<input list="id" name="id_i"  value="<?php echo $id; ?>">
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
			<input list="del_date" name="del_date_i"  value="<?php echo $del_date; ?>">
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
				echo $end_message; 				# set button message

				echo '<input style="width:70px;margin:10px;padding:5px;font-weight:bold;" type="submit" name="ok" value="mehet">';
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
