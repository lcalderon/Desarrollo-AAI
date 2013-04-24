<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_bitacora.inc.php');

include_once('../includes/head_prot_win.php');
include_once('../includes/arreglos.php');

$bitacora = new bitacora();
$idusuariomod = $_SESSION[user];

$idasistencia= $_GET[idasistencia];


/* DETERMINA ES STATUS DEL EXPEDIENTE*/
$sql="SELECT
ARRSTATUSEXPEDIENTE 
FROM 
$bitacora->temporal.asistencia a,
$bitacora->temporal.expediente e
WHERE
a.IDEXPEDIENTE = e.IDEXPEDIENTE
AND a.IDASISTENCIA='$idasistencia'";
$result = $bitacora->query($sql);
while($reg=$result->fetch_object()){
	$status=$reg->ARRSTATUSEXPEDIENTE;
}

$nfilas =$bitacora->lee_parametro('PAG_CATALOGOS');

if (isset($_GET[idetapa])) {
	$idetapa=$_GET[idetapa];
	$result=$bitacora->carga_datos($idasistencia,$idetapa);
}
else
$result=$bitacora->carga_datos($_GET[idasistencia]);

$etapas=$bitacora->uparray("SELECT IDETAPA,DESCRIPCION FROM $bitacora->catalogo.catalogo_etapa");

?>
<body>
<fieldset>

	<input type="hidden" id="columns" value='0' >
	<input type="hidden" id='query' value="" onchange="sorter.search('query')">
	<table width="100%">
	<tr>
		<td valign="top" width="70%">
			<?=_('ETAPAS')?><br>
			<select id='idetapa' onchange=coloca_text(this.options[this.value].text)>
			<option value='0' selected ><?=_('TODOS')?></option>
			<?		
			foreach ($etapas as $index=>$value)
			{
				if ($idetapa==$value or $idetapa==$index)
				echo "<option value='$index' selected  >"."$value".'</option>';
				else
				echo "<option value='$index' >"."$value".'</option>';

			}
			?> 
			</select>
		</td>
		<td rowspan="2" width="30%">
				<?=_('COMENTARIO')?><br>		
				<textarea id='comentario' cols="30"></textarea>
		</td>
	</tr>
	<tr>
		<td align="center">
			<? if(!$_GET["gestion"]): ?>
				<?if ($status =='PRO'):?>
					<input type="button" value="<?=_('AGREGAR BITACORA')?>" class="normal" onclick="agregar();"> 
				<?else:?>
						<input type="button" value="<?=_('AGREGAR BITACORA')?>" class="normal" disabled> 			
				<?endif;?>
				<? endif;?>
			</td>
	</tr>
   </table>	

</fieldset>			


<table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable">
  <thead>
      <tr>
          <th width="20%"><h3><?=_('ETAPA')?></h3></th>
          <th width="17%"><h3><?=_('FECHAMOD')?></h3></th>
          <th width="10%"><h3><?=_('USUARIO')?></h3></th>
          <th width="10%"><h3><?=_('CLASIFICACION')?></h3></th>
          <th width="10%"><h3><?=_('PROVEEDOR')?></h3></th>
          <th width="30%"><h3><?=_('COMENTARIO')?></h3></th>
    
       </tr>
  </thead>
  <tbody>
    <? while ($reg=$result->fetch_object()):?>
    	<tr>
    		<td><?=$etapas[$reg->IDETAPA]?></td>
    	    <td><?=$reg->FECHAMOD?></td>
    	    <td><?=$reg->IDUSUARIOMOD?></td>
    	    <td><?=$clasificacion[$reg->ARRCLASIFICACION]?></td>
    	    <td><?=$reg->NOMBRECOMERCIAL?></td>
    	    <td><textarea style='width:99%' readonly><?=stripslashes($reg->COMENTARIO)?></textarea></td>
    	</tr>
    <?endwhile;?>
	</tbody>	
</table>
	 <div id="tablefooter" >
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
	size:'<?=$nfilas?>',
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


function coloca_text(t) {
	if (t =="TODOS"  )
	$('query').clear();
	else
	$('query').value=t;
	sorter.search('query');
	return;
}

function agregar()
{
	if ($('idetapa').value=='0') alert("<?=_('SELECCIONE UNA ETAPA')?>");
	else if ($('comentario').value=='') alert("<?=_('INGRESE UN COMENTARIO')?>");
	else if (('<?=$_GET[etapaactiva]?>'=='7')&& ($F('idetapa')!='7')) alert("<?=_('SOLO INGRESE BITACORA PARA LA ETAPA DE COSTEO')?>");
	else{
		new Ajax.Request('/app/controlador/ajax/ajax_grabar_asistencia_bitacora.php',
		{
			method : 'post',
			parameters : {
				IDETAPA: $F('idetapa'),
				IDUSUARIOMOD: "<?=$idusuariomod?>",
				COMENTARIO: $F('comentario'),
				IDASISTENCIA: "<?=$idasistencia?>",
				ARRCLASIFICACION:'BIT'
			},
			onSuccess: function()
			{
				reDirigir('bitacora.php?idasistencia='+"<?=$idasistencia?>");
			}
		});
	}
	return;
}

 </script>
  </body>
  </html>