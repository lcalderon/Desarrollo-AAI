<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');


// recibimos el idasistencia
$idasistencia = (isset($_GET['IDASISTENCIA']))?$_GET['IDASISTENCIA']:'';

$con = new DB_mysqli ( );
if ($con->Errno) {
	printf ( "Fallo de conexion: %s\n", $con->Error );
	exit ();
}


$lat_asis=0;
$lng_asis=0;
$idubigeo=0;
$idservicio=0;

if ($idasistencia!=''){
	// obtenemos lat y lng de la asistencia
	$sql="
	SELECT 
		ce.ID,
		ale.CVEENTIDAD1,
		ale.CVEENTIDAD2, 
		ale.CVEENTIDAD3,
		ale.LATITUD, 
		ale.LONGITUD,
		ale.DIRECCION,
		a.IDSERVICIO,
		a.IDEXPEDIENTE,
		cs.DESCRIPCION
	FROM 
		$con->temporal.asistencia_lugardelevento ale,
		$con->catalogo.catalogo_entidad ce,
		$con->temporal.asistencia a,
		$con->catalogo.catalogo_servicio cs
	WHERE 
		ale.IDASISTENCIA = a.IDASISTENCIA
		AND a.IDSERVICIO = cs.IDSERVICIO
		AND ale.CVEENTIDAD1 = ce.CVEENTIDAD1 
		AND ale.CVEENTIDAD2 = ce.CVEENTIDAD2
		AND ale.idasistencia='$idasistencia'
		ORDER BY ce.ID ASC
	LIMIT 1;
";
	
$result = $con->query($sql);
while($reg=$result->fetch_object()){
	$idubigeo = $reg->ID;
	$cveentidad1 = $reg->CVEENTIDAD1;
	$cveentidad2 = $reg->CVEENTIDAD2;
	$lat_asis = $reg->LATITUD;
	$lng_asis = $reg->LONGITUD;
	$idservicio = $reg->IDSERVICIO;
	$idexpediente = $reg->IDEXPEDIENTE;
	$direccionEvento = $reg->DIRECCION;
	$nombreServicio =$reg->DESCRIPCION;
	}
	
// DATOS DEL EXPEDIENTE
$sql="
SELECT 
	CONCAT(ep.NOMBRE,' ',ep.APPATERNO,' ',ep.APMATERNO) NOMBREAFILIADO,
	ep.IDPERSONA
FROM 
	$con->temporal.expediente_persona ep
WHERE 
	ep.idexpediente ='$idexpediente'	
LIMIT 1
";	
$result = $con->query($sql);
while($reg=$result->fetch_object()){
	$nombreAfiliado = $reg->NOMBREAFILIADO;
	$idpersona = $reg->IDPERSONA;
}

// TELEFONOS DEL AFILIADO

$sql="
SELECT
	CONCAT(ept.CODIGOAREA,ept.NUMEROTELEFONO) TELEFONO
FROM
	$con->temporal.expediente_persona_telefono ept
WHERE
	ept.IDPERSONA='$idpersona'
";
$result = $con->query($sql);
$telefonos= null;
while($reg=$result->fetch_object())
{
   $telefonos[]=$reg->TELEFONO;
}
	
	
}  //fin del if

// Coordenadas por default del mapa de la oficina
$lat_filial=$con->lee_parametro('UBICACION_PRIMARIA_LATITUD');
$lng_filial=$con->lee_parametro('UBICACION_PRIMARIA_LONGITUD');
$lat = ($lat_asis!=0)?$lat_asis:$lat_filial;
$lng = ($lng_asis!=0)?$lng_asis:$lng_filial;


// Crea la matriz JSON para las  entidades 
$sql ="SELECT ID,DESCRIPCION,CVEENTIDAD1 FROM $con->catalogo.catalogo_entidad  WHERE cveentidad1!='0' AND cveentidad2='0'";
$result = $con->query($sql);

