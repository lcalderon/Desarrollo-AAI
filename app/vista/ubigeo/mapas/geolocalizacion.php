<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_lang.inc.php');

$con = new DB_mysqli ( );

if ($con->Errno) {
	printf ( "Fallo de conexion: %s\n", $con->Error );
	exit ();
}

$tabla=$_GET[tabla];
$campo_display = $_GET[campo_display];
$campo_ubigeo = $_GET[campo_ubigeo];

// Datos del lugar del evento
$idubigeo = $_GET['idubigeo'];
$sql="SELECT CVEENTIDAD1,CVEENTIDAD2,CVEENTIDAD3,CVEENTIDAD4,DIRECCION,LATITUD,LONGITUD FROM $con->temporal.asistencia_lugardelevento WHERE ID='$idubigeo'";

$result = $con->query($sql);
while($reg = $result->fetch_object()){
	$cveentidad1 = $reg->CVEENTIDAD1; 
	$cveentidad2 = $reg->CVEENTIDAD2;
	$cveentidad3 = $reg->CVEENTIDAD3;
	$cveentidad4 = $reg->CVEENTIDAD4;
	$direccion   = $reg->DIRECCION;
	$latitud = $reg->LATITUD;
	$longitud = $reg->LONGITUD;
}



// Coordenadas por default del mapa de la oficina

$idpais = $con->lee_parametro('IDPAIS');
$n_ent= $con->lee_parametro('UBIGEO_NIVELES_ENTIDADES');
$lat = ($latitud==0)?$con->lee_parametro('UBICACION_PRIMARIA_LATITUD'):$latitud;
$lng = ($longitud==0)?$con->lee_parametro('UBICACION_PRIMARIA_LONGITUD'):$longitud;
$idusuario = $_SESSION['user'];
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

//coloca las coordenadas de lat y lng

$('#latitud').val("<?=$latitud?>");
$('#longitud').val("<?=$longitud?>");


}

 
/*******************************  Función para cambiar el segundo combo *****************/
$('#cveentidad1').change(function(){
	$.post('combo_entidad2.php',{
		CVEENTIDAD1: this.value
	},function(msg){
		$('#cveentidad2').html(msg); 
		});
});

/*******************************  Función para cambiar el tercer combo *****************/
$('#cveentidad2').live('change',function(){
	$.post('combo_entidad3.php',{
		CVEENTIDAD1: $('#cveentidad1').val(),
		CVEENTIDAD2: this.value
	},function(msg){
		$('#cveentidad3').html(msg); 
		});
});

/*******************************  Función para cambiar el cuarto combo *****************/
$('#cveentidad3').live('change',function(){
	$.post('combo_entidad4.php',{
		CVEENTIDAD1: $('#cveentidad1').val(),
		CVEENTIDAD2: $('#cveentidad2').val(),
		CVEENTIDAD3: this.value
	},function(msg){
		$('#cveentidad4').html(msg); 
		});
});



/******************************* Funcion grabar la posicion ***********************/
$('#btn_grabar').click(function(){

	
  var tabla 	  = '<?=$tabla?>';
  var cveentidad1 = $('#cveentidad1').val();
  var cveentidad2 = $('#cveentidad2').val();
  var cveentidad3 = $('#cveentidad3').val();
  var cveentidad4 = $('#cveentidad4').val();
  var direccion = $('#direccion').val();
  var latitud = $('#latitud').val();
  var longitud = $('#longitud').val();

  //alert("<?=$campo_ubigeo?>");
  $.post('ajax_grabar_localizacion.php?tabla='+tabla,{
	CVEENTIDAD1: cveentidad1,
	CVEENTIDAD2: cveentidad2,
	CVEENTIDAD3: cveentidad3,
	CVEENTIDAD4: cveentidad4,
	DIRECCION  : direccion,
	LATITUD	   : latitud,	
	LONGITUD   : longitud
	 },function(msg){
		// alert(msg);
		window.opener.document["form_datos_generales"].<?=$campo_ubigeo?>.value = msg;
		window.opener.document["form_datos_generales"].<?=$campo_display?>.value = $('#direccion').val();
		 window.close();
		 });
	
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
						$('#cveentidad2').val(ui.item.cveentidad2);
						}
					)
				});
		
		var location = new google.maps.LatLng(ui.item.latitud, ui.item.longitud);
        marker.setPosition(location);
        map.setCenter(location);
	}
});



 
/***************************************************************************/
/********************** opciones del mapa **********************************/ 
 init_map();

});
 




