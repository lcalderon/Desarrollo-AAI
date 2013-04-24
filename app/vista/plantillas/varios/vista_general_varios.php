<?
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../includes/head_prot_win_map.php');

?>
<body>
<div>
<div id='datos_generales_plantilla' style="float:left; weight:48%" align="left">
<fieldset>
<legend>DATOS GENERALES</legend>
<form>
   <input type='hidden' name='IDUBIGEO' id='idubigeo' value=''>
	<table>
		<tr>
			<td><?=_('CONTACTANTE')?></td>
			<td><input type='text' name='CONTACTANTE' id='contactante' value='' size='60' style="text-transform:uppercase;"></td>
		</tr>
		<tr>
			<td><?=_('LUGAR DEL EVENTO')?></td>
			<td><input type='text' name='DIRECCION' id='direccion' value='' size="60" style="text-transform:uppercase;">
			<img src='../../../../imagenes/iconos/tierra.gif' alt="20" width="20" id='btn_ubigeo'></img>
			</td>
		</tr>
		<tr>
			<td><?=_('TIPO INMUEBLE')?></td>
			<td><? $con->cmbselect_ar('INMUEBLE',$desc_tipo_inmueble,'','id=inmueble','','') ?></td>
		</tr>
		<tr>
			<td><?=_('TIPO DE ATENCION')?></td>
			<td><? $con->cmbselect_ar('TIPOATENCION',$desc_prioridadAtencion,'EME','id=tipoatencion','','')?></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><input type="button" value='Guardar' class='guardar'>
			    <input type="button" value='Cancelar' class='cancelar'>
			</td></tr>
	</table>
	</form>
</fieldset>	
</div>

<div id='listado_de_servicios' align="right">

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