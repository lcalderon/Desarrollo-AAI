<?
	session_start();  
		
	include_once('../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$ubigeo[IDAFILIADO]=$_POST[idafiliado];
	$ubigeo[CVEPAIS]=$_POST[CVEPAIS];
	$ubigeo[CVEENTIDAD1]=($_POST[CVEENTIDAD1]=='')?'0':$_POST[CVEENTIDAD1];
	$ubigeo[CVEENTIDAD2]=($_POST[CVEENTIDAD2]=='')?'0':$_POST[CVEENTIDAD2];
	$ubigeo[CVEENTIDAD3]=($_POST[CVEENTIDAD3]=='')?'0':$_POST[CVEENTIDAD3];
	$ubigeo[CVEENTIDAD4]=($_POST[CVEENTIDAD4]=='')?'0':$_POST[CVEENTIDAD4];
	$ubigeo[CVEENTIDAD5]=($_POST[CVEENTIDAD5]=='')?'0':$_POST[CVEENTIDAD5];
	$ubigeo[CVEENTIDAD6]=($_POST[CVEENTIDAD6]=='')?'0':$_POST[CVEENTIDAD6];
	$ubigeo[CVEENTIDAD7]=($_POST[CVEENTIDAD7]=='')?'0':$_POST[CVEENTIDAD7];
	$ubigeo[DESCRIPCION]=$_POST[DESCRIPCION];
	$ubigeo[DIRECCION]=$_POST[DIRECCION];
	$ubigeo[NUMERO]=$_POST[NUMERO];
	$ubigeo[LATITUD]=$_POST[LATITUD];
	$ubigeo[LONGITUD]=$_POST[LONGITUD];
	$ubigeo[REFERENCIA1]=$_POST[REFERENCIA1];
	$ubigeo[REFERENCIA2]=$_POST[REFERENCIA2];
	$ubigeo[IDUSUARIOMOD]=$_SESSION[user];

	if($_POST["iddomicilio"])
	 {
		$respuesta=$con->update("$con->catalogo.catalogo_afiliado_persona_domicilio_ubigeo",$ubigeo,"WHERE ID='".$_POST["iddomicilio"]."' ");
	 }
	else
	 {
		$ubigeo[ID]="";
		$ubigeo[IDUSUARIOCREACION]=$_SESSION[user];
		$ubigeo[FECHACREACION]=date("Y-m-d H:i:s");
	
		$respuesta=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_domicilio_ubigeo",$ubigeo);

		$data=$con->reg_id();
		
		$row2["ID"]=$data;
		$row2["IDAFILIADO"]=$_POST["idafiliado"];
		$row2["IDUSUARIOMOD"]=$_SESSION["user"];		
		if($data)	$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_domicilio_ubigeo_log",$row2);
					
	 }
  
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='domicilio_dafiliado.php?idafiliado=".$_POST['idafiliado']."' ";
    echo "</script>"; 
?>