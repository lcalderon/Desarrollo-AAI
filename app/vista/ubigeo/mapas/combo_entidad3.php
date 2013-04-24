<?

include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$sql="
SELECT  
CVEENTIDAD3, DESCRIPCION  
FROM  
	$con->catalogo.catalogo_entidad 
WHERE 
CVEENTIDAD1     = '$_POST[CVEENTIDAD1]'
AND CVEENTIDAD2 = '$_POST[CVEENTIDAD2]'
AND CVEENTIDAD3 != '0' 
AND CVEENTIDAD4 = '0' 
";
	
$result = $con->query($sql);
?>
<option value='0'></option>
<? while($reg = $result->fetch_object()):?>
	<option value='<?=$reg->CVEENTIDAD3 ?>'><?=$reg->DESCRIPCION?> </option> 
<?endwhile;?>


