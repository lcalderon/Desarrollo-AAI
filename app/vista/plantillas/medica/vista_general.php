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
	<input type='hidden' name='CONCLUCIONTEMPRANA' id='concluciontemprana' value="">
	<input type='hidden' name='CONCLUCIONCONPROVEEDOR' id='conclucionconproveedor' value=""> 	
	<table>
		<tr>
			<td><?=_('ASISTENCIA')?></td>
			<td><input type="text" name="IDASISTENCIA" id='idasistencia' value="<?=$asis->idasistencia?>" size='10' dir="rtl" readonly style="color:red;"></td>
		</tr>
		<tr>
			<td><?=_('SERVICIO EN:')?></td>
			<td>
			<select name='ARRCONDICIONSERVICIO' id='arrcondicionservicio' onchange="act_garantia();cambioseleccion();actualizar_lista(this.value);">
			<? 
			foreach ($desc_cobertura_servicio as $indice=>$valor) {
				if ($arrcondicionservicio=='CON' && $indice=='COB') 
						echo "<option value='$indice' disabled>$valor</option>";
				else if ((isset($asis)) && ($asis->arrcondicionservicio==$indice)) 
						echo "<option value='$indice' selected>$valor</option>";
				else if ($arrcondicionservicio==$indice) 
						echo "<option value='$indice' selected>$valor</option>";
				else echo "<option value='$indice'>$valor</option>";
			}
			?>
			</select>
			<div id='zona_garantia' style='display:none;float:right'>
			<?=_('ASISTENCIA REL');?>
			<input type="text" name="GARANTIA_REL" id='garantia_rel' title="<?=_('ASISTENCIA RELACIONADA A LA GARANTIA')?> " value="<?=$asis->garantia_rel?>">
			<img src="/imagenes/iconos/historial_ubi.gif" onclick="ver_asistencias('<?=$exp->idafiliado?>');"/>
			</div>
			</td>
		</tr>
		<tr>
			<input type='hidden' name='IDCONTACTANTE' id='idcontactante' value="<?=(isset($asis)?$asis->expediente->personas[CONTACTO][IDPERSONA]:$exp->personas[CONTACTO][IDPERSONA]) ?>">
			<td><?=_('CONTACTANTE')?></td> 
			<?
			if (isset($asis)){
				$contactante = $asis->expediente->personas[CONTACTO][NOMBRE].' '.$asis->expediente->personas[CONTACTO][APPATERNO].' '.$asis->expediente->personas[CONTACTO][APMATERNO];

			}
			?>
			<td><input type='text' name='CONTACTANTE' id='contactante' value="<?=$contactante?>" size='60' readonly ></td>
		</tr>
		<tr>
			<input type='hidden' name='IDLUGARDELEVENTO' id='idlugardelevento' value="<?=(isset($asis)?$asis->lugardelevento->ID:'')?>">
			<td><?=_('LUGAR DEL EVENTO').' *'?></td>
			<td><input type='text' name='LUGARDELEVENTO' id='lugardelevento' value="<?=(isset($asis)?$asis->lugardelevento->direccion.' '.$asis->lugardelevento->numero:'')?>" size="60" readonly>
			<? if ($desactivado==''){?>
			<img src='../../../imagenes/iconos/editars.gif' alt="15" width="15" onclick="mod_ubigeo($F('idlugardelevento'),'idlugardelevento','lugardelevento','asistencia_lugardelevento')" align='absbottom' border='0' style='cursor: pointer;'  title="<?=_('MODIFICAR DIRECCION')?>" ></img>
			<img src='../../../imagenes/iconos/new-p.gif' alt="15" width="15" onclick="copiar_direccion()" align='absbottom' border='0' style='cursor: pointer;'  title="<?=_('COPIAR DIRECCION DEL EXPEDIENTE')?>"></img>
			<?}?>
			</td>
		</tr>
		<tr>
			<td><?=_('REEMBOLSO')?></td>
			<td><input type="checkbox" name="REEMBOLSO" id='reembolso' onchange="act_reembolso()" <?=($asis->reembolso)?'checked':'';?>>
			<div id='zona_reembolso'  style='display:none;float:center'>
			<?=_('Reportado');?>
			<input type="radio" name="REPORTADO" id='reportado' value='1' <?=($asis->reportado)?'checked':'';?> >
			<?=_(' No Reportado');?>
			<input type="radio" name="REPORTADO" id='reportado' value='0' <?=($asis->reportado==0)?'checked':'';?>>
			</div>
		</td>
		
		</tr>
		<tr>
			<td><?=_('UBICACION FISICA')?></td>
			<td><? $con->cmbselect_ar('UBICACIONFISICA',$ubicacion_fisica,$asis->asistencia_familia->TIPOINMUEBLE,'id=lugar','','') ?></td>
		</tr>
		
		<tr>
			<td><?=_('PACIENTE').' *'?></td>
			<td><input type='text' name='NOMBREPACIENTE' id='nombrepaciente' value="<?=$asis->asistencia_familia->NOMBREPACIENTE?>" size="50"></td>
		</tr>
		<tr>
			<td><?=_('FECHA NACIMIENTO').' *'?></td>
			<td>
				(AAAA)<input type='text' name='ANIO' id='anio' value="<?=substr($asis->asistencia_familia->FECHANACIMIENTO,0,4)?>" size="6" maxlength="4">
				(MM)<input type='text' name='MES' id='mes' value="<?=substr($asis->asistencia_familia->FECHANACIMIENTO,5,2)?>" size="4" maxlength="2">
				(DD)<input type='text' name='DIA' id='dia' value="<?=substr($asis->asistencia_familia->FECHANACIMIENTO,8,2)?>" size="4" maxlength="2">
				 &nbsp;&nbsp;EDAD <input type='text' name='EDADPACIENTE' id='edadpaciente' value="<?=$asis->asistencia_familia->EDADPACIENTE?>" size="4" maxlength="2">
			</td>
		</tr>
		<tr>
			<td><?=_('VINCULO CON N/A').' *'?></td>
			<td><input type='text' name='VINCULOAFILIADO' id='vinculoafiliado' value="<?=$asis->asistencia_familia->VINCULOAFILIADO?>" size="30"></td>
		</tr>
		<tr>
			<td valign="top"><?=_('DESCRIPCION DEL HECHO')?></td>
			<td><textarea name='DESCRIPCIONHECHO' id='descripcionhecho' cols="30"><?=$asis->asistencia_familia->DESCRIPCIONHECHO?></textarea></td>
		</tr>
	
		<div id='zona_observacion' style="display:none">
		<?=_('JUSTIFICACION PARA EDITAR CONDICION DEL SERVICIO:')?><br>
		<textarea name='OBSERVACION' id='observacion'></textarea>
		</div>
		
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
	if ((($F('arrcondicionservicio')=='GAR') || ($F('arrcondicionservicio')=='CNO') ) && $F('garantia_rel')=='') alert("<?=_('INGRESE NUMERO DE ASISTENCIA RELACIONADA A LA GARANTIA')?>");
	else if ($F('idlugardelevento')=='') alert("<?=_('INGRESE EL LUGAR DEL EVENTO')?>");
	//else if ($F('descripcionhecho')=='') alert("<?=_('INGRESE DESCRIPCION DEL HECHO')?>");
	else if ($F('nombrepaciente')=='' ) alert("<?=_('INGRRESE NOMBRE DEL PACIENTE')?>");
	else if ($F('anio')=='' && $F('edadpaciente')=='') alert("<?=_('INGRESE EL ANIO DEL PASCIENTE')?>");
	else if ($F('vinculoafiliado')=='') alert("<?=_('INGRESE EL VINCULO CON EL AFILIADO')?>");
	else sw = true;

	return sw ;
}

function copiar_direccion(){
	new Ajax.Request('/app/controlador/ajax/ajax_pregrabar_ubigeo_asistencia.php',
	{
		method : 'post',
		parameters : {
			idexpediente: "<?=$idexpediente?>"
		},
		onSuccess: function(t){
			var elemento= t.responseText.split('/');
			$('idlugardelevento').value = elemento[0];
			$('lugardelevento').value = elemento[1];
		}
	});

	return;
}
</script>

