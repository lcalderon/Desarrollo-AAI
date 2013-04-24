<?php
	session_start(); 
 
	include_once("../../../../modelo/clase_lang.inc.php");
	include_once("../../../../modelo/clase_mysqli.inc.php");
	include_once("../../../../vista/login/Auth.class.php");
	
	$con= new DB_mysqli();
	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
	Auth::required();

//obtner proveedores grabados
	$rsprov=$con->query("SELECT IDPROVEEDOR FROM $con->temporal.monitoreo_servicio_programado_xusuario  WHERE TIPO='HOGAR' AND IDCOORDINADOR='".$_SESSION["user"]."' ORDER BY IDPROVEEDOR");
	while($regprov = $rsprov->fetch_object())	$rowprov[]=$regprov->IDPROVEEDOR;

//obtner servicios grabados	
	$rsserv=$con->query("SELECT IDSERVICIOS FROM $con->temporal.monitoreo_servicio_programado_xusuario  WHERE TIPO='HOGAR' AND IDCOORDINADOR='".$_SESSION["user"]."' LIMIT 18");
	$row_serv = $rsserv->fetch_object(); 
	$rowservi = explode(",",$row_serv->IDSERVICIOS); 

	$RegistrosAMostrar=10;
 
//estos valores los recibo por GET
	if(isset($_GET['pag'])){
		$RegistrosAEmpezar=($_GET['pag']-1)*$RegistrosAMostrar;
		$PagAct=$_GET['pag'];
//caso contrario los iniciamos
	}else{
		$RegistrosAEmpezar=0;
		$PagAct=1;
		
	}
	
	$SqlProveedor="SELECT
				  catalogo_proveedor.IDPROVEEDOR,
				  catalogo_proveedor.NOMBRECOMERCIAL,
				  catalogo_proveedor.NOMBREFISCAL,
				  catalogo_proveedor.INTERNO
				FROM $con->catalogo.catalogo_proveedor
				  INNER JOIN $con->catalogo.catalogo_proveedor_servicio
					ON catalogo_proveedor_servicio.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				  INNER JOIN $con->catalogo.catalogo_servicio
					ON catalogo_servicio.IDSERVICIO = catalogo_proveedor_servicio.IDSERVICIO
				  LEFT JOIN $con->temporal.monitoreo_servicio_programado_xusuario
					ON monitoreo_servicio_programado_xusuario.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				WHERE catalogo_servicio.IDFAMILIA = 3
					AND catalogo_proveedor.ACTIVO = 1
					AND (catalogo_proveedor.NOMBRECOMERCIAL LIKE '%".$_GET['nombreprov']."%'
					OR catalogo_proveedor.NOMBREFISCAL LIKE '%".$_GET['nombreprov']."%')
				GROUP BY catalogo_proveedor.IDPROVEEDOR 
				ORDER BY monitoreo_servicio_programado_xusuario.IDPROVEEDOR DESC   LIMIT $RegistrosAEmpezar, $RegistrosAMostrar";
 
	$resultado=$con->query($SqlProveedor);
	
	
	$rs_servicios=$con->query("SELECT IDSERVICIO,DESCRIPCION FROM $con->catalogo.catalogo_servicio WHERE IDFAMILIA=3 ORDER BY DESCRIPCION");

	


//******--------determinar las páginas---------******//
	$SqlProv="SELECT
				  catalogo_proveedor.IDPROVEEDOR,
				  catalogo_proveedor.NOMBRECOMERCIAL,
				  catalogo_proveedor.NOMBREFISCAL,
				  catalogo_proveedor.INTERNO
				FROM catalogo_proveedor
				  INNER JOIN catalogo_proveedor_servicio
					ON catalogo_proveedor_servicio.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				  INNER JOIN catalogo_servicio
					ON catalogo_servicio.IDSERVICIO = catalogo_proveedor_servicio.IDSERVICIO
				WHERE catalogo_servicio.IDFAMILIA = 3
					AND catalogo_proveedor.ACTIVO = 1
					AND (catalogo_proveedor.NOMBRECOMERCIAL LIKE '%".$_GET['nombreprov']."%'
					OR catalogo_proveedor.NOMBREFISCAL LIKE '%".$_GET['nombreprov']."%')
				GROUP BY catalogo_proveedor.IDPROVEEDOR ORDER BY catalogo_proveedor.NOMBRECOMERCIAL";
				
	$NroReg=$con->query($SqlProv);
	$NroRegistros=$NroReg->num_rows;

	$PagAnt=$PagAct-1;
	$PagSig=$PagAct+1;
	$PagUlt=$NroRegistros/$RegistrosAMostrar;

//verificamos residuo para ver si llevará decimales
	$Res=$NroRegistros%$RegistrosAMostrar;
// si hay residuo usamos funcion floor para que me
// devuelva la parte entera, SIN REDONDEAR, y le sumamos
// una unidad para obtener la ultima pagina
	if($Res>0) $PagUlt=floor($PagUlt)+1;

//desplazamiento



