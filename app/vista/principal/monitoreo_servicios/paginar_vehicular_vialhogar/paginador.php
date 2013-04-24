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
	$rsprov=$con->query("SELECT IDPROVEEDOR FROM $con->temporal.monitoreo_servicio_programado WHERE TIPO='VIALHOGAR'  ORDER BY IDPROVEEDOR");
	while($regprov = $rsprov->fetch_object())	$rowprov[]=$regprov->IDPROVEEDOR;
	
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
				  LEFT JOIN $con->temporal.monitoreo_servicio_programado
					ON monitoreo_servicio_programado.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				WHERE catalogo_servicio.IDFAMILIA IN(1,3)
					AND catalogo_proveedor.ACTIVO = 1
					AND catalogo_proveedor_servicio.IDSERVICIO IN(1,8,13,14)
					AND (catalogo_proveedor.NOMBRECOMERCIAL LIKE '%".$_GET['nombreprov']."%'
					OR catalogo_proveedor.NOMBREFISCAL LIKE '%".$_GET['nombreprov']."%')
				GROUP BY catalogo_proveedor.IDPROVEEDOR 
				ORDER BY monitoreo_servicio_programado.IDPROVEEDOR DESC   LIMIT $RegistrosAEmpezar, $RegistrosAMostrar";
 
	$resultado=$con->query($SqlProveedor);

	echo "<table border='1' style='bborder:1px #333333 dashed;border-collapse:collapse'> 
		<tr bgcolor='#BCB9A7'>
			<td style='font-weight:bold;text-align:right' colspan='5'><input type='text' name='txtnombre' id='txtnombre' size='50' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' value='".$_GET['nombreprov']."' /><input type='button' name='btnbuscar' id='btnbuscar' value='Buscar' onclick='buscarProveedor()'/></td>
		</tr> 		
		<tr bgcolor='#BCB9A7'>
			<td style='width:7%'></td>
			<td style='font-weight:bold'>ID</td>
			<td style='font-weight:bold'>NOMBRE COMERCIAL</td>
			<td style='font-weight:bold'>NOMBRE FISCAL</td>
			<td style='font-weight:bold'>TIPO</td>
		</tr>";
		
	while($mostrarFila = $resultado->fetch_object()){
		$forma=($mostrarFila->INTERNO)?"INTERNO":"EXTERNO";
		
		if(in_array($mostrarFila->IDPROVEEDOR,$rowprov))	$marcar="checked"; else $marcar="";
		
		if($c%2==0) $color="#E9E9E9"; else $color="#F4F4F4";
		echo "<tr bgcolor='$color'>";
		echo "<td><input type='checkbox' name='cbkproveedores' id='cbkproveedores$c' $marcar onclick=\"grabar_proveedor('$mostrarFila->IDPROVEEDOR',this.id)\" /></td>";
		echo "<td>".$mostrarFila->IDPROVEEDOR."</td>";
		echo "<td style='text-align:left'>".$mostrarFila->NOMBRECOMERCIAL."</td>";
		echo "<td style='text-align:left'>".$mostrarFila->NOMBREFISCAL."</td>";
		echo "<td>".$forma."</td>";
		echo "</tr>";
		
		$c++;
	}
	
//******--------determinar las páginas---------******//
	$SqlProv="SELECT
				  catalogo_proveedor.IDPROVEEDOR,
				  catalogo_proveedor.NOMBRECOMERCIAL,
				  catalogo_proveedor.NOMBREFISCAL,
				  catalogo_proveedor.INTERNO
				FROM $con->catalogo.catalogo_proveedor
				  INNER JOIN $con->catalogo.catalogo_proveedor_servicio
					ON catalogo_proveedor_servicio.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				  INNER JOIN $con->catalogo.catalogo_servicio
					ON catalogo_servicio.IDSERVICIO = catalogo_proveedor_servicio.IDSERVICIO
				  LEFT JOIN $con->temporal.monitoreo_servicio_programado
					ON monitoreo_servicio_programado.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
				WHERE catalogo_servicio.IDFAMILIA IN(1,3)
					AND catalogo_proveedor.ACTIVO = 1
					AND catalogo_proveedor_servicio.IDSERVICIO IN(1,8,13,14)
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
	echo "<tr bgcolor='#BCB9A7'><td style='font-weight:bold' colspan='5'>";
	echo "<a onclick=\"paginar('1')\">Primero</a> ";
	if($PagAct>1) echo "<a onclick=\"paginar('$PagAnt')\">Anterior</a> ";
	echo "<strong>Pagina ".$PagAct."/".$PagUlt."</strong>";
	if($PagAct<$PagUlt)  echo " <a onclick=\"paginar('$PagSig')\">Siguiente</a> ";
	if($NroRegistros >0) echo " <a onclick=\"paginar('$PagUlt')\">Ultimo</a>"; else echo " <a>Ultimo</a>";
	echo "</td></tr>";
echo "</table>";

?>
