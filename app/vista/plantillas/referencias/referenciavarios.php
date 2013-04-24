<?
session_start();
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_mysqli.inc.php');
$con= new DB_mysqli();
$idusuario= $_SESSION[user];
?>
<style type="text/css">
<!--
.Estilo1 {color: #B4BEE9}
-->
</style>

<legend><?=_('REFERENCIA VARIOS')?></legend>

<form id='form_referenciavarios'>
<input type="hidden" name='IDUSUARIOMOD' id='idusuariomod' value="<?=$idusuario?>">
<table>

	<tbody>
		<tr>
			<td  valign="top"><?=_('TIPO CONSULTA')?><font color="red">*</font><br>
		<?	$con->cmbselect_ar('ARRTIPOCONSULTA',$referencia_varios,(isset($asis))?$asis->asistencia_servicio->TIPOCONSULTA:$tipoconsulta,'id=arrtipoconsulta','onclick=mostrar_subservicio(this,"subreferencia","subreferencia_taller")','')?></td>
			<td colspan="2"><div id='subreferencia' <? if($idasistencia !='' && $asis->asistencia_servicio->TIPOCONSULTA =='TALL'){ echo "style='display: block'"; }else{  echo "style='display: none'"; } ?>><?=_('SUBSERVICIO')?><BR><?	$con->cmbselect_ar('REFTALLER',$referencia_taller,(isset($asis))?$asis->asistencia_servicio->SUBSERVICIO:$subservicio,'id=subservicio','','')?></div>
			<div id='subreferencia_taller' <? if($idasistencia !='' && $asis->asistencia_servicio->TIPOCONSULTA =='RHOG'){ echo "style='display: block'"; }else{  echo "style='display: none'"; } ?>><?=_('SUBSERVICIO')?><BR><?	$con->cmbselect_ar('REFHOGAR',$referencia_hogar,(isset($asis))?$asis->asistencia_servicio->SUBSERVICIO:$subservicio,'id=subservicio','','')?></div>
			</td>
		</tr>
		<tr>
			<td rowspan='2'><span class="Estilo1"></span>
			  <?=_('RESULTADO')?><font color="red">**</font><br>
		  <textarea name='RESULTADO' id='resultado' cols="30"><?=$asis->asistencia_servicio->RESULTADO?></textarea> </td>
			
			<td rowspan='2'><?=_('OTROS')?><br>
			<textarea name='OTROS' id='otros' cols="30"><?=$asis->asistencia_servicio->OTROS?></textarea></td>
			
			
		</tr>
		<tr></tr>
		
	</tbody>
	
</table>
</form>