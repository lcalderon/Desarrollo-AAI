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

?>
<legend><?=$nombreservicio?></legend>
<form id='form_atencionveterinaria'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table>
	<tbody>
		<tr>
			<td rowspan="2"><?=_('SINTOMATOLOGIA')?><font color="red">*</font><br>
			<textarea name='SINTOMATOLOGIA' id='sintomatologia'><?=$asis->asistencia_servicio->SINTOMATOLOGIA ?></textarea></td>
			
			<td valign="top"><?=_('TIPO DE PRESTACION')?><br>
			<? $con->cmbselect_ar('ARRTIPOPRESTACION',$tipo_prestacion,$asis->asistencia_servicio->ARRTIPOPRESTACION,'','id="arrtipoprestacion"','SELECCIONE');?></td>

			<td rowspan='2'><?=_('ANTECEDENTES CLINICOS')?><br>
			<textarea name='ANTECEDENTESCLINICOS' id='antecedentesclinicos'><?=$asis->asistencia_servicio->ANTECEDENTESCLINICOS ?></textarea></td>
			
			<td rowspan='2'><?=_('DIAGNOSTICO')?><font color="red">**</font><br>
			<textarea name='DIAGNOSTICO' id='diagnostico'><?=$asis->asistencia_servicio->DIAGNOSTICO ?></textarea> </td>
			
			<td rowspan='2'><?=_('RECOMENDACION')?><font color="red">**</font><br>
			<textarea name='RECOMENDACION' id='recomendacion'><?=$asis->asistencia_servicio->RECOMENDACION ?></textarea></td>
		</tr>
		
	</tbody>
</table>
</form>

