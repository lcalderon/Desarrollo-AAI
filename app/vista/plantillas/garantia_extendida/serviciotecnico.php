<?
session_start();

include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');

//  VARIABLES DEL SERVICIO GARANTIA_EXTENDIDA




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
<form id='form_serviciotecnico'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod'  value="<?=$idusuario?>">
<table width="100%">
	<tbody>
		<tr>
			<td colspan="2"><?=_('DESCRIPCION DEL PROBLEMA')?><br>
			<textarea name='DESCRIPCIONPROBLEMA' id='descripcionproblema' ><?=$asis->asistencia_servicio->DESCRIPCIONPROBLEMA?></textarea></td>
			<td><?=_('RESULTADO DEL PROBLEMA')?><br>
			<textarea name='RESULTADOPROBLEMA' id='resultadoproblema' ><?=$asis->asistencia_servicio->RESULTADOPROBLEMA?></textarea></td>
			<td colspan="2"><?=_('RECOMENDACON')?><br>
			<textarea name='RECOMENDACION' id='recomendacion'><?=$asis->asistencia_servicio->RECOMENDACION?></textarea></td>
			<td><?=_('OBSERVACION')?><br>
			<textarea name='OBSERVACION' id='observacion' ><?=$asis->asistencia_servicio->OBSERVACION?></textarea></td>
		</tr>
	</tbody>
</table>
