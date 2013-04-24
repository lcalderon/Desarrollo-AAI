<?php 	
	session_start(); 

	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");
	include_once('../../modelo/clase_ubigeo.inc.php');	
	
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
//die($_POST["radiovives"]."//");

	$idafiliado=$_POST["idafiliado"];
	$idpaciente=$_POST["id_paciente"];
//die($_POST["txtusuariofechanacimiento"]."--");
  ///*************** contratante ***************///
/*//datos del contratante
 	$cedulacontrante=trim($_POST["txtcontranterut"]);
	$cedulacodcontrante=trim($_POST["txtcontratanterut2"]);
	$nombrecontratante=trim($_POST["txtcontratantenombre"]);
	$apPaternocontratante=trim($_POST["txtcontratanteapepaterno"]);
	$apMaternocontratante=trim($_POST["txtcontratanteapematerno"]);
	$generoContratante=trim($_POST["cmbcontratantegenero"]);
	
	$celularcontratante=trim($_POST["txtcontratantecelular"]);
	$celularCargocontratante=trim($_POST["txtcontratantecelularcargo"]);
	$emailcontratante=trim($_POST["txtcontratanteemail"]);
	
	$rows["IDDOCUMENTO"]=$cedulacontrante;
	$rows["DIGITOVERIFICADOR"]=$cedulacodcontrante;
	$rows["NOMBRE"]=$nombrecontratante;
	$rows["APPATERNO"]=$apPaternocontratante;
	$rows["APMATERNO"]=$apMaternocontratante;
	$rows["GENERO"]=$generoContratante;
	$rows["TELEFONOFIJO"]=$telefonocontratante;
	$rows["CELULAR"]=$celularcontratante;
	$rows["CELULARCARGO"]=$celularCargocontratante;
	$rows["EMAIL"]=$emailcontratante;
	$rows["IDUSUARIOMOD"]=$_SESSION["user"]; 

//actualiza los datos contratante
	$resultado=$con->update("$con->catalogo.catalogo_afiliado_persona_datoscontratante",$rows,"WHERE IDAFILIADO='".$_POST["idafiliado"]."'");*/
		
	/****** ubigeo contratante ******/
 /*		$ubigeo = new ubigeo(); 
	
		$rowubi["CVEPAIS"]=$con->lee_parametro("IDPAIS");
		$rowubi["IDAFILIADO"]=$_POST["idafiliado"];
		$rowubi["CVEENTIDAD1"]=$_POST["CVEENTIDAD1"];
		$rowubi["CVEENTIDAD2"]=$_POST["CVEENTIDAD2"];
		$rowubi["CVEENTIDAD3"]=$_POST["CVEENTIDAD3"];
		$rowubi["CVEENTIDAD4"]=$_POST["CVEENTIDAD4"];
		$rowubi["CVEENTIDAD5"]=$_POST["CVEENTIDAD5"];
		$rowubi["CVEENTIDAD6"]=$_POST["CVEENTIDAD6"];
		$rowubi["CVEENTIDAD7"]=$_POST["CVEENTIDAD7"];
		$rowubi["LATITUD"]=$_POST["LATITUD"];
		$rowubi["LONGITUD"]=$_POST["LONGITUD"];
		$rowubi["DIRECCION"]=utf8_encode(trim($_POST["txtcontratantedireccion"]));	
		$rowubi["REFERENCIA1"]=utf8_encode(trim($_POST["txtcontratantesector"]));	 
		
	if($_POST["CVEENTIDAD1"] and trim($_POST["txtcontratantedireccion"])){
		
		echo 11;
		$ubigeo->grabar_ubigeo($rowubi,"$con->catalogo.catalogo_afiliado_persona_ubigeo_datoscontrante","IDAFILIADO");
		
	}*/
///************************************///
	
///*************** afiliado ***************///
//datos del afiliado
	$cedulaAfiliado=trim($_POST["txtusuariorut"]);
	$cedulacodAfiliado=trim($_POST["txtusuariorut2"]);
	$nombreAfiliado=trim($_POST["txtusuarionombre"]);
	$apPaternoAfiliado=trim($_POST["txtusuarioapepaterno"]);
	$apMaternoAfiliado=trim($_POST["txtusuarioapematerno"]);
	$generoAfiliado=trim($_POST["cmbusuariogenero"]);
	$estadoCivilAfiliado=trim($_POST["cmbusuarioestadocivil"]);
	$fechaNacimientoAfiliado=trim($_POST["txtusuariofechanacimiento"]);
	$grupoSanguineoAfiliado=trim($_POST["cmbusuariogruposang"]);
	$sistemasaludAfiliado=trim($_POST["cmbusuariosistemasalud"]);
	$emailAfiliado=trim($_POST["txtusuarioemail"]);
	$vivesoloAfiliado=trim($_POST["radiovives"]);
	
	$rows_afi["IDDOCUMENTO"]=$cedulaAfiliado;
	$rows_afi["DIGITOVERIFICADOR"]=$cedulacodAfiliado;
	$rows_afi["NOMBRE"]=$nombreAfiliado;
	$rows_afi["APPATERNO"]=$apPaternoAfiliado;
	$rows_afi["APMATERNO"]=$apMaternoAfiliado;	
	$rows_afi["GENERO"]=$generoAfiliado;
	$rows_afi["ESTADOCIVIL"]=$estadoCivilAfiliado;
	$rows_afi["FECHANACIMIENTO"]=$fechaNacimientoAfiliado;
	$rows_afi["IDGRUPOSANGUINEO"]=$grupoSanguineoAfiliado;
	$rows_afi["ID_SISTEMASALUD"]=$sistemasaludAfiliado;
	$rows_afi["EMAIL1"]=$emailAfiliado;	
	$rows_afi["VIVESOLO"]=$vivesoloAfiliado;
	$rows_afi["IDUSUARIOMOD"]=$_SESSION["user"];

