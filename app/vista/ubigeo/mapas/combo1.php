<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$sql="SELECT CVEENTIDAD1, DESCRIPCION  FROM  $con->catalogo.catalogo_entidad WHERE cveentidad1!='0' and cveentidad2='0' ";
$con->cmbselect_db('IDENTIDAD1',$sql,$_POST['ENTIDAD1'],'id="cveentidad1"',"style='width:150px'",'');
?>