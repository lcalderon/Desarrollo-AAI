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
cpsp.IDPROVEEDOR,
cpsp.IDSERVICIO,
cp.NOMBRECOMERCIAL,
cs.DESCRIPCION,
cpsp.ARRAMBITO,
cpsp.FECHAMOD,
cpsp.IDUSUARIOMOD

FROM
(
catalogo_proveedor_servicio_x_poligono cpsp,
catalogo_proveedor cp,
catalogo_poligono cpl
)
LEFT JOIN  catalogo_servicio cs ON cpsp.IDSERVICIO = cs.IDSERVICIO
WHERE
 cpsp.IDPROVEEDOR = cp.IDPROVEEDOR
AND cpsp.IDPOLIGONO = cpl.IDPOLIGONO 
AND cpsp.IDPROVEEDOR='$idproveedor'
AND cpsp.IDSERVICIO ='$idservicio'
AND cpl.NOMBRE =''
$comp
";

$result=$con->query($sql);
?>

<table cellpadding="0" cellspacing="0" border="0" id="table2" class="tinytable">
<thead>
<tr>
	<th width='3%'><h3><?=_('POLIGONO')?></h3></th>
	<th width='5%'><h3><?=_('AMBITO')?></h3></th>
	<th width="15%"><h3><?=_('SERVICIO')?></h3></th>
	<th width='20%'><h3><?=_('USUARIO MOD')?></h3></th>
	<th width='20%'><h3><?=_('FECHA MOD')?></h3></th>
	<th width='52%' class="nosort"><h3><?=_('OPCION')?></h3></th>
</tr>
</thead>
<tbody style='overflow:auto;'>
<?
$linea=1;
while ($reg= $result->fetch_object()){
?>
	<tr>
	<td align='center'><?=$reg->IDPOLIGONO?></td>
	<td align='center'><?=$reg->ARRAMBITO?></td>
	<td align="center"><?=($reg->DESCRIPCION!='')?$reg->DESCRIPCION:'TODOS'?></td>
	<td align='center'><?=$reg->IDUSUARIOMOD?></td>
	<td align='center'><?=$reg->FECHAMOD?></td>
	<td align='center'>
	<input type='button' class="normal" value="<?=_('VER MAPA')?>" onclick= 'ver_poligono(<?=$reg->IDPOLIGONO?>);' >
	<input type='button' class="normal" value="<?=_('ELIMINAR')?>" onclick= 'eliminar_poligono(<?=$reg->IDPOLIGONO?>);' <?=($edicion==1)?'':'disabled';?>>
	</td>
	</tr>
	
<?}?>
</tbody>
</table>

</body>