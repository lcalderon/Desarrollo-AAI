<?

session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');

$idusuario= $_SESSION[user];
$con= new DB_mysqli();
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


$fechainicio = $asis->asistencia_servicio->FECHAINICIO;
$fechafin = $asis->asistencia_servicio->FECHAFIN;
$fechai = substr($fechainicio,0,10);
$horai = substr($fechainicio,11,2);
$minutoi = substr($fechainicio,14,2);

$fechaf = substr($fechafin,0,10);
$horaf = substr($fechafin,11,2);
$minutof = substr($fechafin,14,2);


?>

<legend><?=$nombreservicio?></legend>
<form id='form_seguridad'>
<input type="hidden" name='IDASISTENCIA' id="idasistencia" value="">
	<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod' value="<?=$idusuario?>">
	<table>
		<tr>
			
			<td><?=_('DESCRIPCION DEL HECHO')?> * </td>
			<td ><?=_('SUBSERVICIO')?></td>
			 <td colspan=5><? $con->cmbselect_ar('SUBSERVICIO',$desc_seguridad_subservicio,$asis->asistencia_servicio->SUBSERVICIO,'id=subservicio','','') ?></td>
		</tr>
		
		<tr>
			
			<td rowspan='2'><textarea name='DESCRIPCIONSERVICIO' id='descripcionservicio' cols="40" rows='0' style="text-transform:uppercase;"><?=$asis->asistencia_servicio->DESCRIPCIONSERVICIO?></textarea></td>
<td colspan=3><?=_('INICIO')?> * </td>
			<td colspan=3><?=_('FIN')?></td>
			
			</tr>
		<tr>	
<td valign="top"><input type="text" name='FECHAINI' id='date3' value="<?=$fechai?>" readonly>
 				<input type="button" id="calendarButton3" value="..." onmouseover="setupCalendars_seguridad();" class='normal'></td>
			 <td valign="top"><select name='cbhoraini' class='classtexto'><? for($t=0;$t<=24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horai==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select></td>
<td valign="top"><select name='cbminutoini' class='classtexto'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutoi==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select></td>
			<td valign="top"><input type="text" name='FECHAFIN' id='date2' value="<?=$fechaf?>" readonly>
 				<input type="button" id="calendarButton2" value="..." onmouseover="setupCalendars_seguridad();" class='normal'></td>
<td valign="top"><select name='cbhorafin' class='classtexto'><? for($t=0;$t<=24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($horaf==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select></td>
<td valign="top"><select name='cbminutofin' class='classtexto'><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($minutof==$t){ ?> selected <? } ?>><?=$t?></option><? } ?></select></td>

		</tr>
		
		
	</table>
	</form>


