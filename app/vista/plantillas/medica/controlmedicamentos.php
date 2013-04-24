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
<form id='form_controlmedicamentos'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table width="100%">
	<tbody>
		<tr>
			<td rowspan="2" valign="top"><?=_('NOMBRE DEL MEDICAMENTO')?><br>
			<textarea name='NOMBREMEDICAMENTO' id='nombremedicamento'><?=$asis->asistencia_servicio->NOMBREMEDICAMENTO?></textarea></td>
			<td>&nbsp;</td>
			<td rowspan="2" valign="top"><?=_('PRESCRIPCION MEDICA').' *'?><br>
			<textarea name='PRESCRIPCIONMEDICA' id='prescripcionmedica'><?=$asis->asistencia_servicio->PRESCRIPCIONMEDICA?></textarea></td>
			<td>&nbsp;</td>
			<td valign="top"><?=_('PROGRAMACION')?><br>
			<input type="text" name='PROGRAMACION' id='programacion' value="<?=$asis->asistencia_servicio->PROGRAMACION?>" size="15"></td>
			<td>&nbsp;</td>
			<td valign="top"><?=_('HORA DE LA PRIMERA TOMA').' *'?><br>
			<input type="text" name='HORAPRIMERATOMA' id='horaprimeratoma' value="<?=$asis->asistencia_servicio->HORAPRIMERATOMA?>" size="15"></td>
		</tr>
	</tbody>
</table>			