//actualiza los datos afiliado
	$resultado_afi=$con->update("$con->catalogo.catalogo_afiliado_persona",$rows_afi,"WHERE IDAFILIADO='$idafiliado'");
		
	/****** ubigeo afiliado ******/
	 
	//consulta cveids	
	$cveids = explode("-",$_POST["cveentidad3_usuario"]);
	$idsoaang=$cveids[0];
	$idcve3usuario=$cveids[1];
	
	$Sql_entidadesSoaa="SELECT CVEENTIDAD1,CVEENTIDAD2 FROM $con->catalogo.catalogo_entidad WHERE ID=$idsoaang";
	$respCveSoaa=$con->consultation($Sql_entidadesSoaa);
 
		$ubigeo = new ubigeo(); 
	
		$rowubi_afi["CVEPAIS"]=$con->lee_parametro("IDPAIS");
		$rowubi_afi["IDAFILIADO"]=$idafiliado;
		$rowubi_afi["CVEENTIDAD1"]=$respCveSoaa[0][0];
		$rowubi_afi["CVEENTIDAD2"]=$respCveSoaa[0][1];
		//$rowubi_afi["CVEENTIDAD3"]=$respCveSoaa[0][2];
		$rowubi_afi["DIRECCION"]=utf8_encode(trim($_POST["txtusuariodireccion"]));	
		$rowubi_afi["REFERENCIA1"]=utf8_encode(trim($_POST["txtusuariosector"]));	
	 
	if(trim($_POST["txtusuariodireccion"]) and $idcve3usuario >0)	$ubigeoUsuario=$ubigeo->grabar_ubigeo($rowubi_afi,"$con->catalogo.catalogo_afiliado_persona_ubigeo","IDAFILIADO");
/************************************/
 
	if($_POST["cveentidad3_usuario"] != $_POST["CVEENTIDAD3_usuario_origen"] and $_POST["cveentidad3_usuario"]){
	
		$rowWebservice_ubigeoUsuario["IDPACIENTE"]=$_POST["id_paciente"];
		$rowWebservice_ubigeoUsuario["NOMBRETABLA"]="tbl_paciente";
		$rowWebservice_ubigeoUsuario["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
		$rowWebservice_ubigeoUsuario["NOMBRECAMPO"]="id_comuna_FK";		
		$rowWebservice_ubigeoUsuario["VALOR"]=$idcve3usuario;
		$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_ubigeoUsuario,1);			
	}
	
	if($_POST["txtusuariodireccion"] != $_POST["txtusuariodireccion_origen"] and $_POST["txtusuariodireccion"]){
	
		$rowWebservice_ubigeoUsuario["IDPACIENTE"]=$_POST["id_paciente"];
		$rowWebservice_ubigeoUsuario["NOMBRETABLA"]="tbl_paciente";
		$rowWebservice_ubigeoUsuario["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
		$rowWebservice_ubigeoUsuario["NOMBRECAMPO"]="direccion_paciente";	
		$rowWebservice_ubigeoUsuario["VALOR"]=utf8_encode(trim($_POST["txtusuariodireccion"]));
		$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_ubigeoUsuario,1);			
	}
	
	if($_POST["txtusuariosector"] != $_POST["txtusuariosector_origen"] and $_POST["txtusuariosector"]){
	
		$rowWebservice_ubigeoUsuario["IDPACIENTE"]=$_POST["id_paciente"];
		$rowWebservice_ubigeoUsuario["NOMBRETABLA"]="tbl_paciente";
		$rowWebservice_ubigeoUsuario["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
		$rowWebservice_ubigeoUsuario["NOMBRECAMPO"]="sector_paciente";	
		$rowWebservice_ubigeoUsuario["VALOR"]=trim($_POST["txtusuariosector"]);
		$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_ubigeoUsuario,1);			
	}

///*************** vivienda secundaria ***************///

 	/****** ubigeo vivienda ******/
 
	//consulta cveids	
		$cveidsec = explode("-",$_POST["cveentidad3_viviendasec"]);
		$idsoaangsec=$cveidsec[0];
		$idcve3usuariosec=$cveidsec[1];
	
		$Sql_entidadesSoaasec="SELECT CVEENTIDAD1,CVEENTIDAD2 FROM $con->catalogo.catalogo_entidad WHERE ID=$idsoaangsec";
		$respCveSoaasec=$con->consultation($Sql_entidadesSoaasec);
 
		$ubigeo = new ubigeo(); 
	
		$rowubi_vivien["CVEPAIS"]=$con->lee_parametro("IDPAIS");
		$rowubi_vivien["IDAFILIADO"]=$idafiliado;
		$rowubi_vivien["CVEENTIDAD1"]=$respCveSoaasec[0][0];
		$rowubi_vivien["CVEENTIDAD2"]=$respCveSoaasec[0][1];
		//$rowubi_vivien["TELEFONO"]=$_POST["txtviviendasectelefono"];
		$rowubi_vivien["DIRECCION"]=utf8_encode(trim($_POST["txtdireccionviviendasecudaria"]));	
		$rowubi_vivien["REFERENCIA1"]=utf8_encode(trim($_POST["txtsectorviviendasecundaria"]));	

	if($_POST["cveentidad3_viviendasec"] and  trim($_POST["txtdireccionviviendasecudaria"]) and $respCveSoaasec[0][0] >0){

		$resp_viviendasec=$ubigeo->grabar_ubigeo($rowubi_vivien,"$con->catalogo.catalogo_afiliado_persona_domicilio_ubigeo","IDAFILIADO");
		$rowubi_viviensec["TELEFONO"]=$_POST["txtviviendasectelefono"];
		if($resp_viviendasec)	$con->update("$con->catalogo.catalogo_afiliado_persona_domicilio_ubigeo",$rowubi_viviensec,"WHERE IDAFILIADO='".$idafiliado."'");		

		$rowWebservice_viviendasec["IDPACIENTE"]=$_POST["id_paciente"];		
		$rowWebservice_viviendasec["NOMBRETABLA"]="tbl_paciente";		
		$rowWebservice_viviendasec["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
	

		if($_POST["cveentidad3_viviendasec"] != $_POST["CVEENTIDAD3_viviendasec_origen"] and $_POST["cveentidad3_viviendasec"]){
		
			$rowWebservice_ubigeoUsuario["IDPACIENTE"]=$_POST["id_paciente"];
			$rowWebservice_ubigeoUsuario["NOMBRETABLA"]="tbl_paciente";
			$rowWebservice_ubigeoUsuario["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
			$rowWebservice_ubigeoUsuario["NOMBRECAMPO"]="id_comuna_secundaria_FK";		
			$rowWebservice_ubigeoUsuario["VALOR"]=$idcve3usuariosec;
			$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_ubigeoUsuario,1);	

		}
				
		if(strtoupper($_POST["txtdireccionviviendasecudaria_origen"]) != strtoupper($_POST["txtdireccionviviendasecudaria"]) and $_POST["txtdireccionviviendasecudaria"]){

			$rowWebservice_viviendasec["NOMBRECAMPO"]="direccion_secundario";		
			$rowWebservice_viviendasec["VALOR"]=strtoupper($_POST["txtdireccionviviendasecudaria"]);	
		
			if($resp_viviendasec) $con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_viviendasec,1);	
		
		}	
		
		if(strtoupper($_POST["txtsectorviviendasecundaria_origen"]) != strtoupper($_POST["txtsectorviviendasecundaria"]) and $_POST["txtsectorviviendasecundaria"]){

			$rowWebservice_viviendasec["NOMBRECAMPO"]="sector_secundario";		
			$rowWebservice_viviendasec["VALOR"]=strtoupper($_POST["txtsectorviviendasecundaria"]);	
		
			if($resp_viviendasec) $con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_viviendasec,1);	
		
		}
		
		if($_POST["txtviviendasectelefono_origen"] != $_POST["txtviviendasectelefono"]){
			
			$rowWebservice_viviendasec["NOMBRECAMPO"]="telefono_secundario";		
			$rowWebservice_viviendasec["VALOR"]=$_POST["txtviviendasectelefono"];		
			if($resp_viviendasec) $con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_viviendasec,1);			
		}
	}
	
