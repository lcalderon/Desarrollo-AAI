<?php
 
 	session_start();
	include_once("../../modelo/clase_mysqli.inc.php");
 	include_once("../../modelo/clase_ubigeo.inc.php");
	include_once("../../vista/login/Auth.class.php");	
	include_once("../../modelo/afiliado/afiliado.class.php");
	include_once("../includes/arreglos.php");
	
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	Auth::required($_POST["txturl"]);
	
	$objafiliado = new afiliado($_GET["idafiliado"]);
	$infoPersona=$objafiliado->informacionAfiliado();	
	list($infoTelefonos,$telefonosOrden)=$objafiliado->personaAfiliadoTelefono();	
	
//contratante
	if($infoPersona["IDAFILIADO"]){

		$respFicha=$con->consultation("SELECT IDFICHA FROM catalogo_afiliado_persona_datosingreso WHERE IDAFILIADO=".$infoPersona["IDAFILIADO"]." LIMIT 1");

					$Sql_contratante="SELECT
									catalogo_afiliado_persona_datoscontratante.IDDOCUMENTO,
									catalogo_afiliado_persona_datoscontratante.IDAFILIADO,
									catalogo_afiliado_persona_datoscontratante.NOMBRE,
									catalogo_afiliado_persona_datoscontratante.APPATERNO,
									catalogo_afiliado_persona_datoscontratante.APMATERNO,
									catalogo_afiliado_persona_datoscontratante.GENERO,
									catalogo_afiliado_persona_datoscontratante.TELEFONOFIJO,
									catalogo_afiliado_persona_datoscontratante.CELULAR,
									catalogo_afiliado_persona_datoscontratante.CELULARCARGO,
									catalogo_afiliado_persona_datoscontratante.DIGITOVERIFICADOR,
									catalogo_afiliado_persona_datoscontratante.EMAIL
								FROM
									$con->catalogo.catalogo_afiliado_persona_datoscontratante
								INNER JOIN $con->catalogo.catalogo_afiliado ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_datoscontratante.IDAFILIADO
								WHERE
									catalogo_afiliado_persona_datoscontratante.IDAFILIADO = '".$infoPersona["IDAFILIADO"]."'";

				$result=$con->query($Sql_contratante);
				$row = $result->fetch_object();
		 
			//telefonos contacto
				$rsContacto=$con->query("SELECT ID,NOMBRE,IDTIPOCONTACTO,TELEFONOFIJO,TELEFONOCELULAR,OTROTELEFONO FROM $con->catalogo.catalogo_afiliado_persona_contacto WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' ORDER BY PRIORIDAD");
					 
			//tipoenfermedad
				$rsTipoEnfermedad=$con->query("SELECT ID,ENFERMEDAD_ALERGIA,TRATAMIENTO FROM $con->catalogo.catalogo_afiliado_persona_enfermedades_alergias_tratamiento WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' AND ARRTIPOPADECIMIENTO='ENFERMEDAD' ORDER BY PRIORIDAD");
				 			
			//tipoenfermedad y tratamiento
				$rsTipoEnfermedad_alergia=$con->query("SELECT ID,ENFERMEDAD_ALERGIA,TRATAMIENTO FROM $con->catalogo.catalogo_afiliado_persona_enfermedades_alergias_tratamiento WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' AND ARRTIPOPADECIMIENTO='ALERGIA' ORDER BY PRIORIDAD");
				 			
			//Otros antecedentes medicos
				$rsOtrosantecedentesmedico=$con->query("SELECT OBSERVACION FROM $con->catalogo.catalogo_afiliado_persona_otrosantecedentesmedico WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' ORDER BY OBSERVACION");
				$row_otrosantecedesMed = $rsOtrosantecedentesmedico->fetch_object();
							 			
			//observaciones especiales
				$rsObservacionesespeciales=$con->query("SELECT OBSERVACION FROM $con->catalogo.catalogo_afiliado_persona_observacionespecial WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' ORDER BY OBSERVACION");
				$row_observacionesespeciales = $rsObservacionesespeciales->fetch_object();
				
			//Asistencia medica
				$rsAsistenciamedica=$con->query("SELECT DRCABECERA,DOC_CABECERA_ESPECIALIDAD,DOC_CABECERA_TELEFONOFIJO,DOC_CABECERA_TELEFONOCELULAR,AMBULANCIA,IDSERVICIOAMBULANCIA,INSTITUCION FROM $con->catalogo.catalogo_afiliado_persona_asistenciamedica WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."'");
				$row_asistenciaMed = $rsAsistenciamedica->fetch_object();
				
			//protocolo atencion personalizada
				$rsProtocoloAtencionPers=$con->query("SELECT ID,LLAMADA FROM $con->catalogo.catalogo_afiliado_persona_protocolo WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' AND ARRTIPOPROTOCOLO='ATENPER' ORDER BY PRIORIDAD");

			//protocolo de seguridad
				$rsProtocoloAtencionSeg=$con->query("SELECT ID,LLAMADA FROM $con->catalogo.catalogo_afiliado_persona_protocolo WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' AND ARRTIPOPROTOCOLO='SEGU' ORDER BY PRIORIDAD");
					
			//protocolo de eventos especiales
				$rsProtocoloAtencionEven=$con->query("SELECT ID,LLAMADA FROM $con->catalogo.catalogo_afiliado_persona_protocolo WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' AND ARRTIPOPROTOCOLO='EVENESP' ORDER BY PRIORIDAD");

			//Plan tarifario
				$rsPlanTarifario=$con->query("SELECT DISPOSITIVO,MODELO,PLAN,TELEFONOCELULAR FROM $con->catalogo.catalogo_afiliado_persona_plantarifario WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."'");
				$row_plantarifario = $rsPlanTarifario->fetch_object();				
				/* if($regusu = $rsPlanTarifario->fetch_object()){
						$planTarifario[]=$regusu->DISPOSITIVO; 
						$planTarifario[]=$regusu->MODELO; 
						// $planTarifario["PLAN"]=$regusu->PLAN; 
						// $planTarifario["TELEFONOCELULAR"]=$regusu->TELEFONOCELULAR; 
				} */
				
			// ubigeo contratante

 			$resp_cveOricontra=$con->consultation("SELECT CVEENTIDAD1,CVEENTIDAD2,DIRECCION,REFERENCIA1 FROM  $con->catalogo.catalogo_afiliado_persona_ubigeo_datoscontratante WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."'");
			$Sql_ubigeoIdcontra="SELECT CONCAT(ID_SOAA,'-',CVEENTIDAD3_LIF) FROM $con->catalogo.catalogo_entidad_LIF WHERE
								ID_SOAA=(SELECT ID FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1='".$resp_cveOricontra[0][0]."' AND  CVEENTIDAD2='".$resp_cveOricontra[0][1]."' LIMIT 1) AND ID_SOAA >0 AND CVEENTIDAD3_LIF >0 ";
			$resp_CVE3contra=$con->consultation($Sql_ubigeoIdcontra);

			// ubigeo usuario

 			$resp_cveOri=$con->consultation("SELECT CVEENTIDAD1,CVEENTIDAD2,DIRECCION,REFERENCIA1 FROM  $con->catalogo.catalogo_afiliado_persona_ubigeo WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."'");
			$Sql_ubigeoId="SELECT CONCAT(ID_SOAA,'-',CVEENTIDAD3_LIF) FROM $con->catalogo.catalogo_entidad_LIF WHERE
							ID_SOAA=(SELECT ID FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1='".$resp_cveOri[0][0]."' AND  CVEENTIDAD2='".$resp_cveOri[0][1]."' LIMIT 1) AND ID_SOAA >0 AND CVEENTIDAD3_LIF >0 ";
			$resp_CVE3=$con->consultation($Sql_ubigeoId);
 
			/*	
			if($resp_CVEs[0][2] ==0)	$sqlAdd="AND CVEENTIDAD2_LIF=0";
			if($resp_CVEs[0][3] ==0)	$sqlAdd=$sqlAdd."AND CVEENTIDAD3_LIF=0";
			
			$resp_cveFinal=$con->consultation("SELECT ID_SOAA,CVEENTIDAD1_LIF,CVEENTIDAD2_LIF,CVEENTIDAD3_LIF FROM $con->catalogo.catalogo_entidad_LIF WHERE ID_SOAA='".$resp_CVEs[0][0]."' $sqlAdd");							 
			 */			
			
			// ubigeo vivienda secundaria
			
		  	$resp_cveOrisec=$con->consultation("SELECT CVEENTIDAD1,CVEENTIDAD2,DIRECCION,REFERENCIA1,TELEFONO FROM  $con->catalogo.catalogo_afiliado_persona_domicilio_ubigeo WHERE IDAFILIADO='".$infoPersona["IDAFILIADO"]."' LIMIT 1");
			$Sql_ubigeoIdsec="SELECT CONCAT(ID_SOAA,'-',CVEENTIDAD3_LIF) FROM $con->catalogo.catalogo_entidad_LIF WHERE
							ID_SOAA=(SELECT ID FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1='".$resp_cveOrisec[0][0]."' AND  CVEENTIDAD2='".$resp_cveOrisec[0][1]."' LIMIT 1) AND ID_SOAA >0 AND CVEENTIDAD3_LIF >0 ";
			$resp_CVE3sec=$con->consultation($Sql_ubigeoIdsec);
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
	<title>LifeCare</title>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../estilos/functionjs/func_global.js"></script>		
	<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css"/>		
	<!-- Common JS calendar -->
		<script type="text/javascript" src="../../../librerias/jscalendar-1.0/calendar.js"></script>
		<script type="text/javascript" src="../../../librerias/jscalendar-1.0/calendar-setup.js"></script>
		<script type="text/javascript" src="../../../librerias/jscalendar-1.0/lang/calendar-es.js"></script>
		<style type="text/css">@import url("../../../librerias/jscalendar-1.0/calendar-system.css");</style>
		
	<!-- Common JS files -->
	<script type='text/javascript' src='../../../librerias/zapatec/utils/zapatec.js'></script>

	<!-- Custom includes -->	
	<script type="text/javascript" src="../../../librerias/zapatec/zptabs/src/zptabs.js"></script>

	<!-- ALL demos need these css -->
	<link href="../../../librerias/zapatec/website/css/zpcal.css" rel="stylesheet" type="text/css"/>
	<!--link href="../../../librerias/zapatec/website/css/template.css" rel="stylesheet" type="text/css"-->
	
	
			<script language="JavaScript">
				
				function validarCampo(variable){
					//alert($('txtcontratantenombre').value);
				
				/* 	$('txtnombre')=$('txtnombre').replace(/^\s*|\s*$/g,"");			
					//document.frmeditar.txtpiloto.value=document.frmeditar.txtpiloto.value.replace(/^\s*|\s*$/g,"");
					
					if(document.frmeditar.txtnombre.value =='' ){
						alert('<?=_("INGRESE EL NOMBRE") ;?>');
						document.frmeditar.txtnombre.focus();
						return (false);
					}		 */	
					 

					//return (false);
					
					 
				}

				function mostrar_servicios(id, estado){
						$(id).style.display=estado;
				}

			//mostrar entidad 3
				/* function recargar_entidad(cveentidad1,cveentidad2,nombrediv,opc){
					//alert(cveentidad);					
					new Ajax.Updater(nombrediv,'combo_entidad_usuario.php',{
						parameters:{
							cveentidad_valor:cveentidad1,
							cveentidad_valor2:cveentidad2,
							opcion:opc
						},
						method: 'post',	 
						onSuccess: function(resp){
							
							if(nombrediv =='div-entidad02'){								 
								
								var sel = document.getElementById("cveentidad3_usuario");
								for(i=(sel.length-1); i>=0; i--)
								{
								   aBorrar = sel.options[i];
								   aBorrar.parentNode.removeChild(aBorrar);
								}
								
							   option = document.createElement("OPTION");
							   option.value = "0";
							   option.text = "TODOS";
							   sel.add(option);							
							} else{
								
								var sel = document.getElementById("cveentidad3_viviendasec");
								for(i=(sel.length-1); i>=0; i--)
								{
								   aBorrar = sel.options[i];
								   aBorrar.parentNode.removeChild(aBorrar);
								}
								
							   option = document.createElement("OPTION");
							   option.value = "0";
							   option.text = "TODOS";
							   sel.add(option);									
								
								
								
								
							}
						}			 
					});
				} */
 	</script>		
			
</head>
<body>

<form id="frm_lifecare" name="frm_lifecare" method="post" action="grabar_datos.php" onSubmit = "return validarCampo(this)">
<input name="idafiliado" id="idafiliado" type="hidden" value="<?=$_GET["idafiliado"]; ?>"/>
<input name="id_paciente" id="id_paciente" type="hidden" value="<?=$respFicha[0][0]?>"/>
<input name="idcuenta" id="idcuenta" type="hidden" value="<?=$infoPersona["IDCUENTA"]?>"/>

	<div id="tabBar"  ></div>
	<div id="tabs" style="width: 97%">
		<div id="datoscontratante" style="width: 100%">
			<label accesskey="h" title="Datos del contratante">CONTRATANTE</label>	

			<table width="100%" border="0" cellpadding="3" cellspacing="3">
				<tbody>				
					<tr>
						<td width="15%"><strong>CEDULA DE IDENTIDAD(RUT)</strong></td>
						<td colspan="3">
							<input name="txtcontranterut" maxlength="8" id="txtcontranterut" type="text" value="<?=$row->IDDOCUMENTO;?>" readonly class="classtexto" style="text-transform:uppercase;color:#ff0000;font-weight:bold" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/> - <input name="txtcontratanterut2" id="txtcontratanterut2" value="<?=$row->DIGITOVERIFICADOR;?>" size="2" maxlength="2" type="text" readonly class="classtexto" style="text-transform:uppercase;color:#ff0000;font-weight:bold" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/>
						</td>
					</tr>
					<tr>
						<td>NOMBRES</td>
					  <td colspan="3"><input name="txtcontratantenombre" id="txtcontratantenombre"  type="text" value="<?=$row->NOMBRE;?>" disabled class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="45"/></td>
					</tr>
					<tr>
						<td>APELLIDO PATERNO</td>
						<td width="29%"><input name="txtcontratanteapepaterno" id="txtcontratanteapepaterno" type="text" value="<?=$row->APPATERNO;?>" disabled class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="45"/></td>
						<td width="17%">APELLIDO MATERNO</td>
						<td width="34%"><input name="txtcontratanteapematerno" id="txtcontratanteapematerno" type="text" value="<?=$row->APMATERNO;?>" disabled class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="45"/></td>
					</tr>
					<tr>
						<td>SEXO</td>
						<td colspan="3">
							<?	$con->cmb_array("cmbcontratantegenero",$desc_genero,$row->GENERO,"disabled class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","","S/D"); ?>
						</td>
					</tr>
					<tr>
						<td><strong>ENTIDAD 3</strong></td>
						<td colspan="3">							 
							<?
								$con->cmbselectdata("SELECT CONCAT(ID_SOAA,'-',CVEENTIDAD3_LIF) AS CODIGO,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE CVEENTIDAD1_LIF >0 and CVEENTIDAD2_LIF >0 and CVEENTIDAD3_LIF >0 AND ID_SOAA >0 ORDER BY DESCRIPCION","cveentidad3_contratante",$resp_CVE3contra[0][0],"disabled onFocus='coloronFocus(this);'onBlur='colorOffFocus(this);' class='classtexto' ","","TODOS",0);			
							?>							 
						</td>						
					</tr>				
					<tr>
						<td>SECTOR</td>
						<td><input name="txtcontratantesector" id="txtcontratantesector" type="text"  size="35" value="<?=utf8_encode($ubigeo->referencia1);?>" disabled class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/></td>		 
						<td><strong>DIRECCION</strong></td>
						<td><input name="txtcontratantedireccion" type="text" class="classtexto" id="txtcontratantedireccion" value="<?=utf8_encode($ubigeo->direccion);?>" disabled style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="65"/></td>
					</tr>
					<tr>
						<td>TELEFONO</td>
						<td><input name="txtcontratantetelefono" size="15" maxlength="10" id="txtcontratantetelefono" value="<?=$row->TELEFONOFIJO?>" type="text" disabled class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/></td>
						<td>CELULAR</td>
						<td><input name="txtcontratantecelular"  size="15" maxlength="8" id="txtcontratantecelular" value="<?=$row->CELULAR?>" type="text" disabled class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/></td>
					</tr>
					<tr>
						<td>E-MAIL</td>
						<td><input name="txtcontratanteemail" id="txtcontratanteemail" type="text" value="<?=strtolower($row->EMAIL); ?>" size="35" disabled class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/>      </td>
						<td>CELULAR CARGO</td>
						<td><input name="txtcontratantecelularcargo"  size="15" maxlength="8" id="txtcontratantecelularcargo" type="text" value="<?=$row->CELULARCARGO?>" disabled class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/>      </td>
					</tr>   
				</tbody>
			</table>
	</div>
	<div id="datosusuario">
		<label accesskey="i" title="Datos del usuario">USUARIO</label>
	
			<table border="0" cellpadding="2" cellspacing="2" width="100%" bgcolor="#9BB7C9" style="border:1px solid #000066">
				<tbody>				
					<tr>
						<td width="15%"> <p><strong>CEDULA DE IDENTIDAD(RUT)</strong></p></td>
						<td colspan="3">
							<input name="txtusuariorut" maxlength="10" id="txtusuariorut" type="text" value="<?=$infoPersona["IDDOCUMENTO"]?>" class="classtexto" style="text-transform:uppercase;color:#ff0000;font-weight:bold" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/> - <input name="txtusuariorut2" id="txtusuariorut2" size="2" maxlength="2" type="text" class="classtexto" style="text-transform:uppercase;color:#ff0000;font-weight:bold" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" value="<?=$infoPersona["DIGITOVERIFICADOR"];?>" />
						</td>
					</tr>
					<tr>
						<td>NOMBRES</td>
						<td colspan="3"><input name="txtusuarionombre"  id="txtusuarionombre" type="text" value="<?=$infoPersona["NOMBRE"]?>" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="45"/></td>
					</tr>
					<tr>
						<td>APELLIDO PATERNO</td>
						<td width="29%"><input name="txtusuarioapepaterno" id="txtusuarioapepaterno" type="text" value="<?=$infoPersona["APPATERNO"]?>" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="45"/></td>
						<td width="17%">APELLIDO MATERNO</td>
						<td width="34%"><input name="txtusuarioapematerno" id="txtusuarioapematerno" type="text" value="<?=$infoPersona["APMATERNO"]?>" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="45"/></td>
					</tr>
					<tr>
						<td>SEXO</td>
						<td>
							<?	$con->cmb_array("cmbusuariogenero",$desc_genero,$infoPersona["GENERO"]," class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);'","","SELECCIONAR"); ?>
						</td>
						<td>ESTADO CIVIL</td>
						<td>
							<?	
								$con->cmbselectdata("SELECT ID_ESTADOCIVIL,DESCRIPCION FROM $con->catalogo.catalogo_tipo_estadocivil WHERE IDCUENTA='LIF' ORDER BY DESCRIPCION","cmbusuarioestadocivil",$infoPersona["ESTADOCIVIL"],"onFocus='coloronFocus(this);'onBlur='colorOffFocus(this);' class='classtexto' ","2");
							?>
						</td>
					</tr>
					<tr>
						<td>F. NACIMIENTO</td>
						<td><input name="txtusuariofechanacimiento" id="txtusuariofechanacimiento" type="text" size="14" class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' value="<?=$infoPersona["FECHANACIMIENTO"]; ?>" readonly><button type="reset" id="f_trigger_b">...</button></td>
						
						<td><strong>GRUPO SANGUINEO</strong></td>
					  	<td>
							<?	
								$con->cmbselectdata("SELECT IDGRUPOSANGUINEO,DESCRIPCION FROM $con->catalogo.catalogo_grupo_sanguineo ORDER BY DESCRIPCION","cmbusuariogruposang",$infoPersona["IDGRUPOSANGUINEO"],"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
							?>
						</td>
					</tr>
					<? 
						//$adicionalUbi=1;
			/* 			if($_GET["idafiliado"]){
							$ubigeo = new ubigeo();
							$ubigeo->leer("IDAFILIADO",$con->catalogo,"catalogo_afiliado_persona_ubigeo",$_GET["idafiliado"]);
						 }
						 
						$nombreCombo="_usuario";
						include("../includes/vista_entidades_final.php"); 	 */					
					?>
					<!--tr>
						<td><strong>ENTIDAD 1</strong></td>
						<td>
							<?	
								//$con->cmbselectdata("SELECT CVEENTIDAD1_LIF,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE CVEENTIDAD1_LIF >=0 AND CVEENTIDAD2_LIF=0 AND CVEENTIDAD3_LIF=0 ORDER BY DESCRIPCION","cveentidad1_usuario",$resp_cveFinal[0][1],"onChange=recargar_entidad($('cveentidad1_usuario').value,this.value,'div-entidad02','2_usuario'); onFocus='coloronFocus(this);'onBlur='colorOffFocus(this);' class='classtexto' ","","TODOS");
							?>
						</td>
						<td><strong>ENTIDAD 2</strong></td>
						<td>
							<div id="div-entidad02">
							<?	
								// if($resp_cveFinal[0][2] >0){
									
									// if($resp_cveFinal[0][1] >0)	$sqlcv1="CVEENTIDAD1_LIF =".$resp_cveFinal[0][1]." AND "; else $sqlcv1="CVEENTIDAD1_LIF >=0 AND ";									
									// if($resp_cveFinal[0][2] ==0)	$resp_cveFinal[0][2]="";									
									// $con->cmbselectdata("SELECT CVEENTIDAD2_LIF,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE 	$sqlcv1 CVEENTIDAD2_LIF >0 AND CVEENTIDAD3_LIF=0 ORDER BY DESCRIPCION","cveentidad2_usuario",$resp_cveFinal[0][2],"onChange=recargar_entidad($('cveentidad1_usuario').value,this.value,'div-entidad03','3_usuario'); onFocus='coloronFocus(this);'onBlur='colorOffFocus(this);' class='classtexto' ","","TODOS");
								// } else{									
							?>
								<select name='cveentidad2_usuario'  id='cveentidad2_usuario' class='classtexto' onfocus='coloronFocus(this);' onblur='colorOffFocus(this);'>
									<option value=''>TODOS</option>
								</select>
							<? //} ?>
							</div>
						</td>
					</tr-->
					<tr>
						<td><strong>ENTIDAD 3</strong></td>
						<td colspan="3">							 
							<?	
								$con->cmbselectdata("SELECT CONCAT(ID_SOAA,'-',CVEENTIDAD3_LIF) AS CODIGO,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE CVEENTIDAD1_LIF >0 and CVEENTIDAD2_LIF >0 and CVEENTIDAD3_LIF >0 AND ID_SOAA >0 ORDER BY DESCRIPCION","cveentidad3_usuario",$resp_CVE3[0][0]," onFocus='coloronFocus(this);'onBlur='colorOffFocus(this);' class='classtexto' ","","TODOS",0);			
							?>							 
						</td>						
					</tr>
					<tr>
						<td>SECTOR</td>
						<td><input name="txtusuariosector" id="txtusuariosector" type="text" value="<?=utf8_encode($resp_cveOri[0][3]);?>" size="35" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/></td>		 
						<td><strong>DIRECCION</strong></td>
						<td><input name="txtusuariodireccion" type="text" value="<?=utf8_encode($resp_cveOri[0][2]);?>" class="classtexto" id="txtusuariodireccion" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="65"/></td>				   
					</tr> 
					<tr>
						<td>TELEFONO</td>
						<td><input name="txtusuariotelefono" size="15" maxlength="10" id="txtusuariotelefono" value="<?=$infoTelefonos["FIJO0"];?>" type="text" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/></td>
						<td>CELULAR</td>
						<td><input name="txtusuariocelular" size="15" maxlength="8" id="txtusuariocelular" value="<?=$infoTelefonos["CELULAR0"];?>" type="text" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/></td>
					</tr>
					<tr>
						<td>E-MAIL</td>
						<td><input name="txtusuarioemail" id="txtusuarioemail" type="text" size="45" value="<?=strtolower($infoPersona["EMAILS"]) ?>" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
						<td>SIST. SALUD</td>
						<td>
							<?	
								$con->cmbselectdata("SELECT ID_SISTEMASALUD,DESCRIPCION FROM $con->catalogo.catalogo_sistema_salud WHERE IDCUENTA='LIF' ORDER BY DESCRIPCION","cmbusuariosistemasalud",$infoPersona["ID_SISTEMASALUD"],"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
							?>
						</td>
					</tr>
					<tr>
						<td><strong>VIVE SOLO</strong></td>
						<td colspan="3"><input name="radiovives" id="radiovives" value="1" <?=($infoPersona["VIVESOLO"] ==1)?"checked":""?> type="radio">SI <input name="radiovives" id="radiovives" value="0" type="radio" <?=($infoPersona["VIVESOLO"] ==0)?"checked":""?> >NO</td>
					</tr>
				</tbody>
			</table>
				<input name="CVEENTIDAD3_usuario_origen" id="CVEENTIDAD3_usuario_origen" type="hidden" value="<?=$resp_CVE3[0][0]; ?>"/>
				<input name="txtusuariodireccion_origen" id="txtusuariodireccion_origen" type="hidden" value="<?=utf8_encode($resp_cveOri[0][2]);; ?>"/>
				<input name="txtusuariosector_origen" id="txtusuariosector_origen" type="hidden" value="<?=utf8_encode($resp_cveOri[0][3]); ?>"/>	
				
				<input name="txtusuariotelefono_origen" id="txtusuariotelefono_origen" type="hidden" value="<?=$infoTelefonos["FIJO0"]; ?>"/>
				<input name="txtusuariocelular_origen" id="txtusuariocelular_origen" type="hidden" value="<?=$infoTelefonos["CELULAR0"]; ?>"/>
				<script type="text/javascript">
						Calendar.setup({
						inputField     :    "txtusuariofechanacimiento",      // id of the input field
						ifFormat       :    "%Y-%m-%d",       // format of the input field
						showsTime      :    false,            // will display a time selector
						button         :    "f_trigger_b",   // trigger for the calendar (button ID)
						singleClick    :    true,           // double-click mode
						step           :    1                // show all years in drop-down boxes (instead of every other year as default)
					});
				</script>				
	</div>
	<div id="viviendasecundaria">
    <label accesskey="r" title="Direcci&oacute;n de vivienda secundaria">VIVIENDA SEC.</label>

		<table border="0" cellpadding="5" cellspacing="5" width="100%">
			<tbody> 				
					<? 


						//$adicionalUbi=1;
						
						// if($_GET["idafiliado"]){
							// $ubigeo = new ubigeo();
							// $ubigeo->leer("IDAFILIADO",$con->catalogo,"catalogo_afiliado_persona_domicilio_ubigeo",$_GET["idafiliado"]);
						 // }
						 
						//$nombreCombo="_viviendasec";
						//include("../includes/vista_entidades_final.php");	
					?>
					
					<tr>
						<td><strong>ENTIDAD 3</strong></td>
						<td colspan="3">							 
							<?	
								$con->cmbselectdata("SELECT CONCAT(ID_SOAA,'-',CVEENTIDAD3_LIF) AS CODIGO,DESCRIPCION FROM $con->catalogo.catalogo_entidad_LIF WHERE CVEENTIDAD1_LIF >0 and CVEENTIDAD2_LIF >0 and CVEENTIDAD3_LIF >0 AND ID_SOAA >0 ORDER BY DESCRIPCION","cveentidad3_viviendasec",$resp_CVE3sec[0][0]," onFocus='coloronFocus(this);'onBlur='colorOffFocus(this);' class='classtexto' ","","TODOS",0);			
							?>							 
						</td>						
					</tr>
					
					<tr>
						<td>SECTOR</td>
						<td><input name="txtsectorviviendasecundaria" id="txtsectorviviendasecundaria" type="text" size="35" value="<?=utf8_encode($resp_cveOrisec[0][3]);?>" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/></td>		 
						<td><strong>DIRECCION</strong></td>
						<td><input name="txtdireccionviviendasecudaria" id="txtdireccionviviendasecudaria" type="text" class="classtexto" value="<?=utf8_encode($resp_cveOrisec[0][2]);?>" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="65"/></td>
					</tr>
					<tr>
						<td>TELEFONO</td>
						<td colspan="3"><input name="txtviviendasectelefono" id="txtviviendasectelefono" size="15" maxlength="10" value="<?=$resp_cveOrisec[0][4]?>" type="text" class="classtexto" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"/></td>
					</tr>
			</tbody>
		</table>

				<input name="CVEENTIDAD3_viviendasec_origen" id="CVEENTIDAD3_viviendasec_origen" type="hidden" value="<?=$resp_CVE3sec[0][0];?>"/>
				<input name="txtdireccionviviendasecudaria_origen" id="txtdireccionviviendasecudaria_origen" type="hidden" value="<?=utf8_encode($resp_cveOrisec[0][2]);; ?>"/>
				<input name="txtsectorviviendasecundaria_origen" id="txtsectorviviendasecundaria_origen" type="hidden" value="<?=utf8_encode($resp_cveOrisec[0][3]); ?>"/>		
				<input name="txtviviendasectelefono_origen" id="txtviviendasectelefono_origen" type="hidden" value="<?=$resp_cveOrisec[0][4] ?>"/>		
  </div>    
  
  <div id="contactos">
    <label accesskey="p" title="Contactos">CONTACTOS</label>	
     
		<table border="1" cellpadding="5" cellspacing="5" width="100%" bgcolor="#9BB7C9" style="border-collapse:collapse">
			<tbody>
                   <tr bgcolor="#456981">
						<td height="37" width="330" align="center" style="color:#FFFFFF"><strong>NOMBRE</strong></td>	
						<td align="center" style="color:#FFFFFF"><strong>CONTACTO</strong></td>
						<td align="center" style="color:#FFFFFF"><strong>FONO FIJO</strong></td>
                        <td align="center" style="color:#FFFFFF"><strong>CELULAR</strong></td>
						<td align="center" style="color:#FFFFFF"><strong>OTRO FONO</strong></td>
                   </tr>
					<?									
						 while($reg= $rsContacto->fetch_object()){
							$cantidad++;
					?>	
                   <tr>
						<td height="37">
							<input name="idcontacto<?=$cantidad?>"  id="idcontacto<?=$cantidad?>" type="hidden" value="<?=$reg->ID?>"/>
							<input name="txtnombrecontacto<?=$cantidad?>" id="txtnombrecontacto<?=$cantidad?>" type="text" class="classtexto" value="<?=$reg->NOMBRE?>" style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="65">
							<input name="txtnombrecontacto_origen<?=$cantidad?>" id="txtnombrecontacto_origen<?=$cantidad?>" type="hidden" value="<?=$reg->NOMBRE?>">
						</td>
						<td align="center"> 
							<?	
								$con->cmbselectdata("SELECT IDTIPOCONTACTO,DESCRIPCION FROM $con->catalogo.catalogo_tipo_contacto WHERE IDCUENTA='LIF' ORDER BY DESCRIPCION","cmbparentesco$cantidad",$reg->IDTIPOCONTACTO,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
								echo "<input name='cmbparentesco_origen$cantidad' id='cmbparentesco_origen$cantidad' type='hidden' value='$reg->IDTIPOCONTACTO'>";
							?>
						</td>
                        <td align="center">
							+56<input name="txtprefijo<?=$cantidad?>" id="txtprefijo<?=$cantidad?>" type="text" class="classtexto" value="<?=$reg->TELEF?>" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="4" maxlength="2">&nbsp;&nbsp;
							<input name="txtfijo<?=$cantidad?>" id="txtfijo<?=$cantidad?>" type="text" value="<?=$reg->TELEFONOFIJO?>" size="20" maxlength="12" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)">                                            
							<input name="txtfijo_origen<?=$cantidad?>" id="txtfijo_origen<?=$cantidad?>" type="hidden" value="<?=$reg->TELEFONOFIJO?>">                                            
							
						</td>
                        <td align="center">
							+569<input name="txtcelular<?=$cantidad?>" id="txtcelular<?=$cantidad?>"  type="text" class="classtexto" value="<?=$reg->TELEFONOCELULAR?>" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="20" maxlength="10">                                            
								<input name="txtcelular_origen<?=$cantidad?>" id="txtcelular_origen<?=$cantidad?>" type="hidden" value="<?=$reg->TELEFONOCELULAR?>">                                            
						</td>
                        <td align="center">
                                <input name="txtotrofono<?=$cantidad?>" id="txtotrofono<?=$cantidad?>" type="text" class="classtexto" value="<?=$reg->OTROTELEFONO?>" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="20" maxlength="10">
                                <input name="txtotrofono_origen<?=$cantidad?>" id="txtotrofono_origen<?=$cantidad?>" type="hidden" value="<?=$reg->OTROTELEFONO?>"/>
						</td>
					</tr>
					<?
						}
						
						for ($n = 1; $n <= (5-$cantidad); $n++){
							$cantidadSec=$cantidad+$n;
					?>
					<tr>
						<td height="37">
							<input name="txtnombrecontacto<?=$cantidadSec?>"  id="txtnombrecontacto<?=$cantidadSec?>" type="text" class="classtexto"style="text-transform:uppercase;" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="65">
							<input name="txtnombrecontacto_origen<?=$cantidadSec?>"   id="txtnombrecontacto_origen<?=$cantidadSec?>" type="hidden">
						</td>
                        <td align="center"> 
							<?	
								$con->cmbselectdata("SELECT IDTIPOCONTACTO,DESCRIPCION FROM $con->catalogo.catalogo_tipo_contacto WHERE IDCUENTA='LIF' ORDER BY DESCRIPCION","cmbparentesco$cantidadSec","","onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
							?>
						</td>
                        <td align="center">
							+56<input name="txtprefijo<?=$cantidadSec?>" id="txtprefijo<?=$cantidadSec?>"  type="text" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="4" maxlength="2">&nbsp;&nbsp;<input name="txtfijo<?=$cantidadSec?>" type="text" id="txtfijo<?=$cantidadSec?>" size="20" maxlength="12" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)">                                            
						</td>
                        <td align="center">
							+569<input name="txtcelular<?=$cantidadSec?>" id="txtcelular<?=$cantidadSec?>"  type="text" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="20" maxlength="10">                                            
						</td>
                        <td align="center">
							<input name="txtotrofono<?=$cantidadSec?>" id="txtotrofono<?=$cantidadSec?>" type="text" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)" size="20" maxlength="10">
						</td>
					</tr>
					<? } ?>
			</tbody>
		</table>
		<input name="cantidadcontactoOriginal" id="cantidadcontactoOriginal" type="hidden" value="<?=$cantidad; ?>"/>
  </div>
  <div id="enfermedadesalergiastratamiento">
    <label accesskey="r" title="Enfermedades - alergias y tratamientos">ENFERM. Y TRATAMIEN.</label>

		<table border="1" cellpadding="5" cellspacing="5" width="100%" style="border-collapse:collapse">
			<tbody>
				<tr bgcolor="#3b65b0">
					<td align="center" height="29" width="50%"><strong>TIPO DE ENFERMEDAD</strong></td>
					<td align="center" width="50%"><strong>TRATAMIENTO</strong></td>
				</tr>		
				<?				
					while($rowEnf= $rsTipoEnfermedad->fetch_object()){
						$contador++;
				?>			
				<tr>
					<td align="center">
						<input name="idtipoenfermedad[<?=$contador?>]"  id="idtipoenfermedad[<?=$contador?>]" type="hidden" value="<?=$rowEnf->ID?>"/>
						<input name="txttipoenfermedad[<?=$contador?>]" id="txttipoenfermedad<?=$contador?>" type="text" value="<?=$rowEnf->ENFERMEDAD_ALERGIA?>" style="text-transform:uppercase;"size="80" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)">
						<input name="txttipoenfermedad_origen[<?=$contador?>]" id="txttipoenfermedad_origen<?=$contador?>" type="hidden" value="<?=$rowEnf->ENFERMEDAD_ALERGIA?>">
					</td>
					<td align="center">
						<textarea name="txtatratamiento[<?=$contador?>]" id="txtatratamiento<?=$contador?>" cols="120" rows="2" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"><?=utf8_encode($rowEnf->TRATAMIENTO)?></textarea>
						<input name="txtatratamiento_origen[<?=$contador?>]" id="txtatratamiento_origen<?=$contador?>" type="hidden" value="<?=utf8_encode($rowEnf->TRATAMIENTO)?>"/>
					</td>
				</tr>			
				<?
					} 
					
					for ($nn = 1; $nn <= (5-$contador); $nn++){
						$cantidadTipoenfermedadSec=$contador+$nn;
				?>
				<tr>
					<td align="center"><input name="txttipoenfermedad[<?=$cantidadTipoenfermedadSec?>]" id="txttipoenfermedad[<?=$cantidadTipoenfermedadSec?>]" type="text" style="text-transform:uppercase;" size="80" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
					<td align="center"><textarea name="txtatratamiento[<?=$cantidadTipoenfermedadSec?>]" id="txtatratamiento[<?=$cantidadTipoenfermedadSec?>]" cols="120" rows="2" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></textarea></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
			<input name="cantidadTipoenfermedad" id="cantidadTipoenfermedad" type="hidden" value="<?=$contador; ?>"/>
		<br/>
		<table border="1" cellpadding="5" cellspacing="5" width="100%" style="border-collapse:collapse">
			<tbody>
				<tr bgcolor="#7696d1">
					<td align="center" height="29" width="50%"><strong>TIPO DE ALERGIA</strong></td>
					<td align="center" width="50%"><strong>TRATAMIENTO</strong></td>
				</tr>		
				<?				
					 while($rowAlerg= $rsTipoEnfermedad_alergia->fetch_object()){
						$contadorAlergia++;
				?>			
				<tr>
					<td align="center">
						<input name="idtipoalergia[<?=$contadorAlergia?>]" id="idtipoalergia[<?=$contadorAlergia?>]" type="hidden" value="<?=$rowAlerg->ID?>"/>
						<input name="txttipoalergia[<?=$contadorAlergia?>]" id="txttipoalergia[<?=$contadorAlergia?>]" type="text" value="<?=$rowAlerg->ENFERMEDAD_ALERGIA?>" style="text-transform:uppercase;"size="80" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)">
						<input name="txttipoalergia_origen[<?=$contadorAlergia?>]" id="txttipoalergia_origen[<?=$contadorAlergia?>]" type="hidden" value="<?=$rowAlerg->ENFERMEDAD_ALERGIA?>">
					</td>
					<td align="center">
						<textarea name="txtalergiatratamiento[<?=$contadorAlergia?>]" id="txtalergiatratamiento[<?=$contadorAlergia?>]" style="text-transform:uppercase;" cols="120" rows="2" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"><?=utf8_encode($rowAlerg->TRATAMIENTO)?></textarea>
						<input name="txtalergiatratamiento_origen[<?=$contadorAlergia?>]" id="txtalergiatratamiento_origen<?=$contadorAlergia?>" type="hidden" value="<?=utf8_encode($rowAlerg->TRATAMIENTO)?>"/>
					</td>
				</tr>			
				<?
					} 
					
					for ($m = 1; $m <= (5-$contadorAlergia); $m++){
						$cantidadTipoAlergia=$contadorAlergia+$m;
				?>
				<tr>
					<td align="center"><input name="txttipoalergia[<?=$cantidadTipoAlergia?>]" id="txttipoalergia[<?=$cantidadTipoAlergia?>]" type="text" style="text-transform:uppercase;"size="80" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
					<td align="center"><textarea name="txtalergiatratamiento[<?=$cantidadTipoAlergia?>]" id="txtalergiatratamiento[<?=$cantidadTipoAlergia?>]" cols="120" rows="2" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></textarea></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
		<input name="cantidadTipoAlergia" id="cantidadTipoAlergia" type="hidden" value="<?=$contadorAlergia; ?>"/>
	</div>     
  
	<div id="antecedentesyobservaciones">
		<label accesskey="g" title="Antecedentes medicos y observaciones Especiales">ANTEC. MEDICOS</label>
		
			<table border="1" cellpadding="5" cellspacing="5" width="100%" bgcolor="#9BB7C9" style="border-collapse:collapse">
				<tbody>
					<tr bgcolor="#456981" >
						<td style="color:#FFFFFF"><strong>OTROS ANTECEDENTES MEDICOS</strong></td>						
					</tr>				
					<tr>
						<td align="center">
							<textarea name="txtaotroantecedente" cols="180" rows="10" id="txtaotroantecedente" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"><?=utf8_encode($row_otrosantecedesMed->OBSERVACION)?></textarea>
							<input name="txtaotroantecedente_origen" id="txtaotroantecedente_origen" type="hidden" value="<?=utf8_encode($row_otrosantecedesMed->OBSERVACION)?>"/>
						</td>
					</tr>
				</tbody>          
			</table>
			<input name="idotrosantecedentes" id="idotrosantecedentes" type="hidden" value="<?=$row_otrosantecedesMed->ID?>"/>
			<br/>
			<table border="1" cellpadding="5" cellspacing="5" width="100%" bgcolor="#9BB7C9" style="border-collapse:collapse">
				<tbody>
					<tr bgcolor="#456981" >
						<td style="color:#FFFFFF"><strong>OBSERVACIONES ESPECIALES</strong></td>						
					</tr>				
					<tr>
						<td align="center">
							<textarea name="observacionesespeciales"  id="observacionesespeciales" cols="180" rows="10"style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"><?=utf8_encode($row_observacionesespeciales->OBSERVACION)?></textarea>
							<input name="observacionesespeciales_origen" id="observacionesespeciales_origen" type="hidden" value="<?=utf8_encode($row_observacionesespeciales->OBSERVACION)?>"/>
						</td>
					</tr>
				</tbody>          
			</table>
	</div>  
   
	<div id="asistenciamedica">
		<label accesskey="g" title="Asistencia Medica">ASISTENCIA MED.</label>
	
		<table border="0" cellpadding="2" cellspacing="5" width="100%">
			<tbody>
                <tr>
                  <td valign="top" width="18%">DR. CABECERA</td>
                  <td width="82%">
						<strong>SI</strong><input name="rddrcabecera" id="rddrcabecera" <?=($row_asistenciaMed->DRCABECERA)?"checked":""?> value="TRUE" onclick="mostrar_servicios('drcabecera', 'block');" type="radio">&nbsp;
						<strong>NO</strong><input name="rddrcabecera" id="rddrcabecera" <?=(!$row_asistenciaMed->DRCABECERA)?"checked":""?> value="FALSE" onclick="mostrar_servicios('drcabecera', 'none');" type="radio">                  </td>
                </tr>
                <tr>
					<td></td>
					<td>
						<div style="display:<?=(!$row_asistenciaMed->DRCABECERA)?"none":""?>" id="drcabecera">
							<table border="0" bgcolor="#dae2e4" cellpadding="2" cellspacing="2" width="50%" style="border: 1px solid #849fa6">
								<tbody>
									<tr>
										<td><strong>Nombre</strong></td>
										<td><input name="dr_nombre" id="dr_nombre" type="text" value="<?=$row_asistenciaMed->DRCABECERA?>" class="classtexto" size="35" style="text-transform:uppercase;"  onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
										<td><strong>Especialidad</strong></td>
										<td><input name="dr_especialidad" id="dr_especialidad" type="text" value="<?=$row_asistenciaMed->DOC_CABECERA_ESPECIALIDAD?>" size="35" style="text-transform:uppercase;"  class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
									</tr>
									<tr>
										<td><strong>Fono Fijo</strong></td>
										<td><input name="dr_fono" id="dr_fono" style="text-transform:uppercase;" type="text" value="<?=$row_asistenciaMed->DOC_CABECERA_TELEFONOFIJO?>" size="35" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
										<td><strong>Celular</strong></td>
										<td><input name="dr_celular" id="dr_celular" style="text-transform:uppercase;" type="text" value="<?=$row_asistenciaMed->DOC_CABECERA_TELEFONOCELULAR?>" size="35" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
                <tr>
					<td>AMBULANCIA</td>
					<td>
						<strong>SI</strong><input name="rdambulancia" id="rdambulancia" <?=($row_asistenciaMed->IDSERVICIOAMBULANCIA)?"checked":""?> value="TRUE" onclick="mostrar_servicios('servicio', 'block');" type="radio">&nbsp;
						<strong>NO</strong><input name="rdambulancia" id="rdambulancia" value="FALSE" <?=(!$row_asistenciaMed->IDSERVICIOAMBULANCIA)?"checked":""?> onclick="mostrar_servicios('servicio', 'none');" type="radio">
					</td>
                </tr>
                <tr>
					<td></td>
					<td>
						<div id="servicio" style="display:<?=(!$row_asistenciaMed->IDSERVICIOAMBULANCIA)?"none":""?>">		
							<table border="0" bgcolor="#dae2e4" cellpadding="2" cellspacing="2" width="40%" style="border: 1px solid #849fa6">
								<tbody>
									<tr>
										<td><strong>Servicio</strong></td>
										<td>
										<?
											$con->cmbselectdata("SELECT IDSERVICIOAMBULANCIA,NOMBRE FROM $con->catalogo.catalogo_servicio_ambulancia WHERE IDCUENTA='LIF' ORDER BY NOMBRE","cmbservicio",$row_asistenciaMed->IDSERVICIOAMBULANCIA,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
										?>										
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
                </tr>
                <tr>
					<td>CONVENIO</td>
					<td>
						<strong>SI</strong><input name="rdconvenio" id="rdconvenio" value="TRUE" <?=($row_asistenciaMed->INSTITUCION)?"checked":""?> onclick="mostrar_servicios('institucion', 'block')" type="radio">&nbsp;
						<strong>NO</strong><input name="rdconvenio" id="rdconvenio" value="FALSE" <?=(!$row_asistenciaMed->INSTITUCION)?"checked":""?> onclick="mostrar_servicios('institucion', 'none')" type="radio">
					</td>
                </tr>
                <tr>
					<td></td>
					<td>
						<div id="institucion" style="display:<?=(!$row_asistenciaMed->INSTITUCION)?"none":""?>">
							<table border="0" bgcolor="#dae2e4" cellpadding="2" cellspacing="2" width="40%" style="border: 1px solid #849fa6">
								<tbody>
									<tr>
										<td><strong>Instituci&oacute;n</strong></td>
										<td><input type="text" name="txtinstitucion" id="txtinstitucion" value="<?=$row_asistenciaMed->INSTITUCION?>" size="35" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"> <font color="red">Ej: Seguro Cl&iacute;nica Alemana</font></td>
									</tr>
								</tbody>
							</table>						
						</div>
					</td>
                </tr>	 
			</tbody>
		</table>	
	</div>
	<div id="protocolo">
		<label accesskey="s" title="Protocolos:De Atenci&oacute;n Personalizado, de Seguridad, de Eventos Especiales">PROTOCOLOS</label>
 
 		<table border="1" cellpadding="5" cellspacing="5" width="100%" style="border-collapse:collapse" bgcolor="#9BB7C9">
			<tr bgcolor="#456981">
				<td align="center" colspan="2" style="color:#FFFFFF"><strong>PROTOCOLO DE ATENCION PERSONALIZADO</strong> </td>
			</tr>			<tr bgcolor="#648fac">
				<td height="40" align="center"><strong>ACCION</strong> </td>
				<td align="center" width="81%"><strong>DESCRIPCION DEL PROTOCOLO</strong> </td>
			</tr>
			<?									
				 while($regProtocoloPers= $rsProtocoloAtencionPers->fetch_object()){
					$cantidadPers++;
			?>				
			<tr>
				<td align="center">LLAMADO NRO <?=$cantidadPers?></td>
				<td align="center">
					<input name="idProtoPersonalizado[<?=$cantidadPers?>]"  id="idProtoPersonalizado[<?=$cantidadPers?>]" type="hidden" value="<?=$regProtocoloPers->ID?>"/>
					<textarea name="txtprotocoloPers[<?=$cantidadPers?>]" rows="3" cols="125" id="txtprotocoloPers<?=$cantidadPers?>" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"><?=utf8_encode($regProtocoloPers->LLAMADA)?></textarea>
					<input name="txtprotocoloPers_origen[<?=$cantidadPers?>]" id="txtprotocoloPers_origen[<?=$cantidadPers?>]" type="hidden" value="<?=utf8_encode($regProtocoloPers->LLAMADA)?>"/>
				</td>
			</tr>
			<?
				}
				
				for ($mn = 1; $mn <= (5-$cantidadPers); $mn++){
					$cantidadSecPersonal=$cantidadPers+$mn;
			?>
			<tr>
				<td align="center">LLAMADO NRO <?=$cantidadSecPersonal?></td>
				<td align="center"><textarea name="txtprotocoloPers[<?=$cantidadSecPersonal?>]" id="txtprotocoloPers<?=$cantidadSecPersonal?>" style="text-transform:uppercase;" rows="3" cols="125" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></textarea></td>
			</tr>			
			
			<? } ?>
		</table>
		<input name="cantidadProtPersonalizado" id="cantidadProtPersonalizado" type="hidden" value="<?=$cantidadPers; ?>"/>
		<br/>
		<table border="1" cellpadding="5" cellspacing="5" width="100%" style="border-collapse:collapse" bgcolor="#9BB7C9">
			<tr bgcolor="#456981">
				<td align="center" colspan="2" style="color:#FFFFFF"><strong>PROTOCOLO DE SEGURIDAD</strong> </td>
			</tr>		
			<tr bgcolor="#3b65b0">
				<td height="40" align="center"><strong>ACCION</strong> </td>
				<td align="center" width="81%"><strong>DESCRIPCION DEL PROTOCOLO</strong> </td>
			</tr>				
			<?									
				 while($regProtocoloSeg= $rsProtocoloAtencionSeg->fetch_object()){
					$cantidadSeg++;
			?>				
			<tr>
				<td align="center">LLAMADO NRO <?=$cantidadSeg?></td>
				<td align="center">
					<input name="idProtoSeguridad[<?=$cantidadSeg?>]" id="idProtoSeguridad[<?=$cantidadSeg?>]" type="hidden" value="<?=$regProtocoloSeg->ID?>"/>
					<textarea name="txtprotocoloSeguridad[<?=$cantidadSeg?>]" rows="3" cols="125" id="txtprotocoloSeguridad<?=$cantidadSeg?>" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"><?=utf8_encode($regProtocoloSeg->LLAMADA)?></textarea>
					<input name="txtprotocoloSeguridad_origen[<?=$cantidadSeg?>]" id="txtprotocoloSeguridad_origen[<?=$cantidadSeg?>]" type="hidden" value="<?=utf8_encode($regProtocoloSeg->LLAMADA)?>"/>					
				</td>
			</tr>

			<?
				} 
				
				for ($k = 1; $k <= (5-$cantidadSeg); $k++){
					$cantidadSecSeguridad=$cantidadSeg+$k;
			?>
			<tr>
				<td align="center">LLAMADO NRO <?=$cantidadSecSeguridad?></td>
				<td align="center"><textarea name="txtprotocoloSeguridad[<?=$cantidadSecSeguridad?>]" id="txtprotocoloSeguridad<?=$cantidadSecSeguridad?>"  style="text-transform:uppercase;" rows="3" cols="125" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></textarea></td>
			</tr>			
			
			<? } ?>
		</table>
		<input name="cantidadProtSeguridad" id="cantidadProtSeguridad" type="hidden" value="<?=$cantidadSeg; ?>"/>	
		<br/>
		<table border="1" cellpadding="5" cellspacing="5" width="100%" style="border-collapse:collapse" bgcolor="#9BB7C9">
			<tr bgcolor="#456981">
				<td align="center" colspan="2" style="color:#FFFFFF"><strong>PROTOCOLO DE EVENTOS ESPECIALES</strong> </td>
			</tr>		
			<tr bgcolor="#7696d1">
				<td height="40" align="center"><strong>ACCION</strong> </td>
				<td align="center" width="81%"><strong>DESCRIPCION DEL PROTOCOLO</strong> </td>
			</tr>
			<?									
				 while($regProtocoloEvenEsp= $rsProtocoloAtencionEven->fetch_object()){
					$cantidadEspec++;
			?>				
			<tr>
				<td align="center">LLAMADO NRO <?=$cantidadEspec?></td>
				<td align="center">
					<input name="idProtoEspecial[<?=$cantidadEspec?>]" id="idProtoEspecial[<?=$cantidadEspec?>]" type="hidden" value="<?=$regProtocoloEvenEsp->ID?>"/>
					<textarea name="txtprotocoloEspecial[<?=$cantidadEspec?>]" rows="3" cols="125" id="txtprotocoloEspecial<?=$cantidadEspec?>" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"><?=utf8_encode($regProtocoloEvenEsp->LLAMADA)?></textarea>
					<input name="txtprotocoloEspecial_origen[<?=$cantidadEspec?>]" id="txtprotocoloEspecial_origen[<?=$cantidadEspec?>]" type="hidden" value="<?=utf8_encode($regProtocoloEvenEsp->LLAMADA)?>"/>										
				</td>
			</tr>

			<?
				} 
				
				for ($k = 1; $k <= (5-$cantidadEspec); $k++){
					$cantidadSecEspec=$cantidadEspec+$k;
			?>
			<tr>
				<td align="center">LLAMADO NRO <?=$cantidadSecEspec?></td>
				<td align="center"><textarea name="txtprotocoloEspecial[<?=$cantidadSecEspec?>]" id="txtprotocoloEspecial<?=$cantidadSecEspec?>" style="text-transform:uppercase;" rows="3" cols="125" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></textarea></td>
			</tr>			
			<? } ?>
		</table>
		<input name="cantidadProtEspecial"  id="cantidadProtEspecial" type="hidden" value="<?=$cantidadEspec; ?>"/>	
  </div>  
     
	<div id="plantarifarios">
		<label accesskey="g" title="Plan Tarifario">TARIFARIO</label>
		
			<table border="0" cellpadding="2" cellspacing="5" width="100%">
				<tbody>
					<tr>
						<td width="67">DISPOSITIVO</td>
						<td width="210"><input name="txtdispositivo" type="text" id="txtdispositivo" value="<?=$row_plantarifario->DISPOSITIVO?>" style="text-transform:uppercase;" size="35" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
						<td width="57">MODELO</td>
						<td width="245"><input name="txtmodelo" type="text" id="txtmodelo" value="<?=$row_plantarifario->MODELO?>" style="text-transform:uppercase;" size="35" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
					</tr>
					<tr>
						<td>PLAN</td>
						<td><input name="txtplan" type="text" id="txtplan" size="35"value="<?=$row_plantarifario->PLAN?>" style="text-transform:uppercase;" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
						<td>CELULAR ABONADO</td>
						<td><input name="txtcelularabonado" type="text" id="txtcelularabonado" value="<?=$row_plantarifario->TELEFONOCELULAR?>" style="text-transform:uppercase;" size="35" class="classtexto" onFocus="coloronFocus(this)" onBlur="colorOffFocus(this)"></td>
					</tr>
				</tbody>
			</table> 
			<input name="txtdispositivo_origen"  id="txtdispositivo_origen" type="hidden" value="<?=$row_plantarifario->DISPOSITIVO?>"/>	
			<input name="txtmodelo_origen"  id="txtmodelo_origen" type="hidden" value="<?=$row_plantarifario->MODELO?>"/>	
			<input name="txtplan_origen" id="txtplan_origen" type="hidden" value="<?=$row_plantarifario->PLAN?>"/>	
			<input name="txtcelularabonado_origen" id="txtcelularabonado_origen" type="hidden" value="<?=$row_plantarifario->TELEFONOCELULAR?>"/>	
	</div>
</div>


<input type="submit" name="btngrabarexp" id="btngrabarexp" value=">>> <?=_("GRABAR DATOS") ;?>"   style="text-align:center;font-weight:bold;font-size:10px;height:35px;"/>


</form>

<script type="text/javascript">
	var objTabs = new Zapatec.Tabs({
	  // ID of Top bar to show the Tabs: Game, Photo, Music, Chat
	  tabBar: 'tabBar',
	  /*
	  ID to get the LABEL contents to create the tabBar tabs
	  Also, each DIV in this ID will contain the contents for each tab
	  */
	  tabs: 'tabs',
	  // Theme to use for the tabs
	  theme: 'rounded',
	  themePath: '../../../librerias/zapatec/zptabs/themes/',
	  closeAction: 'hide'
	});
</script>

</body>
</html>
