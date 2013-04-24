<?php
 
	session_start(); 
 
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once('../../../modelo/functions.php');	
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	Auth::required($_SERVER['REQUEST_URI']);
 
	$result=$con->query("SELECT * FROM $con->catalogo.catalogo_afiliado_movimiento WHERE IDAFILIADO='".$_GET["idafiliado"]."' ORDER BY FECHAMOD DESC");
	$numreg=$result->num_rows; 		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>American Assist</title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">	
<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style_sac.css" />	
<style type="text/css">
<!--
.style3 {color: #FFFFFF; font-weight: bold; }
html body {
	margin:2px;
	padding:1px;
}
-->
</style>
</head>
<body>
        <input type="hidden" id="columns" />
        <input type="hidden" id="query" />
        <? if($numreg >0){	?>
            <span class="details">
				<div><span id="startrecord"></span>-<span id="endrecord"></span><span id="totalrecords"></span></div>
        		 
        	</span>
		<? } ?>		
        <table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable" style="width:100%">
            <thead>
                <tr>
                    <th><h3><?=_("#") ;?></h3></th>
                    <th><h3><?=_("STATUS") ;?></h3></th>
                    <th><h3><?=_("USUARIOMOD") ;?></h3></th>
                    <th><h3><?=_("FECHAMOD") ;?></h3></th>
                </tr>				
            </thead>
            <tbody>
			
			
		<?
	 		if($numreg  >0){
			
				while($reg = $result->fetch_object())
				 {				
					if($c%2==0) $fondo='#FFFFFF'; else $fondo='#F9F9F9';	
					if($c%2==0) $clase='trbuc0'; else $clase='trbuc1';	
					$ii++;				
		?>		
		 <tr>
			<td><div align="center"><?=$ii;?></div></td>	 
			<td align="center"><?=$reg->DESCRIPCION;?></td>
			<td align="center"><?=$reg->USUARIOMOD;?></td>			 
			<td align="center"><?=$reg->FECHAMOD;?></td>			 
			
		 </tr>
		<?
				}
			}

		?>
                
            </tbody>
        </table>
 
        <div id="tablefooter" style="display:<?=($numreg >10)?"":"none"?>;text-align:center;border:1px dashed #003366;width:92px">
          <div id="tablenav" >
            	<div style="text-align:center">
                    <img src="../../../../librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" title="Primera Pagina" onclick="sorter.move(-1,true)" />
                    <img src="../../../../librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" title="Pagina Siguiente" onclick="sorter.move(-1)" />
                    <img src="../../../../librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" title="Pagina Anterior" onclick="sorter.move(1)" />
                    <img src="../../../../librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" title="Ultima Pagina" onclick="sorter.move(1,true)" />
                </div>
       
 
            </div>
			<div id="tablelocation" style="display:none">
            	<div>
                    <select onchange="sorter.size(this.value)" class="classtexto" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);">
                    <option value="5">5</option>
                        <option value="10" selected="selected">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span><?=_("Entrada de Pagina") ;?></span>
                </div>
                <div class="page">Pag. <span id="currentpage"></span> de <span id="totalpages"></span></div>
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
		//pageddid:'pagedropdown',
		navid:'tablenav',
		sortcolumn:3,
		sortdir:1,
		//sum:[8],
		//avg:[6,7,8,9],
		columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
		init:true
	});
  </script>
</body>
</html>