$entidades_json ="{title: 'Entidades','isFolder': true,  expand : true, children: [";
while($reg = $result->fetch_object()){
	$entidades_json.="{title: '$reg->DESCRIPCION','isFolder': true, children: [";
	$sql="SELECT ID,DESCRIPCION  FROM $con->catalogo.catalogo_entidad  WHERE cveentidad1='$reg->CVEENTIDAD1' AND cveentidad2!='0' AND cveentidad3 ='0'";
	$result1= $con->query($sql);
	while($reg1=$result1->fetch_object()){
		if ($reg1->ID == $idubigeo)$entidades_json.="{title: '$reg1->DESCRIPCION', key:'$reg1->ID', select: true },";
		else $entidades_json.="{title: '$reg1->DESCRIPCION', key:'$reg1->ID'},";
	}
	$entidades_json .= "],},";
}
$entidades_json .= "]}";


//  Crea la matriz JSON para los servicios 
$sql="SELECT IDFAMILIA,DESCRIPCION FROM $con->catalogo.catalogo_familia ;";
$result = $con->query($sql);
$servicios_json ="{title: 'Servicios','isFolder': true, expand : true , children: [";
while($reg = $result->fetch_object()){
	$servicios_json.="{title: '$reg->DESCRIPCION','isFolder': true, children: [";
	$sql="select IDSERVICIO,SUBSTRING(DESCRIPCION,INSTR(DESCRIPCION,'-')+2,30 ) DESCRIPCION  from $con->catalogo.catalogo_servicio where IDFAMILIA='$reg->IDFAMILIA'";
	$result1= $con->query($sql);
	while($reg1=$result1->fetch_object()){
		if ($reg1->IDSERVICIO==$idservicio) $servicios_json.="{title: '$reg1->DESCRIPCION', key:'$reg1->IDSERVICIO', select: true},";
		else $servicios_json.="{title: '$reg1->DESCRIPCION', key:'$reg1->IDSERVICIO'},";
	}
	$servicios_json .= "],},";
}
$servicios_json .= "]}";




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
	
 var hexVal = "0123456789ABCDF".split("");
 var markersArray = [];
 var markersInfoWin = [];
 var poly, map;
 var markers = [];
 var path = new google.maps.MVCArray;
 var treeDataServicios = <?=$servicios_json ?>;
 var treeDataEntidades = <?=$entidades_json ?>;
 var serv_selec ="<?=($idservicio!=0)?$idservicio:''?>";
 var entidades_selec = "<?=($idubigeo!=0)?$idubigeo:''?>";


 $("#tree1").dynatree({
     checkbox: true,
     selectMode: 3,
     children: treeDataEntidades,
     onSelect: function(select, node){
    	    // Get a list of all selected nodes, and convert to a key array:
    	    var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
    	    	 var seleccion=[];
    	        if (node.data.key.indexOf('_')==-1) seleccion.push(node.data.key);
    	      return seleccion;
    	    });
    	  //  Aqui ejecutar la funcion para pintar los proveedores
    	    entidades_selec = selKeys;
    	    init_map();
    	    traza_poligonos();	
    	    ver_proveedores();
    	  
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
     cookieId: "dynatree-Cb1",
     idPrefix: "dynatree-Cb1-"

 });
 

 $("#tree2").dynatree({
     checkbox: true,
     selectMode: 3,
     children: treeDataServicios,
     onSelect: function(select, node) {
     // Get a list of all selected nodes, and convert to a key array:
       var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
       	 var seleccion=[];
           if (node.data.key.indexOf('_')==-1) seleccion.push(node.data.key);
         return seleccion;
       });
     //  Aqui ejecutar la funcion para pintar los proveedores
       serv_selec = selKeys;

       init_map();
       traza_poligonos();	
       ver_proveedores();
      
       
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
     cookieId: "dynatree-Cb2",
     idPrefix: "dynatree-Cb2-"

 });




 
/*******************************  Funcion de inicializacion del mapa ****************/
function  init_map(){

	
var nombreAfiliado="<?=$nombreAfiliado?>";
var telefonoAfiliado = "<?=implode(',\n ',$telefonos) ?>";
var direccionEvento ="<?=$direccionEvento?>";
var nombreServicio ="<?=$nombreServicio?>";

var options = {
	            zoom: 10, 
	            center: new google.maps.LatLng('<?=$lat?>','<?=$lng ?>'), 
	            mapTypeId: google.maps.MapTypeId.ROADMAP, 
	            draggableCursor: 'default', 
	            draggingCursor: 'default'
	        };
map = new google.maps.Map(document.getElementById("map_canvas"),options);
 
var datosAfiliado = '<table><tr><td colspan=3><h1>'+nombreAfiliado+'</h1></td><tr>'+
'<tr><td><b>Direccion:</b></td><td>'+direccionEvento+'</td></tr>'+
'<tr><td><b>Telefonos:</b></td><td>'+telefonoAfiliado+'</td></tr>'+
'<tr><td><b>Servicio:</b></td><td>'+nombreServicio+'</td></tr>'+

'</table>';

var infowindow = new google.maps.InfoWindow({content: datosAfiliado});

var posicion = new google.maps.LatLng(<?=$lat_asis?>,<?=$lng_asis?>);
var marker = new google.maps.Marker({
		position: posicion,
		//title:nombre,
		icon: "/imagenes/iconos-markers/circulo.png"
});

google.maps.event.addListener(marker, 'click', function() {
	infowindow.open(map,this);
});


markersArray.push(marker);
markersInfoWin.push(infowindow);
marker.setMap(map);
 
}

