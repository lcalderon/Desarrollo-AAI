<?php
session_start();
include_once('../../../../modelo/clase_lang.inc.php');
include_once('../../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();



$pnIdProducto = $_POST['PNIDPRODUCTO'];
$pcIdTipDocumento =$_POST['PCIDTIPDOCUMENTO'];
$pcNumDocumento= $_POST['PCNUMDOCUMENTO'];
$pnIdMotivo=$_POST['PNIDMOTIVO'];
$pcDescripcionMotivo=$_POST['PCDESCRIPCIONMOTIVO'];
$pcUsuario='anulacionAAP';
$idcuenta =$_POST['IDCUENTA'];
$idprograma = $_POST['IDPROGRAMA'];
$idafiliado = $_POST['IDAFILIADO'];
$idproceso = $_POST['IDPROCESO'];


/* obtener la URL del webservice */
$sql="select URL from $con->catalogo.catalogo_proceso  where IDPROCESO='$idproceso' and TIPOPROCESO='WEBSERVICE'";
$result = $con->query($sql);
while ($reg =$result->fetch_object()){
	$url=$reg->URL;
}

/*  Consulta al webservice */
$client = new  SoapClient("$url");
$param = array('pnIdProducto'=>$pnIdProducto,
				'pcIdTipDocumento'=>"$pcIdTipDocumento",
				'pcNumDocumento'=>"$pcNumDocumento",
				'pnIdMotivo' =>$pnIdMotivo,
				'pcDescripcionMotivo'=>"$pcDescripcionMotivo",
				'pcUsuario'=>"$pcUsuario"
				);

$response =$client->AnularCertificadoxNroDocumento($param);

switch($response->AnularCertificadoxNroDocumentoResult){
	case -1:
			echo "<font color='red'>ERROR DE EXCEPCION  BAJA NO PROCEDE</font>"; 
			$idstatusseguimiento='REC';
			break;
	case 1: 
			//  proceso de desafiliacón 		
			$sql= "UPDATE $con->catalogo.catalogo_afiliado  set STATUSASISTENCIA='CAN'  where IDAFILIADO='$idafiliado' AND IDCUENTA='$idcuenta' AND IDPROGRAMA='$idprograma'";
			$con->query($sql);

			// ACTUALIZA LOS DESAFILICIONES PENDIENTES
			$sql="UPDATE $con->temporal.retencion set STATUS_SEGUIMIENTO='CON' WHERE IDAFILIADO='$idafiliado'  AND IDCUENTA='$idcuenta' AND IDPROGRAMA='$idprograma'";
			$con->query($sql);
			
			
			echo "<font color='green'>SE ANULO CON EXITO</font>";
			$idstatusseguimiento='CON';
			break;
	case 2: 
			echo "<font color='red'>USUARIO NO TIENE PERMISOS PARA EL PRODUCTO BAJA NO PROCEDE</font>";
			$idstatusseguimiento='REC'; 
			break;
	case 3: 
			echo "<font color='red'>EL MOTIVO DE ANULACION NO EXISTE  BAJA NO PROCEDE</font>";
			$idstatusseguimiento='REC'; 
			break;
	case 4: 
			echo "<font color='red'>NO SE ENCONTRO LA INFORMACION DEL CERTIFICADO BAJA NO PROCEDE</font>";
			$idstatusseguimiento='REC'; 
			break;
	case 5: 
			echo "<font color='red'>ERROR AL INTENTO DE ANULACION BAJA NO PROCEDE</font>";
			$idstatusseguimiento='REC';
			break;
}		


$sql="
insert into $con->temporal.retencion set
FECHARETENCION = now(),
IDCUENTA = '$idcuenta',
IDPROGRAMA ='$idprograma',
IDAFILIADO = '$idafiliado', 
MOTIVOLLAMADA = 'DESAFILIACION',
IDDETMOTIVOLLAMADA ='$pnIdMotivo',
COMENTARIO ='$pcDescripcionMotivo',
STATUS_SEGUIMIENTO ='$idstatusseguimiento',
IDUSUARIO ='$_SESSION[user]'
";
$con->query($sql);


?>
