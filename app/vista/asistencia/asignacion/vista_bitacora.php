
<script language="javascript" type="text/javascript">
function ReasignarProv(){
    if(confirm('Esta seguro que desea Reasignar al Proveedor!!')){
	document.getElementById('proyecto').action="reasignarproveedor.php?idproveedor=<?=$idproveedor?>&idasistencia=<?=$idasistencia?>";	
	document.getElementById('proyecto').submit()
    }
    //parent.top.document.location.href = '../../plantillas/etapa4.php?idasistencia=<?=$idasistencia?>';window.close();
}
function CancelaPosterior(){
  if(confirm('Esta seguro que desea Cancelar la asistencia!!')){
      document.getElementById('proyecto').action="cancelaposterior.php?idproveedor=<?=$idproveedor?>&idasistencia=<?=$idasistencia?>";	
      document.getElementById('proyecto').submit();
	   parent.close();
    }
    //parent.top.document.location.href = '../../plantillas/etapa4.php?idasistencia=<?=$idasistencia?>';window.close();
}







function GrabarSatAfil(){
if(confirm("<?=_('ESTA SEGURO QUE DESEA GUARDAR Y CERRAR LA ASISTENCIA')?>")){
xajax_agregarFila(xajax.getFormValues('proyecto'),<?=$idasistencia?>,document.getElementById('comentario').value,'ADD');
    xajax_fgrabar(<?=$idasistencia?>);
    parent.close();
}
    //parent.top.document.location.href = '../../plantillas/etapa6.php?idasistencia=<?=$idasistencia?>';window.close();
}

function valida(){
if(document.getElementById('comentario').value==''){
  alert("DEBE INGRESAR UN COMENTARIO!!!");
}else{
document.getElementById('tabla5').style.display='block';
xajax_cancelar();
xajax_agregarFilabitacora(xajax.getFormValues('proyecto'),<?=$idasistencia?>,document.getElementById('comentario').value,'ADD');

}
}

function valida2(){
if(document.getElementById('comentario').value==''){
  alert("DEBE INGRESAR UN COMENTARIO!!!");
}else{
document.getElementById('tabla5').style.display='block';
xajax_cancelar();
xajax_agregarFila(xajax.getFormValues('proyecto'),<?=$idasistencia?>,document.getElementById('comentario').value,'ADD');

}
}

function valida3(){
if(document.getElementById('comentario').value==''){
  alert("DEBE INGRESAR UN COMENTARIO!!!");
}else{
document.getElementById('tabla5').style.display='block';
xajax_cancelar();
xajax_agregarFila(xajax.getFormValues('proyecto'),<?=$idasistencia?>,document.getElementById('comentario').value,'ADD');

}
}

function valida4(STATUS){
//alert(STATUS);
/*if(document.getElementById('comentario').value==''){
  alert("DEBE INGRESAR UN COMENTARIO!!!");
}else{*/
document.getElementById('tabla5').style.display='block';
xajax_cancelar();
xajax_agregarFila(xajax.getFormValues('proyecto'),<?=$idasistencia?>,document.getElementById('comentario').value,STATUS,'ADD');
 xajax_fgrabar(<?=$idasistencia?>);
//}
}
function valida5(){
if(document.getElementById('comentario').value==''){
  alert("DEBE INGRESAR UN COMENTARIO!!!");
}else{
document.getElementById('tabla5').style.display='block';
xajax_cancelar();
xajax_agregarFila(xajax.getFormValues('proyecto'),<?=$idasistencia?>,document.getElementById('comentario').value,'ADD');

}
}



</script>
<div id='bitacorasel' class="form-horiz">
<? if($varetapa=='MONAFIL' || $varetapa=='SATAFIL' || $varetapa=='ARRCON'){ 
   
$sql_telefono="SELECT  CODIGOAREA,NUMEROTELEFONO from $temporal.expediente_persona_telefono WHERE IDPERSONA = $idpersona";
$exec_telefono = $con->query($sql_telefono);
  ?>
<table width="70%"  style='background-color:#e2ebef;font-size: xx-small;'>
<tr ><td align='left'><?=_('CONTACTO')?></td><td ><?=$paterno.' '.$materno.' '.$nombre?></td>
    <td align='left'><?=_('TELEFONO')?></td><td ><select name="cbtelefono" id="cbtelefono">
	<? while($rset_telefono = $exec_telefono->fetch_object()){
		
		?>
	<option value="<?=$rset_telefono->NUMEROTELEFONO?>"><?=$rset_telefono->NUMEROTELEFONO?></option>

<?	} ?>
	</select>	<img src='../../../../imagenes/iconos/telefono.jpg' align='absbottom'  border='0' style='cursor:pointer' onclick="llamada(1,document.getElementById('cbtelefono').value);" title='Llamar'/>
	</td>
</tr>
</table>
<? }
?>
<table width="100%">
<tr><td>
<fieldset class="fieldset"><legend class='legend'><? if($varetapa=='MONPROV'){ echo _('MONITOREO DE PROVEEDOR'); }elseif($varetapa=='MONAFIL'){ echo _('MONITOREO DE AFILIADO'); }elseif($varetapa=='ARRCON'){ echo _('ARRIBO CONTACTACION'); }elseif($varetapa=='SATAFIL'){ echo _('SATISFACCION DEL AFILIADO'); } else{ echo _('AGREGAR BITACORA'); }?></legend>
	

<table width='100%'>
</tr>
	<tr><td width='60%'><textarea name='COMENTARIO' id='comentario' cols='50' rows='0' style="text-transform:uppercase;widht:auto"></textarea></td>

