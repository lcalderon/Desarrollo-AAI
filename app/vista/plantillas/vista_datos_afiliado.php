<fieldset style="background: #ECE9D8">
<legend><?=_('Afiliado')?></legend>
<table width="100%">
	<thead>
		<tr>
			<th width="30%"><?=_('AFILIADO')?></th>
			<th width="20%"><?=_('TELEFONOS')?></th>
			<th width="30%"><?=_('CONTACTO')?></th>
			<th width="20%"><?=_('TELEFONOS')?></th>
		</tr>
	</thead>
	<tbody>
			<td><?=$afiliado?></td>
			<td align='center'><?			
			$exp->leer_telf_persona($exp->personas['TITULAR'][IDPERSONA]);
			foreach ($exp->telefonos as $indice=>$telefonos) $telf[$indice] = $telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];
			$exp->cmbselect_ar('TELEF_TIT',$telf,'',"id='telf_tit'",'','');
			?>
			<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick=llamada($F('telf_tit'))>
			
			</td>
			<td><?=$contactante?></td>
			<td align='center'><?
			$exp->leer_telf_persona($exp->personas['CONTACTO'][IDPERSONA]);
			foreach ($exp->telefonos as $indice=>$telefonos) $telf[$indice] = $telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];
			$exp->cmbselect_ar('TELEF_CON',$telf,'',"id='telf_con'",'','');
			?>
			<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick=llamada($F('telf_con'))>
			</td>			
	<tbody>
</table>
</fieldset>