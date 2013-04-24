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
	$rscoor=$con->query("SELECT IDCOORDINADOR FROM $con->temporal.monitoreo_servicio_programado WHERE TIPO='COORD'  ORDER BY IDCOORDINADOR");
	while($regcoor = $rscoor->fetch_object())	$rowcoor[]=$regcoor->IDCOORDINADOR;

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
 
	$Sql_coordinador="SELECT DISTINCT
					  catalogo_usuario.IDUSUARIO,
					  catalogo_usuario.ACTIVO,
					  CONCAT(catalogo_usuario.APELLIDOS,', ',catalogo_usuario.NOMBRES) AS USUARIOS
					FROM $con->temporal.grupo_usuario
					  INNER JOIN $con->catalogo.catalogo_usuario
						ON catalogo_usuario.IDUSUARIO = grupo_usuario.IDUSUARIO
					LEFT JOIN $con->temporal.monitoreo_servicio_programado
						ON monitoreo_servicio_programado.IDCOORDINADOR = catalogo_usuario.IDUSUARIO						
					WHERE grupo_usuario.IDGRUPO IN('CORD','SUCA') 
						AND catalogo_usuario.ACTIVO=1 
						AND (catalogo_usuario.APELLIDOS LIKE '%".$_GET['nombrecoord']."%'
						OR catalogo_usuario.NOMBRES LIKE '%".$_GET['nombrecoord']."%')					
					ORDER BY monitoreo_servicio_programado.IDCOORDINADOR DESC   LIMIT $RegistrosAEmpezar, $RegistrosAMostrar";

									
	$resultado=$con->query($Sql_coordinador);

	echo "<table border='1' style='bborder:1px #333333 dashed;border-collapse:collapse'> 
		<tr bgcolor='#BCB9A7'>
			<td style='font-weight:bold;text-align:right' colspan='5'><input type='text' name='txtnombre' id='txtnombre' size='50' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' value='".$_GET['nombrecoord']."' /><input type='button' name='btnbuscar' id='btnbuscar' value='Buscar' onclick='buscarProveedor()'/></td>
		</tr> 		
		<tr bgcolor='#BCB9A7'>
			<td style='width:7%'></td>
			<td style='font-weight:bold'>CVEUSUARIO</td>
			<td style='font-weight:bold'>NOMBRE USUARIO</td>
			<td style='font-weight:bold'>STATUS</td>
		</tr>";
		
	while($mostrarFila = $resultado->fetch_object()){

		$status=($mostrarFila->ACTIVO)?"ACTIVO":"INACTIVO";		
		if(in_array($mostrarFila->IDUSUARIO,$rowcoor))	$marcar="checked"; else $marcar="";
 	
		if($c%2==0) $color="#E9E9E9"; else $color="#F4F4F4";
		echo "<tr bgcolor='$color'>";
		echo "<td><input type='checkbox' name='cbkcoordinador' id='cbkcoordinador$c' $marcar onclick=\"grabar_proveedor('$mostrarFila->IDUSUARIO',this.id)\" /></td>";
		echo "<td>".$mostrarFila->IDUSUARIO."</td>";
		echo "<td style='text-align:left'>".$mostrarFila->USUARIOS."</td>";
		echo "<td style='text-align:left'>".$status."</td>";
		echo "</tr>";
		
		$c++;
	}
	
//******--------determinar las páginas---------******//
	$Sql_TOTcoordinador="SELECT DISTINCT
					  catalogo_usuario.IDUSUARIO,
					  catalogo_usuario.ACTIVO,
					  CONCAT(catalogo_usuario.APELLIDOS,', ',catalogo_usuario.NOMBRES) AS USUARIOS
					FROM $con->temporal.grupo_usuario
					  INNER JOIN $con->catalogo.catalogo_usuario
						ON catalogo_usuario.IDUSUARIO = grupo_usuario.IDUSUARIO
					LEFT JOIN $con->temporal.monitoreo_servicio_programado
						ON monitoreo_servicio_programado.IDCOORDINADOR = catalogo_usuario.IDUSUARIO						
					WHERE grupo_usuario.IDGRUPO IN('CORD','SUCA') 
						AND catalogo_usuario.ACTIVO=1 
						AND (catalogo_usuario.APELLIDOS LIKE '%".$_GET['nombrecoord']."%'
						OR catalogo_usuario.NOMBRES LIKE '%".$_GET['nombrecoord']."%')	";

					
	$NroReg=$con->query($Sql_TOTcoordinador);
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
