<div id='datos_generales_plantilla' style="float:left; weight:48%" align="left">
 <form name='form_datos_generales' id='form_datos_generales'>
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
			<td><?
			$con->cmbselect_ar('ARRCONDICIONSERVICIO',$desc_cobertura_servicio,(isset($asis))?$asis->arrcondicionservicio:$arrcondicionservicio,'id=arrcondicionservicio','onchange=act_garantia();actualizar_lista(this.value);','')
			?>
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
			<input type="radio" name="REPORTADO" id='reportado' value='1'  <?=($asis->reportado)?'checked':'';?> >
			<?=_(' No Reportado');?>
			<input type="radio" name="REPORTADO" id='reportado' value='0'  <?=($asis->reportado==0)?'checked':'';?>>
			</div>
		</td>	
		<tr>
			<td><?=_('AMBITO')?></td>
			<td><? $con->cmbselect_ar('AMBITO',$desc_ambito,'NAC','id=ambito','','')?></td>
		</tr>

	
		
<div id='zona_observacion' style="display:none">
		<?=_('JUSTIFICACION POR CONDICION DEL SERVICIO:')?><br>
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


function mostrar(elemento,valor) {

      if(elemento.value=="PAR" || elemento.value=="PAUX" || elemento.value=="OTRO") {
	  document.getElementById(valor).style.display='block';
      }else{
	  document.getElementById(valor).style.display='none';
      }
}

function mostrar_subservicio(elemento,valor,valor2) {

      if(elemento.value=="TALL") {
	  document.getElementById(valor).style.display='block';
	  document.getElementById(valor2).style.display='none';
      }else if(elemento.value=="RHOG"){
	  document.getElementById(valor).style.display='none';
	  document.getElementById(valor2).style.display='block';
      }else{
		document.getElementById(valor).style.display='none';
	  document.getElementById(valor2).style.display='none';
	  
	  }
}

function validar_datos_generales(){
	var sw=false;
	if ($F('arrcondicionservicio')=='GAR' && $F('garantia_rel')=='') alert("<?=_('INGRESE NUMERO DE ASISTENCIA RELACIONADA A LA GARANTIA')?>");
//	else if ($F('lugardelevento')=='') alert("<?=_('INGRESE LA UBICACION DEL EVENTO')?>");
	else sw = true;
	return sw ;
}

function func_observacion(modo){
	Dialog.confirm($('zona_observacion').innerHTML,
	{
		className:"alphacube",
		width:400,
		showEffect: Element.show,
		hideEffect: Element.hide,
		okLabel: "<?=_('Aceptar')?>",
		cancelLabel: "<?=_('Cancelar')?>",
		buttonClass: 'normal',
		onOk: function(win)
		{
			if ($F('observacion')=='') return true;
			else
			{
				$('justificacion').value = $F('observacion');
				grabar("<?=$fam->descripcion ?>",modo);
			}
			return true;
		}
	}
	);

	return;
}

function actualizar_disponibilidad(idasistencia,idetapa){
	new Ajax.Updater('zona_disponibilidad','vista_disponibilidad_afiliado.php?idasistencia='+idasistencia+'&idetapa='+idetapa,
	{
		method : 'post',
		parameters : {idasistencia : '<?=$asis->idasistencia?>',
			      //idetapa  :'<?=$idetapa?>',
		
		},

	});
	return;
}

function grabar_dispo(){
 FECHAD=document.getElementById('date').value;
 FECHAD2=document.getElementById('date4').value;
 HORAINI= document.getElementById('cbhora1').value+':'+document.getElementById('cbminuto1').value+':00';
  HORAFIN= document.getElementById('cbhora2').value+':'+document.getElementById('cbminuto2').value+':00';

FECHAINI= FECHAD+' '+HORAINI;
FECHAFIN= FECHAD2+' '+HORAFIN;
//alert(FECHAINI);
//alert(FECHAFIN);
  idasist=$('idasistencia').value;
//alert(<?=$idetapa?>);
 //alert(FECHAD + HORAINI + HORAFIN + idasist);
	if(FECHAINI>= FECHAFIN){
		 alert('<?=_('la fecha final no puede ser menor o igual a la fecha inicial!!')?>');
	}else{
				new Ajax.Request('/app/controlador/ajax/ajax_grabar_disponibilidad.php',
				{
				method : 'post',
				evalScripts : true,
				parameters : {
					IDASISTENCIA : idasist,
					IDUSUARIOMOD : '<?=$idusuario?>',
					FECHA : FECHAINI, 
					FECHA2 : FECHAFIN,
					OPCION : $('btnagregardispo').value,
					IDDISPO : $('hid_iddispo').value
				},
				onSuccess: function(t){
				//alert(t.responseText);
					//alert($('btnagregardispo').value);
					if($('btnagregardispo').value=='Guardar Edicion'){
						  $('btnagregardispo').value='Agregar';
					  }
					actualizar_disponibilidad(idasist,<?=$idetapa?>);
					
				}
			});
      }
}

function accion_disponibilidad(modo,fechaini,fechafin,iddispo){
      idasist=$('idasistencia').value;
  
	switch (modo){
		case 'editar': // GUARDA LA BITACORA
		{
			$('btnagregardispo').value='Guardar Edicion';
			$('date').value=fechaini.substring(0,10);
			$('date4').value=fechafin.substring(0,10);
			hora1 = fechaini.substring(11,13);  
			hora2 = fechafin.substring(11,13);  
			
			minuto1 = fechaini.substring(14,16);
			minuto2 = fechafin.substring(14,16);  

			$('cbhora1').value=hora1;
			$('cbhora2').value=hora2;
			$('cbminuto1').value=minuto1;
			$('cbminuto2').value=minuto2;
			$('hid_iddispo').value=iddispo;
			
				/*new Ajax.Request('/app/controlador/ajax/ajax_grabar_bitacora_monitoreo_proveedor.php?proveedor_act='+prov,
				{
					method : 'post',
					parameters : $('form_bitacora').serialize(true),
					onSuccess: function(t)
					{
						//alert(t.responseText);
						$('comentario').value='';
						actualizar_bitacora();
					}
				});
			*/
			break;
		}

	case 'eliminar':  // CANCELADO POSTERIOR DE LA ASISTENCIA
		{
	   // alert(iddispo);
		     if(confirm('<?=_('ESTA SEGURO QUE DESEA ELIMINAR LA DISPONIBILIDAD SELECCIONADA?')?>')){
			new Ajax.Request('/app/controlador/ajax/ajax_eliminar_disponibilidad.php',
			{
				method : 'post',
				parameters : {
					IDDISPO : iddispo
					
				},
				onSuccess: function(t){
					//					alert(t.responseText);
				  $('btnagregardispo').value='Agregar';	
				  actualizar_disponibilidad(idasist,<?=$idetapa?>);	
				}
			});
		    }


			break;
		}
	
		
		
	}
	return;
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
