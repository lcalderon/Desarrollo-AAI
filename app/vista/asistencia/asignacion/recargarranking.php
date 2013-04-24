<?php
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_persona.inc.php');
include_once('../../../modelo/clase_telefono.inc.php');
include_once('../../../modelo/clase_cuenta.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_programa.inc.php');
include_once('../../../modelo/clase_afiliado.inc.php');
include_once('../../../modelo/clase_etapa.inc.php');
include_once('../../../modelo/clase_contacto.inc.php');
include_once('../../../modelo/clase_poligono.inc.php');
include_once('../../../modelo/clase_circulo.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../../modelo/clase_expediente.inc.php');
include_once('../../../modelo/clase_asistencia.inc.php');
//include_once('../../backoffice/ranking_proveedores.php');
include_once('../asignacion_proveedor/AlgoritmoProveedores.php'); 
$con = new DB_mysqli();

$con->select_db($con->temporal);
$db1=$con->catalogo;
$idasistencia = $_GET[idasistencia];
$idservicio = $_GET[idservicio];

 $delete_ranking="DELETE FROM asistencia_ranking WHERE IDASISTENCIA = $idasistencia AND IDSERVICIO = $idservicio";
    $exec_delete_ranking = $con->query($delete_ranking);
    $ranking = AlgoritmoProveedores($idasistencia);
	


echo "<script language='javascript'>";
//echo "alert('$delete_ranking')";
echo "parent.top.document.location.href = '../../plantillas/etapa2.php?idasistencia=$idasistencia';";
	
	 echo "</script>";	

?>