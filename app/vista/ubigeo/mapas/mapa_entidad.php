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
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet"
	href="/librerias/jquery-ui-1.8.16/development-bundle/themes/base/jquery.ui.all.css">
<link href="stilomapas.css" rel="stylesheet" type="text/css" />
<script type="text/javascript"
	src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="/librerias/jquery/jquery-1.7.1.js" type="text/javascript"></script>
<link rel="stylesheet"
	href="/librerias/jquery-ui-1.8.16/development-bundle/demos/demos.css">
<script src="polygon.js" type="text/javascript"></script>
<script type="text/javascript">

/***********************************************************************/

$(document).ready(function(){
	
var hexVal = "0123456789ABCDF".split("");	
var poly, map;
var markers = [];
var path = new google.maps.MVCArray;
 
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
} 

/********************************  Función borrar polygono ******************************/

$('#borrar_poligono').click(function(){
	poly.setMap(null);
	/*
	if (poly)	poly.setMap(null);
	for(var n = 0; n < markers.length; n++){
		markers[n].setMap(null);
		}
	for(var n = 0; n < path.length; n++){
		path[n].setMap(null);
		}*/
});
 
 /*******************************  Funcion grabar los vertices de poligono ***********/
$('#boton_grabar').click(function(){

if ($('#identidad2').val()=='0')  alert('Selecciona la entidad2');
else {	 

	var latitud = new Array();
	var longitud = new Array();
		
	path.forEach(function(elemento,i){
		latitud[i] = elemento.lat();
		longitud[i] = elemento.lng();
	});

	if (latitud[0]==null) alert('Marque el perimetro del poligono');
	else {
	var centro_map = map.getCenter();
	$.post('ajax_graba_poligono_entidad.php',
			{ 
			  CVEENTIDAD1: $('#identidad1').val(),
			  CVEENTIDAD2: $('#identidad2').val(),
			  ZOOMMAP    : map.getZoom(),
		      'LATITUD[]' : latitud,
		      'LONGITUD[]': longitud,
		      CEN_LAT: centro_map.lat(),
			  CEN_LNG: centro_map.lng()   
		  }, 
		  function(msg){
			 alert(msg);
			 $('#identidad2').val('');

			 init_map();
			 traza_poligonos();
			 poly = null;
			 markers = [];
			
			 path = new google.maps.MVCArray;
		 });
	}
}	 
});

/*******************************  Función para cambiar el segundo combo *****************/
$('#identidad1').change(function(){
	$.post('combo_entidad2.php',{
		CVEENTIDAD1: this.value
	},function(msg){
		$('#identidad2').html(msg); 
		traza_poligonos();
		});
});


/*******************************  funcion para crear un poligono **********************/
 $('#identidad2').live('change',function(){
 	
	var array_vertice = new google.maps.MVCArray;
	var zoommap=null;
	var cen_lat,cen_lng;
	
	if ($('#identidad2').val()=='')  init_map();
	else {
	$.ajax({
				data: "CVEENTIDAD1="+$('#identidad1').val()+"&CVEENTIDAD2="+$('#identidad2').val(),
				type: "POST",
			  	dataType: "xml",
				url: 'ajax_vertices_poligono.php',
				success: function(xml)
				{
					
		  		 	$(xml).find('data').each(function(){
		  		 	var bounds = new google.maps.LatLngBounds();
						 $(this).find('vertice').each(function()
									{
										var idpoligono = $(this).find('idpoligono').text();
										var orden = $(this).find('orden').text();
										var latitud = $(this).find('latitud').text();
										var longitud = $(this).find('longitud').text();
										var vertice = new google.maps.LatLng(latitud,longitud);
										array_vertice.push(vertice);
										bounds.extend(vertice);
										zoommap = $(this).find('zoom').text();	
										cen_lat = $(this).find('cen_lat').text();	
									    cen_lng = $(this).find('cen_lng').text();	 	
									});
							
						 /* define el poligono a mostrar */ 
						 poly = new google.maps.Polygon({
						                       paths: array_vertice,
						                       strokeColor: "#FF0000",
						                       strokeOpacity: 0.8,
						                       strokeWeight: 2,
						                       fillColor: "#FF0000",
						                       fillOpacity: 0.35
						                     });
						 poly.setMap(map);
						 map.fitBounds(bounds);
			 			 });
 					}  // fin del success
				});
	} // fin del else
 });

/***********************  Añade puntos al poligono *****************************/
 function addPoint(event){

	 	//alert(event);
	    path.insertAt(path.length, event.latLng);
	    // crea la marca 
	    var marker = new google.maps.Marker({
		    position: event.latLng,
		    map: map,
		    draggable: true
		});
		markers.push(marker);
		marker.setTitle("#" + path.length);
		google.maps.event.addListener(marker, 'click', function() 
				{
				marker.setMap(null);
				for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
				    markers.splice(i, 1);
		  			  path.removeAt(i);
		   		 }
				);
		google.maps.event.addListener(marker, 'dragend', function() {
			for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);			      path.setAt(i, marker.getPosition());
		}
		);
	 } // fin de la función




 /********************  Trazar un  poligono ********************************/	 
