<div id='datos_generales_plantilla' style="float:left; weight:48%" align="left">
<form name='form_datos_generales' id='form_datos_generales'  >
 
	<input type='hidden' name='IDEXPEDIENTE' id='idexpediente' value="<?=$exp->idexpediente ?>"> 
	<input type='hidden' name='IDFAMILIA' id='idfamilia' value="<?=$fam->idfamilia ?>">
	<input type='hidden' name='IDPROGRAMASERVICIO' id='idprogramaservicio' value="<?=$asis->idprogramaservicio?>">
	<input type='hidden' name='IDSERVICIO' id='idservicio' value="<?=$asis->servicio->idservicio?>">
	<input type="hidden" name="ARRSTATUSASISTENCIA" id='arrstatusasistencia' value="PRO">
	<input type='hidden' name='IDETAPA' id='idetapa' value="<?=(isset($asis))?$asis->etapa->idetapa:'2';?>">
	<input type="hidden" name='IDCUENTA' id='idcuenta' value="<?=$exp->cuenta->idcuenta ?>">
	<input type="hidden" name='IDPROGRAMA' id='idprograma' value="<?=$exp->programa->idprograma ?>">
	<input type="hidden" name='IDUSUARIO' id='idusuario' value="<?=$idusuario ?>">
	<input type="hidden" name='JUSTIFICACION' id='justificacion' value="">
	<input type='hidden' name='ETAPACULMINADA' 	id='etapaculminada' value="<?=$idetapa?>">
	<input type='hidden' name='cveafiliado' id='cveafiliado' value="<?=$exp->cveafiliado ?>"> 	
	<input type='hidden' name='CONCLUCIONTEMPRANA' id='concluciontemprana' value=""> 	
	<input type='hidden' name='CONCLUCIONCONPROVEEDOR' id='conclucionconproveedor' value=""> 	

<table >
	<tbody>
		<tr>
			<td><?=_('ASISTENCIA')?>
			<input type="text" name='IDASISTENCIA' id='idasistencia' value="<?=$asis->idasistencia?>" size='10' readonly style="color:red;"></td>
			<td><?=_('Nro. DE CASO')?>
			<input type="text" name='' value='' size='10'></td>
		</tr>		
		<tr>
			<td><?=_('SERVICIO EN:');?></td>
			<td>
			<select name='ARRCONDICIONSERVICIO' id='arrcondicionservicio' onchange="act_garantia();cambioseleccion();actualizar_lista(this.value);">
			<? 
			foreach ($desc_cobertura_servicio as $indice=>$valor) {
				if ($arrcondicionservicio=='CON' && $indice=='COB') echo "<option value='$indice' disabled>$valor</option>";
				else if ((isset($asis)) && ($asis->arrcondicionservicio==$indice)) echo "<option value='$indice' selected>$valor</option>";
				else if ($arrcondicionservicio==$indice) echo "<option value='$indice' selected>$valor</option>";
				else echo "<option value='$indice'>$valor</option>";
			}
			?>
			</select>
			
		</tr>				
		<tr>
			<td colspan="2"><?=_('ORIGEN LLAMADA')?>
			<? $con->cmbselect_ar('ORIGENLLAMADA',$origen_llamada,'','','','ORIGEN DE LLAMADA');?></td>
		</tr>
	
		<div id='zona_observacion' style="display:none">
		<?=_('JUSTIFICACION PARA EDITAR CONDICION DEL SERVICIO:')?><br>
		<textarea name='OBSERVACION' id='observacion'></textarea>
		</div>
	</tbody>
</table>


</form>
</div>


<div id='listado_de_servicios' align="right" style="weight:48%">
<? if (!isset($asis)) include_once('vista_lista_servicios.php');?>
</div>
</body>
</html>


<script type="text/javascript">
function validar_datos_generales(){
	var sw=false;
	 sw = true;
	return sw ;
}
</script>
