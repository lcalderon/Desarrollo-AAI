<table >
<tr >
	<td><?=_('RAZON SOCIAL').'*'?></td>
	<td><input name="NOMBREFISCAL" id='nombrefiscal' type="text" value="<?=$prov->nombrefiscal; ?>" size="60"      ></td>
</tr>
<tr >
	<td><?=_('NOM. COMERCIAL').'*'?></td>
	<td><input name="NOMBRECOMERCIAL" id='nombrecomercial' type="text" value="<?=$prov->nombrecomercial; ?>" size="60"     ></td>
</tr>

<tr >
	<td><?=_('TIPO DOCUMENTO').'*'?></td>
	<td><? $con->cmbselect_db('IDTIPODOCUMENTO',"select IDTIPODOCUMENTO, DESCRIPCION from catalogo_tipodocumento where activo=1 ",$prov->idtipodocumento,' id="idtipodocumento"','','Seleccione' ); ?>  </td>
	</tr>
<tr >
	<td><?=_('NUM. DOCUMENTO').'*'?></td>
	<td><input type="text" name="IDDOCUMENTO" id='iddocumento' value='<?=$prov->iddocumento; ?>'  maxlength="20"  /></td>
</tr>
<tr>
	<td ><?=_('EMAILS')?></td>
	<td>
		1.-<input type="text" name="EMAIL1" id='email1' value='<?=$prov->email1; ?>'  maxlength="40" size="40"   onblur="isEmail(this)"/>
		<img src='/imagenes/32x32/Down.png' id='email_add_img' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onclick="abrir('email_add')"/>		
		<div id='email_add' style='display:none;'>
		2.-<input type="text" name="EMAIL2" id='email2' value='<?=$prov->email2; ?>'  maxlength="40" size="40"  /><br>
		3.-<input type="text" name="EMAIL3" id='email3'  value='<?=$prov->email3; ?>'  maxlength="40" size="40"  />
	</div>
	</td>
</tr>

<tr>
	<td ><?=_('TELEFONOS')?></td>
	<td>
	<table >
		<thead>
			<th></th>
			<th><?=_('DDN')?></th>
			<th><?=_('TELEFONO')?></th>
			<th><?=_('EXT')?></th>
			<th><?=_('TIPO')?></th>
			<th><?=_('COMPANIA')?></th>
			<th></th>
			<th><img src='/imagenes/32x32/Down.png' id='telf_add_img' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' onclick="abrir('telf_add')"/></td>
		</thead>
	<tbody>
	<tr>
		<td><img src='/imagenes/iconos/search.gif' alt="15" width="15" align='absbottom' border='0' style='cursor: pointer;' onclick="ver_ddn('codigoarea[0]');"></td>
		<td><input type='text' name='CODIGOAREA[0]' id='codigoarea[0]' value="<?=($prov->telefonos[0][CODIGOAREA]==''?$con->lee_parametro('PREFIJO_DDN'):$prov->telefonos[0][CODIGOAREA])?>" size='3' ></td>
		<td><input type='text' name='NUMEROTELEFONO[0]'  value ='<?=$prov->telefonos[0][NUMEROTELEFONO]?>' size="12" id='numerotelefono[0]'    onKeyPress="return validarnumtelefono(event)"></td>
		<td><input type='text' name='EXTENSION[0]' value ='<?=$prov->telefonos[0][EXTENSION] ?>' size="4"    onKeyPress="return validarnumero(event)"></td>
		<td><? $con->cmbselect_ar('IDTIPOTELEFONO[0]',$lista_t_telf,($prov->telefonos[0][IDTIPOTELEFONO]==''?'Blank':$prov->telefonos[0][IDTIPOTELEFONO]),'id="cvetipotelefono" ','','Seleccione'); ?> </td>
		<td><? $con->cmbselect_ar('IDTSP[0]',$lista_tsp,($prov->telefonos[0][IDTSP]==''?'Blank':$prov->telefonos[0][IDTSP]),'','','Seleccione') ?></td>
		<textarea name="TELF_COMENTARIO[0]" id='telf_0' style="display:none"><?=$prov->telefonos[0][TELF_COMENTARIO]?></textarea>
		<td><img src='/imagenes/32x32/Paste.png' id='' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' title="<?=_('COMENTARIO')?>" onclick="comentario('telf_0');"/></td>
		<td><img src="/imagenes/iconos/telefono.jpg" width="15px" height="16px"  align='absbottom' border='0' style='cursor: pointer;' title="<?=_('LLAMAR')?>" onClick="llamada($F('codigoarea[0]')+$F('numerotelefono[0]'))" ></img></td>
	</tr>
	</tbody>
	</table>
	
	<div id='telf_add' style='display:none;'>
	<table>
	 <tbody>
		<? for($i=1;$i<=$con->lee_parametro('NUMERO_TELF_PROVEEDOR');$i++) { ?>
		<tr>
		<td><img src='/imagenes/iconos/search.gif' alt="15" width="15" align='absbottom' border='0' style='cursor: pointer;' onclick="ver_ddn('codigoarea[<?=$i?>]');"></td>
		<td><input type='text' name='CODIGOAREA[<?=$i?>]' id='codigoarea[<?=$i?>]' value="<?=($prov->telefonos[$i][CODIGOAREA]==''?$con->lee_parametro('PREFIJO_DDN'):$prov->telefonos[$i][CODIGOAREA])?>" size='3' ></td>
		<td><input type='text' name='NUMEROTELEFONO[<?=$i?>]'  value ='<?=$prov->telefonos[$i][NUMEROTELEFONO]?>' size="12"  id='numerotelefono[<?=$i?>]' onKeyPress="return validarnumtelefono(event)"></td>
		<td><input type='text' name='EXTENSION[<?=$i?>]' value ='<?=$prov->telefonos[$i][EXTENSION] ?>' size="4" onKeyPress="return validarnum(event)"></td>
		<td><? $con->cmbselect_ar("IDTIPOTELEFONO[$i]",$lista_t_telf,($prov->telefonos[$i][IDTIPOTELEFONO]==''?'Blank':$prov->telefonos[$i][IDTIPOTELEFONO]),'id="cvetipotelefono" ','','Seleccione'); ?> </td>
		<td><? $con->cmbselect_ar("IDTSP[$i]",$lista_tsp,($prov->telefonos[$i][IDTSP]==''?'Blank':$prov->telefonos[$i][IDTSP]),'','','Seleccione') ?></td>
		<textarea name="TELF_COMENTARIO[<?=$i?>]" id='telf_<?=$i?>' style="display:none"><?=$prov->telefonos[$i][TELF_COMENTARIO]?></textarea>
		<td><img src='/imagenes/32x32/Paste.png' id='' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' title="<?=_('COMENTARIO')?>" onclick="comentario('telf_<?=$i?>');"/></td>
		<td><img  width="15px" height="16px" src="/imagenes/iconos/telefono.jpg" align='absbottom' border='0' style='cursor: pointer;'  onClick="llamada($F('codigoarea[<?=$i?>]') + $F('numerotelefono[<?=$i?>]'))"></img></td>
	
		</td>
		</tr>
		<?}?>
	</tbody>
	</table>
	</div>
	</td>
