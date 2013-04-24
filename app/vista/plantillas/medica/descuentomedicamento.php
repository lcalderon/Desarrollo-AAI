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
<legend><?=_('DESCUENTO DE MEDICAMENTOS')?></legend>
<form id='form_descuentomedicamento'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table width="100%">
	<tbody>
		<tr>
			<td valign="top" ><?=_('SINTOMATOLOGIA')?><br>
			<textarea name='SINTOMATOLOGIA' id='sintomatologia'><?=$asis->asistencia_servicio->SINTOMATOLOGIA?></textarea>
			</td>
			<td>&nbsp;</td>
			<td valign="top"><?=_('NOMBREMEDICAMENTO')?><br>
			<textarea name="NOMBREMEDICAMENTO" id='nombremedicamento'><?=$asis->asistencia_servicio->NOMBREMEDICAMENTO?></textarea>
			</td>	
			<td>&nbsp;</td>
			<td valign="top"><?=_('DESCUENTO')?><br>
			<input type="text" name='PORCENTAJEDESCUENTO' id='porcentajedescuento' value="<?=$asis->asistencia_servicio->PORCENTAJEDESCUENTO?>"size="20">
			</td>
			<td>&nbsp;</td>
			<td valign="top"><?=_('MONTO FACTURA')?><br>
			<input type="text" name='MONTOFACTURA' id='montofactura' value="<?=$asis->asistencia_servicio->MONTOFACTURA?>" size="20">
			</td>
			<td>&nbsp;</td>
			<td valign="top"><?=_('OTROS')?><br>
			<textarea name="OTROS" id='otros'><?=$asis->asistencia_servicio->OTROS?></textarea>
			
			</td>
		</tr>
	</tbody>
</table>		
</form>