//telefonos usuario	
	//telefono fijo
	$rowWebservice_usuariotelFijo["IDPACIENTE"]=$_POST["id_paciente"];		
	$rowWebservice_usuariotelFijo["NOMBRETABLA"]="tbl_paciente";		
	$rowWebservice_usuariotelFijo["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");		
	$rowWebservice_usuariotelFijo["NOMBRECAMPO"]="telefono_paciente";		
	$rowWebservice_usuariotelFijo["VALOR"]=$_POST["txtusuariotelefono"];
		
	$rowTelefonofijo["NUMEROTELEFONO"]=$_POST["txtusuariotelefono"];
	$rowTelefonofijo["IDTIPOTELEFONO"]=2;
		
	if($_POST["txtusuariotelefono"] != $_POST["txtusuariotelefono_origen"] and $_POST["txtusuariotelefono_origen"] >0){


		
		$resptelefono =$con->update("$con->catalogo.catalogo_afiliado_persona_telefono",$rowTelefonofijo,"WHERE IDAFILIADO='".$idafiliado."' AND NUMEROTELEFONO=".$_POST["txtusuariotelefono_origen"]);			
		
		if($resptelefono) $con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_usuariotelFijo,1);
	} else if($_POST["txtusuariotelefono"]){
		$rowTelefonofijo["IDAFILIADO"]=$idafiliado;
		$rowTelefonofijo["IDUSUARIOMOD"]=$_SESSION["user"];
		$resptelefono=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_telefono",$rowTelefonofijo);
		
		if($resptelefono) $con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_usuariotelFijo,1);		
	}
	
	//telefono celular 
	$rowTelefonocelular["NUMEROTELEFONO"]=$_POST["txtusuariocelular"];		
	$rowTelefonocelular["IDTIPOTELEFONO"]=1;
	
	$rowWebservice_usuariotelCel["IDPACIENTE"]=$_POST["id_paciente"];		
	$rowWebservice_usuariotelCel["NOMBRETABLA"]="tbl_paciente";		
	$rowWebservice_usuariotelCel["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");		
	$rowWebservice_usuariotelCel["NOMBRECAMPO"]="celular_paciente";		
	$rowWebservice_usuariotelCel["VALOR"]=$_POST["txtusuariocelular"];
	
	if($_POST["txtusuariocelular"] != $_POST["txtusuariocelular_origen"] and $_POST["txtusuariocelular_origen"] >0){
						
		$resptelefonocel=$con->update("$con->catalogo.catalogo_afiliado_persona_telefono",$rowTelefonocelular,"WHERE IDAFILIADO='".$idafiliado."' AND NUMEROTELEFONO=".$_POST["txtusuariocelular_origen"]);		
		
		if($resptelefonocel) $con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_usuariotelCel,1);			
	} else if($_POST["txtusuariocelular"]){
		$rowTelefonocelular["IDAFILIADO"]=$idafiliado;
		$rowTelefonocelular["IDUSUARIOMOD"]=$_SESSION["user"];
		$resptelefonocelnew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_telefono",$rowTelefonocelular);

		if($resptelefonocelnew) $con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_usuariotelCel,1);		
	}
	
/************************************/
 