$('#trazar_poligono').click(function(){
	if ($('#identidad2').val()=='0')  alert('Selecciona la entidad2');
	else {	 
	poly = new google.maps.Polygon({
     	strokeWeight: 3,
     	fillColor: '#5555FF'
    	});
 	poly.setMap(map);
 	poly.setPaths(new google.maps.MVCArray([path]));
 	google.maps.event.addListener(map, 'click', addPoint);
    }
 });

 
/************************  BORRAR POLIGONO ***********************************************/

$('#boton_borrar').click(function(){

if (($('#identidad1').val()==0) || ($('#identidad2').val()==0)) alert('Seleccione las entidades');
else
{
$.post('borra_poligono',{
	CVEENTIDAD1 : $('#identidad1').val(),
	CVEENTIDAD2 : $('#identidad2').val()
},function(msg){
	alert(msg);	
	init_map();
	traza_poligonos();

})
}  // fin del else

}
);



/************************ MOSTRAR LOS POLIGONOS DE LA ENTIDAD1 *****************************/
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
			url: 'dibujar_poligonos.php',
			success: function(xml)
			{
				$(xml).find('data').each(function(){
					var bounds = new google.maps.LatLngBounds();
					 $(this).find('poligono').each(function()
								{
						 			var array_vertice = new google.maps.MVCArray;
									var nombre  = $(this).find('nombre').text();
									var cen_lat = $(this).find('cen_lat').text();
									var cen_lng = $(this).find('cen_lng').text();
									var zoom    = $(this).find('zoom').text();
									var cadena='[';
						
									$(this).find('vertices').each(function(){
											
											var latitud = $(this).find('latitud').text();
											var longitud = $(this).find('longitud').text();
											var punto = new google.maps.LatLng(latitud,longitud);
											array_vertice.push(punto);
											bounds.extend(punto);
											cadena = cadena + "{'x':"+latitud+", 'y':"+longitud+"},";
											
									});
										cadena = cadena + "]";
 										var con = new Contour(eval(cadena));
									    center = con.centroid();

									//alert('x: ' + center.x + ' y: ' + center.y)

									var posicion = new google.maps.LatLng(center.x,center.y);
					 				var marker = new google.maps.Marker({
										position: posicion,
										title:nombre,
										icon: "label.php?texto="+nombre,
										
									});
					 				marker.setMap(map);
									 
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
					
					 map.fitBounds(bounds);
						 });
			}});
}



 
 
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

<fieldset><legend>CREACION DE POLIGONOS DE ENTIDADES</legend>
<table style="float: left">
	<tr>
		<td><?=_('ENTIDAD 1')?></td>
		<td><? 
		$sql="SELECT cveentidad1,descripcion FROM $con->catalogo.catalogo_entidad  WHERE cveentidad1!='0' AND cveentidad2='0'";
		$con->cmbselect_db('IDENTIDAD1',$sql,'','id="identidad1"',"style='width:150px'",'');
		?></td>

	</tr>
	<tr>
		<td><?=_('ENTIDAD 2')?></td>
		<td id='zona_entidad2'><select id='identidad2'>
			<option value='0'></option>
		</select></td>
	</tr>

	<tr>
		<td colspan='2'><input type='button' id='trazar_poligono' name=''
			value='Dibujar Poligono'> <input type='button' id='boton_grabar'
			name='' value='Grabar Poligono'> <input type='button'
			id='boton_borrar' name='' value='Borrar Poligono'>
			
		</td>	
	</tr>




</table>
</fieldset>
</form>
</div>

</body>

</html>