</script>

</head>

<body style="width:100%;height:100%;">
<div style='width:30%;margin:0;float:left;height:100%;'>
	
	<input type='hidden' id='latitud' >
	<input type='hidden' id='longitud' >
	<? if ($n_ent>=1):?>
	<span>
	  <label><?=_('ENTIDAD 1')?></label>
		<span id='zona_entidad1'> 
		<? 
		$sql="SELECT cveentidad1,descripcion FROM $con->catalogo.catalogo_entidad  WHERE cveentidad1!='0' AND cveentidad2='0'";
		$con->cmbselect_db('CVEENTIDAD1',$sql,$cveentidad1,'id="cveentidad1"',"style='width:150px'",'');
		?>	
		</span>
	</span>
	<?endif;?>	
	<p>	
	<? if ($n_ent>=2):?>
	<span>
		<label><?=_('ENTIDAD 2')?></label>
		<span id='zona_entidad2'>
		<? if ($cveentidad1!=''):?>
			<? 
			$sql="select CVEENTIDAD2, DESCRIPCION FROM $con->catalogo.catalogo_entidad	WHERE CVEENTIDAD1 = '$cveentidad1' AND CVEENTIDAD2!='0' AND CVEENTIDAD3='0' ORDER BY DESCRIPCION";
			$con->cmbselect_db('CVEENTIDAD2',$sql,$cveentidad2,'id="cveentidad2"',"style='width:150px'",'');
			?>
		<?else:?>
			<select name='CVEENTIDAD2' id='cveentidad2'>
				<option value=''></option> 
			</select>
		<?endif;?>
			
		</span>
	</span>
	<?endif;?>
	<p>
	<? if ($n_ent>=3):?>
	<span>
		<label><?=_('ENTIDAD 3')?></label>
		<span id='zona_entidad3'> 
		<? if ($cveentidad2!=''):?>
			<?
			$sql="select CVEENTIDAD3, DESCRIPCION FROM $con->catalogo.catalogo_entidad	WHERE CVEENTIDAD1 = '$cveentidad1' AND CVEENTIDAD2='$cveentidad2' AND CVEENTIDAD3!='0' AND CVEENTIDAD4 ='0' ORDER BY DESCRIPCION";
			$con->cmbselect_db('CVEENTIDAD3',$sql,'','id="cveentidad3"',"style='width:150px'",'');
			?>
		<? else:?>
			<select name='CVEENTIDAD3' id='cveentidad3'>
				<option value=''></option> 
			</select>
		<? endif;?>
		</span>
	</span>
	<?endif;?>
	<p>
	<? if ($n_ent>=4):?>
	<span>
		<label><?=_('ENTIDAD 4')?></label>
		<span id='zona_entidad4'> 
		<? if ($cveentidad3!=''):?>
			<?
			$sql="select CVEENTIDAD4, DESCRIPCION FROM $con->catalogo.catalogo_entidad	WHERE CVEENTIDAD1 = '$cveentidad1' AND CVEENTIDAD2='$cveentidad2' AND CVEENTIDAD3='$cveentidad3' AND CVEENTIDAD4 !='0' AND CVEENTIDAD5='0' ORDER BY DESCRIPCION";
			$con->cmbselect_db('CVEENTIDAD4',$sql,'','id="cveentidad4"',"style='width:150px'",'');
			?>
		<? else:?>
			<select name='CVEENTIDAD4' id='cveentidad4'>
				<option value=''></option> 
			</select>
		<? endif;?>
		</span>
	</span>
	<?endif;?>
	<p>
	
	
	
	<span >
	<label for="direccion"><?=_('DIRECCION')?></label><p>
		<textarea id='direccion' cols='40' rows='3' ><?=$direccion?></textarea>
	</span>	 
	<p>				
		<input type='button' value='Grabar' id='btn_grabar' >
	

</div>

<div id="map_canvas" style='width:70%;margin:0;float:rigth;height:100%;'></div>

</body>

</html>



