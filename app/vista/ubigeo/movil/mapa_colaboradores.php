<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_usuario.inc.php');
include_once('../../includes/jquery/head_prot_win.php');



// **************** Coordenadas vienen del programa que lo llama  ***************//
$con = new DB_mysqli('','pe');
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
<html>
<head>

<link href="http://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>



<script type="text/javascript">
var map=null;
var segundos = 60 * 1000;
var markersArray = [];
var markersInfoWin = [];

/*              DEFINE EL REFRESH              */
var auto_refresh = setInterval(function(){marcas()}, segundos);


/*          	DIBUJA EL MAPA   			 */
function inicializa_mapa(){
	var latlng = new google.maps.LatLng('<?=$lat?>','<?=$lng?>');
	var myOptions = {
		zoom: 10,
		center: latlng,
		icon: '/imagenes/icono_maps/afiliado.png',
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}


/*              BORRA LAS MARCAS              */
function clearOverlays() {
	if (markersArray) {
		for (i in markersArray) {
			markersArray[i].setMap(null);
			markersInfoWin[i].close();
		}
	}
	markersArray = [];
	markersInfoWin = [];
}

/*				DIBUJA LAS MARCAS             */
function marcas()
{
	$.ajax(
	{
		data: "nombreProveedor="+$('#proveedor').val()+"&idservicio="+$('#idservicio').val(),
		type: "POST",
		dataType: "xml",
		url: 'ajax_datos_proveedor.php',
		success: function(xml)
		{
			clearOverlays();
		//	var limits = new google.maps.LatLngBounds();
						
			$(xml).find('data').each(function(){
				$(this).find('proveedor').each(function()
				{
					var idProveedor = $(this).find('id').text();
					var nombreComercial = $(this).find('nc').text();
					var numerotelefono = $(this).find('telf').text();
					var latitud = $(this).find('lat').text();
					var longitud = $(this).find('lng').text();
					var fecha_gps = $(this).find('fgps').text();
					var servicios = $(this).find('serv').text();

					var contentString = '<table id="content_'+idProveedor+'"><tr><td colspan=3><h1>'+nombreComercial+'</h1></td><tr>'+
					'<tr><td rowspan=4><img src=/app/vista/catalogos/proveedores/foto/foto.php?idproveedor='+idProveedor+' width=96px heigth=72px></td></tr>'+
					'<tr><td><b>Telefono:</b> </td><td>'+numerotelefono +'</td></tr>'+
					'<tr><td><b>Actualizado:</b></td><td>'+ fecha_gps+'</td></tr>'+
					'<tr><td><b>Servicios:</b></td><td>'+ servicios+'</td></tr>'+
					'</table>';

					var infowindow = new google.maps.InfoWindow({content: contentString});
					var posicion = new google.maps.LatLng(latitud,longitud);
					
					/*
					var image = new google.maps.MarkerImage('/app/vista/catalogos/proveedores/foto/foto.php?idproveedor='+idProveedor,
					null,
					null,
					new google.maps.Point(96, 72),
					new google.maps.Size(96,72 )
					);
					*/
					var marker = new google.maps.Marker({
						position: posicion,
						title:"",
						//	icon: image
					});

					/* Añade las ventanas de informacion */
					google.maps.event.addListener(marker, 'mouseover', function() {
						infowindow.open(map,this);
						google.maps.event.addListener(marker, 'mouseout', function() {
								infowindow.close(map,this);
						});
						
					});

					if ($('#ver_info').is(':checked')) infowindow.open(map,marker);

					markersArray.push(marker);
					markersInfoWin.push(infowindow);

					marker.setMap(map);
					//limits.extend(posicion);
				});
			}
			//map.fitBounds(limits);
			)
		}
	}
	);
}

/*                   ENVIO DE MENSAJES                    */
function mensaje(){
	$.newWindow({id:"iframewindow",width: 550,height: 600,title:"Mensaje MMS"});
	$.updateWindowContent("iframewindow",'<iframe src="http://www2.nextel.com.pe/smsweb/servicios/envio_de_mensajes.asp" width="650" height="650"/>');
};

/*                   QUITA LOS GLOBOS DE INFORMACION      */
function infoToggle(){
	if (markersInfoWin)
	{
		for (var i in markersInfoWin)
		{
			if ($('#ver_info').is(':checked')==false) markersInfoWin[i].close();
			else markersInfoWin[i].open(map);
		}
	}

}

/*                PROVEEDORES SUGERIDOS                    */
function suggest_proveedor(inputString){
	if(inputString.length == 0) {
		$('#proveedor_sugeridos').fadeOut();
	} else {
		$('#proveedor_sugeridos').addClass('load');
		$.post("busca_proveedor.php", {txtnombre: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#proveedor_sugeridos').fadeIn();
				$('#proveedor_sugeridos').html(data);
				$('#proveedor').removeClass('load');
			}
		});
	}
}

/*              RELLENA EL CAMPO PROVEEDOR CON EL SELECCIONADO         */
function fill(thisValue) {
	$('#proveedor').val(thisValue);
	setTimeout("$('#proveedor_sugeridos').fadeOut();", 600);
}



$(document).ready(function()
{
	inicializa_mapa();
	marcas();
	$('#buscar').click(function(){
		marcas();


	});

});




</script>
</head>
<body >
<div id="map_canvas" style="width:75%; heigth:100%;float:left"></div>
<div style='float:right;width:25%;heigth:100%' >
<form>
<fieldset>
<legend>Visualizaci&oacute;n y busqueda</legend>
<table style="float:left">
    <tr>
         <td>Ver informaci&oacute;n </td>
         <td><input type="checkbox" name='ver_infowindows' id='ver_info'  onclick="infoToggle();" ></td>
    </tr>
    
    <tr>
        <td>Servicios</td>
        <td><? 
        $sql="SELECT IDSERVICIO,DESCRIPCION FROM $con->catalogo.catalogo_servicio ";
        $con->cmbselect_db('IDSERVICIO',$sql,'','id="idservicio"',"style='width:150px'",'');
        ?>
        </td>    
    </tr>
    <tr>
        <td>Proveedor</td>
        <td><input type='text' name='PROVEEDOR' id='proveedor' size='25' onkeyup="suggest_proveedor(this.value);">
        <div class="autocomplete" id="proveedor_sugeridos" style="display: none;"> 
        </td>
    </tr>
    <tr>
        <td>Telefono</td>
        <td><input type='text' name='TELEFONO'></td>
    </tr>
    <tr>
        <td colspan="2" align="center"> <input type="button" name='BUSCAR' id='buscar' value='BUSCAR' class='normal' >
        <input type="button" value='Enviar SMS' onclick="mensaje()" class="normal">
        
        </td>
        
    </tr>
    
</table>   
</fieldset>
</form>
</div>
</body>
</html>

