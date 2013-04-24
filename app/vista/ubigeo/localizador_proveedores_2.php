<?
/*
Programa de localizaci�n de proveedores

Input: 1.- Ubicaci�n del afiliado (latitud,longitud)
	   2.- Servicio (idservicio)
	   
	   
Autor:  Frank S�nchez Moreyra


*/
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=_('Localizacion de proveedores')?></title>
</head>

<body>
<h1><?=_('Localizacion de proveedores')?></h1>

<table>
<tr><td>

<? include('vista_entidades.php')?>

</td>
<td>
<table>
<tr>
<td><?=_('PROVEEDOR')?></td><td><input type='text' name='txtprov'></td>
</tr>
<tr>
<td><?=_('SERVICIO')?></td><td><input type='text' name='txtprov'></td>
</tr>

</table>
</td>
</tr>
<tr>
	<td colspan=2 align='center'><input type ='button' value='Ver mapa' id='ver_mapa'  ></input>
	<input type ='button' value='Ver proveedores' id='ver_proveedores' ></input></td>
</tr>
</table>

<div id='zona_proveedores'>
</div>

</body>
</html>


<script type="text/javascript"> // ******************  Eventos del formulario  ***************************//

//***********************  Abre la ventana del mapa  **********************//

new Event.observe('ver_mapa','click',function()
	    {
    		lat='<?=$lat?>';
    		lng='<?=$lng?>';
    				var win = new Window({
	    			className: "alphacube",
	    			title: '<?=_("Mapa de localizacion")?>',
	    			width: 800,
	    			height: 600,
	    			url: "../../ubigeo/mapa_proveedores.php?lat="+lat+"&lng="+lng
	    			
	    		});
	    		win.showCenter();
	       });

//************************ Presenta los proveedores  *********************//
new Event.observe('ver_proveedores','click',function (){
	new Ajax.Updater('zona_proveedores',"../../controlador/ajax/ajax_listar_proveedores.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1: $F('cveentidad1') }
	});
});

// *********************** Activa los combos dependientes *******************//
/*
new Event.observe('manual','click',function (){
	$('cveentidad1').disabled=false;
	$('cveentidad2').disabled=false;
	$('cveentidad3').disabled=false;
	$('cveentidad4').disabled=false;
	$('cveentidad5').disabled=false;
	$('cveentidad6').disabled=false;
	$('cveentidad7').disabled=false;
});
*/
// ********************** Desactiva los combos dependeinetes ****************//
/*
new Event.observe('automatico','click',function (){
	$('cveentidad1').disabled=true;
	$('cveentidad2').disabled=true;
	$('cveentidad3').disabled=true;
	$('cveentidad4').disabled=true;
	$('cveentidad5').disabled=true;
	$('cveentidad6').disabled=true;
	$('cveentidad7').disabled=true;
});

*/



</script>

