<?php

	include_once('../../../modelo/clase_mysqli.inc.php');

		
	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	$result=$con->query("SELECT catalogo_programa_conformidad_log.STATUSCONFIRMA,catalogo_programa_conformidad_log.FECHAMOD,catalogo_programa_conformidad_log.MOTIVO,CONCAT(catalogo_usuario.APELLIDOS,',',catalogo_usuario.NOMBRES) AS usuario FROM catalogo_programa_conformidad_log INNER JOIN catalogo_usuario ON catalogo_usuario.IDUSUARIO=catalogo_programa_conformidad_log.IDUSUARIO WHERE catalogo_programa_conformidad_log.IDPROGRAMA='".$_REQUEST["idprograma"]."' and catalogo_programa_conformidad_log.CLAVE='".$_REQUEST["clave"]."' ORDER BY catalogo_programa_conformidad_log.FECHAMOD desc ");

?>
<p><br></p>
LOG
<table width="500" border="0" cellpadding="1" cellspacing="1" class="catalogos">
  <tr>
    <td bgcolor="#333333" style="color:#FFFFFF"><span class="style1">USUARIO</span></td>
    <td bgcolor="#333333" style="color:#FFFFFF"><div align="center"><span class="style1">STATUS</span></div></td>
    <td bgcolor="#333333" style="color:#FFFFFF"><span class="style1">FECHA</span></td>
    <td bgcolor="#333333" style="color:#FFFFFF"><span class="style1">MOTIVO</span></td>
  </tr>
<?
	while($row = $result->fetch_object())
	 {		
?>	  
  <tr>
    <td bgcolor="#ECE9D8" style="text-align:left"><?=$row->usuario; ?></td>
    <td bgcolor="#ECE9D8" style="text-align:left"><?=$row->STATUSCONFIRMA; ?></td>
    <td bgcolor="#ECE9D8"><?=$row->FECHAMOD; ?></td>
    <td bgcolor="#ECE9D8" style="text-align:left"><?=$row->MOTIVO; ?></td>
  </tr>
  
<?
	}		
?>	    
</table>