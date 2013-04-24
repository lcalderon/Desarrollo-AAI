<?
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../includes/head_prot_win.php');

$campo=$_GET[campo];
$con = new DB_mysqli();
$nreg= $con->lee_parametro('PAG_CATALOGOS');


$sql="
	SELECT 
		* 
	FROM 
	$con->catalogo.catalogo_ddn
	";
$result=$con->query($sql);

?>

<div id="tablewrapper" >
		<div id="tableheader" style='display:none'>
        	
            <span class="details" >
				<div>Reg. <span id="startrecord"></span>-<span id="endrecord"></span> of <span id="totalrecords"></span></div>
        		<div><a href="javascript:sorter.reset()">reset</a></div>
        	</span>
        </div>
        <div id="tablefooter" >
       		<span class="search" style="float:left;">
                <select id="columns" onchange="sorter.search('query')" style='display:none'></select>
                <input type="text" id="query" size='30' onkeyup="sorter.search('query')" />
            </span><br>
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
                	<a href="javascript:sorter.showall()">ver todo</a>
                </div><br>
            </div>
			<div id="tablelocation" style='display:none' >
            	<div>
                    <select onchange="sorter.size(this.value)">
                    <option value="5">5</option>
                        <option value="10" selected="selected">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>Pag.</span>
                </div>
                <div class="page">Pag. <span id="currentpage"></span> de <span id="totalpages"></span></div>
            </div>
  	   </div>
 <br> 	   
<div style="float:left;">
	<form id='form_listado'  >
	<table cellpadding="0"  cellspacing="0" border="0" id="table" class="tinytable" >
		<thead>
			<tr>
				<th width="50%"><h3><?=_('Ciudad')?></h3></th>
				<th width="50%"><h3><?=_('Codigo')?></h3></th>
			</tr>
		</thead>
		<tbody>
		<?	$linea=0;
		while ($reg=$result->fetch_object())
		{
			$colorlinea = ($linea%2)? 'par':'impar';
			echo "<tr class='$colorlinea' onclick=seleccionar('$reg->DDN')>";
			echo "<td>$reg->DESCRIPCION</td>";
			echo "<td>$reg->DDN</td>";
			echo "</tr>";
			$linea++;
		}
		?>
		</tbody>
	</table>
	</form>
 </div>
 
 </div>

<script type="text/javascript" src="/librerias/tinytablev3.0/script.js"></script>
<script type="text/javascript">
var sorter = new TINY.table.sorter('sorter','table',{
	headclass:'head',
	ascclass:'asc',
	descclass:'desc',
	evenclass:'evenrow',
	oddclass:'oddrow',
	evenselclass:'evenselected',
	oddselclass:'oddselected',
	paginate:true,
	size:<?=$nreg?>,
	colddid:'columns',
	currentid:'currentpage',
	totalid:'totalpages',
	startingrecid:'startrecord',
	endingrecid:'endrecord',
	totalrecid:'totalrecords',
	hoverid:'selectedrow',
	pageddid:'pagedropdown',
	navid:'tablenav',
	sortcolumn:1,
	sortdir:1,
	columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
	init:true
});


function seleccionar(ddn){
	parent.$('<?=$campo?>').value=ddn;
	parent.win.close();
	return;
}
</script>


	

