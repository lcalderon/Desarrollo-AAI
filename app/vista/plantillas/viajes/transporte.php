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
<form id='form_transporte'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
	<style>
		.cambiocolor {
			border-color: red;
			border-style: solid;
			border-width: 1px;
			color: #333333;
			background-color: #FFE8E8;
			font-size: 10px; 
			font-family: Verdana, Arial, Helvetica, sans-serif;
			text-transform: uppercase;
		}	  
	</style> 
<table width="100%">
	<tbody 	>
		<tr>
			<td><?=_('TIPO TRANSPORTE')?><br>
				<? $con->cmbselect_ar('ARRTIPOTRANSPORTE',$arrtipotransporte,$asis->asistencia_servicio->ARRTIPOTRANSPORTE,'','','SELECCIONE');?>
			</td>
			<td><?=_('LINEA TRANSPORTE').' *'?><br>
			<input type='text' name='LINEATRANSPORTE' id='lineatransporte' size='45' value="<?=$asis->asistencia_servicio->LINEATRANSPORTE ?>"></td>
			
			<td><?=_('# PERSONAS')?><br>
			<input type='text' name='NPERSONAS' id='npersonas' size='8' value="<?=$asis->asistencia_servicio->NPERSONAS ?>">
			</td>
		</tr>
		<tr></tr>
		<tr>
			<td ><?=_('ORIGEN')?><br>
			<textarea name='ORIGEN' id='origen'><?=$asis->asistencia_servicio->ORIGEN ?></textarea>
			</td>
			
			<td ><?=_('DESTINO')?><br>
			<textarea name='DESTINO' id='destino'><?=$asis->asistencia_servicio->DESTINO ?></textarea>
			</td>
			
			<td ><?=_('NOMBRES')?><br>
			<textarea name='NOMBRES' id='nombres'><?=$asis->asistencia_servicio->NOMBRES ?></textarea>
			</td>
			
			<td colspan="2"><?=_('OBSERVACIONES')?><br>
			<textarea name='OBSERVACION' id='observacion'><?=$asis->asistencia_servicio->OBSERVACION ?></textarea>
			</td>


		</tr>
		
	</tbody>
</table>
</form>