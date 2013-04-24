<form name='form_costeo' id='form_costeo'>
<input type="hidden" name="ASIGPROV" id='asigprov' value="<?=$asis->proveedores[0][idasigprov]?>">
<input type="hidden" name="IDASISTENCIA" id="idasistencia" value="<?=$idasistencia?>">
<input type="hidden" name="IDETAPA" id="idetapa" value="<?=$idetapa ?>">
<input type="hidden" name="IDUSUARIOMOD" id="idusuariomod" value="<?=$idusuario?>">


<div>
	<table border="1">
	<thead>
		<tr>
			<td colspan="16" align="left"><?=_('COSTO DE PROVEEDORES')?></td>
		</tr>
		<tr>
			<th colspan="2"></th>
			<th colspan="4" class='estimado'><?=_('COSTO PRE-ACORDADO')?></th>
			<th colspan="4" class='real''><?=_('COSTO CABINA')?></th>
			<? if ($modo=='AUTORIZA'):?>
				<th colspan="4" class='autorizado''><?=_('AUTORIZADO')?></th>
				<th></th>
				<th></th>
				<th></th>
			<?endif;?>	
		</tr>
	</thead>
	<tbody>
		<tr>
			<th width="10%"><?=_('PROVEEDOR')?></th>
			<th width="1%" title="<?=_('MONEDA')?>"><?=_('M')?></th>
			<th class='estimado'><?=_('AA')?></th>
			<th class='estimado'><?=_('NA')?></th>
			<th class='estimado'><?=_('CC')?></th>
			<th class='estimado'><?=_('TOTAL')?></th>
			
			<th class='real''><?=_('AA')?></th>
			<th class='real''><?=_('NA')?></th>
			<th class='real''><?=_('CC')?></th>
			<th class='real''><?=_('TOTAL')?></th>
			<? if ($modo=='AUTORIZA'):?>
			<th class='autorizado''><?=_('AA')?></th>
			<th class='autorizado''><?=_('NA')?></th>
			<th class='autorizado''><?=_('CC')?></th>
			<th class='autorizado''><?=_('TOTAL')?></th>
			<th width="13%"><?=('TELEFONOS')?></th>
			<th title="<?=_('AUTORIZACION')?>"><?=_('A')?></th>
			<th><?=_('COSTO')?></th>
			<?endif;?>	
	
			
		</tr>
		<? $linea=0;
		foreach ($asis->proveedores as $prov ){
			$proveedor = new proveedor();
			$proveedor->carga_datos($prov[idproveedor]);
			foreach ($proveedor->telefonos as $telefonos)  $telef_prov[$telefonos[NUMEROTELEFONO]]=$telefonos[CODIGOAREA].$telefonos[NUMEROTELEFONO];

			$moneda = new moneda();
			$moneda->carga_datos($prov[idmoneda]);
		?>
		<tr>
			<td><?=$proveedor->nombrefiscal?></td>
			<td align="center"><?=$moneda->simbolo?></td>
			<td class='estimado'><input type="text" name='AA_TOTALESTIMADO' id='aa_totalestimado' size='8' dir='rtl' value="<?=$prov[aa_totalestimado]?>" readonly></td>
			<td class='estimado'><input type="text" name='NA_TOTALESTIMADO' id='na_totalestimado' size='8' dir='rtl' value="<?=$prov[na_totalestimado]?>" readonly></td>						
			<td class='estimado'><input type="text" name='CC_TOTALESTIMADO' id='cc_totalestimado' size='8' dir='rtl' value="<?=$prov[cc_totalestimado]?>" readonly></td>
			<td class='estimado'><input type="text" name='IMPORTEESTIMADO' id='importeestimado' size='8' dir='rtl' value="<?=$prov[importeestimado]?>" readonly></td>
					
			<td class='real''><input type="text" name='AA_TOTALREAL' id='aa_totalreal' size='8' dir='rtl' value="<?=$prov[aa_totalreal]?>" readonly></td>
			<td class='real''><input type="text" name='NA_TOTALREAL' id='na_totalreal' size='8' dir='rtl' value="<?=$prov[na_totalreal]?>" readonly></td>						
			<td class='real''><input type="text" name='CC_TOTALREAL' id='cc_totalreal' size='8' dir='rtl' value="<?=$prov[cc_totalreal]?>" readonly></td>
			<td class='real''><input type="text" name='IMPORTEREAL' id='importereal' size='8' dir='rtl' value="<?=$prov[importereal]?>" readonly></td>
		 <? if ($modo=='AUTORIZA'):?>			
			<td class='autorizado''><input type="text" name='AA_TOTALAUTORIZADO' id='aa_totalautorizado' size='8' dir='rtl' value="<?=$prov[aa_totalautorizado]?>" readonly></td>
			<td class='autorizado''><input type="text" name='NA_TOTALAUTORIZADO' id='na_totalautorizado' size='8' dir='rtl' value="<?=$prov[na_totalautorizado]?>" readonly></td>						
			<td class='autorizado''><input type="text" name='CC_TOTALAUTORIZADO' id='cc_totalautorizado' size='8' dir='rtl' value="<?=$prov[cc_totalautorizado]?>" readonly></td>
			<td class='autorizado''><input type="text" name='IMPORTEAAUTORIZADO' id='importeautorizado' size='8' dir='rtl' value="<?=$prov[importeautorizado]?>" readonly></td>
			<td><? $asis->cmbselect_ar('TELEFONO_PROV',$telef_prov,'','',"id=telefono_prov_$linea",'')?>
				<img src='/imagenes/iconos/telefono.jpg' title='Llamar' align='absbottom' border='0' style='cursor: pointer;' onclick=llamada($F('telefono_prov_'+<?=$linea?>))> </td>
				
				<?
			 	if ($asis->proveedores[$linea][statusautorizaciondesvio] && ($asis->proveedores[$linea][idusuarioautorizaciondesvio]!='')) $semaforo='semaforoverde.jpg';
			 	  elseif ($asis->proveedores[$linea][statusautorizaciondesvio] && $asis->proveedores[$linea][statusnegociacionposterior]) $semaforo='semaforoamarillo.jpg';
			 	  else
			 	  $semaforo='semafororojo.jpg';
				?>
				
			<td><img src='/imagenes/iconos/<?=$semaforo?>' align='absbottom' border='0' alt='15px' width='15px'></td>	
			<td><img src='/imagenes/iconos/calculadora.gif' title='Costos' align='absbottom' border='0' style='cursor: pointer;' onclick=carga_calculadora("<?=$asis->proveedores[$linea][idasigprov]?>")> </td>
		 <?endif;?>		
			
		</tr>
			<?
			$linea++;
		}
		?>
		</tbody>	
		
		<tfoot>
			<? 
			$result = $asis->total_asistencia($idasistencia);
			while ($reg = $result->fetch_object()){
				$moneda = new moneda();
				$moneda->carga_datos($reg->IDMONEDA);
				?>
				<tr>
				<td><?=_('TOTAL')?></td>
				<td><?=$moneda->simbolo?></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_AA_TOTALESTIMADO?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_NA_TOTALESTIMADO?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_CC_TOTALESTIMADO?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_IMPORTEESTIMADO?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_AA_TOTALREAL?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_NA_TOTALREAL?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_CC_TOTALREAL?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_IMPORTEREAL?>"></td>
				 <? if ($modo=='AUTORIZA'):?>			
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_AA_TOTALAUTORIZADO?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_NA_TOTALAUTORIZADO?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_CC_TOTALAUTORIZADO?>"></td>
				<td><input type="text"  size="8" dir='rtl' value="<?=$reg->SUMA_IMPORTEAUTORIZADO?>"></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?endif;?>		
				
			<?}?>
		
		
		</tfoot>	
	</table>
