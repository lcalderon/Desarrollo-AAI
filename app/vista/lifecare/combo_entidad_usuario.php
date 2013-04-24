<?
	include_once("../../modelo/clase_mysqli.inc.php");
	
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}	
	
	if($_POST["opcion"] =="2_usuario"){
		$sql="SELECT CVEENTIDAD2_LIF,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE CVEENTIDAD1_LIF='".$_POST["cveentidad_valor"]."' AND CVEENTIDAD2_LIF >0 AND CVEENTIDAD3_LIF=0 ";
	} else if($_POST["opcion"] =="3_usuario"){
		$sql="SELECT CVEENTIDAD3_LIF,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE CVEENTIDAD1_LIF='".$_POST["cveentidad_valor"]."' AND CVEENTIDAD2_LIF ='".$_POST["cveentidad_valor2"]."' AND CVEENTIDAD3_LIF >0 ";
	}
	
	if($_POST["opcion"] =="2_viviendasec"){
		$sql="SELECT CVEENTIDAD2_LIF,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE CVEENTIDAD1_LIF='".$_POST["cveentidad_valor"]."' AND CVEENTIDAD2_LIF >0 AND CVEENTIDAD3_LIF=0 ";
	} else if($_POST["opcion"] =="3_viviendasec"){
		$sql="SELECT CVEENTIDAD3_LIF,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE CVEENTIDAD1_LIF='".$_POST["cveentidad_valor"]."' AND CVEENTIDAD2_LIF ='".$_POST["cveentidad_valor2"]."' AND CVEENTIDAD3_LIF >0 ";
	}
	
 	$nombrecombo="cveentidad".$_POST["opcion"];
	if($_POST["opcion"] =="2_usuario")	$funcion="onChange=recargar_entidad($('cveentidad1_usuario').value,this.value,'div-entidad03','3_usuario');";
	if($_POST["opcion"] =="2_viviendasec")	$funcion="onChange=recargar_entidad($('cveentidad1_viviendasec').value,this.value,'div-entidad03sec','3_viviendasec');";
	
	//if($_POST["cveentidad_valor"]){			
		$con->cmbselectdata($sql,$nombrecombo,"","$funcion onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","","TODOS",0); 
	// } else{
		
		// echo "<select name='cveentidad'  id='cveentidad' class='classtexto' onfocus='coloronFocus(this);' onblur='colorOffFocus(this);'>
				// <option value=''>TODOS</option>
			  // </select>";
	// }
?>