<?
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../includes/head_prot_win.php');

if (isset($_POST[idproveedor])){
	$idproveedor = $_POST[idproveedor];
	$edicion = (isset($_POST[edicion]))?$_POST[edicion]:0;
}

$prov = new proveedor();
$result =$prov->listar_contactos($idproveedor);

$nreg= $prov->lee_parametro('PAG_CATALOGOS');
?>
<body>
<div id="tablewrapper" style="display:none">
		<div id="tableheader" >
			<div class="search" >
				<input type="hidden" id="columns" value='0' >
				<input type="hidden" id='query' value="" onchange="sorter.search('query')">
			</div>	
        </div>
</div>
<table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable" width="100%">
<thead>
	<tr>
		<th width='70%'><h3><?=_('NOMBRE')?></h3></th>
		<th width="5%" class="nosort" ><h3><?=_('RESPONSABLE')?></h3></th>
		<th width='25%' colspan ='2' class="nosort" ><h3><?=_('OPCIONES')?></h3></th>
	</tr>
</thead>
<tbody>
<?
while($reg= $result->fetch_object())
{
	echo "<tr>";
	echo "<td>$reg->NOMBRE</td>";
	echo "<td align='center'><img src='/imagenes/iconos/".(($reg->RESPONSABLE==1)?"ok.gif'":"'")."></img></td>";
	?>	
	<td align='center'>
	<input type='button' value="<?=_('EDITAR')?>" class='normal' onclick=editar_contacto("<?=$reg->IDCONTACTO?>") >
	<input type='button' value="<?=_('ELIMINAR')?>" class='normal' onclick=borrar_contacto("<?=$reg->IDCONTACTO?>") <?=($edicion==1)?'':'disabled';?>>
	</td>
<?	
	echo "</tr>";
}
?>
</tbody>
</table>
<div id="tablefooter">
          <div id="tablenav">
            	<div>
                    <img src="/librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
                    <img src="/librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
                    <img src="/librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
                    <img src="/librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />
                </div>
                <div>
                	<select id="pagedropdown"></select>
				</div>
                <div>
                	<a href="javascript:sorter.showall()"><?=_('Ver Todos')?></a>
                </div>
            </div>
			<div id="tablelocation">
            	<div>
                    <select onchange="sorter.size(this.value)">
                    <option value="5">5</option>
                        <option value="10" selected="selected">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span><?=_('Entradas por pagina')?></span>
                </div>
                <div class="page"><?=_('Pagina')?> <span id="currentpage"></span> <?=_('de') ?><span id="totalpages"></span></div>
            </div>
        </div>
    </div>
</div>    
</body>

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
	paginate:true,
	size:'<?=$nreg?>',
	colddid:'columns',
	currentid:'currentpage',
	totalid:'totalpages',
	startingrecid:'',
	endingrecid:'endrecord',
	totalrecid:'',
	hoverid:'selectedrow',
	pageddid:'pagedropdown',
	navid:'tablenav',
	sortcolumn:1,
	sortdir:-1,
	init:true
});
</script>

