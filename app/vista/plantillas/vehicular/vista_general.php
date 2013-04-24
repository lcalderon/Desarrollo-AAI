


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
	
	
<div id='datos_generales_plantilla' style="float:left; weight:48%" align="left">
 <form name='form_datos_generales' id='form_datos_generales'  >
 
	<input type='hidden' name='idvehiculo' id='idvehiculo' value="<?=$asis->asistencia_familia->IDVEHICULO; ?>"> 
	<input type='hidden' name='idafiliado' id='idafiliado' value="<?=$exp->idafiliado ?>"> 
	<input type='hidden' name='IDEXPEDIENTE' id='idexpediente' value="<?=$exp->idexpediente ?>"> 
	<input type='hidden' name='cveafiliado' id='cveafiliado' value="<?=$exp->cveafiliado ?>"> 
	<input type='hidden' name='IDFAMILIA' id='idfamilia' value="<?=$fam->idfamilia ?>">
	<input type='hidden' name='IDSERVICIO' id='idservicio' value="<?=$asis->servicio->idservicio?>">
	<input type="hidden" name="ARRSTATUSASISTENCIA" id='arrstatusasistencia' value="PRO">
	<input type='hidden' name='IDETAPA' id='idetapa' value="<?=(isset($asis))?$asis->etapa->idetapa:'2';?>">
	<input type="hidden" name='IDCUENTA' id='idcuenta' value="<?=$exp->cuenta->idcuenta ?>">
	<input type="hidden" name='IDPROGRAMA' id='idprograma' value="<?=$exp->programa->idprograma ?>">
	<input type="hidden" name='IDUSUARIO' id='idusuario' value="<?=$idusuario ?>">
	<input type="hidden" name='JUSTIFICACION' id='justificacion' value="">
	<input type='hidden' name='ETAPACULMINADA' 	id='etapaculminada' value="<?=$idetapa?>">
	<input type='hidden' name='IDPROGRAMASERVICIO' id='idprogramaservicio' value="<?=$asis->idprogramaservicio?>">
	<input type="hidden" name="fechareg" id="fechareg" value="<?=date("Y-m-d H:i:s");?>" /> 
	<input type='hidden' name='CONCLUCIONTEMPRANA' id='concluciontemprana' value="">
	<input type='hidden' name='CONCLUCIONCONPROVEEDOR' id='conclucionconproveedor' value=""> 	

	<table>
		<tr>
			<td><?=_('ASISTENCIA')?></td>
			<td><input type="text" name="IDASISTENCIA" id='idasistencia' value="<?=$asis->idasistencia?>" size='10' dir="rtl" readonly style="color:red;"></td>
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
			<td><?=_('LUGAR DEL EVENTO')?></td>
			<td><input type='text' name='LUGARDELEVENTO' id='lugardelevento' onblur="cambiarestilo('lugardelevento');" value="<?=(isset($asis)?$asis->lugardelevento->direccion.' '.$asis->lugardelevento->numero:'')?>" size="60" readonly>
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
			<td><?=_('TIPO DE INMUEBLE')?></td>
			<td><? $con->cmbselect_ar('TIPOINMUEBLE',$desc_tipo_inmueble_veh,$asis->asistencia_familia->TIPOINMUEBLE,'id=lugar','','') ?></td>
		</tr>
	 
		
	</table>
	<div id='div-vehiculo'>
	<? include_once("datos_vehiculo.php");?>
	</div>	
	
	<div id='zona_observacion' style="display:none">
	<?=_('JUSTIFICACION PARA EDITAR CONDICION DEL SERVICIO:')?><br>
	<textarea name='OBSERVACION' id='observacion'></textarea>
	</div>
	
</form>
</div>

<div id='listado_de_servicios' align="right" style="weight:48%">
<? if (!isset($asis)) include_once('vista_lista_servicios.php');?>
</div>
</body>
</html>


<script type="text/javascript">

	function cambiarestilo(nombre,opc){
		if(opc ==1)	nomclass='cambiocolor';	else nomclass='';
		$(nombre).className =nomclass;
	}
		
	function validar_datos_generales(){

	var sw=false;
	if ($F('lugardelevento')==''){
		alert('<?=_('INGRESE EL LUGAR DEL EVENTO')?>.');
		
		cambiarestilo('lugardelevento','1');

	} else if($F('txtmarca')==''){
		alert('<?=_('INGRESE LA MARCA')?>.');
		cambiarestilo('txtmarca','1');
	} else if($F('txtsubmarca')==''){
		alert('<?=_('INGRESE EL MODELO')?>.');
		cambiarestilo('txtsubmarca','1');
	} else if($F('txtplaca')==''){
		alert('<?=_('INGRESE LA PLACA')?>.');
		cambiarestilo('txtplaca','1');		
	/*} else if($F('cmbtrasmision')=='' && $F('idservicio')==8) {
	
		alert('<?=_('SELECCIONE EL TIPO DE TRANSMISION')?>.');
		cambiarestilo('cmbtrasmision','1');	
	} else if($F('cmbcombustible')=='' && $F('idservicio')==8){
	
		alert('<?=_('SELECCIONE EL TIPO DE COMBUSTIBLE')?>.');
		cambiarestilo('cmbcombustible','1');
		 */
	}else if($F('cmbfamilia')==''){
		alert('<?=_('SELECCIONE LA CLASE DE VEHICULO')?>.');
		cambiarestilo('cmbfamilia','1');
	} else if($F('txtcolor')==''){
		alert('<?=_('INGRESE EL COLOR')?>.');
		cambiarestilo('txtcolor','1');
	} else if($F('cmbanio')==''){
		alert('<?=_('INGRESE EL ANIO')?>.');
		cambiarestilo('cmbanio','1');
	} else{
		sw = true;
	}
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

	
	function cargar_vehiculo(idvalor,opcion)
	{
		new Ajax.Updater('div-vehiculo', 'vehicular/datos_vehiculo.php', {			
			  parameters: { 
				idvehiculo:idvalor,
				opcion:opcion
			  },
			  method: 'post'
			 // evalScripts:true 
			});
	} 	
	 
	function ventana_vehiculo(){
		
		var cve_id=document.form_datos_generales.cveafiliado.value;
		window.open("vehicular/frm_vehiculosac.php?cve_id="+cve_id,"mediop","height=400, width=800,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes");
		
	} 
	
</script>	