/////*************** contactos *********************/////
 
	for ($n = 1; $n <=5; $n++){

		$idcontacto=$_POST["idcontacto$n"];

		$nombrecontacto[$n]=trim($_POST["txtnombrecontacto$n"]);
		$nombrecontacto_origen[$n]=trim($_POST["txtnombrecontacto_origen$n"]);
		$cmbparentesco[$n]=trim($_POST["cmbparentesco$n"]);
		$cmbparentesco_origen[$n]=trim($_POST["cmbparentesco_origen$n"]);
		$numerofijo[$n]=trim($_POST["txtfijo$n"]);
		$numerofijo_origen[$n]=trim($_POST["txtfijo_origen$n"]);
		$numerocelular[$n]=trim($_POST["txtcelular$n"]);
		$numerocelular_origen[$n]=trim($_POST["txtcelular_origen$n"]);
		$otrotelefono[$n]=trim($_POST["txtotrofono$n"]);
		$otrotelefono_origen[$n]=trim($_POST["txtotrofono_origen$n"]);
 
		//actualiza contacto
		$rowCont["NOMBRE"]=$nombrecontacto[$n];
		$rowCont["IDTIPOCONTACTO"]=$cmbparentesco[$n];
		$rowCont["TELEFONOFIJO"]=$numerofijo[$n];
		$rowCont["TELEFONOCELULAR"]=$numerocelular[$n];
		$rowCont["OTROTELEFONO"]=$otrotelefono[$n];
		$rowCont["PRIORIDAD"]=$n;
		
		$rowWebservice_contacto["IDPACIENTE"]=$_POST["id_paciente"];		
		$rowWebservice_contacto["NOMBRETABLA"]="tbl_paciente";		
		$rowWebservice_contacto["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");		

		 if($n <=$_POST["cantidadcontactoOriginal"] and $idcontacto){
						
			$rscotacto=$con->update("$con->catalogo.catalogo_afiliado_persona_contacto",$rowCont,"WHERE ID='$idcontacto' AND IDAFILIADO='$idafiliado'");
			
			if($rscotacto){
				
				if(strtoupper($nombrecontacto[$n]) != strtoupper($nombrecontacto_origen[$n])){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_nombre_$n";		
					$rowWebservice_contacto["VALOR"]=strtoupper($nombrecontacto[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}	
				
				if($cmbparentesco[$n] != $cmbparentesco_origen[$n]){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_tipo_".$n."_FK";		
					$rowWebservice_contacto["VALOR"]=strtoupper($cmbparentesco[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}	
				
				if($numerofijo[$n] != $numerofijo_origen[$n]){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_fono_$n";		
					$rowWebservice_contacto["VALOR"]=strtoupper($numerofijo[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}
				
				if($numerocelular[$n] != $numerocelular_origen[$n]){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_celular_$n";		
					$rowWebservice_contacto["VALOR"]=strtoupper($numerocelular[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}
				
				if($otrotelefono[$n] != $otrotelefono_origen[$n]){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_otro_$n";		
					$rowWebservice_contacto["VALOR"]=strtoupper($otrotelefono[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}				
			}			
		} else if($nombrecontacto[$n] and $cmbparentesco[$n] and !$idcontacto){
			
			$rowCont["IDAFILIADO"]=$idafiliado;
			$rscotactonew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_contacto",$rowCont);

			if($rscotactonew){
				
				if(strtoupper($nombrecontacto[$n])){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_nombre_$n";		
					$rowWebservice_contacto["VALOR"]=strtoupper($nombrecontacto[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}	
				
				if($cmbparentesco[$n]){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_tipo_".$n."_FK";
					$rowWebservice_contacto["VALOR"]=strtoupper($cmbparentesco[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}	
				
				if($numerofijo[$n]){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_fono_$n";		
					$rowWebservice_contacto["VALOR"]=strtoupper($numerofijo[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}
				
				if($numerocelular[$n]){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_celular_$n";		
					$rowWebservice_contacto["VALOR"]=strtoupper($numerocelular[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);		
				}
				
				if($otrotelefono[$n]){
					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_otro_$n";		
					$rowWebservice_contacto["VALOR"]=strtoupper($otrotelefono[$n]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);
				}				
			}	
		} 
		 
		if($nombrecontacto[$n] =="" and $cmbparentesco[$n] <1 and $idcontacto){
			
			$rsdeleteContacto=$con->query("DELETE FROM $con->catalogo.catalogo_afiliado_persona_contacto WHERE ID ='$idcontacto'");

			if($rsdeleteContacto){

					$rowWebservice_contacto["IDPACIENTE"]=$idpaciente;
					$rowWebservice_contacto["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_nombre_$n";		
					$rowWebservice_contacto["VALOR"]="";
					$rowWebservice_contacto["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);	
				
					$rowWebservice_contacto["IDPACIENTE"]=$idpaciente;
					$rowWebservice_contacto["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_tipo_".$n."_FK";
					$rowWebservice_contacto["VALOR"]="";
					$rowWebservice_contacto["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);	
					 
					$rowWebservice_contacto["IDPACIENTE"]=$idpaciente;
					$rowWebservice_contacto["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_fono_$n";		
					$rowWebservice_contacto["VALOR"]="";
					$rowWebservice_contacto["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);
				
					$rowWebservice_contacto["IDPACIENTE"]=$idpaciente;
					$rowWebservice_contacto["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_celular_$n";		
					$rowWebservice_contacto["VALOR"]="";
					$rowWebservice_contacto["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);	

					$rowWebservice_contacto["IDPACIENTE"]=$idpaciente;	
					$rowWebservice_contacto["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_contacto["NOMBRECAMPO"]="contacto_otro_$n";		
					$rowWebservice_contacto["VALOR"]="";
					$rowWebservice_contacto["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_contacto,1);
					
			}
		}
		
		$idcontacto="";
	}

/////*************** enfermedades y alergias *********************/////
	//tipo enfermedad
	for ($a = 1; $a<=5; $a++){

		$idtipoenfermedad=$_POST["idtipoenfermedad"][$a];
		$tipoenfermedad[$a]=utf8_encode(strtoupper(trim($_POST["txttipoenfermedad"][$a])));
		$tipoenfermedad_origen[$a]=utf8_encode(strtoupper(trim($_POST["txttipoenfermedad_origen"][$a])));
		$tratamientoEnfermedad[$a]=utf8_encode(strtoupper(trim($_POST["txtatratamiento"][$a])));
		$tratamientoEnfermedad_origen[$a]=utf8_encode(strtoupper(trim($_POST["txtatratamiento_origen"][$a])));
		//actualiza 
		$rowTipoenf["ENFERMEDAD_ALERGIA"]=$tipoenfermedad[$a];
		$rowTipoenf["TRATAMIENTO"]=$tratamientoEnfermedad[$a];
		$rowTipoenf["PRIORIDAD"]=$a;		

		if($a <=$_POST["cantidadTipoenfermedad"] and $idtipoenfermedad){
			$rsenfermedadtratamiento=$con->update("$con->catalogo.catalogo_afiliado_persona_enfermedades_alergias_tratamiento",$rowTipoenf,"WHERE ID='$idtipoenfermedad' AND IDAFILIADO='$idafiliado'");		
			
			$rowWebservice_tipoenfer["IDPACIENTE"]=$_POST["id_paciente"];		
			$rowWebservice_tipoenfer["NOMBRETABLA"]="tbl_paciente";		
			$rowWebservice_tipoenfer["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	

			if($rsenfermedadtratamiento){
				
				if($tipoenfermedad[$a] != $tipoenfermedad_origen[$a]){
					
					$rowWebservice_tipoenfer["NOMBRECAMPO"]="tipo_enfermedad_$a";		
					$rowWebservice_tipoenfer["VALOR"]=$tipoenfermedad[$a];
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoenfer,1);		
				}
				
				if($tratamientoEnfermedad[$a] != $tratamientoEnfermedad_origen[$a]){
					
					$rowWebservice_tipoenfer["NOMBRECAMPO"]="tratamiento_enfermedad_$a";		
					$rowWebservice_tipoenfer["VALOR"]=$tratamientoEnfermedad[$a];
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoenfer,1);		
				}			
			}
			
		}  else if($tipoenfermedad[$a] and !$idtipoenfermedad){
			
			$rowTipoenf["IDAFILIADO"]=$idafiliado;
			$rowTipoenf["ARRTIPOPADECIMIENTO"]="ENFERMEDAD";
			$rsenfermedadtratamientoNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_enfermedades_alergias_tratamiento",$rowTipoenf);
			
			if($rsenfermedadtratamientoNew){
				
				if($tipoenfermedad[$a]){
					
					$rowWebservice_tipoenfer["NOMBRECAMPO"]="tipo_enfermedad_$a";		
					$rowWebservice_tipoenfer["VALOR"]=$tipoenfermedad[$a];
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoenfer,1);		
				}
				
				if($tratamientoEnfermedad[$a]){
					
					$rowWebservice_tipoenfer["NOMBRECAMPO"]="tratamiento_enfermedad_$a";		
					$rowWebservice_tipoenfer["VALOR"]=$tratamientoEnfermedad[$a];
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoenfer,1);		
				}		
			}			
		}
		
/* 
		if($tipoenfermedad[$a] ==""  and $tratamientoEnfermedad[$a] =="" and $idtipoenfermedad){
			
			$rsdeleteContacto=$con->query("DELETE FROM $con->catalogo.catalogo_afiliado_persona_enfermedades_alergias_tratamiento WHERE ID ='$idtipoenfermedad'");

			if($rsdeleteContacto){

					$rowWebservice_tipoenfer["IDPACIENTE"]=$idpaciente;
					$rowWebservice_tipoenfer["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_tipoenfer["NOMBRECAMPO"]="tipo_enfermedad_$a";		
					$rowWebservice_tipoenfer["VALOR"]="";
					$rowWebservice_tipoenfer["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoenfer,1);	
				
					$rowWebservice_tipoenfer["IDPACIENTE"]=$idpaciente;
					$rowWebservice_tipoenfer["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_tipoenfer["NOMBRECAMPO"]="tratamiento_enfermedad_$a";
					$rowWebservice_tipoenfer["VALOR"]="";
					$rowWebservice_tipoenfer["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoenfer,1);				
			}
		} */
		
	}	

	//tipo alergias
	for ($i = 1; $i<=5; $i++){

		$idtipoalergia=$_POST["idtipoalergia"][$i];
		$tipoalergia[$i]=utf8_encode(trim($_POST["txttipoalergia"][$i]));
		$tipoalergia_origen[$i]=utf8_encode(trim($_POST["txttipoalergia_origen"][$i]));
		$tratamientoAlergia[$i]=utf8_encode(trim($_POST["txtalergiatratamiento"][$i]));
		$tratamientoAlergia_origen[$i]=utf8_encode(trim($_POST["txtalergiatratamiento_origen"][$i]));
		
		//actualiza 
		$rowTipoalerg["ENFERMEDAD_ALERGIA"]=$tipoalergia[$i];
		$rowTipoalerg["TRATAMIENTO"]=$tratamientoAlergia[$i];
		$rowTipoalerg["PRIORIDAD"]=$i;		
	 
		if($i <=$_POST["cantidadTipoAlergia"] and $idtipoalergia){
			$rsalergias=$con->update("$con->catalogo.catalogo_afiliado_persona_enfermedades_alergias_tratamiento",$rowTipoalerg,"WHERE ID='$idtipoalergia' AND IDAFILIADO='$idafiliado'");		
					
			$rowWebservice_tipoalergia["IDPACIENTE"]=$_POST["id_paciente"];		
			$rowWebservice_tipoalergia["NOMBRETABLA"]="tbl_paciente";		
			$rowWebservice_tipoalergia["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	

			if($rsalergias){
				
				if(strtoupper($tipoalergia[$i]) != strtoupper($tipoalergia_origen[$i])){
					
					$rowWebservice_tipoalergia["NOMBRECAMPO"]="tipo_alergia_$i";		
					$rowWebservice_tipoalergia["VALOR"]=strtoupper($tipoalergia[$i]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoalergia,1);		
				}
				
				if(strtoupper($tratamientoAlergia[$i]) != strtoupper($tratamientoAlergia_origen[$i])){
					
					$rowWebservice_tipoalergia["NOMBRECAMPO"]="tratamiento_alergia_$i";		
					$rowWebservice_tipoalergia["VALOR"]=strtoupper($tratamientoAlergia[$i]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoalergia,1);		
				}			
			}
		}  else if($tipoalergia[$i] and !$idtipoalergia){
			
			$rowTipoalerg["IDAFILIADO"]=$idafiliado;
			$rowTipoalerg["ARRTIPOPADECIMIENTO"]="ALERGIA";
			$rsalergiasNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_enfermedades_alergias_tratamiento",$rowTipoalerg);
			
			$rowWebservice_tipoalergia["IDPACIENTE"]=$_POST["id_paciente"];		
			$rowWebservice_tipoalergia["NOMBRETABLA"]="tbl_paciente";		
			$rowWebservice_tipoalergia["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	

			if($rsalergiasNew){
				
				if(strtoupper($tipoalergia[$i])){
					
					$rowWebservice_tipoalergia["NOMBRECAMPO"]="tipo_alergia_$i";		
					$rowWebservice_tipoalergia["VALOR"]=strtoupper($tipoalergia[$i]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoalergia,1);		
				}
				
				if(strtoupper($tratamientoAlergia[$i])){
					
					$rowWebservice_tipoalergia["NOMBRECAMPO"]="tratamiento_alergia_$i";		
					$rowWebservice_tipoalergia["VALOR"]=strtoupper($tratamientoAlergia[$i]);
					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoalergia,1);		
				}			
			}			
		}
		
	/* 	if($tipoalergia[$i] ==""  and $tratamientoAlergia[$i] =="" and $idtipoalergia){
			
			$rsdeleteContacto=$con->query("DELETE FROM $con->catalogo.catalogo_afiliado_persona_enfermedades_alergias_tratamiento WHERE ID ='$idtipoalergia'");

			if($rsdeleteContacto){

					$rowWebservice_tipoalergia["IDPACIENTE"]=$idpaciente;
					$rowWebservice_tipoalergia["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_tipoalergia["NOMBRECAMPO"]="tipo_alergia_$i";		
					$rowWebservice_tipoalergia["VALOR"]="";
					$rowWebservice_tipoalergia["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoalergia,1);	
				
					$rowWebservice_tipoalergia["IDPACIENTE"]=$idpaciente;
					$rowWebservice_tipoalergia["NOMBRETABLA"]="tbl_paciente";					
					$rowWebservice_tipoalergia["NOMBRECAMPO"]="tratamiento_alergia_$i";
					$rowWebservice_tipoalergia["VALOR"]="";
					$rowWebservice_tipoalergia["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_tipoalergia,1);				
			}
		} */		
	
	}

/////*************** Antecedentes medico y observaciones *********************/////
	//Antecedentes medico
	
 		$obsotrosantecedentes=utf8_encode(strtoupper(trim($_POST["txtaotroantecedente"])));
 		$obsotrosantecedentes_origen=utf8_encode(strtoupper(trim($_POST["txtaotroantecedente_origen"])));
		
		$row_antecendentes["OBSERVACION"]=$obsotrosantecedentes;
		$row_antecendentes["IDUSUARIOMOD"]=$_SESSION["user"];
	
		$rowWebservice_otrosAntmed["IDPACIENTE"]=$_POST["id_paciente"];
		$rowWebservice_otrosAntmed["NOMBRETABLA"]="tbl_paciente";
		$rowWebservice_otrosAntmed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
		$rowWebservice_otrosAntmed["NOMBRECAMPO"]="antecedentes_medicos";		
		$rowWebservice_otrosAntmed["VALOR"]=$obsotrosantecedentes;
		
		if ($con->exist("$con->catalogo.catalogo_afiliado_persona_otrosantecedentesmedico","IDAFILIADO"," WHERE IDAFILIADO=$idafiliado")){
			
			$rsotrosAntecMed=$con->update("$con->catalogo.catalogo_afiliado_persona_otrosantecedentesmedico",$row_antecendentes,"WHERE IDAFILIADO='$idafiliado'");

			if($rsotrosAntecMed){					
				if($obsotrosantecedentes != $obsotrosantecedentes_origen)	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_otrosAntmed,1);
			}				
			
		} else if($obsotrosantecedentes){
			$row_antecendentes["IDAFILIADO"]=$idafiliado;
			$rsaAntecedenetsNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_otrosantecedentesmedico",$row_antecendentes);
			if($rsaAntecedenetsNew)	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_otrosAntmed,1);
		}
 	
		//observaciones especiales
	
		$obsespeciales=utf8_encode(strtoupper(trim($_POST["observacionesespeciales"])));
		$obsespeciales_origen=utf8_encode(strtoupper(trim($_POST["observacionesespeciales_origen"])));
		
		$row_especial["OBSERVACION"]=$obsespeciales;
		$row_especial["IDUSUARIOMOD"]=$_SESSION["user"];
		
		$rowWebservice_obsespecial["IDPACIENTE"]=$_POST["id_paciente"];
		$rowWebservice_obsespecial["NOMBRETABLA"]="tbl_paciente";
		$rowWebservice_obsespecial["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
		$rowWebservice_obsespecial["NOMBRECAMPO"]="observacion_especial";		
		$rowWebservice_obsespecial["VALOR"]=$obsespeciales;		
	
		if($con->exist("$con->catalogo.catalogo_afiliado_persona_observacionespecial","IDAFILIADO"," WHERE IDAFILIADO=$idafiliado")){
		
			$rsoObservacionEspecial=$con->update("$con->catalogo.catalogo_afiliado_persona_observacionespecial",$row_especial,"WHERE IDAFILIADO='$idafiliado'");		
			
			if($rsoObservacionEspecial){		
				if($obsespeciales != $obsespeciales_origen)	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_obsespecial,1);
			}			
		} else if($obsespeciales){
			$row_especial["IDAFILIADO"]=$idafiliado;
			$rsaOtrosEspecialNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_observacionespecial",$row_especial);
			if($rsaOtrosEspecialNew)	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_obsespecial,1);
		} 			
	
/////*************** Asistencia medica *********************/////
	if($_POST["rddrcabecera"] =="TRUE"){		
		$rowAsistenciamedica["DRCABECERA"]=$_POST["dr_nombre"];
		$rowAsistenciamedica["DOC_CABECERA_ESPECIALIDAD"]=$_POST["dr_especialidad"];
		$rowAsistenciamedica["DOC_CABECERA_TELEFONOFIJO"]=$_POST["dr_fono"];
		$rowAsistenciamedica["DOC_CABECERA_TELEFONOCELULAR"]=$_POST["dr_celular"];
	} else{
		
		$rowAsistenciamedica["DRCABECERA"]="";
		$rowAsistenciamedica["DOC_CABECERA_ESPECIALIDAD"]="";
		$rowAsistenciamedica["DOC_CABECERA_TELEFONOFIJO"]="";
		$rowAsistenciamedica["DOC_CABECERA_TELEFONOCELULAR"]="";
	}	
		
	if($_POST["rdambulancia"] =="TRUE"){	
		
		if($_POST["cmbservicio"] >0) $rowAsistenciamedica["AMBULANCIA"]=1;		
		$rowAsistenciamedica["IDSERVICIOAMBULANCIA"]=$_POST["cmbservicio"];
	} else{
		
		$rowAsistenciamedica["IDSERVICIOAMBULANCIA"]="";
		$rowAsistenciamedica["AMBULANCIA"]=0;		
	}

	if($_POST["rdconvenio"] =="TRUE"){
		if($_POST["txtinstitucion"] !="") $rowAsistenciamedica["CONVENIO"]=1;
		$rowAsistenciamedica["INSTITUCION"]=$_POST["txtinstitucion"];
	} else{
		$rowAsistenciamedica["CONVENIO"]=0;
		$rowAsistenciamedica["INSTITUCION"]="";
	}	
	
	if($con->exist("$con->catalogo.catalogo_afiliado_persona_asistenciamedica","IDAFILIADO"," WHERE IDAFILIADO=$idafiliado")){		
		$con->update("$con->catalogo.catalogo_afiliado_persona_asistenciamedica",$rowAsistenciamedica,"WHERE IDAFILIADO='$idafiliado'");
		
	} else if($_POST["rddrcabecera"] =="TRUE" || $_POST["rdambulancia"] =="TRUE" || $_POST["rdconvenio"] =="TRUE"){	
		
		$rowAsistenciamedica["IDAFILIADO"]=$idafiliado;
		$rsasistenciaMedNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_asistenciamedica",$rowAsistenciamedica);
		
		if($rsasistenciaMedNew){		
			if($_POST["rddrcabecera"] =="TRUE"){
				
				if($_POST["dr_nombre"]){		
		
					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="doc_cabecera_nombre";		
					$rowWebservice_asistenciaMed["VALOR"]=$_POST["dr_nombre"];
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);
				}			
				
				if($_POST["dr_especialidad"]){		
		
					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="doc_cabecera_especialidad";		
					$rowWebservice_asistenciaMed["VALOR"]=$_POST["dr_especialidad"];
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);
				}
				
				if($_POST["dr_fono"]){		
		
					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="doc_cabecera_fono";		
					$rowWebservice_asistenciaMed["VALOR"]=$_POST["dr_fono"];
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);
				}
				
				if($_POST["dr_celular"]){		
		
					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="doc_cabecera_celular";		
					$rowWebservice_asistenciaMed["VALOR"]=$_POST["dr_celular"];
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);
				}			
			}			
		
	
			if($_POST["rdambulancia"] =="TRUE"){
				
				if($_POST["cmbservicio"] >0){
					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="ambulancia";		
					$rowWebservice_asistenciaMed["VALOR"]="t";
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);	

					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="ambulancia_id_servicio_FK";		
					$rowWebservice_asistenciaMed["VALOR"]=$_POST["cmbservicio"];
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);					
				}
			}
		 
			if($_POST["rdconvenio"] =="TRUE"){
				
				if($_POST["txtinstitucion"]){
					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="convenio";		
					$rowWebservice_asistenciaMed["VALOR"]="t";
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);	

					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="convenio_institucion";		
					$rowWebservice_asistenciaMed["VALOR"]=$_POST["txtinstitucion"];
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);					
				}
			} 						
		}
	}
	
/////*************** PROTOCOLOS *********************/////
	 //PROTOCOLO DE ATENCION PERSONALIZADO
	
	for ($i = 1; $i<=5; $i++){

		$idProtoPersonalizado=$_POST["idProtoPersonalizado"][$i];
		$descripcionPersonal[$i]=utf8_encode(strtoupper(trim($_POST["txtprotocoloPers"][$i])));
		$descripcionPersonal_origen[$i]=utf8_encode(strtoupper(trim($_POST["txtprotocoloPers_origen"][$i])));
		
		//actualiza 
		$rowprotPersonal["LLAMADA"]=$descripcionPersonal[$i];
		$rowprotPersonal["PRIORIDAD"]=$i;
		$rowprotPersonal["IDUSUARIOMOD"]=$_SESSION["user"];
		
		$rowprotocoloPersonaliza["IDPACIENTE"]=$_POST["id_paciente"];		
		$rowprotocoloPersonaliza["NOMBRETABLA"]="tbl_paciente";		
		$rowprotocoloPersonaliza["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	
		$rowprotocoloPersonaliza["NOMBRECAMPO"]="protocolo_atencion_$i";		
		$rowprotocoloPersonaliza["VALOR"]=$descripcionPersonal[$i];
		
		if($i <=$_POST["cantidadProtPersonalizado"] and $idProtoPersonalizado){
			
			$rsAtencionPers=$con->update("$con->catalogo.catalogo_afiliado_persona_protocolo",$rowprotPersonal,"WHERE ID='$idProtoPersonalizado' AND IDAFILIADO='$idafiliado'");

			if($rsAtencionPers){
				
				if($descripcionPersonal[$i] != $descripcionPersonal_origen[$i])	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowprotocoloPersonaliza,1);
			}			
			
		} else if($descripcionPersonal[$i] and !$idProtoPersonalizado){

			$rowprotPersonal["IDAFILIADO"]=$idafiliado;
			$rowprotPersonal["ARRTIPOPROTOCOLO"]="ATENPER";
			$rsprotoPersonNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_protocolo",$rowprotPersonal);
			
			if($rsprotoPersonNew){					
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowprotocoloPersonaliza,1);		 			
			}			
		}
	}
	
	//PROTOCOLO DE SEGURIDAD
	
	for ($ii = 1; $ii<=5; $ii++){

		$idProtoSeguridad=$_POST["idProtoSeguridad"][$ii];
		$descripcionSeguridad[$ii]=utf8_encode(strtoupper(trim($_POST["txtprotocoloSeguridad"][$ii])));
		$descripcionSeguridad_origen[$ii]=utf8_encode(strtoupper(trim($_POST["txtprotocoloSeguridad_origen"][$ii])));
		//actualiza 
		$rowprotSeguridad["LLAMADA"]=$descripcionSeguridad[$ii];
		$rowprotSeguridad["PRIORIDAD"]=$ii;
		$rowprotSeguridad["IDUSUARIOMOD"]=$_SESSION["user"];
	 
	 
		$rowWebservice_protocolo_seg["IDPACIENTE"]=$_POST["id_paciente"];		
		$rowWebservice_protocolo_seg["NOMBRETABLA"]="tbl_paciente";		
		$rowWebservice_protocolo_seg["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	
		$rowWebservice_protocolo_seg["NOMBRECAMPO"]="protocolo_seguridad_$ii";		
		$rowWebservice_protocolo_seg["VALOR"]=$descripcionSeguridad[$ii];
		
		if($ii <=$_POST["cantidadProtSeguridad"] and $idProtoSeguridad){
			
			$rsAtencionSeg=$con->update("$con->catalogo.catalogo_afiliado_persona_protocolo",$rowprotSeguridad,"WHERE ID='$idProtoSeguridad' AND IDAFILIADO='$idafiliado'");

			if($rsAtencionSeg){				
				if($descripcionSeguridad[$ii] != $descripcionSeguridad_origen[$ii])	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_protocolo_seg,1);		
			}			
			
		} else if($descripcionSeguridad[$ii] and !$idProtoSeguridad){
		
			$rowprotSeguridad["IDAFILIADO"]=$idafiliado;
			$rowprotSeguridad["ARRTIPOPROTOCOLO"]="SEGU";
			$rsprotoSeguridadNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_protocolo",$rowprotSeguridad);
			
			if($rsprotoSeguridadNew)	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_protocolo_seg,1);
		}	 
	}	
 	
	//PROTOCOLO DE ESPECIAL
 
	for ($ii = 1; $ii<=5; $ii++){

		$idProtoEspecial=$_POST["idProtoEspecial"][$ii];
		$descripcionEspecial[$ii]=utf8_encode(strtoupper(trim($_POST["txtprotocoloEspecial"][$ii])));
		$descripcionEspecial_origen[$ii]=utf8_encode(strtoupper(trim($_POST["txtprotocoloEspecial_origen"][$ii])));
		//actualiza 
		$rowprotEspecial["LLAMADA"]=$descripcionEspecial[$ii];
		$rowprotEspecial["PRIORIDAD"]=$ii;
		$rowprotEspecial["IDUSUARIOMOD"]=$_SESSION["user"];
	 
		$rowWebservice_protocolo_espec["IDPACIENTE"]=$_POST["id_paciente"];		
		$rowWebservice_protocolo_espec["NOMBRETABLA"]="tbl_paciente";		
		$rowWebservice_protocolo_espec["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");	
		$rowWebservice_protocolo_espec["NOMBRECAMPO"]="protocolo_eventos_$ii";		
		$rowWebservice_protocolo_espec["VALOR"]=$descripcionEspecial[$ii];
		
		if($ii <=$_POST["cantidadProtEspecial"] and $idProtoEspecial){
			
			$rsAtencionEsp=$con->update("$con->catalogo.catalogo_afiliado_persona_protocolo",$rowprotEspecial,"WHERE ID='$idProtoEspecial' AND IDAFILIADO='$idafiliado'");

			if($rsAtencionEsp){				
				if($descripcionEspecial[$ii] != $descripcionEspecial_origen[$ii])	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_protocolo_espec,1);		
			}			
			
		} else if($descripcionEspecial[$ii] and !$idProtoEspecial){
		
			$rowprotEspecial["IDAFILIADO"]=$idafiliado;
			$rowprotEspecial["ARRTIPOPROTOCOLO"]="EVENESP";
			$rsprotoEspecialNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_protocolo",$rowprotEspecial);
			
			if($rsprotoEspecialNew)	$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_protocolo_espec,1);
		}
	}		 

	//PLAN TARIFARIO
 		$dispositivo=utf8_encode(strtoupper(trim($_POST["txtdispositivo"])));
 		$modelo=utf8_encode(strtoupper(trim($_POST["txtmodelo"])));
 		$plan=utf8_encode(strtoupper(trim($_POST["txtplan"])));
 		$celular=utf8_encode(strtoupper(trim($_POST["txtcelularabonado"]))); 
		
		$rowPlantarifario["DISPOSITIVO"]=$dispositivo;
		$rowPlantarifario["MODELO"]=$modelo;
		$rowPlantarifario["PLAN"]=$plan;
		$rowPlantarifario["TELEFONOCELULAR"]=$celular;
		$rowPlantarifario["IDUSUARIOMOD"]=$_SESSION["user"];

		if($con->exist("$con->catalogo.catalogo_afiliado_persona_plantarifario","IDAFILIADO"," WHERE IDAFILIADO=$idafiliado")){
			$respuesta_tarifario=$con->update("$con->catalogo.catalogo_afiliado_persona_plantarifario",$rowPlantarifario,"WHERE IDAFILIADO='$idafiliado'");
		} else if($dispositivo){
			
			$rowPlantarifario["IDAFILIADO"]=$idafiliado;
			$rsplanTarifarioNew=$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_plantarifario",$rowPlantarifario);
			
			if($rsplanTarifarioNew){
				
				if($dispositivo){

					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="dispositivo";		
					$rowWebservice_asistenciaMed["VALOR"]=$dispositivo;
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);				
				}
				
				if($modelo){

					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="dispositivo_modelo";		
					$rowWebservice_asistenciaMed["VALOR"]=$modelo;
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);				
				}
				
				if($plan){

					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="dispositivo_plan";		
					$rowWebservice_asistenciaMed["VALOR"]=$plan;
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);				
				}
				
				if($celular){

					$rowWebservice_asistenciaMed["IDPACIENTE"]=$_POST["id_paciente"];
					$rowWebservice_asistenciaMed["NOMBRETABLA"]="tbl_paciente";
					$rowWebservice_asistenciaMed["FECHAHORAREGISTRO"]=date("Y-m-d H:i:s");
					$rowWebservice_asistenciaMed["NOMBRECAMPO"]="numero_abonado";		
					$rowWebservice_asistenciaMed["VALOR"]=$celular;
					$con->insert_reg("$con->temporal.actualizaciones_lifecare",$rowWebservice_asistenciaMed,1);				
				}
			}			
		}
		
	echo "<script>";
	if(!$resultado_afi)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
    echo "document.location.href='detalles.php?idafiliado=".$idafiliado."'";
    echo "</script>";	

?>