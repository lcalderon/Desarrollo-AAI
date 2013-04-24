<?
if (isset($_POST[idasistencia])){
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/clase_asistencia.inc.php');
	include_once('/app/vista/includes/head_prot_win.php');
	$asis = new asistencia();
	$asis->carga_bitacora($_POST[idasistencia],$_POST[idetapa]);
}
?>

<fieldset>
<legend><?=_('Bitacora')?></legend>


<table cellpadding="0"  cellspacing="0" border="0" id="table" class="tinytable" width=80%>
	<thead>
		<tr>
			<th width='20%' ><h3><?=_('FECHAHORA')?></h3></th>
			<th width='20%' ><h3><?=_('USUARIO')?></h3></th>
			<th width='50%' class="nosort"><h3><?=_('COMENTARIO')?></h3></th>
		</tr>
	</thead>

	<tbody  >
	<?
	$linea=0;
	foreach ($asis->bitacora as $reg_bitacora ){
		$colorlinea = ($linea%2)? 'par':'impar';
		echo '<tr class='.$colorlinea.'>';
		echo '<td align="center">'.$reg_bitacora[fechamod].'</td>';
		echo '<td align="center">'.$reg_bitacora[idusuariomod].'</td>';
		echo '<td>'.$reg_bitacora[comentario].'</td>';
		echo '</tr>';
		$linea++;
		}
	?>
	</tbody>
</table>
</div>
</fieldset>
