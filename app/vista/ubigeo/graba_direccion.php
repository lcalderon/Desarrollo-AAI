<?
include_once("../../modelo/clase_mysqli.inc.php");

$con = new DB_mysqli();
$reg[CVEPAIS] = $con->lee_parametro('IDPAIS');
$reg[LATITUD] = $_POST[LATITUD];
$reg[LONGITUD] = $_POST[LONGITUD];
$reg[CALLE] = $_POST[CALLE];

$con->insert_reg("$con->catalogo.catalogo_guiacalle",$reg);
header("location: mapa_sitios_interes.php?lat=$reg[LATITUD]&lng=$reg[LONGITUD]");

?>