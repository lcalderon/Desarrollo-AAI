<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="../../../librerias/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window.js"></script>
<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
<link href="../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link>
<link href="../../../librerias/windows_js_1.3/themes/spread.css" rel="stylesheet" type="text/css" ></link>
<link href="../../../librerias/windows_js_1.3/themes/alert.css" rel="stylesheet" type="text/css" ></link>
<link href="../../../librerias/windows_js_1.3/themes/alert_lite.css" rel="stylesheet" type="text/css" ></link>
<link href="../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>
<link href="../../../librerias/windows_js_1.3/themes/debug.css" rel="stylesheet" type="text/css" ></link>
	
<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" ></link>

</head>
<body>
	
	<fieldset>
	<?=_('AREAS PREDEFINIDAS')?>
	<hr>
	<form id='form_poligonos' name='form_poligonos' >
	<table>
	<tr>
		<td colspan="2" align="center">
		<input type='button' value="<?=_('Crear nueva area')?>" onclick="mapa('areas_predefinidas')"></input>
		</td>
	</tr>
	</table>
	</form>
	
	<?=_('LISTADO DE AREAS PREDEFINIDAS');
	echo "<table class='catalogos'><tr>";
	echo "<th width= '20p'>"._('ID')."</th>";
	echo "<th width= '200p'>"._('NOMBRE')."</th>";
	echo "<th width= '100p'>"._('USUARIO MOD')."</th>";
	echo "<th width= '125p'>"._('FECHA MOD')."</th>";
	echo "<th width= '160p' colspan=2 >"._('OPCIONES')."</th>";
	echo "</tr></table>";
	?>
	
	<div id='listado_areas_predefinidas' style="width: 100%; height:120px; overflow: auto">
		<? include_once('listado_areas_predefinidas.php')?>
	</div>

	</fieldset>
<script type="text/javascript">
var win = null;

function mapa(tipo_mapa)
{
	if (win != null) alert('<?=_('CIERRE EL MAPA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: 	tipo_mapa,
			width: 500,
			height: 500,
			resizable: false,
			destroyOnClose: true,
			url: "mapa_"+tipo_mapa+".php"
		});
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
				actualizar_listado(tipo_mapa);
			}
		}
		}
		Windows.addObserver(myObserver);
	}
	return;
}


function actualizar_listado(tipo_mapa){
	new Ajax.Updater('listado_'+tipo_mapa,'listado_'+tipo_mapa+'.php',
	{
		method : 'post'
	});
	return;
}



/******************** FUNCIONES DE POLIGONOS ***************************************/
function ver_poligono(idpoligono)
{
	if (win != null) alert('<?=_('CIERRE EL MAPA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("Poligonos")?>',
			width: 500,
			height: 500,
			resizable: false,
			destroyOnClose: true,
			url: "mapa_areas_predefinidas.php?idpoligono="+idpoligono
		});

		win.keepMultiModalWindow = true;
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
				actualizar_listado('areas_predefinidas');
			}
		}
		}
		Windows.addObserver(myObserver);
	}
}

function eliminar_poligono(idpoligono){
	new Ajax.Request('../../controlador/ajax/ajax_areas_predefinidas.php?opcion=contar',
	{
		method: 'post',
		parameters: { IDPOLIGONO: idpoligono },
		onSuccess: function(t){
			contador=t.responseText;
			if (contador!=0) alert("<?=_('ESTE POLIGONO ESTA ASIGNADO A UNO O MAS PROVEEDORES')?>");
			else
			new Ajax.Request('../../controlador/ajax/ajax_areas_predefinidas.php?opcion=borrar',
			{
				method: 'post',
				parameters: { IDPOLIGONO: idpoligono },
				onSuccess: function(t){
						actualizar_listado('areas_predefinidas');
				}
			});
		}
	});
}
</script>