<td></td>

    </tr>

</table>
</fieldset>
</td></tr></table>
</div>
<fieldset class="fieldset"> <legend class='legend'> <?=_('BITACORA')?></legend>
<div id='tabla5' style='height:200px;overflow:auto'>


    <div class="clear"></div>
    <div id="form4" >
        <table width="100%" id="tblDetalle" class='listado'><tbody id="tbDetalle" ></tbody></table>
    </div>


</div>
</fieldset>
<table width='100%'><tr>
<? if($varetapa=='MONPROV'){?><td width='40%'  align='right'><input type="button" id="btnbitacora" <?=$disabledgral?> name="btnbitacora"   value="<?=_('GUARDAR PARCIAL')?>" class='guardar' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="valida2()" />&nbsp;&nbsp;
<input type="button" id="btnReasignar" name="btnReasignar"   value="<?=_('REASIGNAR PROVEEDOR')?>" <?=$disabledgral?> class='normal' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="ReasignarProv()" />&nbsp;&nbsp;
<input type="button" id="btnCancelaPost" name="btnCancelaPost"   value="<?=_('CANCELADO POSTERIOR')?>" <?=$disabledgral?> class='normal' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="CancelaPosterior()" />&nbsp;&nbsp;
<input type="button" id="btngrabar" name="btngrabar"   value="<?=_('GUARDAR')?>" class='guardar' <?=$disabledgral?> style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:green;border-width:1px;border-style:solid' onClick="GrabarMonProv()" />&nbsp;&nbsp;</td>
<? }elseif($varetapa=='MONAFIL'){?><td width='40%' align='right'><input type="button" id="btnbitacora" <?=$disabledgral?> name="btnbitacora"   value="<?=_('GUARDAR PARCIAL')?>" class='guardar' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="valida3()" />&nbsp;&nbsp;
<input type="button" id="btnReasignar" name="btnReasignar"   value="<?=_('REASIGNAR PROVEEDOR')?>" <?=$disabledgral?> class='normal' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="ReasignarProv()" />&nbsp;&nbsp;
<input type="button" id="btnCancelaPost" name="btnCancelaPost"   value="<?=_('CANCELADO POSTERIOR')?>"  <?=$disabledgral?> class='normal' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="CancelaPosterior()" />&nbsp;&nbsp;
<input type="button" id="btngrabar" name="btngrabar"   value="<?=_('GUARDAR')?>" class='guardar' <?=$disabledgral?> style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:green;border-width:1px;border-style:solid' onClick="GrabarMonAfil()" /></td>
<? }elseif($varetapa=='ARRCON'){?>
<td width='40%' align='right'><!--<input type="button" id="btnbitacora" name="btnbitacora"   value="<?=_('GUARDAR BITACORA')?>" class='guardar' style='font-weight:bold;font-size:9px;border-color:blue;border-width:1px;border-style:solid' onClick="valida4()" />&nbsp;&nbsp;-->
<input type="button" id="btnArrSal" name="btnArrSal"   value="<?=_('ARRIBO SALIENTE')?>" <?=$disabledgral?> class='normal' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="valida4('ARRSAL')" />&nbsp;&nbsp;
<input type="button" id="btnArrEnt" name="btnArrEnt"   value="<?=_('ARRIBO ENTRANTE')?>" <?=$disabledgral?> class='normal' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="valida4('ARRENT')" />&nbsp;&nbsp;
<input type="button" id="btnConSal" name="btnConSal"   value="<?=_('CONTACTO SALIENTE')?>" <?=$disabledgral?> class='normal' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="valida4('CONSAL')" />&nbsp;&nbsp;
<input type="button" id="btnConEnt" name="btnConEnt"   value="<?=_('CONTACTO ENTRANTE')?>" <?=$disabledgral?> class='normal' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="valida4('CONENT')" />&nbsp;&nbsp;
<!--<input type="button" id="btngrabar" name="btngrabar"   value="<?=_('GUARDAR')?>" <?=$disabledgral?> class='guardar' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:green;border-width:1px;border-style:solid' onClick="GrabarArrCon()" /></td>-->
<? }elseif($varetapa=='SATAFIL'){?><td width='40%' align='right'><input type="button" id="btnbitacora" name="btnbitacora"  <?=$disabledgral?> value="<?=_('GUARDAR PARCIAL')?>" class='guardar' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="valida5()" />&nbsp;&nbsp;
<input type="button" id="btnReclamo" name="btnReclamo"  <?=$disabledgral?> value="<?=_('RECLAMO')?>"  style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid'  />&nbsp;&nbsp;
<input type="button" id="btngrabar" name="btngrabar" <?=$disabledgral?>  value="<?=_('GUARDAR Y CONCLUIR')?>" class='guardar' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:green;border-width:1px;border-style:solid' onClick="GrabarSatAfil()" /></td>
<? }else{ ?>
<td width='40%' align='right'><input type="button" id="btnbitacora" <?=$disabledgral?> name="btnbitacora"   value="<?=_('GUARDAR PARCIAL')?>" class='guardar' style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="valida()" />&nbsp;&nbsp;
<input type="button" id="btnCancela" name="btnCancela" <?=$disabledgral?> value="CANCELA MOMENTO"  style='-moz-border-radius:3px;-webkit-border-radius:3px;height:22px;border-radius:3px;font-weight:bold;font-size:9px;border-color:#003C74;border-width:1px;border-style:solid' onClick="Cancelar()" /></td>
<? } ?></tr>
</table>


