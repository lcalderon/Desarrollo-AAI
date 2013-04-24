<form name="frm_buscar" id="frm_buscar">
<table>
	<tr>
		<td width="40%" ><?=_('ENTIDADES')?></td>
		<td width="10%" ><?=_('INTERNO')?></td>
		<td width="30%" ><?=_('NOMBRE COMERCIAL / FISCAL')?></td>
		<td width="10%" valign="middle" rowspan="4"> <input type="button" value="<?=_('BUSCAR')?>" onclick="actualizar_busqueda();"  class="normal"/></td>
	</tr>
	<tr>
		<td rowspan="9"  id='vista_entidades' >
			<table>
				<? include_once('../includes/vista_entidades_ubigeo.php');?>	
			</table>
		</td>
		
		<td><select name='INTERNO' id='interno'>
			<option value='1' ><?=_('INTERNO')?></option>
			<option value='0'><?=_('EXTERNO')?></option>
			<option value='' selected><?=_('INT/EXT')?></option>
			</SELECT>
		</td>
		
		<td><input type='text' name="TEXTOBUSQUEDA" id='textobusqueda' value="" size="40" onkeypress=" return enabledEnter(event)" /></td>		
	</tr>
	
	<tr>
		<td colspan="2"><?=_('SERVICIO')?></td>
	</tr>
	<tr>
		<td colspan="2"><? $con->cmbselect_ar('SERVICIO',$servicio,$idservicio,'id=servicio'," ",'TODOS')?></td>	
	</tr>

</table>
</form>
<script type="text/javascript">
function enabledEnter(e) {
	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla ==13) { return false; }

	patron =/[A-Za-z\s]\d/; //letras y nunmeros
	return patron.test(te);
}


</script>