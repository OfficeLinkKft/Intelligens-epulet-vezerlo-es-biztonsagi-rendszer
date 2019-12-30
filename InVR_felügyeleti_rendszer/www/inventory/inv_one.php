<?php
	session_start();
#	if( $_SESSION['nick'] == "" ) { header('Location: ../index.php'); exit; }
	ob_start();

	include "../common_files/connect.php";

	$ptitle = "InVR &#9832; Elem adatlap";
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
# ***   GETting parameters   ***
		$pn=$_GET["PN"];						# get the partnumber
		$sn=$_GET["SN"];						# get the serial number
		$location=$_GET["location"];			# get location
		#echo "<b>PN / SN : </b>".$pn." / ".$sn;    # for test






#   *** product's datasheet ***
	$result = mysqli_query($conn,"SELECT * FROM products6 WHERE PN like '$pn'");	
	while($row = mysqli_fetch_array($result))
	{
    	$N = $row['N'];  
    	$TOP = $row['TOP']; 
    	$PN = $row['PN'];
    	$dscr = $row['dscr'];
    	$DOB = $row['DOB'];
    	$orig = $row['orig'];
    	#echo "<br>FROM products (".$pn."): ".$N.": ".$TOP." / ".$PN." / ".$dscr." / ".$DOB." / ".$orig; # for test
	}



# ***   last record from database to this part ***
	$result = mysqli_query($conn,"SELECT * FROM inv_log6 WHERE PN like '$pn' AND SN like '$sn' AND last='1'");	
	while($row = mysqli_fetch_array($result)) {
  		$PN = $row[PN]; 
  		$SN = $row['SN'];
  	  	$systempart = $row[systempart];
  	  	$inv_act = $row[inv_act];
  	  	$set_for = $row[set_for];
  	  	$location = $row[location];
  	  	$id = $row[id];
  	  	$name = $row[name];
  	  	$status = $row[status];
  	   	$date = $row[date];
		$operator = $row[operator]; 
  	  	$port = $row[port];
  	  	$ip = $row[ip];
		$warranty = $row[warranty];
		$note = $row['note'];
		#echo "from inv_log PN/SN: ".$PN." / ".$SN;	# for test
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
	?>







<!-- *** datalists *** -->

<?php
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

<?php
	}
  	mysqli_close($conn);
?>
<!-- ***   closing database connection   *** -->


<?php
	# set table color
	$inSys_color = "#bbf";		# part is inSystem - implemented
	$outSys_color = "#bfb";		# part is outSystem - not implemented
	if ($systempart=="no") $ioSys_color = $outSys_color;
	else $ioSys_color = $inSys_color;
?>




















<! ***** head  *****>
<div class="header">
	<div class="hlogo"><img src="../common_files/img/officelink_logo.png" height="21" border="0"  title="OfficeLink logo"></div>
	<div class="hname">Elem adatlap</div>
	<div class="hdate"><?php echo date('Y.m.d. H:i'); ?> </div>
</div>
<! ***** head  *****>



<! ***** box  *****>
<div class="box">
<br>
	<?php # set status color
		switch ($status) {
		    case "empty":
				$bgc="background-color:LightGreen;"; break;
			case "ok":
				$bgc="background-color:Cyan;color:Black;"; break;
			case "mcs":
				$bgc="background-color:RoyalBlue;color:Black;"; break;
			case "fail":
				$bgc="background-color:Coral;color:Black;"; break;
			case "set":
				$bgc="background-color:SkyBlue;color:Black;"; break;
			case "scrap":
				$bgc="background-color:Black;color:White;"; break;

		    case "üres":
				$bgc="background-color:LightGreen;"; break;
			case "ok":
				$bgc="background-color:Cyan;color:Black;"; break;
			case "hiba":
				$bgc="background-color:Coral;color:Black;"; break;
			case "beállítva":
				$bgc="background-color:SkyBlue;color:Black;"; break;
			case "selejt":
				$bgc="background-color:Gray;color:White;"; break;

			default:
				$bgc="background-color:White;color:Black;";
		}
	?>

