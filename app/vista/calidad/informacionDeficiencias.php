<?php
	session_start();  
	
	include_once("../../modelo/clase_lang.inc.php");	
	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");
	include_once("../includes/arreglos.php");
	include_once("../../modelo/functions.php");
 
    $con= new DB_mysqli();
	 
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	Auth::required($_SERVER['REQUEST_URI']); 
	
	$anio=mostrar_Anio(1);
	
	$Sql_coordinador="SELECT
		catalogo_usuario.IDUSUARIO,
		CONCAT(
			catalogo_usuario.APELLIDOS,
			', ',
			catalogo_usuario.NOMBRES
		) AS NOMBRES
	FROM
		$con->catalogo.catalogo_usuario
	INNER JOIN $con->temporal.grupo_usuario ON grupo_usuario.IDUSUARIO = catalogo_usuario.IDUSUARIO
	WHERE grupo_usuario.IDGRUPO='CORD' ";	
	
	$Sql_proveedores="SELECT DISTINCT
					catalogo_proveedor.IDPROVEEDOR,
					catalogo_proveedor.NOMBRECOMERCIAL
				FROM
					$con->catalogo.catalogo_proveedor
				INNER JOIN $con->temporal.expediente_deficiencia ON expediente_deficiencia.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				GROUP BY
					catalogo_proveedor.IDPROVEEDOR
				ORDER BY
					catalogo_proveedor.NOMBRECOMERCIAL";
			
	$fecha=$_POST["cmbanio"]."-".$_POST["cmbmes"];

	if($_POST["rdopcion"] ==1){
		$whereadd="AND expediente_deficiencia.IDCOORDINADOR = '".$_POST["cmbcoordinador"]."'";		
	} else{		
		$whereadd="AND expediente_deficiencia.IDPROVEEDOR = '".$_POST["cmbproveedor"]."' AND expediente_deficiencia.IDPROVEEDOR != ''";
	}
	
	if($_POST["txtbusqueda"]*1)	$whereadd=$whereadd." AND expediente_deficiencia.IDASISTENCIA='".$_POST["txtbusqueda"]."'";
 
	$Sql_consulta="SELECT
				expediente_deficiencia.IDDEFICIENCIA_CORRELATIVO,
				expediente_deficiencia.IDEXPEDIENTE,
				expediente_deficiencia.IDASISTENCIA,
				expediente_deficiencia.ORIGEN,
				expediente_deficiencia.MOVIMIENTO,
				expediente_deficiencia.IDETAPA,
				expediente_deficiencia.FECHAREGISTRO,
				expediente_deficiencia.IDSUPERVISOR,
				catalogo_deficiencia.NOMBRE
			FROM
				$con->temporal.expediente_deficiencia
			INNER JOIN $con->catalogo.catalogo_deficiencia ON catalogo_deficiencia.CVEDEFICIENCIA = expediente_deficiencia.CVEDEFICIENCIA
			WHERE expediente_deficiencia.FECHAREGISTRO LIKE '$fecha%' $whereadd ORDER BY expediente_deficiencia.IDASISTENCIA DESC";					

	$result=$con->query($Sql_consulta);
	
	$sql="SELECT IDGRUPO FROM $con->temporal.grupo_usuario where IDUSUARIO='".$_SESSION["user"]."' AND IDGRUPO='SUCA'";
	$rsusuaario=$con->query($sql);
	while($regusu = $rsusuaario->fetch_object())	$accesos[$regusu->IDGRUPO]=$regusu->IDGRUPO;
	  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Descargo Deficiencias</title>


	<script type="text/javascript" src="../../../estilos/functionjs/func_global.js"></script>	
	<link rel="stylesheet" href="../../../librerias/tinytablev3.0/style_sac.css" />
	<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	<!-- se usa para del prototype -->
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link> 
	<link href="../../../librerias/windows_js_1.3/themes/mac_os_x.css" rel="stylesheet" type="text/css" ></link>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window_ext.js"></script>
</head>

