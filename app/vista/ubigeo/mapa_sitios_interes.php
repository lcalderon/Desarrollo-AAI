<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_usuario.inc.php');
include_once('../includes/head_prot_win.php');


// **************** Coordenadas vienen del programa que lo llama  ***************//
$con = new DB_mysqli();
$clave = $con->lee_parametro($con->url());
$nreg=20;
$nc=2;
if (isset($_GET[lat])){
	$lat = $_GET[lat];
	$lng = $_GET[lng];
}
else {
	$lat = $con->lee_parametro('UBICACION_PRIMARIA_LATITUD');
	$lng = $con->lee_parametro('UBICACION_PRIMARIA_LONGITUD');
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript"	src="http://maps.google.com/maps?file=api&v=2&key=<?=$clave?>"></script>	

</head>
<body onload="mapa()">
<div id='map' style="width: 700px; height: 550px;float:left" >
</div>
<div id='grid' style="float:right" >
  <div id='form_busqueda'>
  	<input type='text' name='DIRECCION' id='direccion' size='30' value='' onkeyup="buscar_direccion(this.value)">
 	<input type="button" value='Ver en Mapa' onclick="mostrar_puntos();" class="normal">
  </div>
  <div id='resultado'>
  	<table cellpadding="0"  cellspacing="0" border="0" id="table" class="tinytable" align="left">
 	 <thead>
	  <tr>
    	<th class="nosort">Puntos de interes</th>
      </tr>
  	</thead>
 	<tbody>
  	 	</tbody>
  	</table>
   </div>
</div>


<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script>
<script type="text/javascript">

var sorter = new TINY.table.sorter('sorter','table',{
	headclass:'head',
	ascclass:'asc',
	descclass:'desc',
	evenclass:'evenrow',
	oddclass:'oddrow',
	evenselclass:'evenselected',
	oddselclass:'oddselected',
	paginate:false,
	size:10,
	hoverid:'selectedrow',
	pageddid:'pagedropdown',
	sortcolumn:1,
	sortdir:1,
	init:true
});

</script>




<script type="text/javascript">
var map= null;
var bounds;
function mapa()
{
	if (GBrowserIsCompatible())
	{
		map = new GMap2(document.getElementById("map"));
		map.disableDoubleClickZoom();
		map.addControl(new GLargeMapControl());

		var point = new GLatLng('<?=$lat?>','<?=$lng?>');
		map.setCenter(point,17);
		bounds = new GLatLngBounds();

		var marker = new GMarker(point);
		GEvent.addListener(map, "click", function (overlay,point){
			if (point){
				var form_contenido ="<div style='font-size: 8pt; font-family: verdana'><form action='graba_direccion.php' method='post' name='form_calle'><table><br>Direccion<input type='text' name='CALLE'><BR>LAT <input type='text' name='LATITUD' value='"+ point.lat()+"' readonly><BR>LNG <input type='text' name='LONGITUD' value='"+ point.lng()+"' readonly><br><input type='submit' value='grabar'><table></div>";
				marker.setPoint(point);
				marker.openInfoWindowHtml(form_contenido);
				parent.$('latitud').value=point.lat();
				parent.$('longitud').value=point.lng();
			}
		});
		bounds.extend(marker.getPoint());
		map.addOverlay(marker);
		return;
	}
}


function buscar_direccion(direccion){
	$('resultado').innerHTML='';

	if (direccion.length > <?=$nc?>) {
		new Ajax.Updater('resultado','ajax_calles.php',
		{
			method : 'get',
			parameters: {
				calle : direccion,
				nreg : '<?=$nreg?>'
			}

		});
	}
}



function var_dump(obj) {
	if(typeof obj == "object") {
		return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "")+"\nValue: " + obj;
	} else {
		return "Type: "+typeof(obj)+"\nValue: "+obj;
	}
}


Array.prototype.isItAnArray = true; // give this property only to real arrays

function getLength(thing) {
	if (typeof thing == "object" && !thing.isItAnArray) {
		var count = 0;
		for (var test in thing) {
			count++;
		}
		return count;
	} else {
		return thing.length;
	}
}


function mostrar_puntos()
{
	new Ajax.Request('ajax_puntos.php',
	{
		method : 'get',
		parameters: {
			calle : $F('direccion'),
			nreg : '<?=$nreg?>'
		},
		onSuccess: function(t){
			//BORRA LOS PUNTOS

			mapa();

			//PINTA LOS GLOBOS AZULES
			var blueIcon = new GIcon(G_DEFAULT_ICON);
			var puntos = JSON.parse(t.responseText);


			for(i=1;i<=getLength(puntos);i++)
			{
				var blueIcon = new GIcon(G_DEFAULT_ICON);
				var cadena = puntos[i].toString();
				var coord = cadena.split(',');
				blueIcon.image = "icon.php?num="+i;
				markerOptions = { icon:blueIcon };
				var point = new GLatLng(coord[1],coord[2]);
				var marker = new GMarker(point,markerOptions);
				bounds.extend(marker.getPoint());
				map.addOverlay(marker);
			}

			map.setZoom(map.getBoundsZoomLevel(bounds));
			map.setCenter(bounds.getCenter());
		}
	});
}


function eliminar_punto(id)
{
	Dialog.confirm("<?=_('¿ESTA SEGURO DE BORRAR EL PUNTO DE INTERES?');?>",
	{
		top: 10,
		width:250,
		className: "alphacube",
		okLabel: "Si",
		cancelLabel:"No",
		buttonClass: "normal",
		onOk: function(dlg)
		{
			new Ajax.Request('ajax_borrar_punto.php',
			{
				method : 'post',
				parameters:{
					ID : id
				},
				onSuccess: function (dlg)
				{
					buscar_direccion($F('direccion'));
				}
			});
			return true;
		}
	});
}
</script>
