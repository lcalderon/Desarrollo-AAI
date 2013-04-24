<?
if (isset($_POST[idproveedor])){
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once('../../modelo/clase_lang.inc.php');
	$con = new DB_mysqli();
	$idproveedor = $_POST[idproveedor];
	$idservicio = $_POST[idservicio];
	$edicion = (isset($_POST[edicion]))?$_POST[edicion]:0;
}

if ($idservicio=='0') $comp=' AND cs.IDSERVICIO IS NULL';
$sql="
SELECT
cpsp.IDPOLIGONO,
cpl.NOMBRE,
cpsp.IDPROVEEDOR,
cpsp.IDSERVICIO,
cp.NOMBRECOMERCIAL,
cs.DESCRIPCION,
cpsp.ARRAMBITO,
cpsp.FECHAMOD,
cpsp.IDUSUARIOMOD


FROM
(catalogo_proveedor_servicio_x_poligono cpsp,
catalogo_proveedor cp,
catalogo_poligono cpl)
LEFT JOIN catalogo_servicio cs ON  cs.IDSERVICIO= cpsp.IDSERVICIO 

WHERE
cpsp.IDPROVEEDOR = cp.IDPROVEEDOR

AND cpsp.IDPOLIGONO = cpl.IDPOLIGONO
AND cpl.NOMBRE<>''
AND cpsp.IDPROVEEDOR='$idproveedor'
AND cpsp.IDSERVICIO ='$idservicio'
$comp
";
//echo $sql;
$result=$con->query($sql);
?>

<table cellpadding="0" cellspacing="0" border="0" id="table4" class="tinytable">
<thead>
<tr>
	<th width='3%'><h3><?=_('POLIGONO')?></h3></th>
	<th width='20%'><h3><?=_('NOMBRE')?></h3></th>
	<th width='5%'><h3><?=_('AMBITO')?></h3></th>
	<th width="15%"><h3><?=_('SERVICIO')?></h3></th>
	<th width='15%'><h3><?=_('USUARIO MOD')?></h3></th>
	<th width='15%'><h3><?=_('FECHA MOD')?></h3></th>
	<th width='20%' class="nosort"><h3><?=_('OPCIONES')?></h3></th>
</tr>
</thead>
<tbody style='overflow:auto;'>
<? while ($reg= $result->fetch_object()){?>

	<tr bgcolor='$colores[$color]'>
	<td align='center'><?=$reg->IDPOLIGONO?></td>
	<td><?=$reg->NOMBRE?></td>
	<td><?=$reg->ARRAMBITO?></td>
	<td align="center"><?=($reg->DESCRIPCION!='')?$reg->DESCRIPCION:'TODOS'?></td>
	<td align='center'><?=$reg->IDUSUARIOMOD?></td>
	<td><?=$reg->FECHAMOD?></td>
	<td>
	<input type='button' class="normal" value="<?=_('VER MAPA')?>" onclick= 'ver_area(<?=$reg->IDPOLIGONO?>);' >
	<input type='button' class="normal" value="<?=_('ELIMINAR')?>" onclick= "eliminar_area('<?=$reg->IDPOLIGONO?>','<?=$reg->IDPROVEEDOR?>','<?=$reg->IDSERVICIO?>','<?=$reg->ARRAMBITO?>')" <?=($edicion==1)?'':'disabled';?>>
	</td>
	</tr>
	
<?}?>
</tbody>
</table>
</body>
