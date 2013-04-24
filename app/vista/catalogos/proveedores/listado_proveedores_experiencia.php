<?

if (isset($_POST[idproveedor])){
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_lang.inc.php');
	$con = new DB_mysqli();
	$idproveedor = $_POST[idproveedor];
	$idservicio = $_POST[idservicio];
	$edicion = (isset($_POST[edicion]))?$_POST[edicion]:0;
}

$colores = array(0=>'#f0f0f0',1=>'#bbe0ff');

$sql="select * from catalogo_proveedor_experiencia where IDPROVEEDOR='$idproveedor'";

$result = $con->query($sql);

if ($result->num_rows){
	echo "<table class='catalogos'>";
	echo "<tr>";
	echo "<th>"._('EMPRESA DE REFERENCIA')."</th>";
	echo "<th>"._('ANIOS DE SERVICIO')."</th>";
	echo "<th>"._('OPCIONES')."</th>";
	echo "</tr>";
	$linea=0;
	while($reg= $result->fetch_object()){
		?>
		<tr bgcolor="<?=$colores[$linea % 2]?>">
		<td width='40%'><?=$reg->EMPRESAREFERENCIA ?></td>
		<td width='40%'><?=$reg->ANIOSERVICIO ?></td>
		<td width='20%'>
		<input type='button' value="<?=_('ELIMINAR')?>" onclick='borrar_empresa("<?=$reg->IDPROVEEDOREXP?>")' class="normal" <?=($edicion==1)?'':'disabled'?> ></td>
		</tr>
		<?
		$linea++;
	}

	echo "</table>";
}




?>