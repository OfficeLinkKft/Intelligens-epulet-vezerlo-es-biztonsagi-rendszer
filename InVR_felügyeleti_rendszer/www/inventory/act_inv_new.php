<?php
	session_start();
#	if( $_SESSION['nick'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../common_files/connect.php";

	$ptitle = "InVR &#9832; Új elem";
?>

<!DOCTYPE HTML>

<html>
<head>
	<title><?=$ptitle?></title>

	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="hu-hu" />

	<script type="text/javascript" language="javascript" src="lytebox.js"></script>

	<link rel="stylesheet" href="lytebox.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="act_inv.css" type="text/css">
	<link rel="icon" href="../common_files/img/officelink_favicon.png" type="image/png" sizes="16x16">

</head>


<body>
 
<?php


## select activity from datalist	
	$result = mysqli_query($conn,"SELECT * FROM datalist WHERE Name like 'act_inv_new'");	
	while($row = mysqli_fetch_array($result))
	{
		$Nd = 1;
		while($row[$Nd]) {
			$act_inv_new[$Nd] = $row[$Nd]; 	#echo "<h4>".$act_inv_new[$Nd]."</h4>";
			$Nd++;			
		}
	}





##   ***   POST-ed data -> select next move | $self: out / submited / checked   ***
if ($_SERVER["REQUEST_METHOD"] == "POST") { # posted datas?

	# ***   GETting parameters   ***

		# part of system
		$PN=$_GET["PN"];							# get the partnumber
		$SN=$_GET["SN"];							# get the serial number


		# GET inside system act 
		$self="out";
		if ( !isset($_GET["self"]) ) { # called from outside: inv_one
			$self="out";
			echo "<h1>Empty</h1>";	## check call inv_onv.php

		} else {	# submitted data - check datas
			$self=$_GET['self'];
			echo "<h1>Filled up?: YES / ".$self."</h1>"; ## check call self

		}

		# select activity from datalist	
		$result = mysqli_query($conn,"SELECT * FROM datalist WHERE Name like 'act_inv_new'");	
		while($row = mysqli_fetch_array($result))
		{
			$Nd = 1;
			while($row[$Nd]) {
				## call from inv_one
				if ( isset($_POST[$row[$Nd]]) ) {
					$act_inv_new = $_POST[$row[$Nd]] ;

					echo "<h1>act_inv_new: ".$act_inv_new."</h1>"; ## check activity
				}
				$Nd++;			
			}
		}




	## check datas in the second line
	echo "<h2><b>act_inv / PN / SN / self: </b>".$act_inv_new." / ".$PN." / ".$SN." / ".$self."</h2>";    # for test

} 
## ***   POST-ed data -> select next move | $self :    ***

















