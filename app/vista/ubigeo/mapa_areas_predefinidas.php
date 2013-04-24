<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_poligono.inc.php');
include_once('../../modelo/clase_lang.inc.php');

$con = new DB_mysqli ( );
if ($con->Errno) {
	printf ( "Fallo de conexion: %s\n", $con->Error );
	exit ();
}

// Coordenadas por default del mapa de la oficina
$lat = $con->lee_parametro('UBICACION_PRIMARIA_LATITUD');
$lng = $con->lee_parametro('UBICACION_PRIMARIA_LONGITUD');
$clave = $con->lee_parametro($con->url());

$poligono = new  poligono();
$poligono->leer($_GET[idpoligono]);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript"	src="http://maps.google.com/maps?file=api&v=2&key=<?=$clave?>"></script>	
<script type="text/javascript" 	src="/librerias/windows_js_1.3/javascripts/prototype.js"></script>


</head>
<body onload="mapa()">

<div>
	<input type='text' id='nombre' name='NOMBRE' size='25' value="<?=$poligono->nombre?>" ></input>
	<input type="button" name="clear" value="limpiar lineas" onclick="limpiar()" ></input>
	<input type="button" name="editar" value="editar" onclick="editar()" ></input>
	<input type ='button' value='Grabar' onclick="grabar();"></input>
</div>

<div id='map' style="width: 500px; height: 450px"></div>

</body>
</html>
<script>

var map = null;
var polyline = new GPolyline([],"#ff0000", 5);

function mapa() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"),{draggableCursor: 'crosshair', draggingCursor: 'pointer'});
		map.disableDoubleClickZoom();
		map.addControl(new GLargeMapControl());
		var point = new GLatLng(<?=$lat?>,<?=$lng?>);
		map.setCenter(point,<?=($poligono->zoommap=='')?13:$poligono->zoommap;?>);
		mostrar_vertices(<?=json_encode($poligono->vertices[lat])?>,<?=json_encode($poligono->vertices[lng])?>,<?=count($poligono->vertices[lat]) ?>);
		GEvent.addListener(map,"click", function(overlay,latlng){
			polyline.insertVertex(polyline.getVertexCount(),latlng);
		});
		map.addOverlay(polyline);
		polyline.enableEditing();
	}
}

function mostrar_vertices(lat,lng,n) {
	for(i=1;i<=n-1;i++){
		var point = new GLatLng(lat[i],lng[i]);
		polyline.insertVertex(i,point);
	}
	map.setCenter(point,<?=($poligono->zoommap=='')?13:$poligono->zoommap;?>);
	return;
}


function limpiar(){
	map.clearOverlays();
	polyline = new GPolyline([],"#ff0000", 5);
	map.addOverlay(polyline);
	polyline.enableEditing();
	return;
}

function editar()
{
	map.addOverlay(polyline);
	polyline.enableEditing();
	return;
}

function serialize(arr)
{
	var res = '';
	for(i=0; i<arr.length; i++)
	{
		res += arr[i]+';';
	}
	return res;
}

function grabar()
{
	var latitud = new Array();
	var longitud = new Array();

	if ($F('nombre')=='') alert("<?=_('INGRESE EL NOMBRE DEL POLIGONO')?>");
	else if (polyline.getVertexCount() < 3 ) alert("<?=_('MARQUE 3 VERTICES COMO MINIMO')?>");
	else
	{
		for (var i=0; i<polyline.getVertexCount(); i++ )
		{
			latitud[i] = polyline.getVertex(i).lat();
			longitud[i] = polyline.getVertex(i).lng();
		}
		latitud[i] = polyline.getVertex(0).lat();
		longitud[i] = polyline.getVertex(0).lng();


		new Ajax.Request('../../controlador/ajax/ajax_areas_predefinidas.php?opcion=grabar',
		{
			method : 'post',
			parameters: {
				IDPOLIGONO:'<?=$poligono->idpoligono?>',
				NOMBRE: $('nombre').value,
				ZOOMMAP: map.getZoom(),
				IDUSUARIOMOD: '<?=$_SESSION[user]?>',
				LATITUD: serialize(latitud),
				LONGITUD: serialize(longitud)
			},
			onSucess: function(t){
				alert(t.responseText);
			}

		});
		parent.win.close();
	}
	return;
}

</script>

