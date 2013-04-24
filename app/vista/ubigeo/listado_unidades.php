<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_unidadfederativa.inc.php');
if (isset($_POST[idproveedor])){
	include_once('../../modelo/clase_lang.inc.php');
	include_once('../../modelo/clase_mysqli.inc.php');
	

	$con = new DB_mysqli();
	$n_ent= $con->lee_parametro('UBIGEO_NIVELES_ENTIDADES');
	$idproveedor = $_POST[idproveedor];
	$idservicio = $_POST[idservicio];
	$edicion = (isset($_POST[edicion]))?$_POST[edicion]:0;
}
$unidad = new unidadfederativa();

if ($idservicio=='0') $comp=' AND cs.IDSERVICIO IS NULL';

$sql="
SELECT 
cuf.IDUNIDADFEDERATIVA,
cpsuf.IDSERVICIO,
cpsuf.ARRAMBITO,
cpsuf.FECHAMOD,
cpsuf.IDUSUARIOMOD,
(SELECT ce.DESCRIPCION FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEPAIS = cuf.CVEPAIS AND ce.CVEENTIDAD1='0' LIMIT 1) CVEPAIS,
IF (cuf.CVEENTIDAD1<>'0',(SELECT ce.DESCRIPCION FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEPAIS = cuf.CVEPAIS AND ce.CVEENTIDAD1=cuf.CVEENTIDAD1 AND ce.CVEENTIDAD2='0'  LIMIT 1),'') CVEENTIDAD1,
IF (cuf.CVEENTIDAD2<>'0',(SELECT ce.DESCRIPCION FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEPAIS = cuf.CVEPAIS AND ce.CVEENTIDAD1=cuf.CVEENTIDAD1 AND ce.CVEENTIDAD2=cuf.CVEENTIDAD2 AND ce.CVEENTIDAD3='0' LIMIT 1),'') CVEENTIDAD2,
IF (cuf.CVEENTIDAD3<>'0',(SELECT ce.DESCRIPCION FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEPAIS = cuf.CVEPAIS AND ce.CVEENTIDAD1=cuf.CVEENTIDAD1 AND ce.CVEENTIDAD2=cuf.CVEENTIDAD2 AND ce.CVEENTIDAD3=cuf.CVEENTIDAD3 AND ce.CVEENTIDAD4='0' LIMIT 1),'') CVEENTIDAD3,
IF (cuf.CVEENTIDAD4<>'0',(SELECT ce.DESCRIPCION FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEPAIS = cuf.CVEPAIS AND ce.CVEENTIDAD1=cuf.CVEENTIDAD1 AND ce.CVEENTIDAD2=cuf.CVEENTIDAD2 AND ce.CVEENTIDAD3=cuf.CVEENTIDAD3 AND ce.CVEENTIDAD4=cuf.CVEENTIDAD4 AND ce.CVEENTIDAD5='0' LIMIT 1),'') CVEENTIDAD4,
IF (cuf.CVEENTIDAD5<>'0',(SELECT ce.DESCRIPCION FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEPAIS = cuf.CVEPAIS AND ce.CVEENTIDAD1=cuf.CVEENTIDAD1 AND ce.CVEENTIDAD2=cuf.CVEENTIDAD2 AND ce.CVEENTIDAD3=cuf.CVEENTIDAD3 AND ce.CVEENTIDAD4=cuf.CVEENTIDAD4 AND ce.CVEENTIDAD5=cuf.CVEENTIDAD5 AND ce.CVEENTIDAD6='0' LIMIT 1),'') CVEENTIDAD5,
IF (cuf.CVEENTIDAD6<>'0',(SELECT ce.DESCRIPCION FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEPAIS = cuf.CVEPAIS AND ce.CVEENTIDAD1=cuf.CVEENTIDAD1 AND ce.CVEENTIDAD2=cuf.CVEENTIDAD2 AND ce.CVEENTIDAD3=cuf.CVEENTIDAD3 AND ce.CVEENTIDAD4=cuf.CVEENTIDAD4 AND ce.CVEENTIDAD5=cuf.CVEENTIDAD5 AND ce.CVEENTIDAD6=cuf.CVEENTIDAD6 AND ce.CVEENTIDAD7='0'  LIMIT 1),'') CVEENTIDAD6,
IF (cuf.CVEENTIDAD7<>'0',(SELECT ce.DESCRIPCION FROM $con->catalogo.catalogo_entidad ce WHERE ce.CVEPAIS = cuf.CVEPAIS AND ce.CVEENTIDAD1=cuf.CVEENTIDAD1 AND ce.CVEENTIDAD2=cuf.CVEENTIDAD2 AND ce.CVEENTIDAD3=cuf.CVEENTIDAD3 AND ce.CVEENTIDAD4=cuf.CVEENTIDAD4 AND ce.CVEENTIDAD5=cuf.CVEENTIDAD5 AND ce.CVEENTIDAD6=cuf.CVEENTIDAD6 AND ce.CVEENTIDAD7=cuf.CVEENTIDAD7  LIMIT 1),'') CVEENTIDAD7
FROM 
(
$con->catalogo.catalogo_proveedor_servicio_x_unidad_federativa cpsuf,
$con->catalogo.catalogo_entidad ce
)
LEFT JOIN $con->catalogo.catalogo_unidadfederativa  cuf ON cuf.IDUNIDADFEDERATIVA = cpsuf.IDUNIDADFEDERATIVA 
LEFT JOIN $con->catalogo.catalogo_servicio cs ON cs.IDSERVICIO=cpsuf.IDSERVICIO
WHERE 
cpsuf.IDPROVEEDOR ='$idproveedor'
AND  cpsuf.IDSERVICIO ='$idservicio'
AND
(
cuf.CVEPAIS = ce.CVEPAIS
AND cuf.CVEENTIDAD1 = ce.CVEENTIDAD1
AND cuf.CVEENTIDAD2 = ce.CVEENTIDAD2
AND cuf.CVEENTIDAD3 = ce.CVEENTIDAD3
AND cuf.CVEENTIDAD4 = ce.CVEENTIDAD4
AND cuf.CVEENTIDAD5 = ce.CVEENTIDAD5
AND cuf.CVEENTIDAD6 = ce.CVEENTIDAD6
AND cuf.CVEENTIDAD7 = ce.CVEENTIDAD7
)
ORDER BY 6,7,8,9,10,11,12,13
";
//echo $sql;
$result=$con->query($sql);