</tr>

<tr class="modo1">
<td><?=_('PAIS')?>
</td>
<td>
<? $con->cmbselect_ar('CVEPAIS',$lista_pais,($prov->cvepais==''?$con->lee_parametro('IDPAIS'):$prov->cvepais),'id=cvepais ','','Seleccione')?> 
</td>
</tr>
<?  include_once('../../includes/vista_entidades.php');?>
<tr >
<td><?=_('TIPO VIA')?></td>
<td>
<? $con->cmbselect_db('CVETIPOVIA','select IDTIPOVIA, DESCRIPCION from catalogo_tipo_via',($prov->cvetipovia==''?'Blank':$prov->cvetipovia),'id="cvetipovia" ','','TODOS')?>
</td>
</tr>
<tr class="modo1">
<td><?=_('DIRECCION *')?></td>
<td><input type="text" name='DIRECCION' value="<?=$prov->direccion; ?>" id='direccion' size='65' autocomplete="off"    ></td>
<div id='sugeridos' class="autocomplete" style="display:none" ></div>
</tr>

<tr class="modo1">
	<td><?=_('Nro')?></td>
	<td><input type="text" name="NUMERO" value='<?=$prov->numero ?>' size="12" maxlength="12" id='numero' autocomplete="off"   onkeypress="return validarnumero(event)" >
	<?=_('Cod. Postal ')?>
	<input type="text" name='CODPOSTAL' value="<?=$prov->codpostal?>" size='10' id='cod_postal' autocomplete="off"   >
	<input type ='button' value="<?=_('AJUSTES EN MAPA') ?>" id='ver_mapa' class='normal' ></input>
	<img src='/imagenes/32x32/Down.png' id='referencia_add_img' alt='16px' height='16px' align='absbottom' border='0' style='cursor: pointer;' title="<?=_('REFERENCIAS')?>" onclick="abrir('referencia_add')"/>
	<div id='referencia_add' style="display:none">
	<?=_('Entre:')?><input type=text' name='REFERENCIA1' value="<?=$prov->referencia1?>" size='50' id='referencia1' autocomplete="off"  ><br>
	<?=_('Entre:')?><input type=text' name='REFERENCIA2' value="<?=$prov->referencia2?>" size='50' id='referencia2' autocomplete="off"  >
	</div>
	</td>
</tr>

<tr >
	<td><?=_('CONDICION')?></td>
	<td>
	<input type="radio" name="INTERNO" value="1"  <?= ($prov->interno)?'Checked':'';?> /> <?=_('Interno')?>
	<input type="radio" name="interno" value="0" <?= ($prov->interno)?'':'Checked';?>  /><?=_('Externo')?>
	<input type="button" name="" value="<?=_('OBSERVACIONES')?>" class="normal" onclick="observaciones_prov();" />
	</td>
	<textarea name="OBSERVACIONES" id="observaciones"  cols="50" rows="5" style='display:none'><?=$prov->observaciones?></textarea>
	
