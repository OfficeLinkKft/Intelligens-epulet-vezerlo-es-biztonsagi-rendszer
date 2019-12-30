<?
	session_start();
	if( $_SESSION['owner'] == " " ) { header('Location: ../index.php'); exit; }
	ob_start();
?>

<!DOCTYPE HTML>

<html>
<head>
<title>mcss.hu clients control map</title>
<meta http-equiv="refresh" content="8000;url=map.php">

<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="hu-hu" />

<link rel="stylesheet" href="map.css" type="text/css">
<script type="text/javascript" language="javascript" src="lytebox.js"></script>
<link rel="stylesheet" href="lytebox.css" type="text/css" media="screen" />

<link rel="shortcut icon" href="/favicon.ico" />


<! *****    for map    *****>
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.6.0/ol.css" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.6.0/ol.js"></script>
<link rel="stylesheet" href="overlay.css">

<?
// paraméter fogadás url-ben: változó neve, értékadás
	$cam=1;					//1 -> kamera képek látszanak
	$cam=$_GET["cam"];

	$br=0;					// 1 -> csak a hibás rendszerek látszanak
	$br=$_GET["br"];

	$cty=0;					// megyék: 1,2,3,4,5,6,7,8,9,1,10,11,12,13,14,15,16,17,18,19 - 0 = összes
	$cty=$_GET["cty"];


	$off=1;					// offline -> OFFline rendszerek
	$off=$_GET["off"];

	$s=1;					// servers -> mcs servers
	$s=$_GET["s"];

	$dev=1;					// development -> mcs devel systems
	$dev=$_GET["dev"];

	$q=1;					// officelink -> officelink
	$q=$_GET["q"];

	$rd=1;					// road type -> officelink kamerák
	$rd=$_GET["rd"];

	$hw=1;					// road type -> officelink kamerák
	$hw=$_GET["hw"];

	$mk_new=1;				// road type -> new road cameras
	$mk_new=$_GET["mk_new"];

	//ordering
	$ord="id";
//	$ord=$_GET["ord"];

// adatbázis kapcsolat
	$con = mysql_connect("localhost","mcsc","qwe1230");
	if (!$con)
		{ die('Could not connect: ' . mysql_error()); }

	mysql_select_db("mcsc", $con);
	mysql_query('SET NAMES utf8');

	// ******  select clients ******
	$user = $_SESSION['nick'];
//	echo $user."<br><br>";

	$cnd = $cnd." ( owner!='s' AND owner!='dev' AND owner!='q' AND owner!='MK' AND owner!='MKa' AND owner!='MKn'";

	if($s==1)  { $cnd = $cnd." OR owner='s' "; }		// servers
	if($dev==1)  { $cnd = $cnd." OR owner='dev' "; }	// development systems
	if($q==1)  { $cnd = $cnd." OR owner='q'"; }		// officelink
	if($rd==1)  { $cnd = $cnd." OR owner='MK' "; }		// MK road systems
	if($hw==1)  { $cnd = $cnd." OR owner='MKa' "; }		// MK highway systems
	if($mk_new==1)  { $cnd = $cnd." OR owner='MKn' "; }	// new MK systems
	$cnd = $cnd." ) AND ";

	if($off==1)  { $cnd = $cnd."OFF='OFF'"; } else { $cnd = $cnd."OFF!='OFF'"; }	// OFFline systems
	if($br==1)  { $cnd = $cnd." AND (status=0 OR status=3)"; }			// broken systems

	// MK users
	if($user=="MKadmin")  { $cnd = $cnd." AND (owner='MK' OR owner='MKa' OR owner='MKn') "; }	// MK admin user
	if($user=="MK")  { $cnd = $cnd." AND (owner='MK' OR owner='MKa') "; }				// MK user

// echo $cnd."<br><br>";

	$result = mysql_query("SELECT * FROM clients WHERE $cnd ORDER BY $ord");	//for list
	$result2 = mysql_query("SELECT avg(avail28days) FROM clients WHERE $cnd"); 	//for available data

	$row2 = mysql_fetch_array($result2);	//result of system available data

	$i=0;	//number of clients
	$j=0;	//number of cameras

  // save the switch's state
  $adr = "map.php?s=$s&q=$q&rd=$rd&hw=$hw&dev=$dev&mk_new=$mk_new&cam=$cam&off=$off&br=$br&ord=$ord&";
