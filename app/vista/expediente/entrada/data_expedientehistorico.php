<?php
 	session_start();
	
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../../modelo/functions.php");	
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
	Auth::required($_SERVER['REQUEST_URI']);
	
	include("consultareincidencia.php");
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
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">
		<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
		<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">
 	<link rel="stylesheet" href="../../../../librerias/TinyAccordion/style.css" type="text/css" />
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
<h2 class="Box"><?=_("REINCIDENCIA DE EXPEDIENTE-ASISTENCIAS") ;?></h2>
<ul class="acc" id="acc"  style="width:100%">
	<li>
		<h3 style="width:97%"><?=_("REINCIDENCIAS CUENTA Y PLAN ACTUAL - CONCLUIDOS / EN PROCESO")." [".$result->num_rows."]";?></h3>
		<div class="acc-section" style="width:98%">
			<div class="acc-content"  style="width:99%">	
				<ul class="acc" id="nested"></ul>
<?			
	if($result->num_rows*1 >0){
?>			 
	<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F5F5F5" style="border:1px solid #DBDBDB;width:100%">
		<tr>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("#EXPEDIENTE") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("#ASISTENCIA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("SERVICIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("CONDICION SERV.") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("TIPO SERVICIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("STATUS ASISTENCIA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("FECHA REGISTRO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("USUARIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("CUENTA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("PROGRAMA") ;?></strong></div></td>
			<td bgcolor="#454545"></td>
			<td bgcolor="#454545"></td>
		</tr>	
		 <?
				while($reg = $result->fetch_object()){		
					if($c%2==0) $fondo="#c7c7c7"; else $fondo='#F9F9F9';
		?>
		<tr bgcolor="<?=$fondo;?>" title="<?=$reg->IDASISTENCIA;?>" >			
			<td bgcolor="#a7a7a7"><div align="center"><strong><?=$reg->IDEXPEDIENTE;?></strong></div></td>
			<td align="center"><?=$reg->IDASISTENCIA;?></td>			
			<td><?=utf8_encode($reg->ETIQUETA);?></td>			 
			<td align="center" style="color:red"><strong><?=$desc_cobertura_servicio[$reg->ARRCONDICIONSERVICIO];?></strong></td>
			<td><?=$desc_prioridadAtencion[$reg->ARRPRIORIDADATENCION];?></td>
			<td><?=$desc_status_asistencia[$reg->ARRSTATUSASISTENCIA];?></td>
			<td><?=$reg->FECHAHORA;?></td> 
			<td align="center"><?=$reg->IDUSUARIO;?></td>
			<td><?=$reg->cuenta;?></td> 
			<td><?=$reg->programa;?></td> 
			<td align="center"><input type="button" name="btnasignar" id="btnasignar" value="<?=_("ASIGNAR") ;?>" <?=($_GET["validaexist"])?"disabled":""?> style="font-weight:bold;font-size:9px;" onClick="window.close();window.opener.recargar_reincidencias('<?=$reg->IDEXPEDIENTE;?>','<?=$reg->IDAFILIADO;?>','<?=$reg->IDCUENTA;?>','<?=$reg->IDPROGRAMA;?>')" /></td> 			
			<td><a href="../../plantillas/etapa1_1.php?idasistencia=<?=$reg->IDASISTENCIA;?>" target="_blank"><?=_("VER");?></a></td> 
		</tr>	
	<?
					$c=$c+1;
				}
	?>
	</table>
	<? } ?>   				


				
			</div>
		</div>
		
		
	</li>
	<li>
		<h3 style="width:97%"><?=_("REINCIDENCIAS CUENTA Y PLAN ACTUAL - CANCELADO AL MOMENTO / POSTERIOR")." [".$resultCueOtroPlan->num_rows."]";?></h3>
		<div class="acc-section" style="width:98%">
			<div class="acc-content"  style="width:99%">			 
<?			
	if($resultCueOtroPlan->num_rows*1 >0){
?>			 
	<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F5F5F5" style="border:1px solid #DBDBDB;width:100%">
		<tr>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("#EXPEDIENTE") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("#ASISTENCIA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("SERVICIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("CONDICION SERV.") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("TIPO SERVICIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("STATUS ASISTENCIA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("FECHA REGISTRO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("USUARIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("CUENTA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("PROGRAMA") ;?></strong></div></td>
			<td bgcolor="#454545"></td>
			<td bgcolor="#454545"></td>
		</tr>
		 <?
				while($reg = $resultCueOtroPlan->fetch_object()){	
					if($c%2==0) $fondo="#c7c7c7"; else $fondo='#F9F9F9';
		?>
		<tr bgcolor="<?=$fondo;?>" title="<?=$reg->IDASISTENCIA;?>" >			
			<td bgcolor="#a7a7a7"><div align="center"><strong><?=$reg->IDEXPEDIENTE;?></strong></div></td>
			<td align="center"><?=$reg->IDASISTENCIA;?></td>			
			<td><?=utf8_encode($reg->ETIQUETA);?></td>			 
			<td align="center" style="color:red"><strong><?=$desc_cobertura_servicio[$reg->ARRCONDICIONSERVICIO];?></strong></td>
			<td><?=$desc_prioridadAtencion[$reg->ARRPRIORIDADATENCION];?></td>
			<td><?=$desc_status_asistencia[$reg->ARRSTATUSASISTENCIA];?></td>
			<td><?=$reg->FECHAHORA;?></td> 
			<td align="center"><?=$reg->IDUSUARIO;?></td>
			<td><?=$reg->cuenta;?></td> 
			<td><?=$reg->programa;?></td> 
			<td align="center"><input type="button" name="btnasignar" id="btnasignar" value="<?=_("ASIGNAR") ;?>" <?=($_GET["validaexist"])?"disabled":""?> style="font-weight:bold;font-size:9px;" onClick="window.close();window.opener.recargar_reincidencias('<?=$reg->IDEXPEDIENTE;?>','<?=$reg->IDAFILIADO;?>','<?=$reg->IDCUENTA;?>','<?=$reg->IDPROGRAMA;?>')" /></td> 			
			<td><a href="../../plantillas/etapa1_1.php?idasistencia=<?=$reg->IDASISTENCIA;?>" target="_blank"><?=_("VER");?></a></td> 
		</tr>	
	<?
					$c=$c+1;
				}
	?>
	</table>
	<? } ?>   
			</div>
		</div>
	</li>
	
	<li>
		<h3 style="width:97%"><?=_("REINCIDENCIAS DE OTRAS CUENTAS")." [".$resultOtros->num_rows."]" ;?></h3>
		<div class="acc-section"  style="width:98%">
			<div class="acc-content"  style="width:99%">
<?			
	if($resultOtros->num_rows*1 >0){
?>			 
	<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F5F5F5" style="border:1px solid #DBDBDB;width:100%">
		<tr>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("#EXPEDIENTE") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("#ASISTENCIA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("SERVICIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("CONDICION SERV.") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("TIPO SERVICIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("STATUS ASISTENCIA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("FECHA REGISTRO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("USUARIO") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("CUENTA") ;?></strong></div></td>
			<td bgcolor="#454545" style="color:#FFFFFF"><strong><div align="center"><?=_("PROGRAMA") ;?></strong></div></td>
			<td bgcolor="#454545"></td>
			<td bgcolor="#454545"></td>
		</tr>	
		 <?
				while($rowOtro = $resultOtros->fetch_object()){	
					if($c%2==0) $fondo_otr="#c7c7c7"; else $fondo_otr='#F9F9F9';
		?>
		<tr bgcolor="<?=$fondo_otr;?>" title="<?=$rowOtro->IDASISTENCIA;?>" >			
			<td bgcolor="#a7a7a7"><div align="center"><strong><?=$rowOtro->IDEXPEDIENTE;?></strong></div></td>
			<td align="center"><?=$rowOtro->IDASISTENCIA;?></td>			
			<td><?=utf8_encode($rowOtro->ETIQUETA);?></td>			 
			<td align="center" style="color:red"><strong><?=$desc_cobertura_servicio[$rowOtro->ARRCONDICIONSERVICIO];?></strong></td>
			<td><?=$desc_prioridadAtencion[$rowOtro->ARRPRIORIDADATENCION];?></td>
			<td><?=$desc_status_asistencia[$rowOtro->ARRSTATUSASISTENCIA];?></td>
			<td><?=$rowOtro->FECHAHORA;?></td> 
			<td align="center"><?=$rowOtro->IDUSUARIO;?></td>
			<td><?=$rowOtro->cuenta;?></td> 
			<td><?=$rowOtro->programa;?></td> 
			<td align="center"><input type="button" name="btnasignar" id="btnasignar" value="<?=_("ASIGNAR") ;?>" <?=($_GET["validaexist"])?"disabled":""?> style="font-weight:bold;font-size:9px;" onClick="window.close();window.opener.recargar_reincidencias('<?=$rowOtro->IDEXPEDIENTE;?>','EXP','<?=$rowOtro->IDPROGRAMA;?>')" /></td> 			
			<td><a href="../../plantillas/etapa1_1.php?idasistencia=<?=$rowOtro->IDASISTENCIA;?>" target="_blank"><?=_("VER");?></a></td> 
		</tr>	
	<?
					$c=$c+1;
				}
	?>
	</table>
	<? } ?>  
  			</div>
		</div>
	</li>	
</ul>

<script type="text/javascript" src="../../../../librerias/TinyAccordion/script.js"></script>
<script type="text/javascript">
	var parentAccordion=new TINY.accordion.slider("parentAccordion");
	parentAccordion.init("acc","h3",0,0);

	var nestedAccordion=new TINY.accordion.slider("nestedAccordion");
	nestedAccordion.init("nested","h3",1,-1,"acc-selected");
	
</script>

</body>
</html>