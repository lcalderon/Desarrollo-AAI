<?php


$con = new DB_mysqli();
$modulo = 'PROV';
$con->select_db($con->catalogo);
$lista_justificacion = $con->uparray('SELECT IDJUSTIFICACION,MOTIVO FROM '.$db.'.catalogo_justificacion WHERE ACTIVO=1  AND MODULO = "'.$modulo.'" ORDER BY MOTIVO ');
?>

	<table>
	<tr>
	<td valign="top">
		
			<table  align="left" border="0" cellpadding="1" cellspacing="1" width="99%" class="catalogos">
				<tr bgcolor="#333333">
					<th style="text-align:left" colspan="2"></th>
				    <input type="hidden" name="hid_idasistencia" value="<?=$idasistencia?>">
				</tr>
			
				<tr class='modo1'>
					<td><?=_('MOTIVO').' '._('JUSTIFICACION')?>  </td>
					<td><?$con->cmbselect_ar('JUSTIFICACION',$lista_justificacion, 'Blank',"class='classtexto'","","");?>
					 </td>
				</tr>
				<tr class='modo1'>
					<td><?=_('OBSERVACION')?></td>
					<td>&nbsp;</td>
				</tr>	
				<tr class='modo1'>
					<td colspan="2"><textarea  name="OBSERVACION" id="OBSERVACION" rows="3" cols="40" class='classtexto' style="text-transform:uppercase;"></textarea></td>
					
				</tr>
				
			<tr>
			<td colspan="2"></td>
		</tr>		
			<tr class='modo1'>	
				
					<td align="center" colspan="2"> 
					&nbsp;	
				</td>
				</tr>
						

			</table>            
		</td>
		</tr>
	
		
	</table>
