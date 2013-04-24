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
<form id='form_ambulancia'>
<input type="hidden" name='IDUSUARIOMOD' id="idusuariomod" value="<?=$idusuario?>">
<table width="100%">
	<tbody>
		<tr>
			<td rowspan="2"><?=_('SINTOMATOLOGIA')?><font color="red">*</font><br>
			<textarea name='SINTOMATOLOGIA' id='sintomatologia'><?=$asis->asistencia_servicio->SINTOMATOLOGIA ?></textarea></td>

			<td rowspan='2'><?=_('ANTECEDENTES CLINICOS')?><br>
			<textarea name='ANTECEDENTESCLINICOS' id='antecedentes'><?=$asis->asistencia_servicio->ANTECEDENTESCLINICOS ?></textarea></td>
			
			<td rowspan='2'><?=_('DIAGNOSTICO')?><font color="red">**</font><br>
			<textarea name='DIAGNOSTICO' id='diagnostico'><?=$asis->asistencia_servicio->DIAGNOSTICO ?></textarea> </td>
			
			<td rowspan='2'><?=_('RECOMENDACION')?><font color="red">**</font><br>
			<textarea name='RECOMENDACION' id='recomendacion'><?=$asis->asistencia_servicio->RECOMENDACION ?></textarea></td>
		</tr>
		<tr></tr>
		<tr>
			<td><?=_('DERIVACION')?><br>
				<? $con->cmbselect_ar('DERIVACION',$confirmar,$asis->asistencia_servicio->DERIVACION,'','','SELECCIONE');?>
			</td>
			<?if (isset($asis))
			{
				$ubigeo = new  ubigeo();
				$ubigeo->leer('ID',$asis->temporal,'asistencia_medica_ambulancia_destino',$asis->asistencia_servicio->IDLUGARDESTINO);
			}
			?>
			<input type='hidden' name='IDLUGARDESTINO' id='idlugardestino' value="<?=$ubigeo->ID ?>">
			<td colspan="2"><?=_('LUGAR DE DESTINO')?><br>
			<input type='text' name='LUGARDESTINO' id='lugardestino' value="<?=$ubigeo->direccion.' '.$ubigeo->numero ?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15" title="<?=_('EDITAR DIRECCION')?>" onclick="mod_ubigeo($F('idlugardestino'),'idlugardestino','lugardestino','asistencia_medica_ambulancia_destino')"  align='absbottom' border='0' style='cursor: pointer;' ></img>
			<img src='../../../imagenes/iconos/deletep.gif' alt="15" width="15" title="<?=_('BORRAR DIRECCION')?>" onclick="$('idlugardestino').clear();$('lugardestino').clear();"  align='absbottom' border='0' style='cursor: pointer;' ></img>
			<?}?>
			</td>
			
			
		</tr>
	</tbody>
</table>
</form>

