<?php

	 function validar_permisos($nombreaccess,$opc=0,$nombreaccessOpcional=""){
 
		if($nombreaccessOpcional !="") $nombreaccess=$nombreaccessOpcional;
	
		$db = new DB_mysqli();			
		$db->select_db($db->temporal);	
		 
		$result = $db->query("SELECT IDMODULO FROM seguridad_modulosxusuario WHERE IDUSUARIO = '".$_SESSION["user"]."'");
	
		while($row = $result->fetch_object())	$valoracc[$row->IDMODULO]=$row->IDMODULO;
		if($opc==0)
		 {
			if(in_array($nombreaccess, $valoracc))	return	true;
		 }
		else
		 {
			if(!in_array($nombreaccess, $valoracc))	die(_("*** ACCESO NO PERMITIDO. ***"));;
		 
		 }
	}

?>
