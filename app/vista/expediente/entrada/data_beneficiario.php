<?php

	session_start(); 
	
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../vista/login/Auth.class.php");
	
	$con= new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
 	Auth::required();
	
	$Sql="SELECT
			catalogo_programa.NOMBRE    AS programa,
			  catalogo_cuenta.NOMBRE    AS cuenta,
			  catalogo_afiliado.IDAFILIADO,
			  catalogo_afiliado_beneficiario.IDBENEFICIARIO,
			  catalogo_afiliado_beneficiario.IDDOCUMENTO,
			  CONCAT(catalogo_afiliado_beneficiario.APPATERNO,' ',catalogo_afiliado_beneficiario.APMATERNO,', ',catalogo_afiliado_beneficiario.NOMBRE) AS nombres,
			  catalogo_afiliado_beneficiario.FECHAMOD
			FROM $con->catalogo.catalogo_afiliado_beneficiario
			  INNER JOIN $con->catalogo.catalogo_afiliado
				ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_beneficiario.IDAFILIADO
			  INNER JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
			  INNER JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = catalogo_programa.IDCUENTA
			  LEFT JOIN $con->catalogo.catalogo_afiliado_beneficiario_telefono
				ON catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO = catalogo_afiliado_beneficiario.IDBENEFICIARIO
			WHERE catalogo_afiliado_beneficiario.IDAFILIADO=".$_GET["cod_afiliado"]." AND catalogo_afiliado_beneficiario.ACTIVO=1
			GROUP BY catalogo_afiliado_beneficiario.IDBENEFICIARIO ";
 
	$result=$con->query($Sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>American Assist</title>
	<style type="text/css">
		<!--
		.style3 {color: #4E4E4E; font-weight: bold; }
		-->
		<!--
		body {
			background-color: #FBFBFB;
		 
		}
		-->
	</style>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
 	
    <style type="text/css">
		<!--
		.style4 {
			color: #FFFFFF;
			font-weight: bold;
		}
		
		.style5 {font-weight: bold}
		-->
    </style>
</head>
<body>
	<h2 class="Box"><?=_("BENEFICIARIOS") ;?></h2>
	<table width="100%" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #E0E0E0">
		<tr>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("ID") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("NOMBRE BENEFICIARIO") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("FECHAREGISTRO") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("TELEFONO1") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("TELEFONO2") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("TELEFONO3") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("TELEFONO4") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("N&ordm;DOCUMENTO") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("CUENTA") ;?></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><div align="center"><?=_("PROGRAMA") ;?></div></td>
			<td bgcolor="#454545" ><div align="center"></div></td>
		</tr>	
		 <?
			
			if($_GET["cod_afiliado"]){
				while($reg = $result->fetch_object()){
					if($c%2==0) $fondo="#c7c7c7"; else $fondo='#F9F9F9';
		?>
		<tr bgcolor="<?=$fondo;?>" style="cursor:pointer" onClick="window.close();window.opener.recargar_contacto('<?=$reg->IDBENEFICIARIO;?>');" title="<?=$reg->nombres;?>" >
			<td bgcolor="#a7a7a7"><div align="center"><strong><?=$reg->IDBENEFICIARIO;?></strong></div></td>
			<td><?=$reg->nombres;?></td>			
			<td width="15%"><?=$reg->FECHAMOD;?></td>			
			 <?
				$ii=0;
				$sql_ben_tel="SELECT
					  catalogo_afiliado_beneficiario_telefono.CODIGOAREA,
					  catalogo_afiliado_beneficiario_telefono.IDTIPOTELEFONO,
					  catalogo_afiliado_beneficiario_telefono.NUMEROTELEFONO,
					  catalogo_afiliado_beneficiario_telefono.EXTENSION,
					  catalogo_afiliado_beneficiario_telefono.IDTSP
					FROM $con->catalogo.catalogo_afiliado_beneficiario_telefono
					  INNER JOIN $con->catalogo.catalogo_afiliado_beneficiario
						ON catalogo_afiliado_beneficiario.IDBENEFICIARIO = catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO 
					WHERE catalogo_afiliado_beneficiario_telefono.IDBENEFICIARIO ='".$reg->IDBENEFICIARIO."'
					ORDER BY catalogo_afiliado_beneficiario_telefono.PRIORIDAD
					LIMIT 4";
		
				$resultel=$con->query($sql_ben_tel);				
				while($row = $resultel->fetch_object()){
					$ii=$ii+1;
					$telefono[$ii]=$row->NUMEROTELEFONO;
				}

				$ii=0;
				for($i=1;$i<=4;$i++){
			?>		
			<td><?=$telefono[$i];?></td>
			<?		
					$telefono[$i]="";
				}
			?>
			<td><?=$reg->IDDOCUMENTO;?></td>
			<td><?=$reg->cuenta;?></td>
			<td><?=$reg->programa;?></td>
			<td><input type="button" name="btngestionar" id="btngestionar" value="<?=_("ASIGNAR") ;?>" style="font-weight:bold;font-size:9px;" onClick="window.close();window.opener.recargar_contacto('<?=$reg->IDBENEFICIARIO;?>')" title="Asignar beneficiario"/></td> 
		</tr>	
	<?
				 $c=$c+1;
				 $stylo="";
				}
			}
	?>
	</table>
</body>
</html>