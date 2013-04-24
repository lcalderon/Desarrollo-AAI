<fieldset >
 <font size="2px"><b><?=_('TIEMPO  COMPROMETIDO POR EL PROVEEDOR')?></b></font>
	<div  align="left">
		<table width="100%" id='tableeme'>
			<tr>
				<td width='20%' style="display:none"><?=_('HORA MINIMA')?>&nbsp;&nbsp;
					<input type='hidden' id ='hid_fecha' value='<?=$fechacompleta?>'>
					<select name='cbminutomin' id="cbminutomin" class='classtexto' <?=$disabledgral?> onchange="Sugerir_Eme()"> 
					<? for($t=10;$t<=120;$t=$t+10):?>
						<option><?=$t?></option>
					<?endfor; ?>
					</select>&nbsp;<?=_('MINUTOS')?>
				</td>
				<td width='20%'><?=_('HORA MAXIMA')?>&nbsp;&nbsp;
					<select name='cbminutomax' id="cbminutomax" class='classtexto' <?=$disabledgral?>>
					<option></option>
					 <? for($t=10;$t<=120;$t=$t+10):?>
					 		<option ><?=$t?></option>
					 <?endfor; ?>
					 	</select>&nbsp;<?=_('MINUTOS')?>
				</td>
				<input type=hidden name='txtservicio' value='<?=$serviciogral?>'>
				
			</tr>
				
		</table>
		<table id='tableeme_re' style="display:none">
			<tr>
				<td><?=_('NUEVA HORA MAXIMA')?>
					&nbsp;&nbsp;
					<select name='cbminutomax_re' id="cbminutomax_re" class='classtexto' <?=$disabledgral?>> <? for($t=10;$t<=60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?><option <? if($t==date('H')){ echo 'selected'; }?>><?=$t?></option><? } ?></select>&nbsp;<?=_('MINUTOS')?></td>
				<td ><input type='button' id='grabartiempoeme' value='<?=_('guardar reprogramacion de  tiempos')?>' class='guardar' style="display:none" onclick="grabar('RTEME')"></td>
			</tr>
		</table>
    </div>
</fieldset>