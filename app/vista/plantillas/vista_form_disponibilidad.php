<? 
if($modulo=='CALIDAD'){}
else
{?>
<table>
		<tr>
		<td colspan="2"><?=('DISPONIBILIDAD DE AFILIADO')?><br><input type='hidden' name='hid_iddispo' id='hid_iddispo'>
		<input type='hidden' name='hid_idasistencia' id='hid_idasistencia' value='<?=$idasistencia?>'>
		<?=_('DESDE')?><input type="text" name='date' id='date' value="<?=date('Y-m-d')?>" readonly size='15'>
 		<input type="button" id="calendarButton" value="..." onmouseover="setupCalendars();" class='normal'>
		<select name='dcbhora1' id='dcbhora1' class='classtexto' ><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if(date('H')==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
		<select name='dcbminuto1' id='dcbminuto1' class='classtexto' ><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if(date('i')==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
		<?=_('HASTA')?>
			<input type="text" name='date4' id='date4' value="<?=date('Y-m-d')?>" readonly size='15'>
			<input type="button" id="calendarButton4" value="..." onmouseover="setupCalendars();" class='normal'>
			<select name='dcbhora2' id='dcbhora2' class='classtexto' ><? for($t=0;$t<24;$t++){ if($t<=9){ $t='0'.$t; } ?> <option <? if(date('H')==$t){ ?> selected <?  } ?>><?=$t?></option><? } ?></select>
			<select name='dcbminuto2' id='dcbminuto2' class='classtexto' ><? for($t=0;$t<60;$t=$t+10){ if($t<=9){ $t='0'.$t; } ?> <option <? if(date('i')==$t){ ?> selected <? } ?> ><?=$t?></option><? } ?></select>
			<input type='button' name="btnagregardispo" id="btnagregardispo" value="<?=_('Agregar')?>" <? if($idasistencia==''){ echo 'disabled'; } ?> class='normal' onclick="grabar_dispo()">
		</td>
		</tr>
<?  } ?>
		<tr>
		<td colspan="2">
			<div id="zona_disponibilidad">
			<? if ($idasistencia!='')  include_once('vista_disponibilidad_afiliado.php');?></div>
		</td>
		</tr>
</table>
