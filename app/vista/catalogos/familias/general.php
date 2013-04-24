<?php
		
 	session_start(); 	
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../modelo/functions.php");
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

 	Auth::required($_SERVER['REQUEST_URI']);
	
//cantidad de paginacion
    $nroubigeo=$con->lee_parametro("PAG_CATALOGOS");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>planes</title>
	<script type="text/javascript" src="../../../../estilos/functionjs/func_global.js"></script>
	<link rel="stylesheet" href="../../../../librerias/tinytablev3.0/style_catalogo.css" />
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
</head>
<body onLoad="document.fmrplanes.txtnombrefam.focus()">
	<form id="fmrplanes" name="fmrplanes" method="post" action="">
		<div id="tablewrapper">
			<div id="tableheader">
				<div class="search" style="display:none">
					<select id="columns" onchange="sorter.search('query')"></select>		
					<input type="text" id="query" onkeyup="sorter.search('query')" size="45" />
				</div>  
				<div class="search">			
					<input type="text" name="txtnombrefam" size="72" value="<?=$_POST["txtnombrefam"]?>" style="text-transform:uppercase" class='classtexto' onFocus='coloronFocus(this);' onBlur="colorOffFocus(this)" />
					<input type="submit" name="btnbuscar" id="btnbuscar" value="<?=_("BUSCAR");?>" title="<?=_("BUSCAR CUENTA");?>" style="font-weight:bold"/>
				</div>
				<span class="details">
					<div style="display:none"><span id="startrecord"></span><span id="endrecord"></span><span id="totalrecords"></span></div>      
				</span>		
			</div>
			<div id="tablenav" style="width:100%">
					<div>
						<img src="../../../../imagenes/iconos/first.png" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
						<img src="../../../../imagenes/iconos/left.png" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
						<img src="../../../../imagenes/iconos/right.png" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
						<img src="../../../../imagenes/iconos/last.png" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />
					</div>
					<div style="display:none">
						<select id="pagedropdown"></select>
					</div>
					<div style="display:none">
						<a href="javascript:sorter.showall()">view all</a>
					</div>
					<div class="page"><strong>Pagina <span id="currentpage"></span> de <span id="totalpages"></span></strong></div>
			</div>			
			<table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable">
				<thead>
					<tr>
						<th><h3><?=_("ID");?></h3></th>
						<th><h3><?=_("DESCRIPCION");?></h3></th>
						<th><h3><?=_("STATUS");?></h3></th>
						<th class="nosort"><h3><? if(validar_permiso("CATFAMILIAS_AGREGAR") ==1){?><input type="button" name="btnnuevo" id="btnnuevo" title="<?=_("AGREGAR NUEVA FAMILIA");?>" class='boton' onclick="reDirigir('add_catalogo.php')"  value="<?=_("NUEVO");?>" /><? } ?></h3></th>
					</tr>
				</thead>
				<tbody>
					<?							 						
						$Sql_familia="select IDFAMILIA,DESCRIPCION,ACTIVO from $con->catalogo.catalogo_familia where DESCRIPCION like '%".$_POST["txtnombrefam"]."%'  ";

						$result=$con->query($Sql_familia);
						while($reg = $result->fetch_object()){							 
					?>
					<tr>
						<td style="text-align:center"><?=$reg->IDFAMILIA;?></td>
						<td><?=$reg->DESCRIPCION;?></td>
						<td><?=($reg->ACTIVO==1)?_("ACTIVO"):_("INACTIVO");?></td>
						<td align="center">						
							<? if(validar_permiso("CATFAMILIAS_EDITAR") ==1){?><input type="button" name="btneditar" id="btneditar" value="<?=_("EDITAR");?>" class='boton' title="<?=_("EDITAR REGISTRO");?>" onclick="reDirigir('edit_catalogo.php?codigo=<?=$reg->IDFAMILIA ?>')"/><? } ?>
							<? if(validar_permiso("CATFAMILIAS_EDITAR") ==1 and validar_permiso("CATFAMILIAS_ELIMINAR") ==1){?>|<? } ?>
							<? if(validar_permiso("CATFAMILIAS_ELIMINAR") ==1){?>
								<img src="../../../../imagenes/iconos/cancel.gif" border="0" style="cursor:pointer" title="<?=_("ELIMINAR REGISTRO");?>" onclick="confirmaRespuesta('<?=_("ESTAS SERGURO QUE DESEAS ELIMINAR EL REGISTRO: ".$reg->NOMBRE."?")?>','eliminacion.php?codigo=<?=$reg->IDFAMILIA?>')" border='0' width="16" height="14" title="<?=_("ELIMINAR") ;?>">
							<? } else{?>
								<img src="../../../../imagenes/iconos/cancel-off.gif" border="0" title="<?=_("ELIMINAR REGISTRO");?>" border='0' width="16" height="14" title="<?=_("ELIMINAR") ;?>">
							<? } ?>	
						</td>						
					</tr>  
					<?
							}						
					?>
					
				</tbody>
			</table>
			<div id="tablefooter">
				<div id="tablelocation" style="display:none">
					<div>
						<select onchange="sorter.size(this.value)">
						<option value="5">5</option>
							<option value="10" selected="selected">10</option>
							<option value="20">20</option>
							<option value="50">50</option>
							<option value="100">100</option>
						</select>						
					</div>                
				</div>
			</div>
		</div>
	</form>	
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
		size:<?=$nroubigeo?>,
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
		// sortdir:1,
		//sum:[8],
		//avg:[6,7,8,9],
		columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
		init:true
	});
  </script>
</body>
</html>