<table>
<tr>
	<td><?=_('MONEDA').' *'?></td>
	<td><? $con->cmbselect_db('IDMONEDA','SELECT cpm.IDMONEDA,cm.DESCRIPCION FROM 	catalogo_parametro_moneda cpm, catalogo_moneda cm WHERE cpm.IDMONEDA = cm.IDMONEDA',($prov->moneda->idmoneda=='')?'0':$prov->moneda->idmoneda,'id="idmoneda" class="classtexto" onchange="cambio_moneda();" onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"','','Seleccione' ); ?></td>
</tr>
<tr>
	<td><?=_('RAMO').' *'?></td>
	<td><? $con->cmbselect_db('BRSCH','select BRSCH,RAMO from catalogo_sap_brsch',($prov->brsch==''?'Blank':$prov->brsch),' id=brsch class="classtexto" onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"','','Seleccione' ); ?>  </td>
</tr>
<tr>
	<td><?=_('GRP. TESORERIA').' *'?></td>
	<td><? $con->cmbselect_db('FDGRV','select FDGRV, GRPTESORERIA from catalogo_sap_fdgrv;',($prov->fdgrv==''?'Blank':$prov->fdgrv),'  id=fdgrv class="classtexto" onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"','','Seleccione' ); ?>  </td>
</tr>
<tr>
	<td><?=_('COND. PAGO').' *'?></td>
	<td><? $con->cmbselect_db('ZTERM','select ZTERM, CONDPAGO from catalogo_sap_zterm;',($prov->zterm==''?'Blank':$prov->zterm),' id=zterm class="classtexto" onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"','','Seleccione' ); ?> </td>
</tr>
<tr>
	<td><?=_('IND. IMPUESTO').' *'?></td>
	<td><? $con->cmbselect_db('MWSKZ','select MWSKZ, INDICADORIMPUESTO from catalogo_sap_mwskz;',($prov->mwskz==''?'Blank':$prov->mwskz),' id=mwskz class="classtexto" onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"','','Seleccione' ); ?>  </td>
</tr>
<tr>
	<td><?=_('TIPO UBICACION').' *'?></td>
	<td><? $con->cmbselect_db('PARVO','select PARVO, TIPOUBICACION from catalogo_sap_parvo;',($prov->parvo==''?'Blank':$prov->parvo),' id=parvo class="classtexto" onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"','','Seleccione' ); ?>  </td>
</tr>
<tr>
	<td><?=_('TIPO EMPRESA').' *'?></td>
	<td><? $con->cmbselect_db('PAVIP','select PAVIP, TIPOEMPRESA from catalogo_sap_pavip;',($prov->pavip==''?'Blank':$prov->pavip),'id=pavip class="classtexto" onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"','','Seleccione' ); ?>  </td>
</tr>	
<tr>
	<td><?=_('SOCIEDADES').' *'?></td>
	<td>
	<?
	foreach ($lista_sociedades as $indice=>$valor)
	{
	    $checked = (isset($prov->sociedades[$indice]))?'checked':'';
	    echo "<input type='checkbox' name='IDSOCIEDAD[]' a='sociedad' value='$indice' $checked>$valor";
	    echo "<br>";
	}
	?>
	</td>
</tr>	
</table>

<script type="text/javascript">

function cambio_moneda(){
    alert("<?=_('Al cambiar la moneda, debe modificar los importes de los costos negociados ')?>");
    return;
}


</script>