?>

</head>
<body>



<div class="container-fluid">

  <!  *****   begin of map layer   *****   >
  <div class="row-fluid">
    <div class="span12">
      <div id="map" class="map"></div>
    </div>
  </div>
  <script>
    var layer = new ol.layer.Tile({
      //source: new ol.source.MapQuest({layer: 'sat'})
      source: new ol.source.MapQuest({layer: 'osm'})
    });
    var map = new ol.Map({
      layers: [layer],
      target: 'map',
      view: new ol.View({
        center: ol.proj.transform([18.7, 47.3], 'EPSG:4326', 'EPSG:3857'),
        zoom: 7.2
      })
    });
  </script>
  <!  *****   end of map layer   *****   >





	<! ***** switches box *****>
  <div class="box">
	  <div class="hlogo"><a href="http://officelink.hu/" target="_blank"><img src="images/mcslogo.png" height="21" border="0"  title="OfficeLink Kft."></a></div>
	  <div class="hname">mcs</div>

		<! ***** map  *****>
		<div class="sw">
			<a href="map.php" target="_blank" title="térkép"><img src="images/map_tmb.png" height="18"></a>
		</div>

		<! ***** dispach  *****>
		<div class="sw">
			<a href="dispatch.php" target="_blank" title="diszpécser képernyõk"><img src="images/dispatch.png" height="18"></a>
		</div>

		<! ***** Servers  *****>
		<?
		if($_SESSION['owner']=="admin")  {
			if($s==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."s=1\" title=\"Servers\"><img src=\"images/server.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."s=0\" title=\"Servers\"><img src=\"images/server.png\" height=\"16\"></a></div>";
		}
		?>

		<! ***** Development  *****>
		<?
		if($_SESSION['owner']=="admin")  {
			if($dev==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."dev=1\" title=\"Development\"><img src=\"images/dev.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."dev=0\" title=\"Development\"><img src=\"images/dev.png\" height=\"16\"></a></div>";
		}
		?>

		<! ***** InVR OfficeLink  *****>
		<?
		if($_SESSION['owner']=="admin")  {
			if($q==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."q=1\" title=\"InVR - OfficeLink\"><img src=\"images/officelink_logo.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."q=0\" title=\"InVR - OfficeLink\"><img src=\"images/officelink_logo.png\" height=\"16\"></a></div>";
		}
		?>

		<! ***** MK systems - road  *****>
		<?
			if($rd==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."rd=1\" title=\"Officelink kamerák\"><img src=\"images/road.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."rd=0\" title=\"Officelink kamerák\"><img src=\"images/road.png\" height=\"16\"></a></div>";
		?>

		<! ***** MK systems - highway  *****>
		<?
			if($hw==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."hw=1\" title=\"OfficeLink kamerák\"><img src=\"images/highway.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."hw=0\" title=\"OfficeLink kamerák\"><img src=\"images/highway.png\" height=\"16\"></a></div>";
		?>

		<! ***** MK Development - new cameras  *****>
		<?
		if($user!=="MK")  {
			if($mk_new==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."mk_new=1\" title=\"MK Development\"><img src=\"images/dev.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."mk_new=0\" title=\"MK Development\"><img src=\"images/dev.png\" height=\"16\"></a></div>";
		}
		?>

		<div class="swsp"></div>

		<! ***** OFFline systems  *****>
		<?
		if($user!=="MK")  {
			if($off==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."off=1\" title=\"OFFline systems\"><img src=\"images/offline.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."off=0\" title=\"OFFline systems\"><img src=\"images/offline.png\" height=\"16\"></a></div>";
		}
		?>

		<! ***** missing systems  *****>
		<?
			if($br==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."br=1\" title=\"only broken systems of the owner\"><img src=\"images/broken.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."br=0\" title=\"all systems of the owner\"><img src=\"images/broken.png\" height=\"16\"></a></div>";
		?>

		<! ***** camera pics on/off  *****>
		<?
			if($cam==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."cam=1\" title=\"camera pics ON\"><img src=\"images/camera.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."cam=0\" title=\"camera pics OFF\"><img src=\"images/camera.png\" height=\"16\"></a></div>";
		?>

		<div class="swsp"></div><div class="swsp"></div>

		<! ***** manual box  *****>
			<div class="swm">
				<a href="manual.php" target="_blank" title="Manuál"><img src="images/manual.png" height="18"></a>
			</div>
		<! *****  manual box  *****>


  </div>

	<! ***** switches box *****>
























  <!  *****   begin of pics layer   *****   >

  	<! ***** begin of database access  *****>
		<? while($row = mysql_fetch_array($result))
      { $i++; /* number of clients */

        #<! ***   colour of status   *** >
					if($row['status']==1) /* OK */
						$bgc="background-color:#cccccc;";
					elseif($row['status']==2) /* 15min - yellow */
						$bgc="background-color:Yellow;";
					elseif($row['status']==0) /* 24hour - red */
						$bgc="background-color:Tomato;color:Black;";
					elseif($row['status']==3) /* no power - blue */
						$bgc="background-color:DeepSkyBlue;color:Black;";

        #<! ***   type of ikon   *** >
					if($row['owner']=='s') /* server */
						$png="server.png";
					elseif($row['owner']=='dev') /* devel */
						$png="dev.png";
					elseif($row['owner']=='q') /* officelink */
						$png="officelink_logo.png";
					elseif($row['owner']=='MK') /* MK road */
						$png="road.png";
					elseif($row['owner']=='MKa') /* MK highway */
						$png="highway.png";
					else						 /* more systems */
						$png="dev.png";

			  #<! ***** number of pix  *****>
					if($row['cam1']!="no") {$j++;}
					if($row['cam2']!="no") {$j++;}
					if($row['cam3']!="no") {$j++;}
					if($row['cam4']!="no") {$j++;}

			  #<! ***** getting datas  *****>
          $id=$row['id'];
          $name=$row['name'];
          $title=$name." (".$id.")";
          $web=$row['web'];
          $gps=$row['GPS'];

			  #<! ***** test list  *****>
          echo $title." _ ".$web." -> ".$gps."  ||  ";

      ?>

        <!  *****   begin of pics layer   *****   >
        <div style="display: none;">
          // pic
          <a id="pic.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <img src="http://<? echo $web; ?>/webcam1.jpg" width="38" height="28">
          </a>
          // pic label
          <a class="overlay" style="text-decoration:none;color:white;font-size:10pt;font-weight:bold;text-shadow:black 0.1em 0.1em 0.2em;" id="label.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <? echo $name; ?>
          </a>
        </div>
        <script>
          // GPS
          var pos = ol.proj.fromLonLat([<? echo $gps; ?>]);
          // pic
          var pic = new ol.Overlay({
            position: pos,
            positioning: 'bottom-right',
            element: document.getElementById('pic.<? echo $id; ?>'),
            stopEvent: false
          });
          map.addOverlay(pic);
          // pic label
          var label = new ol.Overlay({
            position: pos,
            element: document.getElementById('label.<? echo $id; ?>')
          });
          map.addOverlay(label);
        </script>
        <!  *****   end of pics layer   *****   >




      <?
		  } //end of query ?>
  	<! ***** end of database access  *****>









<!--

  	<! ***** begin of pics to layer  *****>
      <div style="display: none;">
        // pic
          <a id="pic.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <img src="http://<? echo $web; ?>/webcam1.jpg" width="38" height="28">
            <!- <img src="map/webcam1.jpg" width="38" height="28">
          </a>
        // pic label
          <a class="overlay" style="text-decoration:none;color:white;font-size:10pt;font-weight:bold;text-shadow:black 0.1em 0.1em 0.2em;" id="label.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <? echo $name; ?>
          </a>
      </div>
      <script>
        // GPS
          var pos = ol.proj.fromLonLat([<? echo $gps; ?>]);
        // pic
          var pic = new ol.Overlay({
            position: pos,
            positioning: 'center-center',
            element: document.getElementById('pic.<? echo $id; ?>'),
            stopEvent: false
          });
          map.addOverlay(pic);
        // pic label
          var label = new ol.Overlay({
            position: pos,
            element: document.getElementById('label.<? echo $id; ?>')
          });
          map.addOverlay(label);
      </script>
 	  <! ***** end of pics to layer  *****>



<?
        $id="mcs008";
        $name="Vecsés";
        $title=$name." (".$id.")";
        $web="kamera.officelink.hu/mcs008";
        $gps="19.271510,47.411266";
?>

  	<! ***** begin of pics to layer  *****>
      <div style="display: none;">
        // pic
          <a id="pic.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <img src="http://<? echo $web; ?>/webcam1.jpg" width="38" height="28">
            <!- <img src="map/webcam1.jpg" width="38" height="28">
          </a>
        // pic label
          <a class="overlay" style="text-decoration:none;color:white;font-size:10pt;font-weight:bold;text-shadow:black 0.1em 0.1em 0.2em;" id="label.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <? echo $name; ?>
          </a>
      </div>
      <script>
        // GPS
          var pos = ol.proj.fromLonLat([<? echo $gps; ?>]);
        // pic
          var pic = new ol.Overlay({
            position: pos,
            positioning: 'center-center',
            element: document.getElementById('pic.<? echo $id; ?>'),
            stopEvent: false
          });
          map.addOverlay(pic);
        // pic label
          var label = new ol.Overlay({
            position: pos,
            element: document.getElementById('label.<? echo $id; ?>')
          });
          map.addOverlay(label);
      </script>
 	  <! ***** end of pics to layer  *****>

<?
        $id="mcs010";
        $name="Dunakeszi";
        $title=$name." (".$id.")";
        $web="kamera.officelink.hu/mcs010";
        $gps="19.131159,47.655684";
?>

  	<! ***** begin of pics to layer  *****>
      <div style="display: none;">
        // pic
          <a id="pic.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <img src="http://<? echo $web; ?>/webcam1.jpg" width="38" height="28">
            <!- <img src="map/webcam1.jpg" width="38" height="28">
          </a>
        // pic label
          <a class="overlay" style="text-decoration:none;color:white;font-size:10pt;font-weight:bold;text-shadow:black 0.1em 0.1em 0.2em;" id="label.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <? echo $name; ?>
          </a>
      </div>
      <script>
        // GPS
          var pos = ol.proj.fromLonLat([<? echo $gps; ?>]);
        // pic
          var pic = new ol.Overlay({
            position: pos,
            positioning: 'center-center',
            element: document.getElementById('pic.<? echo $id; ?>'),
            stopEvent: false
          });
          map.addOverlay(pic);
        // pic label
          var label = new ol.Overlay({
            position: pos,
            element: document.getElementById('label.<? echo $id; ?>')
          });
          map.addOverlay(label);
      </script>
 	  <! ***** end of pics to layer  *****>

-->





  <!  *****   end of pics layer   *****   >
















  <!  *****   begin of report boxes   *****   >

		<! ***** login box  *****>
			<div class="login">
				<a href="../logout.php" title="logout">user: <b><? echo $_SESSION['nick']; ?> >></b></a>
			</div>
		<! ***** login box  *****>

	  <! ***** list box  *****>
	    <div class="lb">

		    <! ******  header  ******>
		    <div class='lbh'>missing systems</div>
		    <? echo "availability | ".(floor($row2[0]*100)/100)."%<br><br>"; ?>

		    <! ******  list  ******>
    	  <?
          while ($row = mysql_fetch_array($result) and $i>0)
			    {
				    echo "<b><a href=\"client_list\" target=\"_blank\" title=\"open clients pages\">" . $row['name'] . "</b> (" . $row['id'] . ")</a><br> - " . $row['last_login'] . " | " . $row['avail28days'] . "%<br>";
				    $i--;
			    }
        ?>
		    <! ******  footer  ******>
		    <br><b><a href="index.php" target="_blank" title="all system"> >>> list of systems</a></b>
    		<div style="margin-top:10px;text-align:center;font-weight:bold;clear:both;"><? echo "Rendszer: ".$i." | Kamera: ".$j." db"; ?></div>

	    </div>
	  <! ***** list box  *****>

	  <! ***** manual box  - only for admin *****>
	    <? if($_SESSION['nick']==admin) { ?>
		    <div class="mb">
			    <b>MK</b>: OfficeLink<br>
		    </div>
	    <? } ?>
	  <! ***** manual box  *****>

  <!  *****   end of report boxes   *****   >









  <! ***** impressum  *****>
  <div class="impr">
	  <div class="imp1">2015 @ <a href="http://officelink.hu/" target="_blank"> OfficeLink Kft. </a></div>
	  <div class="imp2"></div>
  </div>
  <! ***** impressum  *****>



</div> <!-- map div  -->


<? mysql_close($con); ?>
</body>
</html>

<?
	ob_end_flush();
?>