## ***  submit - check datas   ***
	if ($self=="submit") {

		if ( isset($_POST["PN_i"]) ) $PN = $_POST["PN_i"];
		if ( isset($_POST["SN_i"]) ) $SN = $_POST["SN_i"];

		if ( isset($_POST["set_for_i"]) ) $set_for = $_POST["set_for_i"];
		if ( isset($_POST["status_i"]) ) $status = $_POST["status_i"];
		$date = $_POST["date_i"];
		if ( isset($_POST["location_i"]) ) $location = $_POST["location_i"];
		if ( isset($_POST["id_i"]) ) $id = $_POST["id_i"];
		if ( isset($_POST["name_i"]) ) $name = $_POST["name_i"];
		if ( isset($_POST["port_i"]) ) $port = $_POST["port_i"];
		if ( isset($_POST["ip_i"]) ) $ip = $_POST["ip_i"];
		if ( isset($_POST["warranty_i"]) ) $warranty_i = $_POST["warranty_i"];
		if ( isset($_POST["note_i"]) ) $note = $_POST["note_i"];	

		#  ***   test   ***
		  	echo "<b>Your input from the FORM:</b><br>";
		  	echo "PN: <b>".$PN."</b><br>";
		  	echo "SN: <b>".$SN."</b><br>";
		  	echo "inv_act: <b>".$inv_act."</b><br>";		
		  	echo "set_for: <b>".$set_for."</b><br>";		
		  	echo "status: <b>".$status."</b><br>";
		  	echo "date: <b>".$date."</b><br>";
		  	echo "location: <b>".$location."</b><br>";
		  	echo "id: <b>".$id."</b><br>";
		  	echo "name: <b>".$name."</b><br>";
		  	echo "port: <b>".$port."</b><br>";
		  	echo "ip: <b>".$ip."</b><br>";
			echo "warranty: <b>".$warranty."</b><br>";
			echo "note: <b>".$note."</b><br>";
		#  ***   end of test   ***





#  ***   product's datas from products table and datalist  ***
	$result = mysqli_query($conn,"SELECT * FROM products6 WHERE PN like '$PN'");	
	while($row = mysqli_fetch_array($result))
	{
    		$N = $row['N'];  
    		$TOP = $row['TOP']; 
    		$PN = $row['PN'];
    		$dscr = $row['dscr'];
    		$DOB = $row['DOB'];
    		$orig = $row['orig'];
    # echo "<br>FROM products (".$pn."): ".$N.": ".$TOP." / ".$PN." / ".$dscr." / ".$DOB." / ".$orig; # for test
	}

/*
	# ***   getting datas from datalist table   ***
	#echo "<br>DATALIST<br>"; #for test
	$result = mysqli_query($conn,"SELECT * FROM datalist");	
	while($row = mysqli_fetch_array($result)) {

    	#echo "<b>".$row['Name'].": </b>"; 
		$ndl=1;
		while( ($ndl<21) and ($row[$ndl]!='') ) { 
			#echo $ndl.": ".$row[$ndl].", "; #for test
			$ndl++;
		} # datalist counter 
		#echo "<br>";
	} # data from datalist table
*/
#  ***   product's datas from products table and datalist  ***






		#  ***   check SN for this PN - it's empty? -part's datas from inv_log table  ***
		if ( $SN != "" ) {
#		if ( ( !isset($_GET["PN"]) AND !isset($_GET["SN"]) ) OR ( !isset($_GET["PN"]) AND !isset($_POST["SN"]) {
			echo "<h3>getting datas for this item from database: ".$PN." - ".$SN."</h3>";
			$result = mysqli_query($conn,"SELECT * FROM inv_log6 WHERE PN LIKE '$PN' AND SN LIKE '$SN'");	
			while($row = mysqli_fetch_array($result)) { # last action with this item

				$SN_exist = $row['location']; 

				$location = $row['location'];
				$id = $row['id'];
				$name = $row['name'];
				$date = $row['date'];
				$operator = $row['operator'];
				$inv_act = $row['inv_act'];
				$status = $row['status'];
				$set_for = $row['set_for'];
				$port = $row['port'];
				$ip = $row['ip'];
				$warranty = $row['warranty'];
				$note = $row['note'];

				echo "<h3>".$PN." | ".$SN." exist! (".$status.") / ".$date.": ".$location." / ".$port." - ".$ip."</h3r>";	#check
			}
		}
		#  ***   part's datas from inv_log table  ***







		##  ***   check activities and write to db   ***

		# activity -> check datas and write to table new line to inv_log6
		if ($act_inv_new!="") {# 

			#  ***   check datas
			if ( $date=="" ) { # missing
				$end_message = "dátum hiányzik! - fill up and try again ! >> &nbsp;&nbsp;"; # set button message

			} elseif ( ($PN=="") OR ($PN=="PartNumber") ) { # missing
				$end_message = "PN hiányzik! - fill up and try again ! >> &nbsp;&nbsp;"; # set button message

			} elseif ( $SN=="" ) { # missing
				$end_message = "SN hiányzik! - fill up and try again ! >> &nbsp;&nbsp;"; # set button message

			} elseif ( $SN_exist!="" ) { # SN existing soon
				$end_message = "SN már létezik (".$SN_exist.")! - fill up and try again ! >> &nbsp;&nbsp;"; # set button message

			} else { # datas OK, write to db and call inv_one


				if ($act_inv_new=="gyártás") { # write to table new line to inv_log6
					#  ***   datas to database   ***
					mysqli_query($conn,"INSERT INTO inv_log6 (PN,SN,systempart,location,id,name,date,operator,inv_act,status,set_for,port,ip,warranty,last,note) 
						VALUES ('$PN','$SN','$systempart','$location','$id','$name','$date','$_SESSION[nick]','$act_inv_outSys','beállítva','$set_for','$port','$ip','$warranty','1','$note')");

					# ***   display the result on inv_one.php   ***
					echo "<meta http-equiv='Refresh' content='5;url=inv_one.php?PN=$PN&SN=$SN'>activity! (".$PN." / ".$SN.") move along...<br>";

					# set button message
					$end_message = "data OK! - write and call inv_one ! >> &nbsp;&nbsp;"; 
				}

				if ($act_inv_new=="vásárlás") { # write to table new line to inv_log6
					#  ***   datas to database   ***
					mysqli_query($conn,"INSERT INTO inv_log6 (PN,SN,systempart,location,id,name,date,operator,inv_act,status,set_for,port,ip,warranty,last,note) 
						VALUES ('$PN','$SN','$systempart','$location','$id','$name','$date','$_SESSION[nick]','$act_inv_outSys','beállítva','$set_for','$port','$ip','$warranty','1','$note')");

					# ***   display the result on inv_one.php   ***
					echo "<meta http-equiv='Refresh' content='5;url=inv_one.php?PN=$PN&SN=$SN'>activity! (".$PN." / ".$SN.") move along...<br>";

					# set button message
					$end_message = "data OK! - write and call inv_one ! >> &nbsp;&nbsp;"; 
				}

				if ($act_inv_new=="idegen") { # write to table new line to inv_log6
					#  ***   datas to database   ***
					mysqli_query($conn,"INSERT INTO inv_log6 
					(PN,SN,systempart,location,id,name,date,operator,inv_act,status,set_for,port,ip,warranty,last,note) 
					VALUES 
					('$PN','$SN','$systempart','$location','$id','$name','$date','$_SESSION[nick]','$act_inv_outSys','beállítva','$set_for','$port','$ip','$warranty','1','$note')");

					# ***   display the result on inv_one.php   ***
					echo "<meta http-equiv='Refresh' content='5;url=inv_one.php?PN=$PN&SN=$SN'>activity! (".$PN." / ".$SN.") move along...<br>";

					# set button message
					$end_message = "data OK! - write and call inv_one ! >> &nbsp;&nbsp;"; 
				}

			} # act_inv_new is not empty

		}
		##  ***   check activities and write to db   ***

	}
## ***  submit - check datas   ***













/*



	#  ***   checking datas is any empty?  ***
		if ($inv_act!='' && $PN!='' && $SN!='' && $operator!='') { # datas exists
			echo "<br><b>data ready!</b><br>";

			#  ***   set previsios record to not last (last record set from 1 to 0)
#			mysqli_query($conn,"UPDATE inv_log6 SET last='0' WHERE PN='$PN' AND SN='$SN'");

			#  ***   datas to database   ***
#			mysqli_query($conn,"INSERT INTO inv_log6 (PN,SN,location,id,name,date,operator,inv_act,status,set_for,port,ip,warranty,last,note) VALUES 		('$PN','$SN','$location','$id','$name','$date','$operator','$inv_act','$status','$set_for','$port','$ip','$warranty','1','$note')");

			# ***   display the result on inv_one.php   ***
			echo "<meta http-equiv='Refresh' content='1;url=inv_one.php?PN=$PN&SN=$SN&location=$location'>activity! (".$PN." / ".$SN.") move along...<br>";

			echo "<br><b>datas inserted to database!</b><br><br><br><br>";
		} 

#		else { $button = "data missing! - fill up and try again ! >> &nbsp;&nbsp;"; }	# set button message 
		# echo "<b>inv_act / PNi : </b>".$inv_act." / ".$PNi; 	# for check

} 	# posted data


*/













?>



<?php 
	# fill up datalist from datalist table
	$result = mysqli_query($conn,"SELECT * FROM datalist");	
	while($row = mysqli_fetch_array($result))
	{
?>  
    <datalist id="<?php echo $row['Name']; ?>">
      <option value="<?php echo $row['1']; ?>">
      <option value="<?php echo $row['2']; ?>">
      <option value="<?php echo $row['3']; ?>">
      <option value="<?php echo $row['4']; ?>">
      <option value="<?php echo $row['5']; ?>">
      <option value="<?php echo $row['6']; ?>">
      <option value="<?php echo $row['7']; ?>">
      <option value="<?php echo $row['8']; ?>">
      <option value="<?php echo $row['9']; ?>">
      <option value="<?php echo $row['10']; ?>">
      <option value="<?php echo $row['11']; ?>">
      <option value="<?php echo $row['12']; ?>">
      <option value="<?php echo $row['13']; ?>">
      <option value="<?php echo $row['14']; ?>">
      <option value="<?php echo $row['15']; ?>">
      <option value="<?php echo $row['16']; ?>">
      <option value="<?php echo $row['17']; ?>">
      <option value="<?php echo $row['18']; ?>">
      <option value="<?php echo $row['19']; ?>">
      <option value="<?php echo $row['20']; ?>">
    </datalist>
<?php	} ?>
	<!-- *** end of datalists *** -->




<?php
	# set table color
	$inSys_color = "#bbf";		# part is inSystem - implemented
	$outSys_color = "#bfb";		# part is outSystem - not implemented
	if ($systempart=="no") $ioSys_color = $inSys_color;
	else $ioSys_color = $outSys_color;
?>























<! ***** head  ***** >
<div class="header">
	<div class="hlogo"><img src="../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></div>
	<div class="hname">Új elem felvétele</div>
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
	  	Új elem felvétele
    </b></td>
  </tr>

  <tr> 
    <td valign="top" style="background-color:#00cc00">

    <form method="post" action="
		<?php
			echo 'act_inv_new.php?self=submit&PN='.$PN.'&SN='.$SN; 
		?>
	"> 

	<table>

			<?php
			if ( ($PN=="") OR ($PN=="PartNumber") ) { # PN not posted
			?>

				<tr><td width="70">
					PN: <br>
				</td><td width="370" style="padding:10px 10px 10px 10px;">

					<select name="PN_i" style="width:150px;height:30px;margin-top:4px">
						<option value="PartNumber" name="" selected>PartNumber</option>
						<?php
							$result = mysqli_query($conn,"SELECT * FROM products6");
							while($row = mysqli_fetch_array($result)) {
									echo '<option value="'.$row[PN].'">'.$row[PN].'</option>';
							} # data from products table
						?>
					</select>

					<table style="float:right"><tr><td width="100" style="background-color:#ddd;text-align:center">
						<a href="products.php" target="_blank" title="Elem lista megjelenítése">Elem lista</a> 
					</td></tr></table>
			   	</td></tr>

			<?php
			} else {
			?>

				<tr><td width="70">
					PN: <br>
				</td><td width="370" style="padding:10px 10px 10px 10px;"><b>
					<?php echo "&nbsp;&nbsp;".$PN; ?>

					<table style="float:right"><tr><td width="160" style="background-color:#ddd;text-align:center">
						<a href="index.php?pnr=<?php echo $PN; ?>" target="_blank" title="Elem lista megjelenítése"><?php echo $PN; ?><br>elem lista</a> 
					</td></tr></table>
			   	</b></td></tr>

				<tr><td width="70">
				  	Leírás: <br>
				</td><td width="270">
					<?php echo $dscr; ?>
			   	</td></tr>

			<?php
			}
			?>






		    <tr><td>
				SN: 
		    </td><td>
				<input list="SN" name="SN_i" value="<?php echo $SN; ?>">
		    </td></tr>





		    <tr><td>
				Beállítva: 
		    </td><td>
				<input list="location" name="set_for_i"  value="<?php echo $set_for; ?>">
				<span class="error"> * beállítva ehhez</span>
		    </td></tr>

		    <tr><td>
				Státusz: 
		    </td><td>
				<select name="status_i">
					<option value="üres" selected>üres</option>
					<?php
						$result = mysqli_query($conn,"SELECT * FROM datalist WHERE Name LIKE 'product_status'");
						while($row = mysqli_fetch_array($result)) {
							$ndl=1;
							while( ($ndl<21) and ($row[$ndl]!='') ) {
								echo '<option value="'.$row[$ndl].'">'.$row[$ndl].'</option>';
								$ndl++;
							} # datalist counter 
						} # data from datalist table and that row!
					?>
				</select>
			</td></tr>
			
			<tr><td>
				Dátum: 
			</td><td>
				<input list="date" name="date_i" value="<?php echo date('Y-m-d H:i'); ?>">
				<span class="error"> * ha nem most készül </span><br>
			</td></tr>

			<tr><td>
				Lokáció: 
			</td><td>
				<select name="status_i">
					<option value="Műhely" selected>Műhely</option>
					<?php
						$result = mysqli_query($conn,"SELECT * FROM datalist WHERE Name LIKE 'location'");
						while($row = mysqli_fetch_array($result)) {
							$ndl=1;
							while( ($ndl<21) and ($row[$ndl]!='') ) {
								echo '<option value="'.$row[$ndl].'">'.$row[$ndl].'</option>';
								$ndl++;
							} # datalist counter 
						} # data from datalist table and that row!
					?>
				</select>
			</td></tr>

			<tr><td>
				ID: 
			</td><td>
				<input list="id" name="id_i" value="<?php echo $id; ?>">
				<span class="error"> * azonosító a helyszínen</span>
			</td></tr>

			<tr><td>
				Név: 
			</td><td>
				<input list="name" name="name_i" value="<?php echo $name; ?>">
				<span class="error"> * név a helyszínen</span>
			</td></tr>

			<tr><td>
				Port: 
			</td><td>
				<input list="port" name="port_i" value="<?php echo $port; ?>">
				<span class="error"> * válassz vagy írj</span>
			</td></tr>

			<tr><td>
				IP: 
			</td><td>
				<input list="ip" name="ip_i" value="<?php echo $ip; ?>">
				<span class="error"> * válassz vagy írj</span>
			</td></tr>

			<tr><td>
				Garancia: 
			</td><td>
				<select name="warranty_i">
					<option value="<?php echo '1970-01-01 00:00:00'; ?>">nincs</option>
					<option value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" selected><?php echo '1 év: '.date('Y-m-d', strtotime('+1 year')); ?></option>
					<option value="<?php echo date('Y-m-d', strtotime('+2 year')); ?>"><?php echo '2 év: '.date('Y-m-d', strtotime('+2 year')); ?></option>
					<option value="<?php echo date('Y-m-d', strtotime('+3 year')); ?>"><?php echo '3 év: '.date('Y-m-d', strtotime('+3 year')); ?></option>
				</select>
			</td></tr>

			<tr><td>
				Megjegyzés: 
			</td><td>
				<input list="note" name="note_i" value="<?php echo $note; ?>">
			</td></tr>



			<tr><td>
				Operátor: 
			</td><td>
				<b>&nbsp;&nbsp;<?php echo $_SESSION['nick']; ?></b>
			</td></tr>














		<tr><td colspan=2 style="border:0px;">

			<div style="text-align: right;padding:5px">
				<span class="error"><b><?php echo $end_message; ?></b></span>

				<?php
				if ( !isset( $self ) ) {			
					$Nd = 1;
					while($act_inv_new[$Nd])
					{
						echo '<input style="width:70px;margin:10px;padding:5px;font-weight:bold;" type="submit" name="'.$act_inv_new[$Nd].'" value="'.$act_inv_new[$Nd].'">';
						$Nd++;			
					}
				} else {
					echo '<input style="width:70px;margin:10px;padding:5px;font-weight:bold" type="submit" name="'.$act_inv_new.'" value="'.$act_inv_new.'">';				
				}
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
