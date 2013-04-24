<?
session_start();
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/validar_permisos.php');
include_once('../../modelo/clase_asistencia.inc.php');
include_once('../includes/arreglos.php');
include_once('../includes/head_prot_win.php');


$asis = new asistencia();
$user= (isset($_GET[idusuario]))?$_GET[idusuario]:'';
$arrstatusexpediente='CER';
$arrstatusasistencia='';
$idetapas='';
$idcuentas='';
$modo='DESVIO';
$annio='';
$mes='';
$idasistencia='';
$statusnegociacionposterior = (isset($_GET[STATUSNEGOCIACIONPOSTERIOR]))?$_GET[STATUSNEGOCIACIONPOSTERIOR]:'';

$statusautorizaciondesvio = (isset($_GET[STATUSAUTORIZACION]))?"$_GET[STATUSAUTORIZACION]":'0';

$lista_asis = $asis->listar_desviaciones($user,$arrstatusexpediente,$arrstatusasistencia,$idetapas,$idcuentas,$modo,$statusautorizaciondesvio,$annio,$mes,$dia,$hora,$annio2,$mes2,$dia2,$hora2,$idasistencia,$statusnegociacionposterior);

$cuenta = $asis->uparray("SELECT IDCUENTA,NOMBRE from catalogo_cuenta");
$servicio = $asis->uparray("SELECT IDSERVICIO,DESCRIPCION from catalogo_servicio");
$proveedor = $asis->uparray("SELECT IDPROVEEDOR,NOMBRECOMERCIAL from catalogo_proveedor");
?>
<title><?=_('DESVIACIONES')?></title>

<body onload="sorter.search('estado')">
	<div id="tablewrapper" style="width:100%">
	<font size="4px"><?=_("ASISTENCIAS CON DESVIACION DE COSTOS")?></font>
		<div id="tableheader" style="width:100%">
        	<div class="search" style="width:100%">
        	<form id="datos_busqueda">
        		<table style="width:100%;">
    	    		<tr>
    	    			<td class="busqueda"><?=_('BUSQUEDA POR COLUMNAS')?></td>
    	    			<td class="busqueda"><?=_('STATUS AUTORIZACION')?></td>
    	    			<td class="busqueda" colspan="2"><?=_('REVISION')?></td>
    	    			<td class="busqueda" rowspan="2">
                		  <img src='/imagenes/64x64/Search.png' onclick="buscar()"  align="absbottom" border="1" style="cursor: pointer;" title="<?=_('BUSCAR')?>">
                		  <img src='/imagenes/64x64/Refresh.png' align="absbottom" border='1' style='cursor: pointer;' onclick="reDirigir('index.php')">
                		  </td>
    	    		</tr>	
	        		<tr>
	        			<td class="busqueda">
        				<select id="columns" onchange="sorter.search('query')" style="display:none"></select>
		                <input type="text" id="query" size='50' onkeyup="sorter.search('query')" />
                		</td>
                		<td class="busqueda">
                		<select name='STATUSAUTORIZACION' onchange="">
                			<option value='0' <?=($statusautorizaciondesvio=='0')?'selected':''?> ><?=_("NO AUTORIZADOS")?></option>
                			<option value='1' <?=($statusautorizaciondesvio=='1')?'selected':''?>><?=_("AUTORIZADOS")?></option>
                		</select>
                		</td>
                		<td class="busqueda">
                		<select name="STATUSNEGOCIACIONPOSTERIOR">
                			<option value=''><?=_('TODOS')?></option>
                			<option value='0'><?=_('AUTOMATICA')?></option>
                			<option value='1'><?=_('MANUAL')?></option>
                		</select>
                		</td>
                		
                	</tr>
                </table>
            </form>    
            </div>
        </div>
      </div>
