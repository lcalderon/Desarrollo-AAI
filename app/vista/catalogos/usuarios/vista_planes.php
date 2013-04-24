<?php

	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");

	$con = new DB_mysqli();	
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
?>
<table  border="0" cellpadding="1" cellspacing="1" style="border:1px solid #333333">
<tr colspan="2">PLANES <?=$_POST["opc1"]?></tr>							

<? 										
$rsplanes=$con->query("SELECT IDPROGRAMA,NOMBRE from $con->catalogo.catalogo_programa WHERE IDCUENTA='".$_POST["idcodigo"]."' order by NOMBRE ");
  
while($reg = $rsplanes->fetch_object())
{											
	if(in_array($reg->IDCUENTA,$rowcuenta))	$valor="checked";
	if($c%2==0) $fondo='#CADCE3'; else $fondo='#F9F9F9';	
?>								
	  <tr bgcolor="<?=$fondo;?>">
		<td colspan="2"><input type="checkbox" name="chkplans[]" value="<?=$reg->IDPROGRAMA; ?>"><strong><?=$reg->NOMBRE; ?></strong></td>
	  </tr>
<?
}
?> 
</table>	