<!-- ***   form   *** -->
<table class=table>

  <tr> 
    <td style=<?php echo $bgc;?>;>

     	<table><tr>
			<td width="370" align="center" style=<?php echo $bgc;?>>
	  			<?php echo "(".$N.") ".$TOP."<br><b>".$pn." / ".$sn."</b><br> ".$inv_act." / státusz: "."$status"; ?>
			</td><td width="70" align="center" style=<?php echo $bgc;?>;>
				<img src="../common_files/img/officelink_logo.png" height="30" border="0" title="OfficeLink logo">
			</td>
		</tr></table>

    </td>
  </tr>

  <tr> 
    <td valign="top" style="background-color:<?=$ioSys_color?>">
    <form method="post" action="
		<?php
			if ($systempart=="no")
				echo 'act_inv_outSys.php?PN='.$PN.'&SN='.$SN; 

			else
				echo 'act_inv_inSys.php?PN='.$PN.'&SN='.$SN; 
		?>
	"> 
      
	<table>
		
        <tr><td width="70">
          	PN: <br>
        </td><td width="370"><b>
			<?php echo "&nbsp;&nbsp;".$PN; ?>
       	</b></td></tr>

        <tr><td width="70">
          	Leírás: <br>
        </td><td width="270">
			<?php echo $dscr; ?>
       	</td></tr>

        <tr><td>
          	SN: 
        </td><td><b>
			<?php echo "&nbsp;&nbsp;".$SN; ?>
        </b></td></tr>

        <tr><td>
          Rendszerelem: 
        </td><td><b>
			&nbsp;&nbsp;<?php if ($systempart=="no") echo "nem"; else echo $systempart; ?>
        </b></td></tr>

        <tr><td>
          Utolsó művelet: 
        </td><td>
          <?php echo $inv_act; ?>
        </td></tr>

        <tr><td>
          Dátum: 
        </td><td>
          <?php echo $date; ?>
        </td></tr>

        <tr><td>
          Státusz: 
        </td><td>
          <?php echo $status; ?>
        </td></tr>

        <tr><td>
          Beállítva: 
        </td><td>
          <?php echo $set_for; ?>
        </td></tr>

        <tr><td>
          Lokáció: 
        </td><td>
          <?php echo $location; ?>
        </td></tr>

        <tr><td>
          ID: 
        </td><td>
          <?php echo $id; ?>
        </td></tr>

        <tr><td>
          Név: 
        </td><td>
          <?php echo $name; ?>
        </td></tr>

        <tr><td>
         Port: 
        </td><td>
          	<?php echo $port; ?>
        </td></tr>

        <tr><td>
          IP: 
        </td><td>
          	<?php echo $ip; ?>
        </td></tr>

        <tr><td>
          Garancia: 
        </td><td>
          <?php echo $date; ?>
        </td></tr>

        <tr><td>
          Operátor: 
        </td><td>
          <?php echo $operator; ?>
        </td></tr>

        <tr><td>
          Megjegyzés: 
        </td><td>
          <?php echo $note; ?>
        </td></tr>

        <tr><td colspan=2 style="border:0px;">

			<div style="text-align: right;padding:5px">
			<?php
				$Nd = 1;
		
				if ($systempart=="no") { # no part of system - activity: act_inv_outSys
					while($act_inv_outSys[$Nd])
					{
						#echo $act_inv_outSys[$Nd];
						echo '<input style="width:70px;margin:10px;padding:5px" type="submit" name="'.$act_inv_outSys[$Nd].'" value="'.$act_inv_outSys[$Nd].'">';
						$Nd++;			
					}

				} else {	# part of system - activity: act_inv_inSys
					while($act_inv_inSys[$Nd])
					{
						echo '<input style="width:70px;margin:10px;padding:5px;font-weight:bold;" type="submit" name="'.$act_inv_inSys[$Nd].'" value="'.$act_inv_inSys[$Nd].'">';
						$Nd++;			
					}


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
