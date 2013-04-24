<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_ubigeo.inc.php');

$ubigeo[CVEPAIS]    = $_POST[CVEPAIS];
$ubigeo[CVEENTIDAD1]=($_POST[CVEENTIDAD1]=='')?'0':$_POST[CVEENTIDAD1];
$ubigeo[CVEENTIDAD2]=($_POST[CVEENTIDAD2]=='')?'0':$_POST[CVEENTIDAD2];
$ubigeo[CVEENTIDAD3]=($_POST[CVEENTIDAD3]=='')?'0':$_POST[CVEENTIDAD3];
$ubigeo[CVEENTIDAD4]=($_POST[CVEENTIDAD4]=='')?'0':$_POST[CVEENTIDAD4];
$ubigeo[CVEENTIDAD5]=($_POST[CVEENTIDAD5]=='')?'0':$_POST[CVEENTIDAD5];
$ubigeo[CVEENTIDAD6]=($_POST[CVEENTIDAD6]=='')?'0':$_POST[CVEENTIDAD6];
$ubigeo[CVEENTIDAD7]=($_POST[CVEENTIDAD7]=='')?'0':$_POST[CVEENTIDAD7];
$ubigeo[DESCRIPCION]=$_POST[DESCRIPCION];
$ubigeo[DIRECCION]=$_POST[DIRECCION];
$ubigeo[NUMERO]=$_POST[NUMERO];
$ubigeo[LATITUD]=$_POST[LATITUD];
$ubigeo[LONGITUD]=$_POST[LONGITUD];
$ubigeo[REFERENCIA1]=$_POST[REFERENCIA1];
$ubigeo[REFERENCIA2]=$_POST[REFERENCIA2];



$con = new DB_mysqli();
$con->insert_reg("$con->temporal.$_GET[tabla]",$ubigeo);
echo $con->insert_id;

//echo $nuevo_ubigeo->grabar($_POST);

return;



?>