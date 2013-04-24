<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_circulo.inc.php');
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

// datos del proveedor y el servicio

$idproveedor = $_GET[idproveedor];
$idservicio = $_GET[idservicio];
$idusuariomod = $_SESSION[user];
$circulo = new circulo();
$circulo->leer($_GET[idcirculo]);

$array_medida= array('KM'=>'Km','MIL'=>'Ml');

$sql="select ARRAMBITO from catalogo_proveedor_servicio_x_circulo where IDCIRCULO ='$circulo->idcirculo'";
$result=$con->query($sql);
while($reg= $result->fetch_object()){
	$checked = $reg->ARRAMBITO;
}
$checked = ($checked=='')?'LOC':$checked;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" 	src="/librerias/windows_js_1.3/javascripts/prototype.js"></script>
<script type="text/javascript"	src="http://maps.google.com/maps?file=api&v=2&key=<?=$clave?>"></script>	

</head>
<body onload="mapa()">
<div>
	<form name='form_circulos'>
	<?=_('RADIO')?><input type="text" id='rad' name="radius" value="10" size="3"  onChange='redibujar();'></input>
	<? $con->cmbselect_ar('IDMEDIDA',$array_medida, $circulo->idmedida,''," id='idmedida' onChange='redibujar();' ");?>
	<input type='radio' id='local'  name='ambito' <?= ($checked=='LOC')?'checked':'' ?> > <?=_('LOCAL')?>
	<input type='radio' id='foraneo' name='ambito' <?= ($checked=='FOR')?'checked':'' ?> > <?=_('FORANEO')?>
	<input type ='button' value='Grabar' onclick="grabar();"></input>
	</form>
</div>

<div id='map' style="width: 500px; height: 450px"></div>

</body>
</html>
<script>

var map = null;
var idcirculo = '<?=$circulo->idcirculo?>';
var latitud = null;
var longitud = null;


function mapCircle(lat,lng,radius,idmedida)
{
	map.clearOverlays();
	var points = [];
	x = eval(lat);
	y = eval(lng);

	if (idmedida=='KM')		r = eval(radius)/100;
	if (idmedida=='MIL')	r = eval(radius)/60;
	for (var i = 0; i < 37; i++) {
		x1 = x+r*Math.cos(2*Math.PI*i/36);
		y1 = y+r*Math.sin(2*Math.PI*i/36);
		points.push(new GLatLng(x1,y1));
	}
	return points;
}




function mapa() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"));
		map.disableDoubleClickZoom();
		map.addControl(new GLargeMapControl());

		if (idcirculo!=''){
			var point = new GLatLng('<?=$circulo->latitud?>','<?=$circulo->longitud?>');
			var	marker = new GMarker(point);
				latitud = point.lat();
				longitud = point.lng();
			map.addOverlay(new GPolyline(mapCircle('<?=$circulo->latitud?>','<?=$circulo->longitud?>','<?=$circulo->radio?>','<?=$circulo->idmedida?>')));
			map.setCenter(point,<?=($circulo->zoommap=='')?10:$circulo->zoommap;?>);
			map.addOverlay(marker);
		}
		else
		{
			var point = new GLatLng('<?=$lat?>','<?=$lng?>');
				latitud = point.lat();
				longitud = point.lng();
			map.setCenter(point,10);
			var	marker = new GMarker(point);
		}
		GEvent.addListener(map, "click", function (overlay,point){
			var r = $('rad').value;
			var idmedida = $('idmedida').value;
			if (r=='') alert('<?=_('INGRESE UN VALOR PARA EL RADIO')?>');
			else if (idmedida=='') alert('<?=_('SELECCIONE UNA UNIDAD DE MEDIDA')?>');
			else{
				latitud=point.lat();
				longitud=point.lng();
				map.addOverlay(new GPolyline(mapCircle(latitud,longitud,r,idmedida)));
				marker.setPoint(point);
				map.addOverlay(marker);
			}
		});
	}
}


function redibujar(){
		
			var point = new GLatLng(latitud,longitud);
			var	marker = new GMarker(point);
				
			map.addOverlay(new GPolyline(mapCircle(latitud,longitud,$F('rad'),$F('idmedida'))));
			map.setCenter(point,<?=($circulo->zoommap=='')?10:$circulo->zoommap;?>);
			map.addOverlay(marker);
	
	return;
}



function grabar()
{


	if ($F('local'))   ambito= 'LOC';
	else  ambito= 'FOR';

	var r = $('rad').value;
	var idmedida = $('idmedida').value;
	if (r=='') alert('<?=_('INGRESE UN VALOR PARA EL RADIO')?>');
	else if (idmedida=='') alert('<?=_('SELECCIONE UNA UNIDAD DE MEDIDA')?>');
	else
	{
		new Ajax.Request('../../controlador/ajax/ajax_circulo.php?opcion=grabar',
		{
			method : 'post',
			parameters : {
				IDPROVEEDOR: '<?=$idproveedor?>',
				IDSERVICIO: '<?=$idservicio?>',
				ARRAMBITO: ambito,
				IDUSUARIOMOD: '<?=$idusuariomod?>',
				IDCIRCULO: '<?=$circulo->idcirculo?>',
				LATITUD: latitud,
				LONGITUD: longitud,
				RADIO: r,
				ZOOMMAP: map.getZoom(),
				IDMEDIDA: idmedida
			}

		});
		parent.win.close();
	}
	return;
}

</script>

