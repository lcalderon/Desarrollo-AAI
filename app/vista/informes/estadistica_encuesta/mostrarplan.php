<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_lang.inc.php');		
		
	$con= new DB_mysqli();
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
		
	$sql="select IDPROGRAMA,NOMBRE from $con->catalogo.catalogo_programa where IDCUENTA='".$_POST["idcodigo"]."' ";
		
	if($_POST["idcodigo"]!="")
	 {			
		$con->cmbselectdata($sql,"cmbplan","","class='classtexto' onFocus='coloronFocus(this)' onBlur='colorOffFocus(this)'","",_("TODOS >>>"));		
	 }
	else
	 {
		echo "<select name='cmbplan'  id='cmbplan' class='classtexto' onfocus='coloronFocus(this);' onblur='colorOffFocus(this);'>
				<option value=''>TODOS >>></option>
			  </select>";
	 }
 
?>
 