<table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable" width="100%">
  <thead>
      <tr>
          <th width="2%"><h3><?=_('EXP')?></h3></th>
          <th width="2%"><h3><?=_('EST')?></h3></th>
          <th class="desc"  width="2%"><h3><?=_('ASIS')?></h3></th>
          <th width="2%"><h3><?=_('CTA')?></h3></th>
          <th width="2%"><h3><?=_('PLAN')?></h3></th>
          <th width="10%"><h3><?=_('AFILIADO')?></h3></th>
          <th width="15%"><h3><?=_('SERVICIO')?></h3></th>
          <th width="2%" title='<?=_('CONDICION DEL SERVICIO')?>'><h3><?=_('CON')?></h3></th>
          <th width="2%" title='<?=_('PRIORIDAD DEL SERVICIO')?>'><h3><?=_('PRI')?></h3></th>
          <th width="2%"><h3><?=_('ESTADO')?></h3></th>
          <th width="2%" title='<?=_('USUARIO RESPONSABLE')?>'><h3><?=_('RESP.')?></h3></th>
          <th width="2%"><h3><?=_('FECHA')?></h3></th>
          <th width="5%"><h3><?=_('DESVIACION')?></h3></th>
          <th width="5%"><h3><?=_('AUTORIZANTE')?></h3></th>
          <th width="5%" class="nosort"><h3><?=_('REVISADO')?></h3></th>
          <th width="2%" class="nosort"><h3><?=_('OPCIONES')?></h3></th>
       </tr>
  </thead>
  <tbody >
    <?	
    while ($reg=$lista_asis->fetch_object()){

    	echo "<tr>";
    	echo "<td>$reg->IDEXPEDIENTE</td>";
    	echo "<td>$reg->ARRSTATUSEXPEDIENTE</td>";
    	echo "<td>$reg->IDASISTENCIA</td>";
    	echo "<td title='$reg->NOM_CUENTA'>$reg->IDCUENTA</td>";
    	echo "<td title='$reg->NOM_PROGRAMA'>$reg->IDPROGRAMA</td>";
    	echo "<td>$reg->NOM_AFILIADO</td>";
    	$nombre_servicio =($reg->NOM_SERVICIO=='')?$reg->AA_SERVICIO:$reg->NOM_SERVICIO;
    	echo "<td>$nombre_servicio</td>";
    	echo "<td title='{$desc_cobertura_servicio[$reg->ARRCONDICIONSERVICIO]}'>$reg->ARRCONDICIONSERVICIO</td>";
    	echo "<td title='{$desc_prioridadAtencion[$reg->ARRPRIORIDADATENCION]}'>$reg->ARRPRIORIDADATENCION</td>";
    	echo "<td>".$desc_status_asistencia[$reg->ARRSTATUSASISTENCIA]."</td>";
    	echo "<td>$reg->IDUSUARIORESPONSABLE</td>";
    	echo "<td>$reg->FECHAHORA</td>";
    	echo "<td align='center'>".($reg->DESVIACION?"SI":"NO")."</td>";
    	echo "<td>".($reg->IDUSUARIOAUTORIZACIONDESVIO==''?'S/D':$reg->IDUSUARIOAUTORIZACIONDESVIO)."</td>";
    	echo "<td align='center'><img src='/imagenes/iconos/".($reg->STATUSAUTORIZACIONDESVIO?'semaforoverde.jpg':'semafororojo.jpg')."' alt='15px' width='15px'></td>";
    	echo "<td>
    	<img src='/imagenes/iconos/editars.gif' title='Editar' align='absbottom' border='0' style='cursor: pointer;' onclick=window.open('vista_proveedores.php?idasistencia=$reg->IDASISTENCIA','ASISTENCIA_$reg->IDASISTENCIA') >
    	<img src='/imagenes/iconos/bitacora.PNG' alt='12px' width='12px' title='bitacora' align='absbottom' border='0' style='cursor: pointer;' onclick=bitacora('$reg->IDASISTENCIA') >
    	";
    	echo "</tr>	";
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
	size:10,
	colddid:'columns',
	currentid:'currentpage',
	totalid:'totalpages',
	startingrecid:'',
	endingrecid:'',
	totalrecid:'',
	hoverid:'selectedrow',
	pageddid:'pagedropdown',
	navid:'tablenav',
	sortcolumn:11,
	sortdir:-1,
	init:true
});

function bitacora(idasistencia){
	if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
	else
	{
		win = new Window({
			className: "alphacube",
			title:"<?=_('BITACORA')?>",
			width: 600,
			height: 300,
			showEffect: Element.show,
			hideEffect: Element.hide,
			resizable: false,
			destroyOnClose: true,
			minimizable: false,
			maximizable: false,
			url: "/app/vista/bitacora/bitacora.php?idasistencia="+idasistencia
		});

		win.keepMultiModalWindow = true;
		win.showCenter();
		myObserver = {onDestroy: function(eventName, win1)
		{
			if (win1 == win) {
				win = null;
				Windows.removeObserver(this);

			}
		}
		}
		Windows.addObserver(myObserver);

	}
	return;
}


function buscar()
{
	url=$('datos_busqueda').serialize();
	reDirigir('/app/vista/desviaciones/index.php?'+url);
	return;
}


</script>

</body>
</html> 

