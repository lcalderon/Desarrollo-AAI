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
<form id='form_consultoria'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table width="100%">
	<tbody>
		<tr>
			<td rowspan="2"><?=_('DESCRIPCION DEL HECHO')?><font color="red">*</font><br>
			<textarea name='DESCRIPCIONDELHECHO' id='descripciondelhecho'><?=$asis->asistencia_servicio->DESCRIPCIONDELHECHO?></textarea></td>
			
			<td valign="top"><?=_('RAMA')?><br>
			<textarea name="ARRRAMA" id='arrrama'><?=$asis->asistencia_servicio->ARRRAMA?></textarea>
			
			<td rowspan='2'><?=_('CONTRAPARTE')?><br>
			<textarea name='CONTRAPARTE' id='contraparte'><?=$asis->asistencia_servicio->CONTRAPARTE ?></textarea></td>
			
			<td rowspan='2'><?=_('PRETENCION')?><br>
			<textarea name='PRETENCION' id='pretencion'><?=$asis->asistencia_servicio->PRETENCION ?></textarea></td>
			
			<td rowspan='2'><?=_('RECOMENDACIONES DEL ABOGADO')?><font color="red">**</font><br>
			<textarea name='RECOMENDACION' id='recomendacion'><?=$asis->asistencia_servicio->RECOMENDACION ?></textarea> </td>
		</tr>
	</tbody>
</table>
</form>

