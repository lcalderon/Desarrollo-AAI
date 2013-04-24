<?php

	session_start();

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");

	$con= new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	include("vista_encabezado.phtml");

	$idexpediente=$_GET["idexpediente"];

	if($_GET["idexpediente"] or $_POST["opc1"]=="EXP"){

		if($_POST["opc1"]=="EXP")	$idexpediente=$_REQUEST["idcodigo"];
		$Sql_titular="SELECT
						  expediente_persona.IDTIPODOCUMENTO,
						  expediente_persona.IDDOCUMENTO,
						  expediente_persona.IDPERSONA,
						  expediente_persona.NOMBRE,
						  expediente_persona.APMATERNO,
						  expediente_persona.APPATERNO,
						  expediente_persona.CVETITULAR,
						  expediente_persona.DIGITOVERIFICADOR,
						  expediente.IDAFILIADO,
						  expediente.CVEAFILIADO,
						  expediente.IDCUENTA,
						  expediente.IDPROGRAMA,
						  expediente.IDEXPEDIENTE,
						  expediente.CVEAFILIADO,
						  expediente.ANI,
						  asistencia.IDASISTENCIA
						FROM $con->temporal.expediente
						  INNER JOIN $con->temporal.expediente_persona
							ON expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE	
						  LEFT JOIN $con->temporal.asistencia
							ON asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE	
						WHERE expediente.IDEXPEDIENTE ='$idexpediente' AND expediente_persona.ARRTIPOPERSONA='TITULAR' 
                        GROUP BY  expediente.IDEXPEDIENTE";

	} else{

		$Sql_titular="SELECT
						  catalogo_afiliado.IDCUENTA,
						  catalogo_afiliado.IDPROGRAMA,
						  catalogo_afiliado.IDAFILIADO,
						  catalogo_afiliado.CVEAFILIADO,
						  catalogo_afiliado.STATUSASISTENCIA,
						  catalogo_afiliado_persona.IDTIPODOCUMENTO,
						  catalogo_afiliado_persona.IDDOCUMENTO,
						  catalogo_afiliado_persona.NOMBRE,
						  catalogo_afiliado_persona.APMATERNO,
						  catalogo_afiliado_persona.APPATERNO,
						  catalogo_afiliado_persona.DIGITOVERIFICADOR,
						  expediente.IDEXPEDIENTE,
						  expediente.ANI
						FROM $con->catalogo.catalogo_afiliado
						  INNER JOIN $con->catalogo.catalogo_afiliado_persona
							ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
						  LEFT JOIN $con->temporal.expediente
							ON expediente.IDAFILIADO = catalogo_afiliado.IDAFILIADO
						WHERE catalogo_afiliado.IDAFILIADO =".$_REQUEST["idcodigo"]." 
                        GROUP BY catalogo_afiliado.IDAFILIADO";
	}
 
	if($_REQUEST["idcodigo"] or $_GET["idexpediente"]){

		$result=$con->query($Sql_titular);
		$row = $result->fetch_object();
	}

	$datocuenta=($_POST["opc2"])?$_POST["opc1"]:$row->IDCUENTA ;

	$Sql_telfonotsp="SELECT IDTSP,DESCRIPCION FROM $con->catalogo.catalogo_tsp WHERE ACTIVO=1 ORDER BY DESCRIPCION ";
	$Sql_tipodoc="SElECT IDTIPODOCUMENTO, DESCRIPCION FROM $con->catalogo.catalogo_tipodocumento WHERE ACTIVO=1 ORDER BY DESCRIPCION";

	$cantidad_asis=$con->consultation("select count(IDASISTENCIA) as cantidad from $con->temporal.asistencia where IDEXPEDIENTE=".$_GET["idexpediente"]);

	$Sql_programa="SELECT IDPROGRAMA,NOMBRE FROM $con->catalogo.catalogo_programa where IDCUENTA='".$datocuenta."' ORDER BY NOMBRE ";

	$Sql_telefono1="SELECT
						  expediente_persona_telefono.CODIGOAREA,
						  expediente_persona_telefono.IDTIPOTELEFONO,
						  expediente_persona_telefono.NUMEROTELEFONO,
						  expediente_persona_telefono.EXTENSION,
						  expediente_persona_telefono.IDTSP
						FROM $con->temporal.expediente_persona_telefono
						  INNER JOIN $con->temporal.expediente_persona
							ON expediente_persona.IDPERSONA = expediente_persona_telefono.IDPERSONA
						WHERE expediente_persona_telefono.IDPERSONA = '".$row->IDPERSONA."'
						ORDER BY expediente_persona_telefono.PRIORIDAD
						LIMIT 4 ";

	$Sql_telefono2="SELECT
						  catalogo_afiliado_persona_telefono.CODIGOAREA,
						  catalogo_afiliado_persona_telefono.IDTIPOTELEFONO,
						  catalogo_afiliado_persona_telefono.NUMEROTELEFONO,
						  catalogo_afiliado_persona_telefono.EXTENSION,
						  catalogo_afiliado_persona_telefono.IDTSP
						FROM $con->catalogo.catalogo_afiliado_persona_telefono
						  INNER JOIN $con->catalogo.catalogo_afiliado
							ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_telefono.IDAFILIADO						
						WHERE catalogo_afiliado.IDAFILIADO = '".$row->IDAFILIADO."'
						ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD
						LIMIT 4 ";

	$Sql_telefono=($idexpediente)?$Sql_telefono1:$Sql_telefono2;

	$resultel=$con->query($Sql_telefono);
	while($reg = $resultel->fetch_object()){

		$ii=$ii+1;
		$telefono[$ii]=$reg->NUMEROTELEFONO;
		$tipotelefono[$ii]=$reg->IDTIPOTELEFONO;
		$codigoa[$ii]=$reg->CODIGOAREA;
		$extension[$ii]=$reg->EXTENSION;
		$tsp[$ii]=$reg->IDTSP;
	}

	if($_GET["idexpediente"] or $_REQUEST["idcodigo"])	$readonly="readonly";

	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);

//verificando si tiene reclamos no concluidos
	$Sql_reclamo="SELECT
					  COUNT(*),
					  retencion.IDAFILIADO
					FROM $con->temporal.retencion
					  INNER JOIN $con->temporal.expediente
						ON expediente.IDAFILIADO = retencion.IDAFILIADO
					  WHERE MOTIVOLLAMADA = 'QUEJASRECLAMO'
						AND STATUS_SEGUIMIENTO != 'CON'
						AND expediente.CVEAFILIADO = '".$row->CVEAFILIADO."'
						AND expediente.IDEXPEDIENTE = '".$_GET["idexpediente"]."'
						AND retencion.IDAFILIADO!=0
					GROUP BY expediente.IDEXPEDIENTE ";

	$valor_recl=$con->consultation($Sql_reclamo);
	$_GET["id_expediente"]=$idexpediente;
	$_GET["cve_id"]=$row->CVEAFILIADO;

	include("consultareincidencia.php");

//buscar si el plan esta validado para la activacion del afiliado
	$resultadoAct=$con->consultation("SELECT VALIDA_ACTIVACION FROM $con->catalogo.catalogo_programa WHERE IDPROGRAMA='".$_POST["opc2"]."'");
	$seActiva=$resultadoAct[0][0]; 
?>