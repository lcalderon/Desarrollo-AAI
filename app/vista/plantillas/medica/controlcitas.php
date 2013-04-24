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

$fechacita = $asis->asistencia_servicio->FECHACITA;
$fechaproximacita= $asis->asistencia_servicio->PROXIMACITA;

$fechac = substr($fechacita,0,10);
$horac = substr($fechacita,11,2);
$minutoc = substr($fechacita,14,2);

$fechap = substr($fechaproximacita,0,10);
$horap = substr($fechaproximacita,11,2);
$minutop = substr($fechaproximacita,14,2);

?>
<legend><?=$nombreservicio?></legend>
<form id='form_controlcitas'>
	<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table width="100%">
	<tbody>
		<tr>
			<td rowspan="2"><?=_('SINTOMATOLOGIA')?><font color="red">*</font><br>
			<textarea name='SINTOMATOLOGIA' id='sintomatologia'><?=$asis->asistencia_servicio->SINTOMATOLOGIA?></textarea></td>

			<td rowspan='2'><?=_('ANTECEDENTES CLINICOS')?><br>
			<textarea name='ANTECEDENTESCLINICOS' id='antecedentesclinicos'><?=$asis->asistencia_servicio->ANTECEDENTESCLINICOS?></textarea></td>
			
			<td rowspan='2'><?=_('DIAGNOSTICO')?><font color="red">**</font><br>
			<textarea name='DIAGNOSTICO' id='diagnostico'><?=$asis->asistencia_servicio->DIAGNOSTICO?></textarea> </td>
			
			<td rowspan='2'><?=_('RECOMENDACION')?><font color="red">**</font><br>
			<textarea name='RECOMENDACION' id='recomendacion'><?=$asis->asistencia_servicio->RECOMENDACION?></textarea></td>
		</tr>
		<tr></tr>
		<tr>
			<? if (isset($asis))
			{
				$ubigeo = new  ubigeo();
				$ubigeo->leer('ID',$asis->temporal,'asistencia_medica_controlcitas_ubigeo',$asis->asistencia_servicio->IDLUGARCITA);
			}
			?>
			<input type='hidden' name='IDLUGARCITA' id='idlugarcita' value="<?=$ubigeo->ID ?>">
			<td colspan='2'><?=_('LUGAR DE LA CITA').' *'?><br>
			<input type="text" name='LUGARCITA' id="lugarcita" value="<?=$ubigeo->direccion.' '.$ubigeo->numero ?>" size="40">
			<? if ($desactivado==''){?>
				<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15" title="<?=_('EDITAR DIRECCION')?>" onclick="mod_ubigeo($F('idlugarcita'),'idlugarcita','lugarcita','asistencia_medica_controlcitas_ubigeo')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
				<img src='../../../imagenes/iconos/deletep.gif' alt="15" width="15" title="<?=_('BORRAR DIRECCION')?>" onclick="$('idlugarcita').clear();$('lugarcita').clear();"  align='absbottom' border='0' style='cursor: pointer;' ></img>
			<?}?>
			</td>
			<td>
			<?=_('ESPECIALIDAD MEDICA')?><br>
			<?php 
			$sql ="select IDESPECIALIDAD,DESCRIPCION from $catalogo.catalogo_especialidadmedica ";
			$con->cmbselect_db('IDESPECIALIDAD',$sql,$asis->asistencia_servicio->IDESPECIALIDAD,'','','');
			?>
			</td>
		</tr>
		<tr>	
			<td><?=_('FECHA HORA DE LA CITA').' *'?><br>
			<input type="text" name='FECHACITA' id='date2' value="<?=$fechac?>" readonly>
 			<input type="button" id="calendarButton2" value="..." onmouseover="setupCalendars_asist();" class='normal'>
			<select name='CBHORACITA' class='classtexto'><? for($t=0;$t<=24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horac==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
			<select name='CBMINUTOCITA' class='classtexto'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutoc==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
			</td>
			
			<td><?=_('PROXIMA CITA')?><br>
			<input type="text" name='PROXIMACITA' id='date3' value="<?=$fechap?>" readonly>
 			<input type="button" id="calendarButton3" value="..." onmouseover="setupCalendars_asist();" class='normal'>
			<select name='CBHORAPROXIMACITA' class='classtexto'><? for($t=0;$t<=23;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horap==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
			<select name='CBMINUTOPROXIMACITA' class='classtexto'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutop==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
			</td>
			
		</tr>
	</tbody>
</table>	
</form>			
