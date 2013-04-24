<?
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$sql="
SELECT
IDSERVICIO,DESCRIPCION
FROM
$con->catalogo.catalogo_servicio
WHERE 
idfamilia='$_POST[IDFAMILIA]'
;
";
$resul= $con->query($sql);
?>
<option value=''><?=_('TODOS')?></option>
<?while ($reg= $resul->fetch_object()):?>
	<option value='<?=$reg->IDSERVICIO?>' <?=($reg->IDSERVICIO==$_POST[IDSERVICIO])?'selected':'';?>><?=utf8_encode($reg->DESCRIPCION)?></option>
<?endwhile;?> 


