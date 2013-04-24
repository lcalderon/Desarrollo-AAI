<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');

		
	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();

	$rsencargado=$con->query("SELECT catalogo_usuario.IDUSUARIO,SUBSTRING($con->temporal.seguridad_modulosxusuario.IDMODULO,19,10) AS tipo,CONCAT(APELLIDOS,',',NOMBRES) AS nombreusu,EMAIL FROM $con->temporal.seguridad_modulosxusuario INNER JOIN catalogo_usuario ON catalogo_usuario.IDUSUARIO=$con->temporal.seguridad_modulosxusuario.IDUSUARIO  WHERE IDMODULO IN('PROGRAMAS_AUTORIZASISTEMAS','PROGRAMAS_AUTORIZACOMERCIAL','PROGRAMAS_AUTORIZAFINANZAS','PROGRAMAS_AUTORIZACALIDAD') ");
 
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>American Assist</title>
<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
<link href=".../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
-->
</style>
</head>
<body>
RECHAZOS Y CONFIRMACIONES
  <table width="500" border="0" cellpadding="1" cellspacing="1" class="catalogos">
    <tr>
      <td bgcolor="#333333" style="color:#FFFFFF"><div align="center">CARGO</div></td>
      <td bgcolor="#333333" style="color:#FFFFFF"><div align="center"><span class="style1">USUARIO</span></div></td>
      <td bgcolor="#333333" style="color:#FFFFFF"><div align="center">FECHAGESTION</div></td>
      <td bgcolor="#333333" style="color:#FFFFFF"><span class="style1">STATUS</span></td>
    </tr>
    <?
	while($rowe = $rsencargado->fetch_object())
	 {
		$statusconf=$con->consultation("SELECT STATUSCONFIRMA,FECHAMOD,CLAVE FROM catalogo_programa_conformidad where IDPROGRAMA='".$_GET["idprograma"]."' and NOMBRE='".$rowe->tipo."'");
?>
    <tr>
      <td bgcolor="#7EA1E5" style="text-align:left"><?=$rowe->tipo; ?></td>
      <td bgcolor="#7EA1E5" style="text-align:left"><?=$rowe->nombreusu; ?>
   	  <input type="button" name="btngestionar" id="btngestionar" value="<?=_("ver") ;?>" style="font-weight:bold;font-size:9px;" onclick="logConformidad('<?=$_GET["idprograma"];?>','<?=$rowe->IDUSUARIO?>','<?=$statusconf[0][2]; ?>')" />	</td>
      <td bgcolor="#7EA1E5" style="text-align:left"><?=$statusconf[0][1]; ?></td>
      <td bgcolor="#7EA1E5"><?=$statusconf[0][0]; ?></td>
    </tr>
    <?
	}		
?>
  </table>

<div id="resultado"></div>

</body>
</html>

