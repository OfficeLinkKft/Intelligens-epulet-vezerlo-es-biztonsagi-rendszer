
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
// adatbázis kapcsolat
	$con = mysql_connect("localhost","mcsc","qwe1230");
	if (!$con)
		{ die('Could not connect: ' . mysql_error()); }

	mysql_select_db("mcsc", $con);
	mysql_query('SET NAMES utf8');

	// ******  select clients ******
	$user = $_SESSION['nick'];
//	echo $user."<br><br>";

  // ***   select for user and by switches - condition for selection   ***

    // paraméter fogadás url-ben: változó neve, értékadás

	    $cam=$_GET["cam"];          // 1 -> kamera képek látszanak
	    $br=$_GET["br"];            // 1 -> csak a hibás rendszerek látszanak
	    $off=$_GET["off"];          // offline -> OFFline rendszerek

	    $s=$_GET["s"];              // servers -> mcs servers
	    $dev=$_GET["dev"];          // development -> mcs devel systems
	    $q=$_GET["q"];              // officelink -> officelink
	    $rd=$_GET["rd"];            // road type -> officelink kamerák
	    $hw=$_GET["hw"];            // road type -> officelink kamerák

	    $mk_new=$_GET["mk_new"];    // road type -> new road cameras

	    $cty=$_GET["cty"];          // megyék: 1,2,3,4,5,6,7,8,9,1,10,11,12,13,14,15,16,17,18,19 - 0 = összes
      if ($cty=="") { $cty = 0; }

	$cnd = $cnd." ( owner!='s' AND owner!='dev' AND owner!='q' AND owner!='MK' AND owner!='MKa' AND owner!='MKn'";

	if($s==1)  { $cnd = $cnd." OR owner='s' "; }		    // servers
	if($dev==1)  { $cnd = $cnd." OR owner='dev' "; }	  // development systems
	if($q==1)  { $cnd = $cnd." OR owner='q'"; }		      // officelink systems
	if($rd==1)  { $cnd = $cnd." OR owner='MK' "; }		  // MK road systems
	if($hw==1)  { $cnd = $cnd." OR owner='MKa' "; }		  // MK highway systems
	if($mk_new==1)  { $cnd = $cnd." OR owner='MKn' "; }	// new MK systems
	$cnd = $cnd." ) AND ";

	if($off==1)  { $cnd = $cnd."OFF='OFF'"; } else { $cnd = $cnd."OFF!='OFF'"; }	// OFFline systems
	if($br==1)  { $cnd = $cnd." AND (status=0 OR status=3)"; }			// broken systems

	// MK users
	if($user=="MKadmin")  { $cnd = $cnd." AND (owner='MK' OR owner='MKa' OR owner='MKn') "; }	// MK admin user
	if($user=="MK")  { $cnd = $cnd." AND (owner='MK' OR owner='MKa') "; }				// MK user

