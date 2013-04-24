<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$sql="SELECT  CVEENTIDAD2, DESCRIPCION  FROM  $con->catalogo.catalogo_entidad WHERE cveentidad1='$_POST[CVEENTIDAD1]' AND cveentidad2!='0' and cveentidad3='0' ";
$result = $con->query($sql);
?>
<option value='0'></option>
<? while($reg = $result->fetch_object()):?>
	<option value='<?=$reg->CVEENTIDAD2 ?>'><?=$reg->DESCRIPCION?> </option> 
<?endwhile;?>


