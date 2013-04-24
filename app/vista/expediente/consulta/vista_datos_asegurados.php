<table width="100%">
		<tr>
			<td><?=_('PATERNO')?><br>
			<input type='text' name='APPATERNO' id='idappaterno' value='<?=$ase->APETER?>' size="30" readonly>
			</td>
			
			<td><?=_('MATERNO')?><br>
			<input type='text' name='APMATERNO' id='idapmaterno' value='<?=$ase->APEMATTER?>' size="30" readonly>
			</td>
			
			<td><?=_('NOMBRES')?><br>
			<input type='text' name='NOMBRES' id='idnombres' value='<?=$ase->NOMTER?>' size="30" readonly>
			</td>
			
			<td><?=_('TIPO DOC.')?><br>
			<input type='text' name='TIPODOC' id='idtipodoc' value='<?=$ase->DESCTIPODOCID?>' size="7" readonly>
			</td>
			
			<td><?=_('NUM. DOC.')?><br>
			<input type='text' name='NUMDOC' id='idnumdoc' value='<?=$ase->NUMIDDOC?>' size="15" readonly>
			</td>
		</tr>
		<tr>
			<td><?=_('DIRECCION')?><br>
			<input type='text' name='DIRECCION' value="<?=$ase->DIRECCION?>" size="40" readonly></td>
			
			<td><?=_('ENTIDAD3')?><br>
			<input type='text' name='CVEENTIDAD3' id='cveentidad3' value="<?=$ase->DISTRITO?>" size="30" readonly></td>
			
			<td><?=_('ENTIDAD2')?><br>
			<input type='text' name='CVEENTIDAD2' id='cveentidad2' value="<?=$ase->PROVINCIA?>" size="30" readonly></td>
			
			<td colspan="2"><?=_('ENTIDAD1')?><br>
			<input type='text' name='CVEENTIDAD1' id='cveentidad1' value="<?=$ase->DEPARTAMENTO?>" size="30" readonly></td>
		
		</tr>
		<tr>
		<? list($TELEFONO1,$TELEFONO2,$TELEFONO3)= explode(',',$ase->TELEFONO);?>
			<td><?=_('TELEFONO1')?><br>
			<input type='text' name="TELEFONO1" id='telefono1' value="<?=$TELEFONO1?>" readonly></td>
			
			<td><?=_('TELEFONO2')?><br>
			<input type='text' name="TELEFONO2" id='telefono2' value="<?=$TELEFONO2?>" readonly></td>
			
			<td><?=_('TELEFONO3')?><br>
			<input type='text' name="TELEFONO3" id='telefono3' value="<?=$TELEFONO3?>" readonly></td>
		</tr>
</table>