?>

<table cellpadding="0" cellspacing="0" border="0" id="table" class="tinytable">
<thead>
<tr>
	<th width='3%'><h3><?=_('ID')?></h3></th>
	<th width='3%'><h3><?=_('AMBITO')?></h3></th>
	<th width='8%'><h3><?=_('PAIS').' '. $i?></h3></th>
	<?for($i=1;$i<=$n_ent;$i++):?>
		<th width='8%'><h3><?=_('ENTIDAD'.' '.$i)?></h3></th>
	<?endfor;?>
	<th width="15%"><h3><?=_('SERVICIO')?></h3></th>
	<th width= '8%'><h3><?=_('USUARIO MOD')?></h3></th>
	<th width= '10%'><h3><?=_('FECHA MOD')?></h3></th>
	<th width='10%' class="nosort"><h3><?=_('OPCION')?></h3></th>
</tr>
</thead>
<tbody style='overflow:auto;'>
<?while ($reg= $result->fetch_object()):?>
	<tr>
	<td align='center'><?=$reg->IDUNIDADFEDERATIVA?></td>
	<td align='center'><?=$reg->ARRAMBITO?></td>
	<td align='center'><?=$reg->CVEPAIS?></td>
	<?for ($i=1;$i<=$n_ent;$i++):?>
		<td align='center'><?=$reg->{'CVEENTIDAD'.$i}?></td>
	<?endfor;?>

	<td align="center"><?=($reg->DESCRIPCION!='')?$reg->DESCRIPCION:'TODOS'?></td>
	<td align='center'><?=$reg->IDUSUARIOMOD?></td>
	<td align='center'><?=$reg->FECHAMOD?></td>
	<td align="center"><input type='button' value="<?=_('ELIMINAR')?>" onclick= "eliminar_unidad('<?=$reg->IDUNIDADFEDERATIVA?>');" class='normal' <?=($edicion==1)?'':'disabled';?>>
	</tr>
<?endwhile;?>
</tbody>
</table>
</body>