<body> 

	<form name="form1" method="post" action="">

		<table width="50%" border="0" cellpadding="1" cellspacing="1" bgcolor="#335672" style="border:1px solid #3f6a8d">
			<tr>
				<td colspan="3" style="color:#FFFFFF"><div align="center"><h3>INFORMACION DE  DEFICIENCIAS</h3></div></td>
			</tr>
			<tr><td> 
				<table width="100%" border="0"  bgcolor="#becad6">
					<tr>
						<td><input name="rdopcion" type="radio" id="radio" value="1" checked onclick="ocultarVisualizarDiv('coordinador','proveedor');submit();">COORDINADOR</td>
						<td><input type="radio" name="rdopcion" id="radio2" value="2" <?=($accesos["SUCA"])?"":"DISABLED"?> <?=($_POST["rdopcion"] ==2)?"checked":""?> onclick="ocultarVisualizarDiv('proveedor','coordinador');submit()">PROVEEDOR</td>
						<td width="40%">MES</td>
					</tr>					
					<tr>
						<td colspan="2">
							<div id="coordinador" style="display:<?=($_POST["rdopcion"] !=1 && $_POST["rdopcion"])?"none":"block"?>">
								<? $con->cmbselectdata($Sql_coordinador,"cmbcoordinador",(isset($_POST["cmbcoordinador"]))?$_POST["cmbcoordinador"]:$_SESSION["user"]," onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'",($accesos["SUCA"])?"2":"1"); ?>
							</div>
							<div id="proveedor"  style="display:<?=($_POST["rdopcion"] !=2)?"none":"block"?>">
								<? $con->cmbselectdata($Sql_proveedores,"cmbproveedor",$_POST["cmbproveedor"]," onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","2"); ?>		
							</div>			
						</td>
						<td>
							<?
								$con->cmb_array("cmbmes",$mes_del_anio,($_POST["cmbmes"])?$_POST["cmbmes"]:date("m"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1","","1");
								$con->cmb_array("cmbanio",$anio,($_POST["cmbanio"])?$_POST["cmbanio"]:date("Y"),"class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1");
							?>
							<input type="text" name="txtbusqueda" title="Buscar por Asistencia" size="15" value="<?=$_POST["txtbusqueda"]?>" onFocus="coloronFocus(this);" class="classtexto" onBlur="colorOffFocus(this);" style="text-transform:uppercase;">							
						</td>
					</tr>
					<tr>
						<td colspan="3"><div align="right"><input type="submit" name="brnconsultar" id="brnconsultar" value="CONSULTAR >>>" style="text-align:center;font-weight:bold;height:35px;"></div></td>
					</tr>
				</table>
			</td></tr>
		</table>
	</form>
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
		
		<table cellpadding="1" cellspacing="1" border="0" id="table" class="tinytable" style="width:100%">
			<thead>
            <tr> 
                <th width="3%"><h3></h3></th>
                <th width="3%"><h3><?=_("EXPEDIENTE")?></h3></th>
                <th width="3%"><h3><?=_("ASISTENCIA")?></h3></th>
                <th width="3%"><h3><?=_("CREADO POR")?></h3></th>
                <th width="3%"><h3><?=_("ETAPA")?></h3></th>
                <th class="desc" width="3%"><h3><?=_("FECHA CREACION")?></h3></th>
                <th width="15%" align="center"><h3><?=_("TIPO DEFICIENCIA")?></h3></th>
				<th width="10%" align="center"><h3><?=_("STATUS")?></h3></th>
                <th class="nosort"><h3><?=_("DEFICIENCIA")?></h3></th>			
                <th width="5%" class="nosort"><h3></h3></th>			
            </tr>
			</thead>
			<tbody>
			<?
				$i=0;
				while($reg=$result->fetch_object()){
					$i++;
					if($reg->MOVIMIENTO =="VALIDA")		$status="VALIDADO"; else if($reg->MOVIMIENTO =="RETIRA")	$status="RETIRADO"; //else $status=$reg->MOVIMIENTO;
			?>
			<tr>
				<td align="center"><?=$i;?></td>
				<td align="center"><?=$reg->IDEXPEDIENTE;?></td>
				<td align="center"><?=$reg->IDASISTENCIA;?></td>
				<td align="center"><?=$reg->IDSUPERVISOR;?></td>
				<td align="center"><?=$reg->IDETAPA;?></td>
				<td align="center"><?=$reg->FECHAREGISTRO;?></td>
				<td align="center"><?=$reg->ORIGEN;?></td>
				<td align="center"><?=$status;?></td>
				<td><?=$reg->NOMBRE;?></td>
				<td><a href="#" title="Realizar Descargo" onClick="presentar_formulario('','descargo.php','<?=_("CIERRE LA VENTANA ANTERIOR")?>','<?=_("INFORMACION DE DESCARGOS")?>','700','340','true','','','','<?=$reg->IDDEFICIENCIA_CORRELATIVO?>','<?=$reg->NOMBRE?>','','','mac_os_x')">DESCARGOS</a></td>
			</tr>
			<?
					$status="";
				}
			?>		
			</tbody>    
		</table>
        
	        <div id="tablefooter">
				<div id="tablenav">
					<div>
						<img src="/librerias/tinytablev3.0/images/first.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)"/>
						<img src="/librerias/tinytablev3.0/images/previous.gif" width="16" height="16" alt="First Page" onclick="sorter.move(-1)"/>
						<img src="/librerias/tinytablev3.0/images/next.gif" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
						<img src="/librerias/tinytablev3.0/images/last.gif" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)"/>
					</div>
					<div>
						<select id="pagedropdown"></select>
					</div>
					<div>
						<a href="javascript:sorter.showall()">Ver Todo</a>
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
						<span>Entrada por P&oacute;gina</span>
					</div>
					<div class="page">P&aacute;gina <span id="currentpage"></span> de <span id="totalpages"></span></div>
				</div>
			</div>
		</div>		
		 
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
			sortcolumn:5,
			sortdir:1,
			//sum:[8],
			//avg:[6,7,8,9],
			//columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
			init:true
		});
	</script>

</body>
</html>