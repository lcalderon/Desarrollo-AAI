<?
if (isset($_POST[idasistencia])){
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/clase_asistencia.inc.php');
	include_once('/app/vista/includes/head_prot_win.php');
	$asis = new asistencia();
	$asis->carga_bitacora($_POST[idasistencia],$_POST[idetapa]);
	
}

?>

<table cellpadding="0"  cellspacing="0" border="0" id="table" class="tinytable" width="100%" >
	<thead>
		<tr>
			<th width='20%' ><h3><?=_('FECHAHORA DE GRABACION')?></h3></th>
			<th width='10%' ><h3><?=_('CLASIFICACION')?></h3></th>
			<th width='15%' ><h3><?=_('FECHAHORA DEl EVENTO')?></h3></th>
			<th width='20%' ><h3><?=_('USUARIO')?></h3></th>
			<th width='35%' ><h3><?=_('COMENTARIO')?></h3></th>
		</tr>
	</thead>
	<tbody  >
	<?
	$linea=0;
	foreach ($asis->bitacora as $reg_bitacora ){
		$colorlinea = ($linea%2)? 'par':'impar';
		echo '<tr class='.$colorlinea.'>';
		echo '<td align="center">'.$reg_bitacora[fechamod].'</td>';
		echo '<td align="center">'.$reg_bitacora[arrclasificacion].'</td>';
		echo '<td align="center">'.$reg_bitacora[fechamanual].'</td>';
		echo '<td align="center">'.$reg_bitacora[idusuariomod].'</td>';
		echo '<td ><pre>'.$reg_bitacora[comentario].'</pre></td>';
		echo '</tr>';
		$linea++;
		}
	?>
	</tbody>
</table>



<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script>
<script type="text/javascript">
var win = null;

var sorter = new TINY.table.sorter('sorter','table',{
	headclass:'head',
	ascclass:'asc',
	descclass:'desc',
	evenclass:'evenrow',
	oddclass:'oddrow',
	evenselclass:'evenselected',
	oddselclass:'oddselected',
	paginate:false,
	size:10,
	hoverid:'selectedrow',
	pageddid:'pagedropdown',
	sortcolumn:0,
	sortdir:1,
	init:true
});


 </script>
