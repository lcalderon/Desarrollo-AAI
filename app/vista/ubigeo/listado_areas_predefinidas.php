<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');

$colores = array(0=>'#f0f0f0',1=>'#bbe0ff');
$con = new DB_mysqli();

$sql="
SELECT
* 
FROM
catalogo_poligono cp
WHERE
cp.NOMBRE <> ''
";

$result=$con->query($sql);
echo "<table class='catalogos'>";

$linea=1;
while ($reg= $result->fetch_object()){
	$color=$linea % 2;
	echo "<tr bgcolor='$colores[$color]'>";
	echo "<td align='center'  width= '20p'>".$reg->IDPOLIGONO."</td>";
	echo "<td width= '200p'>".$reg->NOMBRE."</td>";
	echo "<td width= '100p' align='center'>".$reg->IDUSUARIOMOD."</td>";
	echo "<td width= '125p'>".$reg->FECHAMOD."</td>";
	echo "<td><input type='button' value='Ver_mapa' onclick= 'ver_poligono($reg->IDPOLIGONO);' >";
	echo "<td><input type='button' value='Eliminar' onclick= 'eliminar_poligono($reg->IDPOLIGONO);' >";
	echo "</tr>";
	$linea++;
}

echo "</table>";

?>