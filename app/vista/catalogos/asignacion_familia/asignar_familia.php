<?php

 	session_start();
	
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
			
	$con = new DB_mysqli();
	
	$con->select_db($con->catalogo);	
	 
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }	
	
	Auth::required(); 
	$datos=$con->consultation("select IDCUENTA,IDPROGRAMA from $con->temporal.expediente where IDEXPEDIENTE='".$_GET["idexpediente"]."' ");
 
	if($_GET["status"] !="CAN"){
 
	$Sql_familiaCobertura="SELECT
			  catalogo_familia.IDFAMILIA,
			  catalogo_familia.DESCRIPCION 
			FROM $con->catalogo.catalogo_programa_servicio
			  INNER JOIN $con->catalogo.catalogo_servicio
				ON catalogo_servicio.IDSERVICIO = catalogo_programa_servicio.IDSERVICIO
			  INNER JOIN $con->catalogo.catalogo_familia
				ON catalogo_familia.IDFAMILIA = catalogo_servicio.IDFAMILIA
			WHERE catalogo_programa_servicio.IDPROGRAMA IN(SELECT
								  IDPROGRAMA
								FROM $con->catalogo.catalogo_programa
								WHERE IDCUENTA  ='".$datos[0][0]."' and  IDPROGRAMA ='".$datos[0][1]."') 
			GROUP BY catalogo_servicio.IDFAMILIA 
			ORDER BY catalogo_familia.DESCRIPCION ";

	$result_cobertura=$con->query($Sql_familiaCobertura);
	
	$Sql_cobertura="SELECT
			  GROUP_CONCAT(DISTINCT '\'',catalogo_familia.IDFAMILIA,'\'') 
			FROM $con->catalogo.catalogo_programa_servicio
			  INNER JOIN $con->catalogo.catalogo_servicio
				ON catalogo_servicio.IDSERVICIO = catalogo_programa_servicio.IDSERVICIO
			  INNER JOIN $con->catalogo.catalogo_familia
				ON catalogo_familia.IDFAMILIA = catalogo_servicio.IDFAMILIA
			WHERE catalogo_programa_servicio.IDPROGRAMA IN(SELECT
								  IDPROGRAMA
								FROM $con->catalogo.catalogo_programa
								WHERE IDCUENTA  ='".$datos[0][0]."' and  IDPROGRAMA ='".$datos[0][1]."') 
			ORDER BY catalogo_familia.DESCRIPCION ";

	$resultado_fam=$con->consultation($Sql_cobertura);	
	if($resultado_fam[0][0]) $cobertura="WHERE IDFAMILIA NOT IN(".$resultado_fam[0][0].")";	
  
	} 
	 
	$Sql_familiaSinCobertura="SELECT
								  IDFAMILIA,
								  DESCRIPCION
								FROM $con->catalogo.catalogo_familia
								$cobertura
								GROUP BY DESCRIPCION"; 
								
	
	
	$result_Sincobertura=$con->query($Sql_familiaSinCobertura);	
 // echo $Sql_familiaCobertura;
 // die();
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>American Assist</title>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="../../../../librerias/TinyAccordion/style.css" type="text/css" />
	
	<style type="text/css">
		<!--
		.style1 {
				font-weight: bold;
				color: #FFFFFF;
			}
			
		.style2 {
				color: #FFFFFF;
				font-weight: bold;
			}
		-->
	</style>
	
	<script language="javascript" type="text/javascript">

		function mostar_ocualtarDiv(valor1,valor2,valor3) {
 
			if(document.getElementById('opc').value==1) {
				document.getElementById('div-justificacion').style.display='block';				 
				document.getElementById('div-principal').style.display='none';
				document.getElementById('opc').value=0;
				document.getElementById('idexped2').value=valor1;
				document.getElementById('idfamilia2').value=valor2;

			}else {
			 
				document.getElementById('div-justificacion').style.display='none';
				document.getElementById('div-principal').style.display='block';
				document.getElementById('opc').value=1;
				
			}
		}
	</script>	