</div>
<br>
<div style="float:left;">
<table>
<tr>
	<td>
	<?	if (isset($asis->costos)) {
		echo _('COSTO DE LA ASISTENCIA');
	?>
	
		<table border="1">
			<tr>
			<th><?=_('MONEDA')?></th>
			<th class='estimado'><?=_('PRE-ACORDADO')?></th>
			<th class="real">	 <?=_('COSTO FIJO')?>  </th>
			<th class='real'>    <?=_('COSTO VARIABLE')?></th>
			<th class='real'>	 <?=_('TOTAL')?></th>
			<? if ($modo=='AUTORIZA'):?>	
				<th class='autorizado''><?=_('AUTORIZADO')?></th>
			<?endif;?>			
			</tr>
			<? foreach($asis->costos as $indice=>$costo) {
				$moneda = new moneda();
				$moneda->carga_datos($indice);
				?>
			<tr>
			<td><font style="font-size: 20px;font-family: Arial, serif;font-weight: normal;"><?=$moneda->simbolo?></font></td>
			<td class='estimado'><input type="text" size="8" dir='rtl' value="<?=$costo[IMPORTEESTIMADO]?>" style="font-size: 20px;font-family: Arial, serif;font-weight: normal;"></td>
			<td class='estimado'><input type='text' size='8' dir="rtl" value="<?=$costo[COSTOFIJO]?>" style="font-size: 20px;font-family: Arial, serif;font-weight: normal;"></td>
			<td class='estimado'><input type='text' size='8' dir="rtl" value="<?=$costo[IMPORTEREAL]-$costo[COSTOFIJO]?>" style="font-size: 20px;font-family: Arial, serif;font-weight: normal;"></td>
			<td class='real''><input type="text" size="8" dir='rtl' value="<?=$costo[IMPORTEREAL]?>" style="font-size: 20px;font-family: Arial, serif;font-weight: normal;"></td>
			 <? if ($modo=='AUTORIZA'):?>			
				<td class='autorizado''><input type="text" size="8" dir='rtl' value="<?=$costo[IMPORTEAUTORIZADO]?>" style="font-size: 20px;font-family: Arial, serif;font-weight: normal;"></td>
						
			 <?endif;?>		
			</tr>
			<?}?>
		</table>		
		<?}?>	
	</td>
</tr>	
</table>	
</div>	

</form>