?>
<html>
 <head>
	<title>Configurar</title>
	
	<script type="text/javascript">	
		//cambio de radio
		function cambio_radio(valor){

			if(valor ==2){			
				
				$('div-proveedores').style.display='none';
				$('div-servicios').style.display='block';
				$('radiochange4').checked=true;	

			} else if(valor =3){
				
				$('div-servicios').style.display='none';
				$('div-proveedores').style.display='block';
				$('radiochange1').checked=true;				

			}
			
		}
	
	</script>
 </head>
 <body>
 
	<div id="div-proveedores">
 	<table width="100%" border='1' style='bborder:1px #333333 dashed;border-collapse:collapse' align="left">
		<tr bgcolor='#004a00'>
			<td align="left" colspan="2" style="color:#FFFFFF"><input type="radio" name="radiochange" id="radiochange1" value="1" checked>PROVEEDORES</td>
			<td colspan="3" align="left" style="color:#FFFFFF"><input type="radio" name="radiochange" id="radiochange2" value="2" onclick="cambio_radio(this.value)">SERVICIOS</td>
		</tr>	
		<tr bgcolor='#6d8318'>
			<td style='font-weight:bold;text-align:right' colspan='5'><input type='text' name='txtnombre' id='txtnombre' size='50' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' value='<?=$_GET['nombreprov']?>' /><input type='button' name='btnbuscar' id='btnbuscar' value='Buscar' onclick='buscarProveedor()'/></td>
		</tr> 		
		<tr bgcolor='#6d8318'>
			<td style='width:7%'></td>
			<td style='font-weight:bold'>ID</td>
			<td style='font-weight:bold'>NOMBRE COMERCIAL</td>
			<td style='font-weight:bold'>NOMBRE FISCAL</td>
			<td style='font-weight:bold'>TIPO</td>
		</tr>
		<?
			while($mostrarFila = $resultado->fetch_object()){
				$forma=($mostrarFila->INTERNO)?"INTERNO":"EXTERNO";
				
				if(in_array($mostrarFila->IDPROVEEDOR,$rowprov))	$marcar="checked"; else $marcar="";
				
				if($c%2==0) $color="#E9E9E9"; else $color="#F4F4F4";
		?>
			<tr bgcolor='<?=$color?>'>
				<td><input type='checkbox' name='cbkproveedores' id='cbkproveedores<?=$c?>' <?=$marcar?> onclick="grabar_proveedor('<?=$mostrarFila->IDPROVEEDOR?>',this.id)"/></td>
				<td><?=$mostrarFila->IDPROVEEDOR?></td>
				<td style='text-align:left'><?=$mostrarFila->NOMBRECOMERCIAL?></td>
				<td style='text-align:left'><?=$mostrarFila->NOMBREFISCAL?></td>
				<td><?=$forma?></td>
			</tr>
		<?	
				$c++;
			}	
		?>			
		<tr bgcolor='#6d8318'>
			<td style='font-weight:bold' colspan='5'><a onclick="paginar('1')">Primero</a>
			<?  
				if($PagAct>1) echo "<a onclick=\"paginar('$PagAnt')\">Anterior</a> ";
				echo "<strong>Pagina ".$PagAct."/".$PagUlt."</strong>";
				if($PagAct<$PagUlt)  echo " <a onclick=\"paginar('$PagSig')\">Siguiente</a> ";
				if($NroRegistros >0) echo " <a onclick=\"paginar('$PagUlt')\">Ultimo</a>"; else echo " <a>Ultimo</a>";
			?>
			</td>
		</tr>
	
	</table> 
	</div>	

	
	<div id="div-servicios" style="display:none">
 	<table width="100%" border='1' style='bborder:1px #333333 dashed;border-collapse:collapse' align="left">
		<tr bgcolor='#004a00'>
			<td align="left" colspan="2" style="color:#FFFFFF"><input type="radio" name="radiochange2" id="radiochange3" value="3" onclick="cambio_radio(this.value)" >PROVEEDORES</td>
			<td colspan="3" align="left" style="color:#FFFFFF"><input type="radio" name="radiochange2" id="radiochange4" value="4">SERVICIOS</td>
		</tr>	
		<tr bgcolor='#6d8318'>
			<td style='width:7%'></td>
			<td style='font-weight:bold'>ID</td>
			<td style='font-weight:bold'>DESCRIPCION</td>
		</tr>
		<?
			while($row = $rs_servicios->fetch_object()){
				
				if(in_array($row->IDSERVICIO,$rowservi))	$marcarser="checked"; else $marcarser="";				
				if($cc%2==0) $color="#E9E9E9"; else $color="#F4F4F4";
		?>
			<tr bgcolor='<?=$color?>'>
				<td width="07px"><input type='checkbox' name='cbkservicios[]' id='cbkservicios[]' <?=$marcarser?> value="<?=$row->IDSERVICIO?>" onclick="grabar_servicios()"/></td>
				<td><?=$row->IDSERVICIO?></td>
				<td style='text-align:left'><?=$row->DESCRIPCION?></td>
			</tr>
		<?	
				$cc++;
			}
		?>	
	</table> 
	</div>
 </body>
</html>
