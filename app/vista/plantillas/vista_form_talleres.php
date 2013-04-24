<?php
 
 	session_start();
	include_once('../../modelo/clase_lang.inc.php');
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/functions.php');
	include_once("../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	//Auth::required($_POST["txturl"]);
     $nroubigeo=$con->lee_parametro("PAG_CATALOGOS");	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../estilos/functionjs/func_global.js"></script>		
	<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="../../../librerias/tinytablev3.0/style_catalogo.css" />
	<style type="text/css"> 
	<!--
	.style3 { color: #FFFFFF; font-weight: bold; }
	html body {
		margin:1px;
		padding:1px;
	}
	-->
	</style>
</head>

<body>
<form action="" method="post" name="form1" id="form1">
	<table bgcolor="#565c70"  width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr>
			<td colspan="4" style="color:#ffffff;font-size:12px"><strong>CONSULTA TALLERES</strong></td>
		</tr>
		
		<tr><td colspan="4">
		<table width="100%" bgcolor="#ecf2f6" border="0"  cellpadding="3" cellspacing="3">		
			<?
				include("../includes/vista_entidades_final.php"); 						
			?>	
			<tr bgcolor="#ecf2f6">
			<td>PRIORIDAD</td>
			<td>
				<select name="cmbprioridad" id="cmbprioridad" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
				</select>
			 </td>
			<td colspan="2"><input name="txtconsultar" type="text" id="txtconsultar" size="50" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" /> <input type="button" name="btnConsultar" id="btnConsultar" value="CONSULTAR" style="font-weight:bold"/></td>
			</tr>
		  </table>
		 </td></tr>
    </table>
	<div id="tablewrapper">
			<div id="tableheader">
				<div class="search" style="display:none">
					<select id="columns" onchange="sorter.search('query')"></select>		
					<input type="text" id="query" onkeyup="sorter.search('query')" size="45" />
				</div>  
				<div class="search"></div>
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
						<th class="nosort" ><h3>Nro</h3></th>
						<th><h3><?=_("ENTIDAD 1");?></h3></th>
						<th><h3><?=_("ENTIDAD 2");?></h3></th>
						<th><h3><?=_("CUENTA");?></h3></th>
						<th><h3><?=_("TALLER");?></h3></th>
						<th><h3><?=_("TELEFONO1");?></h3></th>
						<th><h3><?=_("TELEFONO2");?></h3></th>
						<th><h3><?=_("STATUS");?></h3></th>
						<th class="nosort"><h3></h3></th>
					</tr>
				</thead>
				<tbody>
					<?						 
						
						$Sql_taller="SELECT
									catalogo_cuenta.IDCUENTA,
									catalogo_cuenta.NOMBRE AS CUENTA,
									catalogo_taller.NOMBRE,
									catalogo_taller.DIRECCION,
									catalogo_taller.TELEFONO1,
									catalogo_taller.TELEFONO2,
									catalogo_taller.TELEFONO3,
									catalogo_taller.TELEFONO4,
									catalogo_taller.PRIORIDAD,
									catalogo_taller.PERSONACONTACTO1,
									catalogo_taller.PERSONACONTACTO2,
									catalogo_taller.PERSONACONTACTO3,
									catalogo_taller.DOCUMENTO,
									catalogo_taller.ARRPESO,
									catalogo_taller.ACTIVO,
									catalogo_taller.OBSERVACIONES,									
									(SELECT catalogo_entidad.DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE catalogo_entidad.CVEENTIDAD1=catalogo_taller.CVEENTIDAD1  AND catalogo_entidad. CVEENTIDAD2='0' AND catalogo_entidad.CVEENTIDAD3='0') AS ENTIDAD1,
									(SELECT catalogo_entidad.DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE catalogo_entidad.CVEENTIDAD1=catalogo_taller.CVEENTIDAD1 AND  catalogo_entidad.CVEENTIDAD2=catalogo_taller.CVEENTIDAD2 AND catalogo_entidad.CVEENTIDAD3='0')  AS ENTIDAD2
									
								FROM
									$con->catalogo.catalogo_taller
								INNER JOIN $con->catalogo.catalogo_cuenta ON catalogo_cuenta.IDCUENTA = catalogo_taller.IDCUENTA
								WHERE catalogo_taller.NOMBRE LIKE '%".$_POST["txtconsultar"]."%'";
//echo $Sql_taller;
						$result=$con->query($Sql_taller);
						while($reg = $result->fetch_object()){
							$i++;							 
					?>
					<tr>
						<td align="center"><?=$i;?></td>
						<td><?=$reg->ENTIDAD1;?></td>
						<td><?=$reg->ENTIDAD2;?></td>
						<td><?=$reg->CUENTA;?></td>
						<td><?=$reg->NOMBRE;?></td>
						<td><?=$reg->TELEFONO1;?></td>
						<td><?=$reg->TELEFONO2;?></td>
						<td><?=($reg->ACTIVO==1)?_("ACTIVO"):_("INACTIVO");?></td>
						<td align="center">						
							<input type="button" name="btneditar" id="btneditar" value="<?=_("ASIGNAR");?>" class='boton' title="<?=_("Asignar taller");?>" onclick="reDirigir('edit_catalogo.php?codigo=<?=$reg->IDCUENTA ?>')"/>
		 						
					</tr>  
					<?
						}				
						//}
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
	<script type="text/javascript" src="../../../librerias/tinytablev3.0/script.js"></script>
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