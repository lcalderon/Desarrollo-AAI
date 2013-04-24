<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_usuario.inc.php');

// **************** Coordenadas vienen del programa que lo llama  ***************//
$con = new DB_mysqli();
$lat = $_GET[lat];
$lng = $_GET[lng];
//echo $lat,$lng;
if ($lat==0 && $lng==0)
{
	$lat = $con->lee_parametro('UBICACION_PRIMARIA_LATITUD');
	$lng = $con->lee_parametro('UBICACION_PRIMARIA_LONGITUD');
}


//echo $lat,$lng;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
</head>
<body onload="mapa()">
<div id='map' style="width: 400px; height: 400px">
</div>
</body>
</html>




<script type="text/javascript">


var geocoder;
var map;
	
/*******************************  Funcion de inicializacion del mapa ********************/
function  mapa(){

var options = {
	            zoom: 10
	            , center: new google.maps.LatLng('<?=$lat?>','<?=$lng ?>')
	            , mapTypeId: google.maps.MapTypeId.ROADMAP
	            , draggableCursor: 'default'
	            , draggingCursor: 'default'
	        };
map = new google.maps.Map(document.getElementById("map"),options);
 
geocoder = new google.maps.Geocoder();

// se coloca el punto inicial
var latlng = new google.maps.LatLng("<?=$lat?>","<?=$lng ?>");
 marker = new google.maps.Marker({
   position: latlng,	 
   map: map,
   draggable: true
 });
 
marker.setMap(map);

//  se programa el evento al mover el icono
google.maps.event.addListener(marker, 'drag', function() {
	geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {
          parent.$('latitud').value=marker.getPosition().lat();
          parent.$('longitud').value=marker.getPosition().lng();
         
          }
          
          
      }
    });
  });

//coloca las coordenadas de lat y lng

 parent.$('latitud').value = "<?=$lat?>";
 parent.$('longitud').value = "<?=$lng?>";


}


</script>