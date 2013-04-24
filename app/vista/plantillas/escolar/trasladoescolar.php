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

$fecha1= substr($asis->asistencia_servicio->HORARIOIDA,0,10);
$hora1= substr($asis->asistencia_servicio->HORARIOIDA,11,2);
$min1= substr($asis->asistencia_servicio->HORARIOIDA,14,2);

$fecha2= substr($asis->asistencia_servicio->HORARIOVUELTA,0,10);
$hora2= substr($asis->asistencia_servicio->HORARIOVUELTA,11,2);
$min2= substr($asis->asistencia_servicio->HORARIOVUELTA,14,2);

?>
<legend><?=$nombreservicio?></legend>

<form id='form_trasladoescolar'> 
<style type="text/css">
.CamposObligatorio{
	color: #FF4242;
	font-weight: bold;
	font-size: 17px;
}
	</style>
<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#DBE3E7" style="border:2px double #97AEB9">
  <tr>
    <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="1" bordercolor="#ECE9D8">
      <tr>
        <td width="96" class="style1"><?=_('HORARIO DE IDA')?> </td>
		<td class="style1"><input type="text" name='dateesc1' id='dateesc1' value="<?=($fecha1)?$fecha1:date('Y-m-d')?>" readonly size='12'>
			<input type="button" id="calendarButtonesc1" value="..." onmouseover="setupCalendarsesc1();" class='normal'>
			<select name='dcbhora1' id='dcbhora1' class='classtexto' ><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($hora1==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
			<select name='dcbminuto1' id='dcbminuto1' class='classtexto' ><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($min1==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
		</td>
      </tr>
      <tr>
        <td height="24" colspan="2" class="style1"><?=_('LUGAR DE DESTINO')?><span class="CamposObligatorio">*</span></td>
        </tr>
      <tr>
		<?
			if (isset($asis)){
				$ubigeo = new  ubigeo();
				$ubigeo->leer('ID',$asis->temporal,'asistencia_escolar_trasladoescolar_destino',$asis->asistencia_servicio->IDUBIGEODESTINO);
				}
		?>	
        <td height="24" colspan="2" class="style1"><input type='text' name='direcciondestino' id='direcciondestino' value="<?=(isset($asis)?$ubigeo->direccion.' '.$ubigeo->numero:""); ?>" size="60" readonly>
          <? if ($desactivado==''){?>
          <img src='../../../imagenes/iconos/editars.gif' alt="15" width="15"  onclick="mod_ubigeo($F('iddestino'),'iddestino','direcciondestino','asistencia_escolar_trasladoescolar_destino')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
          <?}?></td>
        </tr>
      <tr>
        <td colspan="2" class="style1">&nbsp;</td>
      </tr>
      
    </table></td>
    <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="1">
 
        <tr>
			<td class="style1"><?=_('HORARIO DE REGRESO')?></td>
			<td class="style1"><input type="text" name='dateesc2' id='dateesc2' value="<?=($fecha2)?$fecha2:date('Y-m-d')?>" readonly size='12'>
				<input type="button" id="calendarButtonesc2" value="..." onmouseover="setupCalendarsesc2();" class='normal'>
				<select name='dcbhora2' id='dcbhora2' class='classtexto' ><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if($hora2==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
				<select name='dcbminuto2' id='dcbminuto2' class='classtexto' ><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if($min2==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
			</td>
        </tr>
        <tr>
          <td colspan="2" class="style1"><?=_('OTROS')?></td>
        </tr>
		<tr>
			<input type='hidden' name='IDDESTINO' id='iddestino' value="<?=$ubigeo->ID ?>">
			<td colspan="2" class="style1"><textarea name="txtaotros"  style="text-transform:uppercase;" cols="48" rows="2" wrap="virtual" id="txtaotros"><?=$asis->asistencia_servicio->OTROS?></textarea>
		    <br></td>
		</tr>
      

    </table></td>
  </tr>
</table>
</form>