</tr>
<tr >
	<td><?=_('ACTIVADO')?></td>
	<td>
	<?	if (($prov->activo) OR (!isset($prov->activo))) $checked='checked';
	else $checked='';
	 ?>
	<input type="checkbox" name="ACTIVO" id="checkbox"  <?=$checked ?>  value="1" />
	 &nbsp; &nbsp;
	<?=_('INICIO DE ACTIVIDADES EN AA')?>
	<input type='hidden' name='DIA' id="dia"  value="<?=$prov->dia?>" size='2' value='01'    onKeyPress="return validarnumero(event)" >
		<?
		$con->cmbselect_ar('MES',$mes_del_anio,$prov->mes,'   ','','Mes');
		?>
	-
	<?
	$anio_inicial = getdate();
	$con->cmbselect_anio('ANIO',$anio_inicial[year],6,$prov->anio,"id='anio'",'');
		?>
<!--	<input type='text' name='ANIO' id="anio" value="<?=$prov->anio?>"size='4' value=''    onKeyPress="return validarnumero(event)" >-->
	</td>	
	
	
	
</tr>
<tr>
	<td ><?=_('CALC. DE RANKING')?></td>
	<td >
	
	<input type='radio' name='ARREVALRANKING' id='id_arrevalranking_cde' value='CDE'    <?= ($prov->arrevalranking=='CDE')?'checked':'' ;?> >CDE
	<input type='text' name='CDE' id='cde' size='3' value='<?=$prov->cde?>'   DISABLED >%  &nbsp; &nbsp;
	<input type='radio' name='ARREVALRANKING' id='id_arrevalranking_skill' value='SKILL' <?= ($prov->arrevalranking=='SKILL')?'checked':'' ;?>>SKILL
	<input type='text' name='SKILL' id='skill' size='3' value='<?=$prov->skill?>'    onKeyPress="return validarnumero(event)" >%
	</td>
</tr>
<tr>
	<td ><?=_('INFRAESTRUCTURA')?></td>
	<td >
	<? $con->cmbselect_ar('EVALINFRAESTRUCTURA',$lista_ponderacion,($prov->evalinfraestructura=='')?1:$prov->evalinfraestructura,'  onBlur="colorOffFocus(this);"','',''); ?>
	&nbsp; &nbsp;
	<?=_('FIDELIDAD')?> 
	<? $con->cmbselect_ar('EVALFIDELIDAD',$lista_ponderacion,($prov->evalfidelidad=='')?1:$prov->evalfidelidad,'  onBlur="colorOffFocus(this);"','',''); ?>	
	&nbsp; &nbsp;
	<?=_('SATISFACCION')?>
	<input type="text" name='EVALSATISFACCION' value="<?=$prov->evalsatisfaccion ?>" size='3'   disabled>
	
</tr>




<tr>
</tr>

<input type='hidden'  name='LATITUD' id='latitud'  value = '<?=$prov->latitud ?>' >
<input type='hidden'  name='LONGITUD' id='longitud' value = '<?=$prov->longitud ?>' >
<input type='hidden'  name='IDPROVEEDOR' id='idproveedor' value = "<?=$idproveedor?>">
<input type='hidden'  name='IDUSUARIOMOD' id='idusuariomod' value = "<?=$idusuariomod ?>" >


</table>

<script language="javascript">

function abrir(campo)
{
	if ( $(campo).style.display=='none' )
	{
		$(campo).show();
		$(campo+'_img').src='/imagenes/32x32/Up.png';
	}
	else
	{
		$(campo).hide();
		$(campo+'_img').src='/imagenes/32x32/Down.png';
	}
	return;
}


function llamada(numero){

	//alert(numero);
	new Ajax.Request('../../../controlador/ajax/ajax_llamada.php',
	{	method : 'get',
	parameters: {
		prefijo: "",
		num: numero,
		ext: '<?=$idextension?>'
	}
	}
	);
}
/*
function cambio_boton(){
if ($F('id__arrevalrankinge'))
{
$('skill').value='';
$('skill').disabled = true;
}
else {
$('skill').disabled = false;
}

return;
}
*/
function comentario(campo){
	Dialog.alert("<?=_('COMENTARIO TELEFONO')?><br><textarea cols='30' id='comentario_telefono'>"+$F(campo)+"</textarea>",
	{
		top: 200,
		width:300,
		showEffect: Element.show,
		hideEffect: Element.hide,
		className: "alphacube",
		okLabel: "SALIR",
		buttonClass: "normal",
		onOk: function(dlg)
		{
			$(campo).value = $F('comentario_telefono');
			return true;
		}
	});

	return;
}


function observaciones_prov(){

	Dialog.alert("<?=_('OBSERVACIONES DEL PROVEEDOR')?><br><textarea cols='50' name='TMPOBSERVACIONES' id='tmpobservaciones'>"+$F('observaciones')+"</textarea>",
	{
		top: 200,
		width:450,
		showEffect: Element.show,
		hideEffect: Element.hide,
		className: "alphacube",
		okLabel: "SALIR",
		buttonClass: "normal",
		onOk: function(dlg)
		{
			$('observaciones').value = $F('tmpobservaciones');
			return true;
		}
	});
	return;
}

</script>