<?
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
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
include_once('../../../modelo/clase_contacto.inc.php');
include_once('../../../modelo/clase_poligono.inc.php');
include_once('../../../modelo/clase_circulo.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../../modelo/clase_expediente.inc.php');
include_once('../../../modelo/clase_asistencia.inc.php');
include_once('../../includes/arreglos.php');
include_once('../../includes/head_prot_win.php');
$idasistencia=$_GET[idasistencia];
$modo=$_POSTT[MODO];

$idusuario=$_SESSION[user];

$asis = new asistencia();
$asis->carga_datos($idasistencia);

$atencion=$asis->arrprioridadatencion;
$idservicio=$asis->servicio->idservicio;

foreach ($asis->proveedores as $prov)
{

	if ($prov[statusproveedor]=='AC') {
		$proveedor_act= $prov[idproveedor];

		$provteat = $prov[teat];
		$provteam = $prov[team];
		$fechateat = substr($provteat,0,10);
		$horateat = substr($provteat,11,2);
		$minutoteat = substr($provteat,14,2);

		$fechateam = substr($provteam,0,10);
		$horateam = substr($provteam,11,2);
		$minutoteam = substr($provteam,14,2);
	}
}

if($provteat==''){
	$fechaactual=date("Y-m-d H:i:s");
	$fechateat = substr($fechaactual,0,10);
	$horateat = substr($fechaactual,11,2);
	$minutoteat = substr($fechaactual,14,2);

	$fechateam = substr($fechaactual,0,10);
	$horateam = substr($fechaactual,11,2);
	$minutoteam = substr($fechaactual,14,2);
}


?>
<form name='frm_disponibilidad' id="frm_disponibilidad" method='POST'>
	<fieldset >
	<font size="2px"><b>
		<? if($modo=='RS'): ?>
			<?=_('TIEMPO COMPROMETIDO POR EL PROVEEDOR ADICIONAL AL TEAM')?>
		<?else :?>
			<?=_('TIEMPO COMPROMETIDO POR EL PROVEEDOR')?>
		<?endif;?>
	</b></font>	
		<?//	include_once('vista_form_disponibilidad.php');?>
		<table align="center" width='<?=$width?>' >
			<tbody>
					<tr>	
						<input type='hidden' id='hid_asistencia' name='hid_asistencia' value='<?=$idasistencia?>'>
						<td>
							<?=_('FECHA MINIMA')?>
						</td>
						<td valign="top">
							<input type="text" name='date5' id='date5' value="<?=$fechateat?>" readonly size=10>
 							<input type="button" id="calendarButton5" value="..." onmouseover="setupCalendars2();" class='normal'>
 						</td>
						<td>
							<select name='cbhora1' id='cbhora1' class='classtexto' onchange='Sugerir()'><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horateat==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
						</td>
						<td>
							<select name='cbminuto1' id='cbminuto1' class='classtexto' onchange='Sugerir()'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutoteat==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
						</td>
						<td>
							<?=_('FECHA MAXIMA')?>
						</td>
						<td valign="top">
							<input type="text" name='date6' id='date6' value="<?=$fechateam?>" readonly>
							<input type="button" id="calendarButton6" value="..." onmouseover="setupCalendars2();" class='normal'>
						</td>
						<td>
							<select name='cbhora2' id='cbhora2' class='classtexto'><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horateam==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
							</td>
						<td>
							<select name='cbminuto2' id='cbminuto2' class='classtexto'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutoteam==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
						</td>
					<? if($modo=='RS'): ?>
						<td colspan=7>
							<textarea id='justificacion' cols='40'></textarea></td>
						<td><input type='button' id='btnReprograma' name='btnReprograma' value='<?=_('Reprogramar Servicio')?>' class='guardar' onclick="Reprogramar()"></td>
					<? else: ?>
						<td><input type='button' id='grabartiempopro' <? if($proveedor_act==''){ echo 'disabled';  }?> value='<?=_('guardar reprogramacion de  tiempos')?>' class='guardar' style="display:none" onclick="grabar('RTPRO')"></td>
					<? endif;?>
					</tr>
			</tbody>
		</table>
	</fieldset>
</form>

<script type="text/javascript">
function Sugerir()
{
	fecha1=document.getElementById('date').value;
	hora1=document.getElementById('cbhora1').value;
	minuto1=document.getElementById('cbminuto1').value;
	hora = parseFloat(hora1)+2;
	if(hora<=9){
		horax='0'+hora;
	}else{
		horax=hora;
	}
	if(horax==24){
		horax='00';
	}

	document.getElementById('date2').value=fecha1;
	document.getElementById('cbhora2').value=horax;
	document.getElementById('cbminuto2').value=minuto1;
	return;
}

function Reprogramar()
{
	teat = document.getElementById('date').value+' '+document.getElementById('cbhora1').value+':'+document.getElementById('cbminuto1').value+':00';
	team = document.getElementById('date2').value+' '+document.getElementById('cbhora2').value+':'+document.getElementById('cbminuto2').value+':00';
	comentario = document.getElementById('justificacion').value;

	if(comentario=='')	alert('<?=_('DEBE INGRESAR UNA JUSTIFICACION!!!')?>');
	else if(document.getElementById('date').value=='' || document.getElementById('date2').value=='' || document.getElementById('cbhora1').value=='' || document.getElementById('cbhora2').value=='') alert('<?=_('DEBE INGRESAR LAS HORAS CORRECTAMENTE!!!')?>');
	else
	{
		if(confirm('<?=_('ESTA SEGURO QUE DESEA REGISTRAR LA PROGRAMACION SIN ASIGNAR UN PROVEEDOR ?')?>')){
			new Ajax.Request('/app/controlador/ajax/ajax_reprogramar_servicio.php',
			{
				method : 'post',
				parameters : {
					IDASISTENCIA : '<?=$idasistencia?>',
					TEAT	     :	teat,
					TEAM	     :	team,
					IDUSUARIOMOD :	'<?=$idusuario?>',
					COMENTARIO   :   comentario,
					IDSERVICIO   :	'<?=$idservicio?>'
				},
				onSuccess: function(t){

					alert(t.responseText);
					parent.win.close();

				}
			});
		}
	}
	return;
}
</script>