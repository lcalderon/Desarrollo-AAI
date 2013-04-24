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
<legend><?=_('DELIVERY DE MEDICAMENTOS')?></legend>
<form id='form_deliverymedicamentos'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">

<table width="100%">
	<tr>
	 	<td colspan='2' valign="top"><?=_('MEDICAMENTO').' *'?><br>
	 	<textarea name='NOMBREMEDICAMENTO' id='nombremedicamento'><?=$asis->asistencia_servicio->NOMBREMEDICAMENTO?></textarea>
		<td>&nbsp;</td>
		<? if (isset($asis))
		{
			$ubigeo = new  ubigeo();
			$ubigeo->leer('ID',$asis->temporal,'asistencia_medica_deliverymedicamentos_ubigeo',$asis->asistencia_servicio->IDLUGARENTREGA);
		}
		?>
		<input type='hidden' name='IDLUGARENTREGA' id='idlugarentrega' value="<?=$ubigeo->ID ?>">
		<td colspan="2" valign="top"><?=_('LUGAR DE ENTREGA').' *'?><br>
		<input type='text' name='LUGARENTREGA' id='lugarentrega' value="<?=$ubigeo->direccion.' '.$ubigeo->numero ?>" size="60" readonly>
		<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15" title="<?=_('EDITAR DIRECCION')?>" onclick="mod_ubigeo($F('idlugarentrega'),'idlugarentrega','lugarentrega','asistencia_medica_deliverymedicamentos_ubigeo')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
			<img src='../../../imagenes/iconos/deletep.gif' alt="15" width="15" title="<?=_('BORRAR DIRECCION')?>" onclick="$('idlugarentrega').clear();$('lugarentrega').clear();"  align='absbottom' border='0' style='cursor: pointer;' ></img>
		<?}?>
		</td>
		<td>&nbsp;</td>		
		<td colspan='2' valign="top"><?=_('NOMBRE DESTINATARIO').' *'?><br>
		<input type="text" name='NOMBREDESTINATARIO' id='nombredestinatario' value="<?=$asis->asistencia_servicio->NOMBREDESTINATARIO?>"size="40"></td>
	</tr>
</tbody>
</table>


