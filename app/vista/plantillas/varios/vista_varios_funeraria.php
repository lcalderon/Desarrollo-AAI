<link rel="stylesheet" type="text/css" media="all" href="../../../../librerias/jscalendar-1.0/skins/aqua/theme.css" title="Aqua" />

<link rel="alternate stylesheet" type="text/css" media="all" href="../../../../librerias/jscalendar-1.0/calendar-win2k-1.css" title="win2k-1" />
<link rel="alternate stylesheet" type="text/css" media="all" href="../../../../librerias/jscalendar-1.0/calendar-win2k-2.css" title="win2k-2" />
<link rel="alternate stylesheet" type="text/css" media="all" href="../../../../librerias/jscalendar-1.0/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<link rel="alternate stylesheet" type="text/css" media="all" href="../../../../librerias/jscalendar-1.0/calendar-win2k-cold-2.css" title="win2k-cold-2" />
<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" ></link>

<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar.js"></script>

<!-- import the language module -->
<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
<script type="text/javascript" src="../../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
<?
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../includes/head_prot_win_map.php');

$SERVICIO = 'FUNERARIO';
?>
<body>
<div>
<div id='datos_generales_plantilla' style="float:left; weight:48%" align="left">
<fieldset>
<legend>DATOS DE LA ASISTENCIA - <?=$SERVICIO?></legend>
<form>
   <input type='hidden' name='IDUBIGEO' id='idubigeo' value=''>
	<table>
		<tr>
			<td><?=_('FECHA DECESO')?></td>
			<td><input type='text' name='FECHADECESO' id='fechadeceso' value=''><img src="../../../../librerias/jscalendar-1.0/img.gif" id="f_trigger_c" style="cursor: pointer; border: 1px solid red;" title="Date selector"
      onmouseover="this.style.background='red';" onMouseOut="this.style.background=''" onclick="return setActiveStyleSheet(this, 'win2k-1');" /></td>
		</tr>
		<tr>
			<td><?=_('MOTIVO')?></td>
			<td><textarea name='MOTIVO' id='motivo' cols="40" rows='0' style="text-transform:uppercase;"></textarea></td>
		
		</tr>
		<tr>
			<td><?=_('LUGAR DE TRASLADO')?></td>
			<td><input type='text' name='TRASLADO' id='traslado' value='' size="60" style="text-transform:uppercase;">
			<img src='../../../../imagenes/iconos/tierra.gif' alt="20" width="20" id='btn_ubigeo'></img>
			</td>
		</tr>
		 <tr>
			<td><?=_('FECHA HORA CEREMONIA')?></td>
			<td><input type='text' name='FECHACEREMONIA' id='fechaceremonia' value=''><img src="../../../../librerias/jscalendar-1.0/img.gif" id="f_trigger_c" style="cursor: pointer; border: 1px solid red;" title="Date selector"
      onmouseover="this.style.background='red';" onMouseOut="this.style.background=''" onclick="return setActiveStyleSheet(this, 'win2k-1');" /></td>
		</tr>
		<tr>
			<td><?=_('OTROS')?></td>
			<td><textarea name='OTROS' id='otros' cols="40" rows='0' style="text-transform:uppercase;"></textarea></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><input type="submit" value='Guardar' class='guardar'>
			    <input type="button" value='Cancelar' class='cancelar'>
			</td>
		</tr>
	</table>
	</form>
</fieldset>	
</div>


</div>
</body>
</html>
<script type="text/javascript">
var win = null;

// ***********************  Abre la ventana del mapa  *****************************************//
new Event.observe('btn_ubigeo','click',function()
{
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title: '<?=_("localizacion")?>',
			width: 800 ,
			heigth: 500 ,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "../../ubigeo/localizador.php?idubigeo="+$F('idubigeo')+"&campo=idubigeo"

		});
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);
			}
		}
		}
		Windows.addObserver(myObserver);
	}
});

new Event.observe('btnGrabar','click',function()
{
	
});

</script>