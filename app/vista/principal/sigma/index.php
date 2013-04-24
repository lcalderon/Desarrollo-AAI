<?php 
session_start();
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
//print_r(get_required_files());

$con = new DB_mysqli();

$sql="
SELECT
a.`IDASISTENCIA`,
e.`IDCUENTA`,
e.`IDPROGRAMA`, 
cs.`DESCRIPCION`,
cp.`NOMBRECOMERCIAL`,
asp.`ARRPRIORIDADATENCION`,
CONCAT(ep.`NOMBRE`,' ',ep.`APPATERNO`,' ',ep.APMATERNO) NOMBRE_AFILIADO,
au.`FECHAHORA`

FROM
$con->temporal.`asistencia` a,
$con->temporal.`expediente` e,
$con->temporal.`expediente_persona` ep,
$con->temporal.`asistencia_asig_proveedor` asp,
$con->catalogo.`catalogo_proveedor` cp,
$con->catalogo.`catalogo_servicio` cs,
$con->temporal.`asistencia_usuario` au
WHERE
a.`IDASISTENCIA`= asp.`IDASISTENCIA`
AND a.`IDEXPEDIENTE`= e.`IDEXPEDIENTE`
AND e.`IDEXPEDIENTE`= ep.`IDEXPEDIENTE`
AND asp.`IDPROVEEDOR` = cp.`IDPROVEEDOR`
AND a.`IDSERVICIO` =cs.`IDSERVICIO`
AND a.`IDASISTENCIA` = au.`IDASISTENCIA`
AND asp.`STATUSPROVEEDOR` IN ('AC')
AND cp.`IDPROVEEDOR` ='6060'
AND a.`ARRSTATUSASISTENCIA`='PRO'
AND e.`ARRSTATUSEXPEDIENTE`='PRO'
AND au.`IDETAPA`=1 
AND ep.`ARRTIPOPERSONA`='TITULAR'
";

$result = $con->query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Language" content="en" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="tinytable/tinytable.css" rel="stylesheet" type="text/css" />
<link href="/estilos/jquery/jquery.windows-engine.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/librerias/jquery/jquery-1.5.js"></script>
<script type="text/javascript" src="/librerias/jquery/jquery.windows-engine.js"></script> 

</head>

<body>
	<div id="tablewrapper">
		<div id="tableheader">
        	<div class="search">
                <select id="columns" onchange="sorter.search('query')"></select>

                <input type="text" id="query" onkeyup="sorter.search('query')" />
            </div>
            <span class="details">
				<div>Registros <span id="startrecord"></span>-<span id="endrecord"></span> de <span id="totalrecords"></span></div>
        		<div><a href="javascript:sorter.reset()">reset</a></div>
        	</span>

        </div>
        <table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable">
            <thead>
                <tr>
                    <th><h3>IDASISTENCIA</h3></th>
                    <th><h3>CUENTA</h3></th>
                    <th><h3>PROGRAMA</h3></th>
                    <th><h3>DESCRIPCION</h3></th>
                    <th><h3>PROVEEDOR</h3></th>
                    <th><h3>PRIORIDAD</h3></th>
                    <th><h3>AFILIADO</h3></th>
                    <th><h3>FECHAHORA</h3></th>
                    <th class="nosort"><h3>OPCIONES</h3></th>
                    <th class="nosort"><h3>OPCIONES</h3></th>
                </tr>
            </thead>
            <tbody>
            <? while($reg = $result->fetch_object()):
					$i++;
			?>
                <tr>
                    <td><?=$reg->IDASISTENCIA?></td>
                    <td><?=$reg->IDCUENTA?></td>
                    <td><?=$reg->IDPROGRAMA?></td>
                    <td><?=$reg->DESCRIPCION?></td>
                    <td><?=$reg->NOMBRECOMERCIAL?></td>
                    <td><?=$reg->ARRPRIORIDADATENCION?></td>
                    <td><?=$reg->NOMBRE_AFILIADO?></td>
                    <td><?=$reg->FECHAHORA?></td>
					<td align="center"><img src='/imagenes/iconos/bitacora.PNG' width="20" alt='bitacora' title='Ver bitacora' border='0' style='cursor: pointer;' onclick="ventaBitacora('<?=$reg->IDASISTENCIA?>')" ><td>
                    	<input type='button' name="brningresar" id="brningresar" value='INGRESAR' onclick= "editarAsistencia('<?=$reg->IDASISTENCIA?>')" title="Ingresar Datos Asistencia">
                    </td>
                 </tr>
            <? endwhile; ?>

            </tbody>
        </table>
        <div id="tablefooter">
          <div id="tablenav">
            	<div>
                    <img src="images/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
                    <img src="images/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
                    <img src="images/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
                    <img src="images/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />

                </div>
                <div>
                	<select id="pagedropdown"></select>
				</div>
                <div>
                	<a href="javascript:sorter.showall()">ver todas</a>
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
                    <span>Reg. por pag</span>
                </div>
                <div class="page">Pag <span id="currentpage"></span> de <span id="totalpages"></span></div>
            </div>

        </div>
    </div>
    </body>
</html>
	<script type="text/javascript" src="tinytable/tinytable.js"></script>
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
		size:10,
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
		//sum:[8],
		//avg:[6,7,8,9],
		//columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
		init:true
	});


	
	function editarAsistencia(idasistencia){
		
		$.newWindow({id:"iframewindow",
			width: 480,
			height: 450,
			posx: 80,
            posy: 10,			
			resizeable: true,
			modal: true
			});
		$.updateWindowContent("iframewindow",
				"<iframe src='form_asistencia.php?idasistencia="+idasistencia+"' width='95%' height='95%'/>");
	}
	
	function ventaBitacora(idasistencia){
		
		$.newWindow({id:"iframewindow",
			width: 900,
			height: 400,
			resizeable: true,
			modal: true
			});
		$.updateWindowContent("iframewindow",
				"<iframe src='/app/vista/bitacora/bitacora.php?idasistencia="+idasistencia+"' width='100%' height='100%'/>");
	}
  </script>
