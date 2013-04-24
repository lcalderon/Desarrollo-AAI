<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$sql="SELECT CVEENTIDAD2, DESCRIPCION  FROM  $con->catalogo.catalogo_entidad WHERE cveentidad1='$_POST[ENTIDAD1]' AND cveentidad2!='0' and cveentidad3='0' ";
$con->cmbselect_db('IDENTIDAD2',$sql,'','id="cveentidad2"',"style='width:150px'",'');
?>