</head>
<body>
 <h2 class="Box"><?=_("SELECCION DEL TIPO DE SERVICIO") ;?></h2>
 <div id="div-principal" >
	<ul class="acc" id="acc">
		<li>
			<h3 style="text-align:left"><?=_("Cobertura")?></h3>
			<div class="acc-section">
				<div class="acc-content">		 
					<table width="100%" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #28414d;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px">
						<tr>
						  <td width="10%" bgcolor="#28414d"><div align="center"><span class="style1"><?=_("FAMILIA") ;?>  </span></div></td>
						  <td bgcolor="#28414d"><div align="center"></div></td>
						</tr>
						<?	if($_GET["status"] !="CAN"){		 
								while($reg = $result_cobertura->fetch_object()){
									if($c%2==0) $fondo='#aac6d2'; else $fondo='#e9f0f3';
		 							 
						?>
						<tr bgcolor="<?=$fondo ;?>" >
						  <td><strong><?=$reg->DESCRIPCION;?></strong></td>
						  <td width="15%"><div align="center">
								
									<input type="hidden" name="idexpediente" id="idexpediente" value="<?=$_GET["idexpediente"];?>" />	
									<input type="hidden" name="idfamilia" value="<?=$reg->IDFAMILIA;?>" />	
									<input type="hidden" name="cobertura" id="cobertura" value="1" />	
									<input type="hidden" name="opc" id="opc" value="1"/>							 
									<input type="button" name="btngestionar" id="btngestionar" value="<?=_("SELECCIONAR") ;?>" style="font-weight:bold;font-size:10px;" title="Seleccionar familia" onclick="Dialog.okCallback(); window.open('../../plantillas/etapa1.php?idfamilia=<?=$reg->IDFAMILIA;?>&idexpediente=<?=$_GET["idexpediente"];?>&cobertura=1','ASISTENCIA');" />						  
						  </div></td>
						</tr>
						<?
								$c=$c+1;
								$ruta="";
								$extras="";
							}
						}
						?>
					</table>					
				</div>
			</div>
		</li>
		<li>
			<h3 style="text-align:left"><?=_("Sin Cobertura")?>(<font size="1px"><?=_("Adicional/CoPago/Conexion")?></font>)</h3>
			<div class="acc-section">
				<div class="acc-content">
					<table width="100%" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #28414d;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px" >
						<tr>
						  <td width="10%" bgcolor="#28414d"><div align="center"><span class="style1"><?=_("FAMILIA") ;?></span></div></td>
						  <td bgcolor="#28414d"><div align="center"></div></td>
						</tr>
						<?			 
								while($reg = $result_Sincobertura->fetch_object()){ 	 
									if($c%2==0) $fondo='#d0e0e6'; else $fondo='#e9f0f3';
								
						?>
						<tr bgcolor="<?=$fondo ;?>" >
						  <td><strong><?=$reg->DESCRIPCION;?></strong></td>
						  <td width="15%"><div align="center">
								<input type="hidden" name="idexpediente" value="<?=$_GET["idexpediente"];?>"/>	
								<input type="hidden" name="idfamilia" value="<?=$reg->IDFAMILIA;?>"/>	
								<input type="button" name="btngestionar" id="btngestionar" value="<?=_("SELECCIONAR") ;?>" style="font-weight:bold;font-size:10px;" title="Seleccionar familia" onclick="Dialog.okCallback(); window.open('../../plantillas/etapa1.php?idfamilia=<?=$reg->IDFAMILIA;?>&idexpediente=<?=$_GET["idexpediente"];?>','_blank');" /> 
						  </div></td>
						</tr>
						<?
									$c=$c+1;
									$ruta="";
								}
						?>
					</table><ul class="acc" id="nested"></ul>
				</div>
			</div>
		</li>	 
	</ul>		
	</div>
 
	<script type="text/javascript" src="../../../../librerias/TinyAccordion/script.js"></script>
	<script type="text/javascript">

		var parentAccordion=new TINY.accordion.slider("parentAccordion");
		parentAccordion.init("acc","h3",<?=($_GET["status"] =="CAN")?"1":"0"?>,<?=($_GET["status"] =="CAN")?"1":"0"?>);

		var nestedAccordion=new TINY.accordion.slider("nestedAccordion");
		nestedAccordion.init("nested","h3",1,-1,"acc-selected");

	</script>
		
	<script type="text/javascript" > 

		function grabar_justificacion(){

			new Ajax.Request('../../catalogos/asignacion_familia/gjustificacion.php',{
			method: 'post',
			parameters:  $('frmdcobetura').serialize(true),
			onSuccess: function(t){
				
				alert(t.responseText);
				window.open('../../plantillas/etapa1.php?idfamilia='+$F('idfamilia2')+'&idexpediente=<?=$_GET["idexpediente"];?>&cobertura=1','_blank');
			 
				
			}
			});					
		}
		 
	</script>	

	<script type="text/javascript">
		function ver_detalle(idprograma){
				 
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("DETALLE DEL CONTRATO")?>',
					width: 700,
					height: 300,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: "../../plantillas/contrato.php?idprograma="+idprograma
				});

				win.showCenter();
				myObserver = {onDestroy: function(eventName, win1)
				{
					if (win1 == win) {
						win = null;
						Windows.removeObserver(this);
					}
				}
				}
				Windows.addObserver(myObserver);
			}
			return;
					
		}
	</script>
		
</body>
</html> 