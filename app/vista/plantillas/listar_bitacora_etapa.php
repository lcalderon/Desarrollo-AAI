<?
if (isset($_POST[idasistencia]))
{
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/clase_asistencia.inc.php');
	include_once('/app/vista/includes/head_prot_win.php');
	include_once('../includes/arreglos.php');
	
	$asis = new asistencia();
	$idasistencia=$_POST[idasistencia];
	$idetapa=$_POST[idetapa];
}
$asis->carga_bitacora($idasistencia,$idetapa);



?>

<table cellpadding="0"  cellspacing="0" border="0" id="table" class="tinytable" width=100%>
	<thead>
		<tr>
			<th width='10%' ><h3><?=_('FECHAHORA')?></h3></th>
			<th width='10%' ><h3><?=_('USUARIO')?></h3></th>
			<th width='20%' ><h3><?=_('CLASIFICACION')?></h3></th>
			<? if (in_array($idetapa,array(2,4,6,7))) : ?>
			<th width='20%' ><h3><?=_('PROVEEDOR')?></h3></th>
			<? endif;?>
			<th width='40%' class="nosort"><h3><?=_('COMENTARIO')?></h3></th>
		</tr>
	</thead>
	<tbody  >
	<?foreach ($asis->bitacora as $reg_bitacora ):?>
		<tr>
			<td align="center"><?=$reg_bitacora[fechamod]?></td>
			<td align="center"><?=$reg_bitacora[idusuariomod]?></td>
			<td align="center"><?=$clasificacion[$reg_bitacora[arrclasificacion]]?></td>
			<? if (in_array($idetapa,array(2,4,6,7))) : ?>
			<td align="center"><?=$reg_bitacora[nom_proveedor]?></td>
			<? endif;?>
		<td><textarea style="width:99%" readonly><?=stripslashes($reg_bitacora[comentario])?></textarea></td>
		</tr>
		
	<?endforeach;?>
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