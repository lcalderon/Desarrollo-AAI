<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_lang.inc.php');


$con = new DB_mysqli ( );
if ($con->Errno) {
	printf ( "Fallo de conexion: %s\n", $con->Error );
	exit ();
}

// Coordenadas por default del mapa de la oficina
$lat = $con->lee_parametro('UBICACION_PRIMARIA_LATITUD');
$lng = $con->lee_parametro('UBICACION_PRIMARIA_LONGITUD');
$idpais = $con->lee_parametro('IDPAIS');
$idusuario = $_SESSION['user'];
$clave = $con->lee_parametro($con->url());

$sql="
SELECT 
	ci.IDICONO,
	cic.DESCRIPCION,
	ci.NOMBRE
FROM
	$con->catalogo.catalogo_icono ci,
	$con->catalogo.catalogo_icono_categoria cic
WHERE
	ci.IDCATEGORIA = cic.IDCATEGORIA
ORDER BY cic.IDCATEGORIA
 ";

$result = $con->query($sql);
while ($reg = $result->fetch_object()){
$iconos[$reg->DESCRIPCION][] = array('id'=>$reg->IDICONO,'nombre'=>$reg->NOMBRE);
	
}


?>

<html>
<head>
<link rel="stylesheet" href="/librerias/jquery-ui-1.8.16/development-bundle/themes/base/jquery.ui.all.css">
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="/librerias/jquery/jquery-1.7.1.js" type="text/javascript"></script>

<script src="/librerias/jquery-ui-1.8.16/development-bundle/ui/jquery.ui.core.js" type="text/javascript"></script>
<script src="/librerias/jquery-ui-1.8.16/development-bundle/ui/jquery.ui.widget.js" type="text/javascript"></script>
<script src="/librerias/jquery-ui-1.8.16/development-bundle/ui/jquery.ui.position.js" type="text/javascript"></script>
<script src="/librerias/jquery-ui-1.8.16/development-bundle/ui/jquery.ui.autocomplete.js" type="text/javascript"></script>
<script src="/librerias/jquery-ui-1.8.16/development-bundle/ui/jquery.ui.resizable.js"></script>
<script src="/librerias/jquery-ui-1.8.16/development-bundle/ui/jquery.ui.accordion.js" type="text/javascript"></script>
<link rel="stylesheet" href="/librerias/jquery-ui-1.8.16/development-bundle/demos/demos.css">

<style>
	.ui-autocomplete-loading {
	 background: white url('/librerias/jquery-ui-1.8.16/development-bundle/demos/autocomplete/images/ui-anim_basic_16x16.gif') right center no-repeat; 
	 }
	</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript">

/***********************************************************************/


$(document).ready(function(){
var geocoder;
var map;
	
/*******************************  Funcion de inicializacion del mapa ********************/
function  init_map(){

var options = {
	            zoom: 10
	            , center: new google.maps.LatLng('<?=$lat?>','<?=$lng ?>')
	            , mapTypeId: google.maps.MapTypeId.ROADMAP
	            , draggableCursor: 'default'
	            , draggingCursor: 'default'
	        };
map = new google.maps.Map(document.getElementById("map_canvas"),options);
 
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
          $('#idPosicion').val('');   
          $('#direccion').val(results[0].formatted_address);
          $('#latitud').val(marker.getPosition().lat());
          $('#longitud').val(marker.getPosition().lng());
         
          }
          
          
      }
    });
  });
 	 
}

 
/*******************************  Función para cambiar el segundo combo *****************/
$('#idEntidad1').change(function(){
	$.post('combo2.php',{
		ENTIDAD1: this.value
	},function(msg){
		$('#zona_entidad2').html(msg); 
		});
	
});

/******************************* Funcion grabar la posicion ***********************/
$('#btn_grabar').click(function(){
	
  var id = $('#idPosicion').val();
  var entidad1 = $('#idEntidad1').val();
  var entidad2 = $('#idEntidad2').val();
  var direccion = $('#direccion').val();
  var latPosicion = $('#latitud').val();
  var lngPosicion = $('#longitud').val();
  var idIcono = $('input:checked').val();
	
  if (entidad1=='') alert('Seleccione la entidad1');
   else if (entidad2=='') alert('Seleccione la entidad2');
   else { 	
  
  if ($('#idPais').val()=='') 
	  	cvepais="<?=$idpais?>";
  else 
	  cvepais = $('#idPais').val();
  
  
  $.post('guardarPosicion.php',{
	  	ID: id,
	  	CVEPAIS: cvepais,	
		CVEENTIDAD1: entidad1,
		CVEENTIDAD2: entidad2,
		CALLE: direccion,
		LATITUD: latPosicion,
		LONGITUD: lngPosicion,
		IDICONO: idIcono,
		IDUSUARIOMOD: "<?=$idusuario?>" 
	},function(msg){
		  alert('Se grabo correctamente');
		});
   }
});



