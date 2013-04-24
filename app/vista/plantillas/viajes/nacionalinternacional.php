<?
session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');

$con = new DB_mysqli();
$idusuario= $_SESSION[user];

if (isset($asis)) {
	
	$idprogramaservicio=$asis->idprogramaservicio;
	$idservicio=$asis->servicio->idservicio;
}
else 
{
	$idprogramaservicio=$_POST[IDPROGRAMASERVICIO];
	$idservicio=$_POST[IDSERVICIO];
	
}

if ($idprogramaservicio!=0 OR $_POST[IDPROGRAMASERVICIO]!=''){
	$prog = new programa_servicio();
	$prog->carga_datos($idprogramaservicio);
	$nombreservicio =$prog->etiqueta;
}
else
{
	$serv= new servicio();
	$serv->carga_datos($idservicio);
	$nombreservicio = $serv->descripcion;
}

$hora1= substr($asis->asistencia_servicio->FECHAORIGEN,11,2);
$min1= substr($asis->asistencia_servicio->FECHAORIGEN,14,2);

$hora2= substr($asis->asistencia_servicio->FECHADESTINO,11,2);
$min2= substr($asis->asistencia_servicio->FECHADESTINO,14,2);
?>
<legend><?=$nombreservicio?></legend>
<form id='form_nacionalinternacional'>
	<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
	<table width="100%">
		<tbody>
			<tr>		 
				<td><?=_('FECHA ORIGEN')?><br>
					<input type="text" name='FECHAORIGEN' id='fechaorigen' readonly size='12' value="<?=substr($asis->asistencia_servicio->FECHAORIGEN,0,10) ?>">
					<input type="button" id="calendarButtonesc1" value="..." onmouseover="setupCalendarsesc1();" class='normal'>
					<select name='dcbhora1' id='dcbhora1' class='classtexto'><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($hora1==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
					<select name='dcbminuto1' id='dcbminuto1' class='classtexto' ><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($min1==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
					
				</td>
				<td colspan="2"><?=_('FECHA DE RETORNO').''?><br>
					<input type="text" name='FECHADESTINO' id='fechadestino' readonly size='12' value="<?=substr($asis->asistencia_servicio->FECHADESTINO,0,10) ?>">
					<input type="button" id="calendarButtonesc2" value="..." onmouseover="setupCalendarsesc2();" class='normal'>
					<select name='dcbhora1dest' id='dcbhora1dest' class='classtexto' ><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($hora2==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
					<select name='dcbminuto1dest' id='dcbminuto1dest' class='classtexto' ><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($min2==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
					
					
				</td>					
			</tr>
			<tr>
				<td><?=_('ORIGEN')?><br>
					<textarea name='LUGARORIGEN' id='lugarorigen' cols="30"><?=$asis->asistencia_servicio->LUGARORIGEN ?></textarea>
				</td>			
				<td><?=_('RETORNO')?><br>
					<textarea name='LUGARDESTINO' id='lugardestino' cols="30"><?=$asis->asistencia_servicio->LUGARDESTINO ?></textarea>
				</td>			
				<td><?=_('OBSERVACIONES')?><br>
					<textarea name='OBSERVACION' id='observacion' cols="50"><?=$asis->asistencia_servicio->OBSERVACION ?></textarea>
				</td>
			</tr>		
		</tbody>
	</table>
</form>