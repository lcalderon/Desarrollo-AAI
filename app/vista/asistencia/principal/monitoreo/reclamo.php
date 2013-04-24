<?php

	include_once('../../../../modelo/clase_mysqli.inc.php');
	include_once("../../../../vista/login/Auth.class.php");
	include_once("../../../includes/arreglos.php");
	
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
	<script type="text/javascript" src="../../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	  

	<style type="text/css">
	<!--
	body,td,th {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 10px;
	}
	-->
	</style>
	
	<script language='javascript'>		
 
		function EnviarReclamo(){
		
		  if(document.getElementById('cmbasignacionc').value ==""){
				alert("ASIGNE EL AREA RESPONSABLE DEL CASO.");
                document.getElementById('cmbasignacionc').focus();
				 return (false);
				
		   } else if(document.getElementById('txtacomentario').value==""){
				alert("INGRESE LA OBSERVACION.");
                document.getElementById('txtacomentario').focus();
                return (false);
			} else{
			
				document.getElementById('frmreclamo').action='grabarreclamo.php';
				document.getElementById('frmreclamo').submit();
			}		
		}
		   
		function cerrar(){
		
			parent.win.close();	
			return;
			
		}
	</script>	
 </head>
 <body>  
 <form name='frmreclamo' id='frmreclamo' method='POST'>
 <input type='hidden' name='id_expediente' value='<?=$_GET['idexpediente']?>'>
 <input type='hidden' name='id_asistencia' value='<?=$_GET['idasistencia']?>'>
 
 <input name="txtmotllamada" type="hidden" id="txtmotllamada" value="QUEJASRECLAMO">
	<table width="60%" border="0" cellpadding="1" cellspacing="1" bgcolor="#B1C3D9" style="border:1px solid #999999">		
		<tr>
			<td width="257"><?=_("MOTIVO DE RECLAMO:") ;?></td>
			<td width="322"><?=_(" ASIGACION CASO:") ;?></td>
		</tr>
		<tr>
			<td>
				<?
					$sql="select IDDETMOTIVOLLAMADA,DESCRIPCION from $con->catalogo.catalogo_detallemotivollamada  where MOTIVOLLAMADA='QUEJASRECLAMO' AND ACTIVO=1 order by DESCRIPCION";
					$con->cmbselectdata($sql,"cmbopciones","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
				?>
			</td>
			<td><?
				$sql="select IDGRUPO,NOMBRE from $con->catalogo.catalogo_grupo order by NOMBRE";
				$con->cmbselectdata("select IDGRUPO,NOMBRE from $con->catalogo.catalogo_grupo order by NOMBRE","cmbasignacionc","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","","SELECCIONE");	
					
			?></td>
		</tr>
		<tr>
			<td height="2">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td height="2"><?=_("PROCEDENCIA Y MEDIO DE GESTION:") ;?></td>
			<td><?=_("OBSERVACION:") ;?></td>
		</tr>
		<tr>
			<td height="21">
				<?					 
					$con->cmb_array("cmbprocedencia",$procedencia_mediogestion,"","class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","1","","");
				?>
			</td>
		  <td rowspan="2"><textarea name="txtacomentario" id="txtacomentario" cols="55" rows="3" style="text-transform:uppercase;" class="classtexto"  onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"></textarea></td>
		</tr>    
		<tr>
			<td height="2">&nbsp;</td>
		</tr>
		<tr>
			<td height="2" colspan="2"></td>
		</tr>
		<tr>
			<td height="2">
			  <div align="right">
				<input type="button" name="btncerrar" id="btncerrar" value="<?=_("CANCELAR") ;?>"  style="font-size:10px;" onClick="cerrar()"/>
			  </div></td>
			<td height="2"><input type="button" name="btnaceptar" id="btnaceptar" value="<?=_("GUARDAR RECLAMO") ;?>"  style="font-weight:bold;font-size:10px;" onclick='EnviarReclamo();'/></td>
		</tr>
	</table>

</form>
 </body>
</html>