<?php
	session_start();
#	if( $_SESSION['nick'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../../common_files/connect.php";

	$ptitle = "InVR &#9832; Rendszer adatlap";
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
# ***   GETting parameters   ***
		$id=$_GET["id"];							# get the client's id
#		$location=$_GET["location"];					# get location
		#echo "<b>id : </b>".$id." / ".$location;    	# for test


# ***   record from database to this part ***
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
		#echo "from clients id/name: ".$id." / ".$name;	# for test
	}

?>



	<! ***** inventory activities  *****>
	<?php 
		$result = mysqli_query($conn,"SELECT * FROM datalist WHERE Name like 'act_inv_inSys'");	
		while($row = mysqli_fetch_array($result))
		{
			$Nd = 1;
			while($row[$Nd]) {
				$act_inv_inSys[$Nd] = $row[$Nd]; 	#echo "<h4>".$act_inv_inSys[$Nd]."</h4>";
				$Nd++;			
			}
		}

		$result = mysqli_query($conn,"SELECT * FROM datalist WHERE Name like 'act_inv_outSys'");	
		while($row = mysqli_fetch_array($result))
		{
			$Nd = 1;
			while($row[$Nd]) {
				$act_inv_outSys[$Nd] = $row[$Nd];
				$Nd++;			
			}
		}



  	mysqli_close($conn);
?>
<!-- ***   closing database connection   *** -->



















<! ***** head  *****>
<div class="header">
	<div class="hlogo"><img src="../../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></div>
	<div class="hname">Rendszer adatlap</div>
	<div class="hdate"><?php echo date('Y.m.d. H:i'); ?> </div>
</div>
<! ***** head  *****>



<! ***** box  *****>
<div class="box">
<br>
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

<!-- ***   form   *** -->
<table class=table>

  <tr> 
    <td style=<?php echo $bgc;?>;>

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

    </td>
  </tr>

  <tr> 
    <td valign="top" style="background-color:Gray">
    <form method="post" action="<?php echo 'act_client.php?id='.$id; ?>"> 
      
	<table>
		
        <tr><td width="120">
          	id: <br>
        </td><td width="320"><b>
			<?php echo "&nbsp;&nbsp;".$id; ?>
       	</b></td></tr>

        <tr><td>
          	Név: <br>
        </td><td>
			<?php echo $name; ?>
       	</td></tr>

        <tr><td>
          	Webcím: 
        </td><td><b>
			<?php echo $web; ?>
        </b></td></tr>

        <tr><td>
          Cím: 
        </td><td><b>
			<?php echo $address; ?>
<!-			&nbsp;&nbsp;<?php if ($systempart=="no") echo "nem"; else echo $systempart; ?>
        </b></td></tr>

        <tr><td>
          GPS (long,lat): 
        </td><td>
          <?php echo $GPS; ?>
        </td></tr>

        <tr><td>
          Megye: 
        </td><td>
          <?php echo $county; ?>
        </td></tr>

        <tr><td>
         Üzembe helyezve: 
        </td><td>
          <?php echo $del_date; ?>
        </td></tr>

        <tr><td>
          Hálózati kapcsolat: 
        </td><td>
          <?php echo $comm; ?>
        </td></tr>

        <tr><td>
          Tulajdonos: 
        </td><td>
          <?php echo $owner; ?>
        </td></tr>

        <tr><td>
          Kapcsolattartó: 
        </td><td>
          <?php echo $contact; ?>
        </td></tr>

        <tr><td>
          Telefonszám: 
        </td><td>
          <?php echo $cont_phone; ?>
        </td></tr>

        <tr><td>
         Email cím: 
        </td><td>
          	<?php echo $cont_email; ?>
        </td></tr>

        <tr><td>
          Megjegyzés: 
        </td><td>
          	<?php echo $note; ?>
        </td></tr>

        <tr><td>
          IP cím (publikus): 
        </td><td>
          <?php if ($ip=="NULL") echo "nincs"; else echo $ip; ?>
        </td></tr>

        <tr><td>
          VPN IP cím: 
        </td><td>
          <?php echo $vpn_ip; ?>
        </td></tr>


        <tr><td>
			Felügyelet: 
        </td><td>
			<b>&nbsp;&nbsp;<?php if ($OFF=="on") echo "on"; else echo "szünet"; ?></b>
        </td></tr>

        <tr><td colspan=2 style="border:0px;">

			<div style="text-align: right;padding:5px">
			<?php
				echo '<input style="width:90px;margin:10px;padding:5px;font-weight:bold;" type="submit" name="edit" value="szerkeszt">';
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
	ob_end_flush();
?>
