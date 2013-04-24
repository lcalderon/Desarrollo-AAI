<?php
	session_start();  
	
	include_once('../../../modelo/clase_lang.inc.php');	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
		
	$con= new DB_mysqli();	
	if($con->Errno){
	
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	Auth::required();
	
	$result=$con->query("SELECT IDRETENCION,COMENTARIO,FECHAMOD,IDUSUARIO,COMENTARIO FROM $con->temporal.retencion WHERE IDAFILIADO='".$_GET["idafiliado"]."' AND IDDETMOTIVOLLAMADA=85 ORDER BY FECHAMOD DESC");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TinyTable</title>
<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style.css"/>
</head>
<body>
	<div id="tablewrapper">
		<div id="tableheader" style="display:none">
        	<div class="search">
                <select id="columns" onchange="sorter.search('query')"></select>
                <input type="text" id="query" onkeyup="sorter.search('query')" />
            </div>
            <span class="details">
				<div>Records <span id="startrecord"></span>-<span id="endrecord"></span> of <span id="totalrecords"></span></div>
        		<div><a href="javascript:sorter.reset()">reset</a></div>
        	</span>
        </div>
        <table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable">
            <thead>
                <tr>
                    <th class="nosort"><h3>#</h3></th>
                    <th><h3>#Caso</h3></th>
                    <th><h3>Fecha</h3></th>
                    <th class="nosort"><h3>Comentario</h3></th>
 
                </tr>
            </thead>
            <tbody>
				 <?				 
					while($row = $result->fetch_object()){
						$a=$a+1;
				 ?>
                <tr>
                    <td><?=$a?></td>
                    <td style="text-align:center"><?=$row->IDRETENCION;?></td>  
                    <td><?=$row->FECHAMOD;?></td>  
                    <td><?=$row->COMENTARIO;?></td>  
                </tr>
				<? } ?>                   
            </tbody>
        </table>
        <div id="tablefooter">
			<div id="tablenav">
            	<div>
                    <img src="../../../../librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
                    <img src="../../../../librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
                    <img src="../../../../librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
                    <img src="../../../../librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />
                </div>
                <div>
                	<select id="pagedropdown"></select>
				</div>
                <div style="display:none">
                	<a href="javascript:sorter.showall()">view all</a>
                </div>
            </div>
			<div id="tablelocation">
            	<div style="display:none">
                    <select onchange="sorter.size(this.value)">
                    <option value="5">5</option>
                        <option value="10" selected="selected">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>Entries Per Page</span>
                </div>
                <div class="page">Pagina <span id="currentpage"></span> de <span id="totalpages"></span></div>
            </div>
        </div>
    </div>
	<script type="text/javascript" src="../../../../librerias/tinytablev3.0/script.js"></script>
	
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
		// sortcolumn:1,
		sortdir:1,
		init:true
	});
  </script>
</body>
</html>