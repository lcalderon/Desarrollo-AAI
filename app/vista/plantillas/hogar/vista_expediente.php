<?
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_persona.inc.php');
include_once('../../../modelo/clase_telefono.inc.php');
include_once('../../../modelo/clase_cuenta.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_programa.inc.php');
include_once('../../../modelo/clase_afiliado.inc.php');
include_once('../../../modelo/clase_etapa.inc.php');
include_once('../../../modelo/clase_expediente.inc.php');
include_once('../../../modelo/clase_asistencia.inc.php');


include_once('../../includes/arreglos.php');
include_once('../../includes/head_prot_win_map.php');


$idasistencia=21;
$idfamilia=3;

$fam= new familia();
$fam->carga_datos($idfamilia);

$asis = new asistencia();
$asis->carga_datos($idasistencia);


$afiliado = $asis->expediente->titular_afiliado->origenafiliado->nombre.' '.$asis->expediente->titular_afiliado->origenafiliado->appaterno.' '.$asis->expediente->titular_afiliado->origenafiliado->apmaterno;
$contactante = $asis->expediente->contacto->nombre.' '.$asis->expediente->contacto->appaterno.' '.$asis->expediente->contacto->apmaterno;
$cuenta = $asis->expediente->cuenta->nombre;
$plan = $asis->expediente->programa->nombre;
$etapa = $asis->etapa->descripcion;
$objetivo = $asis->etapa->objetivo;
$idubigeo = $asis->expediente->ubigeo->idubigeo;
$direccion = $asis->expediente->ubigeo->direccion;

$servicios =$asis->expediente->programa->servicios;
?>

<body>
<div id='datos_expediente'>
	<fieldset style="background: #ECE9D8">
	<legend><?=_('Datos del expediente')?></legend>
	<table align="left">
		<tbody>
			<tr>
				<td width='7%'><?=_('ETAPA')?></td>
				<td width='20%'><input type='text' size='40' value="<?=$etapa?>" readonly></td>
				<td width='7%'><?=_('Cuenta')?></td>
				<td width='20%'><input type='text'value="<?=$cuenta?>" readonly></td>
				<td width='7%'><?=_('Titular')?></td>
				<td width='30%'><input type='text' size='40' value="<?=$afiliado?>" readonly></td>
			</tr>
			<tr>
				<td><?=_('OBJETIVO')?></td>
				<td><input type='text' size='50' value="<?=$objetivo?>" readonly></td>
				<td><?=_('Plan')?></td>
				<td><input type='text' value="<?=$plan?>" readonly></td>
				<td><?=_('Contactante')?></td>
				<td><input type='text'  size='40' value="<?=$contactante?>" readonly></td>
			</tr>
		</tbody>
	</table>
	</fieldset>
</div>  <!--fin del DIV   DATOS DEL EXPEDIENTE-->

<div id='datos_familia' >
	<fieldset style="background: <?=$fam->color?> ">
		<legend><?=$fam->descripcion?></legend>
			<? include_once('vista_general.php'); ?>
	</fieldset>
</div>  <!--fin del DIV   DATOS DE LA FAMILIA -->

<div id='datos_servicio' >
	<fieldset style="background: <?=$fam->color?>" id='form_servicio'>
		
			
	</fieldset>
</div>  <!--fin del DIV   DATOS DEL SERVICIO-->

<div id='barra' style="position:relative; align:right">


<table 	>
	<tbody>
		<tr>
			<td><input type='button' value='Guardar Parcial' class='normal'></td>
			<td><input type='button' value='Guardar' class='guardar'></td>
			<td><input type='button' value='Cancelar' class="cancelar"></td>
		</tr>
	</tbody>
</table>
</div> <!--FIN DEL DIV DE LA BARRA-->

</body>
<script type="text/javascript">
var win = null;

function mod_ubigeo(idubigeo,campo_ubigeo,campo_display){
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
			url: "../../ubigeo/localizador.php?idubigeo="+$F('idubigeo')+"&campo_ubigeo="+campo_ubigeo+"&campo_display="+campo_display

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
	
	return;
}


function plantilla(vista)
{
	new Ajax.Updater('form_servicio',vista,
	{
		method : 'post'
	});
	return;
		
}

</script>