/******************************* Funcion crea color *********************************/
function makeColor(){
    
    return '#' + hexVal.sort(function(){
        return (Math.round(Math.random())-0.5);
    }).slice(0,6).join('');
}

function traza_poligonos(){
	
	$.ajax({
		data: "CVEENTIDAD1="+$('#identidad1').val()+"&ENTIDADES="+entidades_selec+"&SERVICIO="+serv_selec,
		type: "POST",
	   	dataType: "xml",
		url: 'ajax_poligonos_V2.php',
		success: function(xml)
		{
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


/******************************** Funcion ver proveedores *************************/

function ver_proveedores(){
	
$.ajax({
		data: "CVEENTIDAD1=15&CVEENTIDAD2=05&SERVICIO="+serv_selec+"&ENTIDADES="+entidades_selec,
		type: "POST",
	 	dataType: "xml",
		url: 'lista_proveedores.php',
		success: function(xml){
			$(xml).find('data').each(function(){
				var bounds = new google.maps.LatLngBounds();
		 		$(this).find('proveedor').each(function(){
		 				var idproveedor  = $(this).find('idproveedor').text();
		 				var nombre  = $(this).find('nombre').text();
		 				var latitud  = $(this).find('latitud').text();
		 				var longitud  = $(this).find('longitud').text();
		 				var direccion  = $(this).find('direccion').text();
		 				var servicios = $(this).find('servicios').text();
		 				var telefonos = $(this).find('telefonos').text();
		 				var icono = $(this).find('color').text();
		 				
		 				var datosProveedor = '<table id="content_'+idproveedor+'"><tr><td colspan=3><h1>'+nombre+'</h1></td><tr>'+
		 				'<tr><td><b>Direccion</b></td><td>'+direccion+'</td></tr>'+
		 				'<tr><td><b>Servicios:</b></td><td>'+ servicios+'</td></tr>'+
		 				'<tr><td><b>Telefonos:</b></td><td>'+ telefonos+'</td></tr>'+
		 				'<tr><td colspan=2><input type="button" value="Asignar"></td></tr>'+
						'</table>';
						
						var infowindow = new google.maps.InfoWindow({content: datosProveedor});
					
		 				var posicion = new google.maps.LatLng(latitud,longitud);
		 				var marker = new google.maps.Marker({
							position: posicion,
							title:nombre,
							icon: "/imagenes/iconos-markers/"+icono+".png"
							
						});
	
		 				google.maps.event.addListener(marker, 'click', function() {
							infowindow.open(map,this);
						/*	google.maps.event.addListener(marker, 'mouseout', function() {
									infowindow.close(map,this);
							});*/
							
						});
						
		 				markersArray.push(marker);

		 				bounds.extend(marker.getPosition());
		 				markersInfoWin.push(infowindow);
						marker.setMap(map);
						map.fitBounds(bounds);
			 	});
		 		
		 	});
	
			}
		});
	
}


/***************************************************************************/
/********************** opciones del mapa **********************************/ 
 init_map();
 traza_poligonos();	
 ver_proveedores();

});
 
</script>
</head>
<body style="width:100%;height:100%;">

<div id="map_canvas" style='width:70%;margin:0;float:left;height:100%;'></div>
<div style='width:30%;margin:0;float:right;height:100%;overflow:auto'>
	<div style='float:right;width:100%;margin:0;' id='tree1'>
	</div>
	<div style='float:right;width:100%;margin:0;'  id="tree2">
	</div>
	
</div>

</body>

</html>


