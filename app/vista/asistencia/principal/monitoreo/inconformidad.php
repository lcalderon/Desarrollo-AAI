<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start();
	Auth::required();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	  

	<style type="text/css">
	<!--
	body,td,th {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 10px;
	}
	-->
	</style>
	
	<script language='javascript'>	
		
 
		function EnviarInconformidad(){
		
			if(document.getElementById('txtaobservacion').value==""){
				alert("INGRESE LA OBSERVACION.");
                document.getElementById('txtaobservacion').focus();
                return (false);
			}
			else
			{				
				document.getElementById('frminconformidad').action='grabarinconformidad.php';
				document.getElementById('frminconformidad').submit();
			}		
		}
		   
		function cerrar(){
		
			parent.win.close();	
			return;
			
		}
	</script>
	
 </head>
 <body>  
 <form name='frminconformidad' id='frminconformidad' method='POST'>
 <input type='hidden' name='id_expediente' value='<?=$_GET['idexpediente']?>'>
 <input type='hidden' name='id_asistencia' value='<?=$_GET['idasistencia']?>'>
 
 <table width="60%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFCF9F" style="border:1px solid #999999">		
    <tr>
		<td width="257"><?=_("MOTIVO DE INCONFORMIDAD") ;?>:</td>
        <td width="322"><?=_(" ASIGACION CASO") ;?>:</td>
    </tr>
    <tr>
		<td>
		<?
				$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from $con->catalogo.catalogo_detallemotivollamada  where MOTIVOLLAMADA='INCONFORMIDAD' AND ACTIVO=1 order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?></td>
        <td><?
			$con->cmbselectdata("SELECT IDGRUPO,NOMBRE from $con->catalogo.grupo_usuario WHERE TIPO='INCONFORMIDAD' ORDER BY NOMBRE","cmbasignacionc","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","",_("NINGUNO"));			
				
		?></td>
    </tr>
    <tr>
      <td height="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="2"><?=_("OBSERVACION") ;?>:</td>
      <td><?=_("SOLUCION") ;?>:</td>
    </tr>
    <tr>
      <td><textarea name="txtaobservacion" id="txtaobservacion" cols="55" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onblur="colorOffFocus(this);"></textarea></td>
      <td><textarea name="txtasolucion" id="txtasolucion" cols="55" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></textarea></td>
    </tr>    
    
    <tr>
      <td height="2" colspan="2"></td>
    </tr>
    <tr>
		<td height="2">
		  <div align="right">
		    <input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onClick="cerrar()"/>
          </div></td>
        <td height="2"><input type="button" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR INCONFORMIDAD") ;?>"  style="font-weight:bold;font-size:10px;" onclick='EnviarInconformidad();'/></td>
    </tr>
</table>

</form>
 </body>
</html>