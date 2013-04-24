<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');


$con = new DB_mysqli ( );
if ($con->Errno) {
	printf ( "Fallo de conexion: %s\n", $con->Error );
	exit ();
}

// Coordenadas por default del mapa de la oficina
$lat = $con->lee_parametro('UBICACION_PRIMARIA_LATITUD');
$lng = $con->lee_parametro('UBICACION_PRIMARIA_LONGITUD');
$clave = $con->lee_parametro($con->url());

//  Crea la matriz JSON para los servicios 
$sql="SELECT IDFAMILIA,DESCRIPCION FROM $con->catalogo.catalogo_familia ;";
$result = $con->query($sql);
$listado_json ="{title: 'Servicios','isFolder': true,  children: [";
while($reg = $result->fetch_object()){
	$listado_json.="{title: '$reg->DESCRIPCION','isFolder': true, children: [";
	$sql="select IDSERVICIO,DESCRIPCION  from $con->catalogo.catalogo_servicio where IDFAMILIA='$reg->IDFAMILIA'";
	$result1= $con->query($sql);
	while($reg1=$result1->fetch_object()){
		$listado_json.="{title: '$reg1->DESCRIPCION', key:'$reg1->IDSERVICIO'},";
	}
	$listado_json .= "]},";
}
$listado_json .= "]}";




?>

<html>
<head>
<link href="stilomapas.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" 	src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script src="jquery/jquery.js" type="text/javascript"></script>
<script src="jquery/jquery-ui.custom.js" type="text/javascript"></script>
<script src="jquery/jquery.cookie.js" type="text/javascript"></script>
<link href="src/skin/ui.dynatree.css" rel="stylesheet" type="text/css" id="skinSheet">
<script src="src/jquery.dynatree.js" type="text/javascript"></script>
<script type="text/javascript">

/***********************************************************************/

$(document).ready(function(){
	
 var hexVal = "0123456789ABCDEF".split("");
 var markersArray = [];
 var poly, map;
 var markers = [];
 var path = new google.maps.MVCArray;
 var treeData = <?=$listado_json ?>;
 var serv_selec ='';


 $("#tree3").dynatree({
     checkbox: true,
     selectMode: 3,
     children: treeData,
     onSelect: function(select, node) {
     // Get a list of all selected nodes, and convert to a key array:
       var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
       	 var seleccion=[];
           if (node.data.key.indexOf('_')==-1) seleccion.push(node.data.key);
         return seleccion;
       });
     //  Aqui ejecutar la funcion para pintar los proveedores
       serv_selec = selKeys;
   
     
     },
     onDblClick: function(node, event) {
       node.toggleSelect();
     },
     onKeydown: function(node, event) {
       if( event.which == 32 ) {
         node.toggleSelect();
         return false;
       }
     },
     // The following options are only required, if we have more than one tree on one page:
//       initId: "treeData",
     cookieId: "dynatree-Cb3",
     idPrefix: "dynatree-Cb3-"

 });




 
/*******************************  Funcion de inicializacion del mapa ****************/
function  init_map(){
var options = {
	            zoom: 10, 
	            center: new google.maps.LatLng('<?=$lat?>','<?=$lng ?>'), 
	            mapTypeId: google.maps.MapTypeId.ROADMAP, 
	            draggableCursor: 'default', 
	            draggingCursor: 'default'
	        };
 map = new google.maps.Map(document.getElementById("map_canvas"),options);
}

/******************************* Funcion crea color *********************************/
function makeColor(){
    
    return '#' + hexVal.sort(function(){
        return (Math.round(Math.random())-0.5);
    }).slice(0,6).join('');
}

function traza_poligonos(){
	$.ajax({
		data: "CVEENTIDAD1="+$('#identidad1').val(),
		type: "POST",
	   	dataType: "xml",
		url: 'ajax_poligonos.php',
		success: function(xml)
		{
		init_map();
  		 	$(xml).find('data').each(function(){
				 $(this).find('poligono').each(function()
							{
					 			var array_vertice = new google.maps.MVCArray;
								var nombre  = $(this).find('nombre').text();
								var cen_lat = $(this).find('cen_lat').text();
								var cen_lng = $(this).find('cen_lng').text();
								var zoom    = $(this).find('zoom').text();
								$(this).find('vertices').each(function(){
										var latitud = $(this).find('latitud').text();
										var longitud = $(this).find('longitud').text();
										var punto = new google.maps.LatLng(latitud,longitud);
										array_vertice.push(punto);
								});
								 
							    var	colorPoligono = makeColor();
								poly = new google.maps.Polygon({
								                       paths: array_vertice,
								                       strokeColor: colorPoligono,
								                       strokeOpacity: 0.8,
								                       strokeWeight: 2,
								                       fillColor: colorPoligono,
								                       fillOpacity: 0.35
								                     });
			                    poly.setMap(map);
							});
					 });
		}});
}

/*******************************  Función para cambiar el segundo combo *****************/
$('#identidad1').change(function(){
	$.post('combo2.php',{
		ENTIDAD1: this.value
	},function(msg){
		$('#zona_entidad2').html(msg);
		traza_poligonos();
		 
		});
});



/******************************** Funcion ver proveedores *************************/

$('#btn_verproveedores').click(function(){
	
$.ajax({
		data: "CVEENTIDAD1=15&CVEENTIDAD2=05&SERVICIO="+serv_selec,
		type: "POST",
	   	dataType: "xml",
		url: 'lista_proveedores.php',
		success: function(xml){
			$(xml).find('data').each(function(){
		 		$(this).find('proveedor').each(function(){
		 				var idproveedor  = $(this).find('idproveedor').text();
		 				var nombre  = $(this).find('nombre').text();
		 				var latitud  = $(this).find('latitud').text();
		 				var longitud  = $(this).find('longitud').text();

		 				var posicion = new google.maps.LatLng(latitud,longitud);
		 				var marker = new google.maps.Marker({
							position: posicion,
							title:""
							
						});
		 				markersArray.push(marker);
						marker.setMap(map);
			 	});
		 	});
	
			}
		});
	
});


/***************************************************************************/
/********************** opciones del mapa **********************************/ 
 init_map();

});
 
</script>
</head>
<body>

<div id="map_canvas" style="width: 75%; height: 100%; float: left"></div>
<div style='float: right; width: 25%; height: 100%'>

<div>
<form>
<fieldset><legend>Visualizaci&oacute;n y busqueda</legend>
<table style="float: left">
	<tr>
		<td>Provincia</td>
		<td><? 
		$sql="SELECT cveentidad1,descripcion FROM $con->catalogo.catalogo_entidad  WHERE cveentidad1!='0' AND cveentidad2='0'";
		$con->cmbselect_db('IDENTIDAD1',$sql,'','id="identidad1"',"style='width:150px'",'');
		?></td>	
	</tr>
	<tr>
		<td>Cantones</td>
		<td id='zona_entidad2'> 
			<select></select>
		</td>
	</tr>
	<tr>
	</tr>
	<tr>
		<td colspan='2' id="tree3"></td>
	</tr>
	<tr>
		<td colspan='2' > <input type='button' name='' value='Ver proveedores' id='btn_verproveedores'></td>
	</tr>
	
</table>
</fieldset>
</form>
</div>

</body>

</html>


