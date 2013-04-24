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

$fechainicio = substr($asis->asistencia_servicio->INICIOHOSPEDAJE,0,10);
$fechafin= substr($asis->asistencia_servicio->FINHOSPEDAJE,0,10);

?>
<legend><?=$nombreservicio?></legend>
<form id='form_hotel'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table width="100%">
	<tbody 	>
		<tr>
			<td><?=_('NOMBRE HOTEL').' *'?><br>
			<input type='text' name='NOMBREHOTEL' id='nombrehotel' size='45' value="<?=$asis->asistencia_servicio->NOMBREHOTEL ?>"></td>
			
			<td><?=_('FECHA INICIO HOSPEDAJE').' *'?><br>
			<input type="text" name='INICIOHOSPEDAJE' id='date2' value="<?=$fechainicio?>" readonly>
 			<input type="button" id="calendarButton2" value="..." onmouseover="setupCalendars_asist();" class='normal'>
			</td>
	
			<td><?=_('FECHA FIN HOSPEDAJE').' '?><br>
			<input type="text" name='FINHOSPEDAJE' id='date3' value="<?=$fechafin?>" readonly>
 			<input type="button" id="calendarButton3" value="..." onmouseover="setupCalendars_asist();" class='normal'>
			</td>
			
			<td><?=_('# DIAS')?><br>
			<input type='text' name='NDIAS' id='ndias' size='8' value="<?=$asis->asistencia_servicio->NDIAS ?>">
			</td>
		
			<td><?=_('# PERSONAS')?><br>
			<input type='text' name='NPERSONAS' id='npersonas' size='8' value="<?=$asis->asistencia_servicio->NPERSONAS ?>">
			</td>

		</tr>
			<tr>
			<td ><?=_('UBICACION DEL HOTEL')?><br>
			<textarea name='DIRECCIONHOTEL' id='direccionhotel'><?=$asis->asistencia_servicio->DIRECCIONHOTEL ?></textarea>
			</td>
		
			<td colspan="2"><?=_('OBSERVACIONES')?><br>
			<textarea name='OBSERVACION' id='observacion'><?=$asis->asistencia_servicio->OBSERVACION ?></textarea>
			</td>
		</tr>
		
	</tbody>
</table>
</form>