<?php

	session_start();

	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");
	include_once("../../modelo/afiliado/asistencias.class.php");
	include_once("../includes/arreglos.php");

	Auth::required();
	
	$con = new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	if(trim($_POST["cuenta"])!=""){

		$result=$con->query("SELECT IDPROVEEDOR,NOMBRECOMERCIAL FROM $con->catalogo.catalogo_proveedor WHERE NOMBRECOMERCIAL!=''  AND NOMBRECOMERCIAL LIKE '%".trim($_POST["cuenta"])."%' ORDER BY NOMBRECOMERCIAL");
?>
<table width="100%" cellpadding="1" cellspacing="1" border="1" style="border-collapse:collapse;font-size:10px">
<?	
	while($reg = $result->fetch_object()){
		$c++;
		
		if($c%2==0){
			$fondo='#000000';
			$color='#C6C6C7';
		} else{
			$fondo='#C6C6C7';
			$color='#000000';
		}
?>
	<tr bgcolor="<?=$fondo?>">
		<td style="color:<?=$color?>"><?=$reg->NOMBRECOMERCIAL?></td>
		<td style="color:<?=$color?>" align="center"><a href="#" style="color:<?=$color?>" onclick="asignar_proveedor('<?=$reg->IDPROVEEDOR?>','<?=$reg->NOMBRECOMERCIAL?>');document.getElementById('div-resultado').style.display='none'">Asignar</a></td>
	</tr>
<? } ?>
</table>
<? } ?>
