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

?>
<legend><?=$nombreservicio?></legend>
<form id='form_asesorialegal'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#DBE3E7" style="border:2px double #97AEB9">
	<tbody>
		<tr>
			<td><?=_('DESCRIPCION DEL HECHO')?><font color="red">*</font><br>
			<textarea name='DESCRIPCIONDELHECHO' id='descripciondelhecho'><?=$asis->asistencia_servicio->DESCRIPCIONDELHECHO?></textarea></td>
			
			<td><?=_('CONTRAPARTE')?><font color="red">**</font><br>
			<textarea name="CONTRAPARTE" id='contraparte'><?=$asis->asistencia_servicio->CONTRAPARTE?></textarea></td>
			
			<td><?=_('RESULTADO DEL HECHO')?><br>
			<textarea name='RESULTADODELHECHO' id='resultadodelhecho'><?=$asis->asistencia_servicio->RESULTADODELHECHO ?></textarea></td>
			
			<td><?=_('STATUS DE N/A')?><br>
			<textarea name='STATUSAFILIADO' id='statusafiliado'><?=$asis->asistencia_servicio->STATUSAFILIADO ?></textarea></td>
			
			<td><?=_('RECOMENDACIONES DEL ABOGADO/INSPECTOR')?><font color="red">**</font><br>
			<textarea name='RECOMENDACION' id='recomendacion'><?=$asis->asistencia_servicio->RECOMENDACION ?></textarea> </td>
		</tr>
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr>
			<td colspan="2"><?=_('NRO DE RECLAMO')?>&nbsp;<input name="NUMERORECLAMO" id="NUMERORECLAMO" onKeyPress="return validarnumero(event)" size="14" maxlength="14" type="text" value="<?=$asis->asistencia_servicio->NUMERORECLAMO ?>"></td>
			<td colspan="3"><?=_('NOMBRE DEL AJUSTADOR')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="NOMBREAJUSTADOR" id="NOMBREAJUSTADOR" size="30" maxlength="30" type="text"  value="<?=$asis->asistencia_servicio->NOMBREAJUSTADOR?>"></td>
 		</tr>				
	</tbody>
</table>
</form>

