<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_usuario.inc.php');
include_once('../../modelo/clase_expediente.inc.php');

// Coordenadas por default del mapa de la oficina
$con = new DB_mysqli();
$lat = $con->lee_parametro('UBICACION_PRIMARIA_LATITUD');
$lng = $con->lee_parametro('UBICACION_PRIMARIA_LONGITUD');
$clave = $con->lee_parametro($con->url());

// ****************	datos del contacto ******************************************//

$idexpediente = 1;//$_GET[idexpediente];
$expediente  = new expediente($idexpediente);
$contacto = $expediente->datos_persona('CON');


// **************** Datos de la Extension	  ***********************************//
$usuario = new usuario();
$extension = $usuario->extension_usada($_SESSION[user]);

$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');
// **************** Lee las familias de servicios *******************************//
$sql="
	SELECT
		*
	FROM
		catalogo_familia;
	";
$result=$con->query($sql);
while($reg=$result->fetch_object()){
	$familia[$reg->IDFAMILIA]= $reg->DESCRIPCION;	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript"	src="http://maps.google.com/maps?file=api&v=2&key=<?=$clave?>"></script>	
<script type='text/javascript' src='../../../librerias/zapatec/utils/zapatec.js'></script>
<script type="text/javascript" src="../../../librerias/zapatec/zptabs/src/zptabs.js"></script>
<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
<link href="../../../librerias/zapatec/website/css/zpcal.css" rel="stylesheet" type="text/css">
<link href="../../../librerias/zapatec/website/css/template.css" rel="stylesheet" type="text/css">


<script type="text/javascript">
    
    var map = null;

// ********************* Inicializa el mapa ***************************//   
    function mapa(){
        if (GBrowserIsCompatible()) {
          map = new GMap2(document.getElementById("map"),{draggableCursor: 'crosshair', draggingCursor: 'pointer'});
          map.disableDoubleClickZoom();
  		map.addControl(new GLargeMapControl()); 	
  		marca_afiliado(); 				
  	    	      }
     } 

// ********************* Crea la marca del afiliado ******************//    
	function marca_afiliado(){
		var nombre = '<?= $contacto->NOMBRE ?>';
		var tipotelefono ='<?= $contacto->TIPOTELEFONO ?>';
		var proveedortelefonico ='<?= $contacto->PROVEEDORTELEFONICO ?>';
		var telefono = '<?= $contacto->CODIGOAREA.$contacto->NUMEROTELEFONO ?>';
		
		var msgHTML='<div style=font-size:10px><b>'+nombre+'<br><br>'
		+'<a href=# style="color:#000066; background-color:#FFFF66; font-size:11px" onclick=llamada('+telefono+')><b>'
		+ telefono+ '</b><img src=../../../imagenes/iconos/telefono.jpg></img></a><br>'
		+'</div>';
	
		var point = new GLatLng('<?=$lat?>','<?=$lng?>');
	    map.setCenter(point,13);
	    var marker = new GMarker(point);
	    GEvent.addListener(marker,'click', function(){
			marker.openInfoWindowHtml(msgHTML);
		});
	    map.addOverlay(marker);
	    return;
		} 

// ********************* Crea nuevas marcas **************************//
	function createmarker(point,index,msgHTML,color,numero){
		// define la letra en el icono del proveedor
		IconAfil = new GIcon(G_DEFAULT_ICON, "../../../imagenes/icono_maps/afiliado.png");
		IconAfil.iconSize=new GSize(32,32);
		var IconProv = new GIcon(IconAfil);
		IconProv.image = '../../controlador/ajax/globo_icon.php?color='+color+'&num='+numero;
		markerOptions = { icon:IconProv };
		var PuntoProv = new GMarker(point,markerOptions);
		GEvent.addListener(PuntoProv,'click', function(){
			PuntoProv.openInfoWindowHtml(msgHTML);
		});
		return PuntoProv;
	} 

// ********************** Agrega nuevas marcas del proveedor **********//
    function agregar_marcas(lista){
    	/*			campos =>
    	0 = IDPROVEEDOR
		1 = NOMBREFISCAL
		2 = DESCRIPCION (SERVICIO)
		3 = PRIORIDAD
		4 = LATITUD
		5 = LONGITUD
		6 =	CODIGOAREA
		7 = TSP (nombre del proveedor tsp)
		8 = NUMEROTELEFONO
		9 = IDSERVICIO
		*/
        var num=1;
		for(i=0; i < lista.length -1; i++){
			campos=lista[i].split(',');
			
			var point = new GLatLng(campos[4],campos[5]);
			var msgHTML='<div style=font-size:10px><b>'
						+campos[1]+'</b><br>'
						+campos[2]+'<br>'
						+'Telf: '+ campos[7] 
						+' <a href=# style="color:#000066; background-color:#FFFF66; font-size:11px" onclick=llamada('+campos[8]+')><b> '
						+ campos[8] 
						+ '</b><img src=../../../imagenes/iconos/telefono.jpg></img></a><br>'
						+ "<center><input type='button' value='Asignar Proveedor' onclick = asignar("+campos[0]+","+campos[9]+")></center> " 
						+ '</div>';
			map.addOverlay( createmarker(point,i, msgHTML,'AZUL',i+1 ));
		}
        }

// *************** Verifica los checkboxes y actualiza los proveedores ************//
    function actualizar_proveedores(){
    	var checkboxes = document.getElementsByName("servicios");
    	map.clearOverlays();
    	for(i=0; i < checkboxes.length; i++)
    	{   
        	
        	marca_afiliado();
      		if ( checkboxes[i].checked )  proveedores_servicios(checkboxes[i].value);
    	}
        }
    
// *************** Ajax que obtiene la lista de proveedores por servicio ***********//
    function proveedores_servicios(idservicio){
          	new Ajax.Request('../../controlador/ajax/ajax_proveedores_x_servicios.php',
    			{	method : 'get',
    				parameters : { idservicio : idservicio },
    				onSuccess : function(t){
    				list_prov=t.responseText.split('/');
//					alert(list_prov);
    				agregar_marcas(list_prov);
    			}
    			}
    			);
       }

// *************** Ajax para realizar la llamada ***********************************//
    function llamada(numero){
        new Ajax.Request('../../controlador/ajax/ajax_llamada.php',
    	{	method : 'get',
    		parameters: {ext:<?=$extension ?>, prefijo :<?=$prefijo ?>, num: numero }
    	}
    	);
   }

// ***************** Llama el proceso de asignacion del proveedor ******************//
    function asignar(idproveedor,idservicio){
		alert(idproveedor+" "+ idservicio);

		}
        
    
</script>
</head>
<body onload="mapa()">
<div id='map' style="width: 800px; height: 400px"></div>

<div id="tabBarExped" style="width: 100%">&nbsp;</div>
 <div id="tabsExped"  style="display: block; height:100%"; >
 
<? foreach($familia as $index=>$value){?>	
   <div id='<?=$value?>' style="height:120px">
	<label title=<?=_($value) ?>><?=_(substr($value,0,3)) ?></label>
	<?$col=1;?>
	
	<table>
	<?
	$sql="
	SELECT
		*
	FROM
		catalogo_servicio
	WHERE
		IDFAMILIA='$index';
	";
	$result=$con->query($sql);
	echo '<tr>';
	
	while($reg= $result->fetch_object())
	{
		if ( $col<=4){
			$col++;
			echo '<td>';
		}
		else
		{
		 	$col=1;
		 	echo '<tr><td>';
		}
		 
	?>
	<input type='checkbox' value='<?=$reg->IDSERVICIO ?>' name='servicios' onclick="actualizar_proveedores();" /><?=$reg->DESCRIPCION?>
	</td><td> </td><td> </td>
	<?}?>
	</table>
	</div>
<?}?>	
	

</body>
</html>

<script type="text/javascript">

var objTabs = new Zapatec.Tabs({
	// ID of Top bar to show the Tabs: Game, Photo, Music, Chat
	tabBar: 'tabBarExped',
	/*
	ID to get the LABEL contents to create the tabBar tabs
	Also, each DIV in this ID will contain the contents for each tab
	*/
	tabs: 'tabsExped',
	// Theme to use for the tabs
	theme: 'rounded',
	themePath: '../../../librerias/zapatec/zptabs/themes/',
	closeAction: 'hide'
});



</script>
