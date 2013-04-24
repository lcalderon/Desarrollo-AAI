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
<form id='form_cabinadesiniestro'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table>
	<tbody>
		<tr>
			<td rowspan="2"><?=_('DESCRIPCION DEL HECHO').' *'?><br>
			<textarea name='DESCRIPCIONDELHECHO' id='descripciondelhecho'><?=$asis->asistencia_servicio->DESCRIPCIONDELHECHO?></textarea></td>
			
			<td valign="top"><?=_('DANO')?><br>
			<textarea name="DANIO" id='danio'><?=$asis->asistencia_servicio->DANIO?></textarea>
			
			<td rowspan='2'><?=_('TRAMITES')?><br>
			<textarea name='TRAMITE' id='tramite'><?=$asis->asistencia_servicio->TRAMITE ?></textarea></td>
			
			<td rowspan='2'><?=_('OTROS')?><br>
			<textarea name='OTRO' id='otro'><?=$asis->asistencia_servicio->OTRO ?></textarea> </td>
			
			
		</tr>
		
	</tbody>
</table>
</form>

