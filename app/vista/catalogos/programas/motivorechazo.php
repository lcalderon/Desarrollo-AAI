<?php
  
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_lang.inc.php');
		
	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);
	session_start();

	$verifica=$con->consultation("SELECT count(*) as cantidad from catalogo_programa_conformidad WHERE (STATUSCONFIRMA='APROBADO'  OR STATUSCONFIRMA='RECHAZO') and IDPROGRAMA='".$_GET["programa"]."' and CLAVE='".$_GET["codigo"]."'");
	if($verifica[0][0]==1)	die("EL PROGRAMA YA FUE GESTIONADO.");		
	
	if($_POST["bntaceptar"])
	 {
			$row["IDUSUARIO"]=$_SESSION["user"];
			$row["STATUSCONFIRMA"]="RECHAZO";
			$row["MOTIVO"]=strtoupper($_POST["txtcomentario"]);

			$respuesta=$con->update("catalogo_programa_conformidad",$row,"WHERE IDPROGRAMA='".$_POST["txtprograma"]."' and CLAVE='".$_POST["txtcodigo"]."'");
			
			if($respuesta)	die("SE REGISTRO EL MOTIVO...");	else echo "HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.";
	 }
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
		
		<script language="JavaScript">
		
			function validarCampo(variable){
			
				document.frmmotivo.txtcomentario.value=document.frmmotivo.txtcomentario.value.replace(/^\s*|\s*$/g,"");			
		
				if(document.frmmotivo.txtcomentario.value =='' ){
					alert('<?=_("INGRESE EL COMENTARIO ADECUADO.") ;?>');
					document.frmmotivo.txtcomentario.focus();
					return (false);
				}
				
				return (true);
			}
			
		</script>		
</head>
<body>
<form id="frmmotivo" name="frmmotivo" method="post" action="motivorechazo.php" onSubmit = "return validarCampo(this)">
    <input type="hidden" name="txtprograma" id="txtprograma" value="<?=$_GET["programa"] ;?>" />
    <input type="hidden" name="txtcodigo" id="txtcodigo" value="<?=$_GET["codigo"] ;?>" />
  <table width="200" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #333333">
    <tr>
      <td colspan="2" bgcolor="#999999"><strong>MOTIVO DE RECHAZO</strong></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td>COMENTARIO</td>
      <td rowspan="3"><label>
        <textarea name="txtcomentario" id="txtcomentario" cols="30" rows="3" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" class="classtexto" style="text-transform:uppercase;"></textarea>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p>
    <label>
    <input type="submit" name="bntaceptar" id="bntaceptar" value="ACEPTAR" />
    </label>
  </p>
</form>

</body>
</html>
