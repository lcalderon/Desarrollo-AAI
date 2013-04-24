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
<body>
<form id='form_visitatecnica'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod'  value="<?=$idusuario?>">
<table width="100%">
	<tbody>
		<tr>
			<td rowspan="2"><?=_('DESCRIPCION DE LA FALLA')?><font color="red">*</font><br>
			<textarea name='DESCRIPCIONSERVICIO' id='descripcionservicio'><?=$asis->asistencia_servicio->DESCRIPCIONSERVICIO?></textarea></td>

			<td rowspan='2'><?=_('DIAGNOSTICO')?><font color="red">**</font><br>
			<textarea name='DIAGNOSTICO' id='diagnostico'><?=$asis->asistencia_servicio->DIAGNOSTICO?></textarea> </td>
			
			<td rowspan='2'><?=_('SOLUCION DE LA FALLA')?><font color="red">**</font><br>
			<textarea name='SOLUCIONFALLA' id='solucionfalla'><?=$asis->asistencia_servicio->SOLUCIONFALLA?></textarea></td>
			
			<td rowspan='2'><?=_('RECOMENDACIONES')?><font color="red">**</font><br>
			<textarea name='RECOMENDACION' id='recomendacion'><?=$asis->asistencia_servicio->RECOMENDACION?></textarea></td>
			
			<td rowspan='2'><?=_('OTROS')?><br>
			<textarea name='OTROS' id='otros'><?=$asis->asistencia_servicio->OTROS?></textarea></td>
		</tr>
		<tr></tr>
		
		
	</tbody>
</table>
 
</form>

</body>
