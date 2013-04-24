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
$sql=
"
SELECT
cpsc.IDCIRCULO,
cpsc.IDPROVEEDOR,
cpsc.IDSERVICIO,
cp.NOMBRECOMERCIAL,
cs.DESCRIPCION,
cpsc.ARRAMBITO,
cpsc.FECHAMOD,
cpsc.IDUSUARIOMOD

FROM
(catalogo_proveedor_servicio_x_circulo cpsc,
catalogo_proveedor cp)
LEFT JOIN catalogo_servicio cs ON cpsc.IDSERVICIO = cs.IDSERVICIO
WHERE
cpsc.IDPROVEEDOR = cp.IDPROVEEDOR

AND cpsc.IDPROVEEDOR='$idproveedor'
AND cpsc.IDSERVICIO ='$idservicio'
$comp
";


$result=$con->query($sql);

?>
<table cellpadding="0" cellspacing="0" border="0" id="table3" class="tinytable">
<thead>
<tr>
	<th width='3%'><h3><?=_('CIRCULO')?></h3></th>
	<th width='5%'><h3><?=_('AMBITO')?></h3></th>
	<th width="15%"><h3><?=_('SERVICIO')?></h3></th>
	<th width='20%'><h3><?=_('USUARIO MOD')?></h3></th>
	<th width='20%'><h3><?=_('FECHA MOD')?></h3></th>
	<th width='52%' class="nosort"><h3><?=_('OPCION')?></h3></th>
</tr>
</thead>
<tbody style='overflow:auto;'>

<?while ($reg= $result->fetch_object()){?>
	
	<tr bgcolor='$colores[$color]'>
	<td align='center'><?=$reg->IDCIRCULO?></td>
	<td align='center'><?=$reg->ARRAMBITO?></td>
	<td align="center"><?=($reg->DESCRIPCION!='')?$reg->DESCRIPCION:'TODOS'?></td>
	<td align='center'><?=$reg->IDUSUARIOMOD?></td>
	<td align='center'><?=$reg->FECHAMOD?></td>
	<td align='center'>
	<input type='button' class="normal" value="<?=_('VER MAPA')?>" onclick= 'ver_circulo(<?=$reg->IDCIRCULO?>) ' >
	<input type='button' class="normal" value="<?=_('ELIMINAR')?>" onclick= 'eliminar_circulo(<?=$reg->IDCIRCULO?>)' <?=($edicion==1)?'':'disabled';?>>
	</td>
	</tr>
<?}?>
</tbody>
</table>
</body>