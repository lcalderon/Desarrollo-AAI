<?
include_once('../../includes/arreglos.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../includes/head_prot_win_map.php');

$SERVICIO = 'ORIENTACION TELEFONICA';
?>
<body>
<div>
<div id='datos_generales_plantilla' style="float:left; weight:48%" align="left">
<fieldset>
<legend>DATOS DE LA ASISTENCIA - <?=$SERVICIO?></legend>
<form>
   <input type='hidden' name='IDUBIGEO' id='idubigeo' value=''>
	<table>
		<tr>
			<td><?=_('TIPO CONSULTA')?><font color="red">*</font></td>
			<td><textarea name='TIPOCONSULTA' id='tipoconsulta' cols="40" rows='0' style="text-transform:uppercase;"></textarea></td>
		</tr>
		<tr>
			<td><?=_('RESULTADO')?><font color="red">**</font></td>
			<td><textarea name='RESULTADO' id='resultado' cols="40" rows='0' style="text-transform:uppercase;"></textarea></td>
		
		</tr>
		<tr>
			<td><?=_('OTROS')?></td>
			<td><textarea name='otros' id='otros' cols="40" rows='0' style="text-transform:uppercase;"></textarea></td>
		
		</tr>
		<tr>
			<td align="center" colspan="2"><input type="submit" value='Guardar' class='guardar'>
			    <input type="button" value='Cancelar' class='cancelar'>
			</td>
		</tr>
	</table>
	</form>
</fieldset>	
</div>


</div>
</body>
</html>