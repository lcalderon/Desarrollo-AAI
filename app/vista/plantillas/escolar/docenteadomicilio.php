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

//EXTRAYENDO HORARIO

$fecha1= substr($asis->asistencia_servicio->HORARIO,0,10);
$hora1= substr($asis->asistencia_servicio->HORARIO,11,2);
$min1= substr($asis->asistencia_servicio->HORARIO,14,2);

?>
<legend><?=$nombreservicio?></legend>

<form id='form_docenteadomicilio'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod' value="<?=$idusuario?>">
<input type="hidden" name='IDASISTENCIA' id="idasistencia" value="">

<table width="100%" bgcolor="#DBE3E7" style="border:2px double #97AEB9">
  <tr>
    <td colspan="3" class="style1"></td>
  </tr>
  <tr>
    <td class="style1"><?=_('HORARIO')?></td>
    <td class="style1"><input type="text" name='dateesc1' id='dateesc1' value="<?=($fecha1)?$fecha1:date('Y-m-d')?>" readonly size='12'>
 		<input type="button" id="calendarButtonesc1" value="..." onmouseover="setupCalendarsesc1();" class='normal'>
		<select name='dcbhora1' id='dcbhora1' class='classtexto' ><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($hora1==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
		<select name='dcbminuto1' id='dcbminuto1' class='classtexto' ><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($min1==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select></td>
    <td class="style1"><?=_('OTROS')?></td>
  </tr>
  <tr>
    <td width="8%" class="style1"><?=_('MATERIAS')?></td>
    <td width="42%" class="style1"><input name="txtmaterias" type="text" id="txtmaterias" size="60" value="<?=$asis->asistencia_servicio->MATERIAS?>" /></td>	
    <td class="style1"><textarea name="txtaotros" id="txtaotros" cols="55" rows="2"><?=$asis->asistencia_servicio->OTROS?></textarea></td> 
  </tr>
</table>
</form>