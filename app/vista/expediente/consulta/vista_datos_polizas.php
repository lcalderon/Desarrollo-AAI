
<input type='hidden' name='NUMCERT' value="<?=$poli->NUMCERT?>" size='12' dir='rtl'/>
<input type='hidden' name='' value="<?=$poli->TIPOIDDOCASEG?>" size=''/>	

<table width="100%">
	<thead>
		<tr>
			<th colspan="2"><?=_('PRODUCTO')?></th>
			<th colspan="2"><?=_('VIGENCIA')?></th>
			<th colspan="2"><?=_('ASEGURADO')?></th>
			<th colspan="2"><?=_('ARTICULO EN G.E')?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?=_('CODIGO')?></td>
			<td><b><input type='text' name='CODPROD' id='codprod' value="<?=$poli->CODPROD?>" size='12' dir='rtl' readonly/></td>
		
			<td><?=_('INICIO')?></td>
			<td><b><input type='text' name='F_INIC' id='f_inic' value="<?=substr($poli->INIVIG,0,4)."-".substr($poli->INIVIG,4,2)."-".substr($poli->INIVIG,6,2);?>" size='' dir="rtl" readonly/></b></td>

			<td><?=_('NOMBRE')?></td>
			<td><b><input type='text' name='NOMBRE' id="nombre" value="<?=$poli->APEPERASEG.' '.$poli->APEMATPERASEG.' '.$poli->NOMPERASEG?>" size='30' maxlength="60" readonly/></b></td>

			<td><?=_('ARTICULO')?></td>
			<td><b><input type='text' name='ARTICULO' id='articulo' value="<?=$poli->DATOSPARTICULARES[0]->VALOR?>" size='35' maxlength="60" readonly/></b></td>
		</tr>

		<tr>
			<td><?=_('DESCRIPCION ')?></td>
			<td><b><input type='text' name='DESCRIPCION' id='descripcion'  value="<?=$poli->DESCPROD?>" size='30' readonly/></b></td>
			
			<td><?=_('FIN')?></td>
			<td><b><input type='text' name='F_FIN' id='f_fin' value="<?=substr($poli->FINVIG,0,4)."-".substr($poli->FINVIG,4,2)."-".substr($poli->FINVIG,6,2);?>" size='' dir='rtl' readonly/></b></td>

			<td><?=_('TIPO DOC.')?></td>
			<td><b><input type='text' name='TIPODOC' id='tipodoc' value="<?=$poli->DESCTIPODOCIDASEG?>" size='' readonly/></b></td>

			<td><?=_('MARCA')?></td>
			<td><b><input type='text' name='MARCA' id='marca' value="<?=$poli->DATOSPARTICULARES[4]->DESCRIPCION?>" size='35' maxlength="60" readonly/></b></td>

		</tr>

		<tr>
			<td><?=_('POLIZA')?></td>
			<td><b><input type='text' name='POLIZA' id='poliza' value="<?=$poli->NUMPOL?>" size='12' dir='rtl' readonly/></b></td>

			<td><?=_('MONTO')?></td>
			<td><b><input type='text' name='MONTO' id='monto' value="<?=$poli->SUMAASEG?>" size='' dir="rtl" readonly/></b></td>
		
			<td><?=_('NRO DOC.')?></td>
			<td><b><input type='text' name='NDOC' id='ndoc' value="<?=$poli->NUMIDDOCASEG?>" size='' dir="rtl" readonly/></b></td>
			
			<td><?=_('MODELO')?></td>
			<td><b><input type='text' name='MODELO' id='modelo' value="<?=$poli->DATOSPARTICULARES[5]->VALOR?>" size='' readonly/></b></td>
			
		</tr>
		
		<tr>
			<td><?=_('ESTADO')?></td>
			<td><b><input type='text' name='ESTADO' id='estado' value="<?=$array_estado_certificado[trim($poli->STSCERT)]?>" size='12' readonly/></b></td>

			<td><?=_('MONEDA')?></td>
			<td><b><input type='text' name='MONEDA' id='moneda' value="<?=$poli->CODMONEDA?>" size='' readonly/></b></td>
	
			<td><?=_('CONTRATANTE')?></td>
			<td><b><input type='text' name='CONTRATANTE' id='contratante' value="<?=$poli->NOMBRECONTRATANTE?>" size='30' readonly/></b></td>
			
			<td><?=_('FECHA VENTA ')?></td>
			<td><b><input type='text' name='F_VENTA' id='f_venta' value="<?=substr($poli->DATOSPARTICULARES[9]->VALOR,0,4)."-".substr($poli->DATOSPARTICULARES[9]->VALOR,4,2)."-".substr($poli->DATOSPARTICULARES[9]->VALOR,6,2);?>" size='' dir="rtl" readonly/></b></td>
		</tr>

		<tr>
			<td><?=_('PLAN DE G.E')?></td>
			<td><b><input type='text' name='PLAN_GE' id='plan_ge' value="<?=$poli->DATOSPARTICULARES[11]->DESCRIPCION?>" size='15' readonly/></b></td>
			<td></td>
			<td></td>
						
			<td><?=_('ID ASEGURADO')?></td>
			<td><b><input type='text' name='NUMIDASEG' id='numidaseg' value="<?=$poli->NUMIDASEG?>" size='' dir="rtl" readonly/></b></td>
			
			<td><?=_('TIEMPO GAR')?></td>
			<td><b><input type='text' name='TIEMPO_GE' id='tiempo_ge' value="<?=$poli->DATOSPARTICULARES[10]->VALOR?>" size='5' dir="rtl" readonly/><?=_('MESES')?></b></td>
		</tr>
	</tbody>	
</table>