<!DOCTYPE html>
<html>
<head>
<title>Overlay example for officelink</title>
<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="hu-hu" />


<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.6.0/ol.css" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.6.0/ol.js"></script>
<link rel="stylesheet" href="overlay.css">



<style>
#map {
  width: 960px;
  height: 600px;
}


.mb {
	position:absolute;
	left:20px;
	top:283px;
	background-color:SkyBlue;
	opacity:0.8;
	width:165px;
	height:47px;
	float:left;
	font-family:Arial;
	font-size:7pt;
	padding-top:10px;
	padding-left:10px;
	padding-right:8px;
	border-radius:7px;
	border:10px;
}





#pic {
  width: 40px;
  height: 30px;
  border: 1px solid #088;
  border-radius: 2px;
  background-color: #FFF;
  opacity: 0.7;
}
#label {
  text-decoration: none;
  color: white;
  font-size: 10pt;
  font-weight: bold;
  text-shadow: black 0.1em 0.1em 0.2em;
}

</style>
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
      //source: new ol.source.MapQuest({layer: 'hyb'})
      //source: new ol.source.OSM({})


    });
    var map = new ol.Map({
      layers: [layer],
      target: 'map',
      view: new ol.View({
        center: ol.proj.transform([19.3, 47.3], 'EPSG:4326', 'EPSG:3857'),
        zoom: 7.5
      })
    });
  </script>
  <!  *****   end of map layer   *****   >







  <!  *****   begin of switch layer   *****   >

  <! ***** manual box  - only for admin *****>
		<div class="mb">
		</div>
	<! ***** manual box  *****>


  <!  *****   end of switch layer   *****   >








  <!  *****   begin of pics layer   *****   >
  <?
    $id="mcs008";
    $name="OL";
    $title=$name." (".$id.")";
    $web="kamera.officelink.hu/mcs008";
    $gps="";
  ?>
  <div style="display: none;">
    // pic
    <a id="pic" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
      <img src="http://<? echo $web; ?>/webcam1.jpg" width="38" height="28">
    </a>
    // pic label
    <a class="overlay" id="label" target="_blank" title="<? echo $title; ?>" href="http://<? echo $web; ?>">
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
      element: document.getElementById('pic'),
      stopEvent: false
    });
    map.addOverlay(pic);
    // pic label
    var label = new ol.Overlay({
      position: pos,
      element: document.getElementById('label')
    });
    map.addOverlay(label);
  </script>
  <!  *****   end of pics layer   *****   >

</div>

</body>
</html>
