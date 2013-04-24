<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);
	
	if($_POST["idcanal"]!="")
	 {
		$combo=$con->cmbselectdata("SELECT IDCANALCUENTA,DESCRIPCION FROM catalogo_canal_venta_cuenta WHERE IDCANAL=".$_POST["idcanal"]." ORDER BY DESCRIPCION","cmbcanalcuenta","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
	 }
	else	 
	 {	
		echo "<select name='cmbcanalcuenta' disabled class='classtexto' onfocus='coloronFocus(this);' onblur='colorOffFocus(this);'>
		<option value=''>S/D</option >
		</select>";
	 }
 
?>