$('#btn_borrar').click(function(){
	var id = $('#idPosicion').val();
	if (id!=''){
	$.post('ajax_borrarDireccion.php',{
		ID: id
		},function(msg){
			alert('Se borro la ubicación');
			});	
	}
	else alert('Esta dirección no esta registrada');
});

$("#direccion").autocomplete({
    source: "ajax_direccion.php",
    minLength: 2,
	select: function( event, ui ) {
		$('#idPosicion').val(ui.item.id);
		$('#latitud').val(ui.item.latitud);
		$('#longitud').val(ui.item.longitud);
		//$('#idEntidad1').val(ui.item.cveentidad1);
		$.post('combo1.php',{
			ENTIDAD1: ui.item.cveentidad1
			},function(data1){
				$('#zona_entidad1').html(data1);
				$.post('combo2.php',{
					ENTIDAD1: ui.item.cveentidad1
					},function(data2){
						$('#zona_entidad2').html(data2);
						$('#idEntidad2').val(ui.item.cveentidad2);
						}

					)

				});
		
		
		var location = new google.maps.LatLng(ui.item.latitud, ui.item.longitud);
        marker.setPosition(location);
        map.setCenter(location);
				
		
	}
   
  });

$( "#accordion" ).accordion(
		{collapsible: true,
			}
		);


$(function() {
	$( "#accordionResizer" ).resizable({
		minHeight: 40,
		resize: function() {
			$( "#accordion" ).accordion( "resize" );
		}
	});
});

 
/***************************************************************************/
/********************** opciones del mapa **********************************/ 
 init_map();

});
 




</script>

</head>

<body style="width:100%;height:100%;">
<div id="map_canvas" style='width:70%;margin:0;float:left;height:100%;'></div>
<div style='width:30%;margin:0;float:right;height:100%;'>
	<input type='hidden' id='idPosicion' >
	<input type='hidden' id='idPais' >
	<input type='hidden' id='latitud' >
	<input type='hidden' id='longitud' >
	<span >
	<label><?=_('ENTIDAD 1')?></label>
		<span id='zona_entidad1'> 
		<? 
		$sql="SELECT cveentidad1,descripcion FROM $con->catalogo.catalogo_entidad  WHERE cveentidad1!='0' AND cveentidad2='0'";
		$con->cmbselect_db('IDENTIDAD1',$sql,'','id="idEntidad1"',"style='width:150px'",'');
		?>	
		</span>
	</span>	
	<p>	
	<span >
	<label><?=_('ENTIDAD 2')?></label>
		<span id='zona_entidad2'> 
			<select></select>
		</span>
	<p>
	</span>

	<span >
	<label for="direccion"><?=_('DIRECCION')?></label><p>
		<textarea id='direccion' cols='40' rows='3' ></textarea>
	</span>	 
	<p>				
		<input type='button' value='Grabar' id='btn_grabar' >
		<input type='button' value='Eliminar' id='btn_borrar'>
		
<!-- 
<div id="accordionResizer" style="padding:10px; width:350px;" class="ui-widget-content">
<div   id="accordion" style='overflow:auto;'>
 <?foreach ($iconos as $categoria =>$arr_categoria):?>
	<h3><a href="#"><?=$categoria?></a></h3>
	<div style="padding:10px; height:50px;">
		<?$i=0;?>
		<p>
		<table>
		<?foreach ($arr_categoria as $icono):?>
		<?if (($i%5)==0):?> <tr> <?endif;?>
		<?$i++;?>
		<td>
			<table>
				<tr><td align='center'><input type='radio' name='IDCONO' value='<?=$icono[id]?>'   <?=($icono[id]==1?'checked':'');?>></td></tr>
				<tr><td align='center'><img src="/imagenes/iconos-markers/iconos-mapas/<?=$icono[nombre]?>".png></td></tr>
				<tr><td align='center'><?=$icono[nombre]?></td></tr>
			</table>
		</td>
		<?if (($i%5)==0):?> </tr> <?endif;?>
		<?endforeach;?>
		</table>
		</p>
	</div>	
  <?endforeach;?>
</div>  <!-- fin del accordion -->
<span class="ui-icon ui-icon-grip-dotted-horizontal" style="margin:2px auto;"></span>
</div><!-- End accordionResizer -->

 -->
</div>
</body>

</html>



