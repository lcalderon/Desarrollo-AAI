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
<form id='form_rentaauto'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table width="100%">
	<tbody 	>
		<tr>
			<td valign="top"><?=_('TIPO MOVILIDAD')?><br>
				<? $con->cmbselect_ar('ARRTIPOMOVILIDAD',$arrtipomovilidad,$asis->asistencia_servicio->ARRTIPOMOVILIDAD,'','','SELECCIONE');?>
			</td>
			
			<td valign="top"><?=_('# DIAS')?><br>
			<input type='text' name='NDIAS' id='ndias' size='8' value="<?=$asis->asistencia_servicio->NDIAS ?>">
			</td>
			
			<td colspan="2"><?=_('OBSERVACIONES')?><br>
			<textarea name='OBSERVACION' id='observacion'><?=$asis->asistencia_servicio->OBSERVACION ?></textarea>
			</td>
		</tr>
		
	</tbody>
</table>
</form>