// echo $cnd."<br><br>";

  // ***   selection ordering - condition for ordering   ***
    // paraméter fogadás url-ben: változó neve, értékadás
    $ord=$_GET["ord"];      //ordering
    if ($ord=="") { $ord = id; }

  // ***   map center and zoom   ***
    // paraméter fogadás url-ben: változó neve, értékadás
	  $mapcenter=$_GET["mapcenter"];      // map center
	  $mapzoom=$_GET["mapzoom"];          // map zoom
	  $mapsource=$_GET["mapsource"];      // map source - see below

    // center and zoom setting for counties
    if ($cty==0) { $mapcenter="18.7, 47.3"; $mapzoom="7.2"; }     // its Hungary
    if ($cty==1) { $mapcenter="19.5, 46.6"; $mapzoom="10"; }     // its Bács-Kiskun, Hungary
    if ($cty==2) { $mapcenter="18.2, 46.1"; $mapzoom="10"; }     // its Baranya, Hungary
    if ($cty==3) { $mapcenter="20.75, 46.6"; $mapzoom="10"; }     // its Békés, Hungary
    if ($cty==4) { $mapcenter="20.86, 48.12"; $mapzoom="10"; }     // its Borsod-Abaúj-Zemplén, Hungary
    if ($cty==5) { $mapcenter="20.18, 46.53"; $mapzoom="10"; }     // its Csongrád, Hungary
    if ($cty==6) { $mapcenter="18.55, 47.2"; $mapzoom="10"; }     // its Fejér, Hungary
    if ($cty==7) { $mapcenter="17.55, 47.6"; $mapzoom="10"; }     // its Győr, Hungary
    if ($cty==8) { $mapcenter="21.28, 47.57"; $mapzoom="10"; }     // its Hajdú-Bihar, Hungary
    if ($cty==9) { $mapcenter="20.37, 47.85"; $mapzoom="10"; }     // its Heves, Hungary
    if ($cty==10) { $mapcenter="20.12, 47.2"; $mapzoom="10"; }     // its Jász-Nagykun-Szolnok, Hungary
    if ($cty==11) { $mapcenter="18.45, 47.55"; $mapzoom="10"; }     // its Komárom-Esztergom, Hungary
    if ($cty==12) { $mapcenter="19.75, 47.97"; $mapzoom="10"; }     // its Nógrád, Hungary
    if ($cty==13) { $mapcenter="19.1, 47.5"; $mapzoom="10"; }     // its Pest, Hungary
    if ($cty==14) { $mapcenter="17.65, 46.5"; $mapzoom="10"; }     // its Somogy, Hungary
    if ($cty==15) { $mapcenter="21.75, 48.0"; $mapzoom="10"; }     // its Szabolcs, Hungary
    if ($cty==16) { $mapcenter="18.64, 46.47"; $mapzoom="10"; }     // its Tolna, Hungary
    if ($cty==17) { $mapcenter="17.13, 47.12"; $mapzoom="10"; }     // its Vas, Hungary
    if ($cty==18) { $mapcenter="17.86, 47.12"; $mapzoom="10"; }     // its Veszprém, Hungary
    if ($cty==19) { $mapcenter="17.34, 46.67"; $mapzoom="10"; }     // its Zala, Hungary
    if ($cty==100) { $mapcenter="18.7, 47.3"; $mapzoom="4"; }     // its Europe


    // default
    if ($mapcenter=="") { $mapcenter="18.7, 47.3"; }
    if ($mapzoom=="") { $mapzoom="7.2"; }
    if ($mapsource=="") { $mapsource="OSM()"; }




  // and now something completely different...
	$result = mysql_query("SELECT * FROM clients WHERE $cnd ORDER BY $ord");	//for list
  $missing = mysql_query("SELECT * FROM clients WHERE ($cnd AND (status=0 OR status=3)) ORDER BY last_login");	//for missing system list

	$result2 = mysql_query("SELECT avg(avail28days) FROM clients WHERE $cnd"); 	//for available data
	$row2 = mysql_fetch_array($result2);	//result of system available data

	$i=0;	//number of clients
	$j=0;	//number of cameras

  // save the switch's state
  $adr = "map.php?s=$s&q=$q&rd=$rd&hw=$hw&dev=$dev&mk_new=$mk_new&cam=$cam&off=$off&br=$br&ord=$ord&cty=$cty&mapcenter=$mapcenter&mapzoom=$mapzoom&mapsource=$mapsource&";
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
      //source: new ol.source.MapQuest({layer: 'osm'})
      //source: new ol.source.MapQuest({layer: 'hyb'})
      //source: new ol.source.OSM()
      source: new ol.source.<? echo $mapsource; ?>
    });
    var map = new ol.Map({
      layers: [layer],
      target: 'map',
      view: new ol.View({
        center: ol.proj.transform([<? echo $mapcenter; ?>], 'EPSG:4326', 'EPSG:3857'),
        zoom: <? echo $mapzoom; ?>
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

		<! ***** OfficeLink  *****>
		<?
		if($_SESSION['owner']=="admin")  {
			if($q==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."q=1\" title=\"OfficeLink\"><img src=\"images/officelink_logo.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."q=0\" title=\"OfficeLink\"><img src=\"images/officelink_logo.png\" height=\"16\"></a></div>";
		}
		?>

		<! ***** MK systems - road  *****>
		<?
			if($rd==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."rd=1\" title=\"OfficeLink kamerák\"><img src=\"images/officelink_logo.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."rd=0\" title=\"OfficeLink kamerák\"><img src=\"images/officelink_logo.png\" height=\"16\"></a></div>";
		?>

		<! ***** MK systems - highway  *****>
		<?
			if($hw==0)
				echo "<div class=\"sw0\"><a href=\"".$adr."hw=1\" title=\"Officelink kamerák\"><img src=\"images/officelink.png\" height=\"16\"></a></div>";
			else
				echo "<div class=\"sw1\"><a href=\"".$adr."hw=0\" title=\"Officelink amerák\"><img src=\"images/officelink.png\" height=\"16\"></a></div>";
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
		if($_SESSION['owner']=="admin")  {
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
					if($row['status']==1) /* OK */ {
						$bgc="background-color:#cccccc;";
            $ftc="#cccccc"; }
					elseif($row['status']==2) /* 15min - yellow */ {
						$bgc="background-color:Yellow;";
            $ftc="yellow"; }
					elseif($row['status']==0) /* 24hour - red */ {
						$bgc="background-color:Tomato;color:Black;";
            $ftc="tomato"; }
					elseif($row['status']==3) /* no power - blue */ {
						$bgc="background-color:DeepSkyBlue;color:Black;";
            $ftc="DeepSkyBlue"; }

        #<! ***   type of ikon   *** >
					if($row['owner']=='s') /* server */
						$png="server.png";
					elseif($row['owner']=='dev') /* devel */
						$png="dev.png";
					elseif($row['owner']=='q') /* Officelink */
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
//          echo $title." _ ".$web." -> ".$gps."  ||  ";

      ?>

        <!  *****   begin of pics layer   *****   >
        <div style="display: none;">
          // pic
   				<? if($cam==1) { ?>
          <a id="pic.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
            <img src="http://<? echo $web; ?>/webcam1.jpg" width="38" height="28">
          </a>
          <? } ?>
          // pic label
          <a class="overlay" style="text-decoration:none;color:<? echo $ftc; ?>;font-size:9pt;font-weight:bold;text-shadow:black 0.1em 0.1em 0.2em;" id="label.<? echo $id; ?>" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
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
        <!  *****   end of pics layer   *****   >




      <?
		  } //end of query ?>
  	<! ***** end of database access  *****>

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
          $l = 2;
          while ($row = mysql_fetch_array($missing) and $l>0)
			    {
				    echo "<b><a href=\"client_list\" target=\"_blank\" title=\"open clients pages\">" . $row['name'] . "</b> (" . $row['id'] . ")</a><br> - " . $row['last_login'] . " | " . $row['avail28days'] . "%<br>";
				    $l--;
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
			    <b>officelink</b>: OfficeLink
			    <b>OLadmin</b>: OfficeLink <br>
			    <b>officelink</b>: OfficeLink <br>
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


</body>

<? mysql_close($con); ?>
</html>

<?
	ob_end_flush();
?>
