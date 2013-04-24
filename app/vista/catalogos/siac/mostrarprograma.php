<?php

	session_start();
	include_once("../../../modelo/clase_mysqli.inc.php");
		
	$con = new DB_mysqli();		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
	if($_POST["opc"]!="")	$sql="SELECT IDPROGRAMA,NOMBRE from $con->catalogo.catalogo_programa where ACTIVO=1 AND IDCUENTA='".$_POST["opc"]."' ";
	$nombreplan=_("SELECCIONE");
 
	if($_POST["opc"]!=""){			
		$con->cmbselectdata($sql,"cmbprograma",$_POST["opc1"],"onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","",$nombreplan); 
	} else{
		echo "<select name='cmbprograma'  id='cmbprograma' class='classtexto' onfocus='coloronFocus(this);' onblur='colorOffFocus(this);'>
				<option value=''>$nombreplan</option>
			  </select>";
	}
?>
 
 