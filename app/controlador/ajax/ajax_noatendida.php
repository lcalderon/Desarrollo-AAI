<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../includes/head_prot.php');

$con = new DB_mysqli();
$extension = $_SESSION['extension'];
$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');
$temporal = $con->temporal;
$linea=0;

$sql="
SELECT 
MLL.ID,
MLL.FECHA,
MLL.UNIQUEID,
CASE(MLL.ORIGEN)
		WHEN 'AFILIADO' THEN 'AFILIADO'
		WHEN 'EXPEDIENTE' THEN 'EN PROCESO'
		WHEN 'CONTACTO' THEN 'PROVEEDOR'
		WHEN 'PROVEEDOR' THEN 'PROVEEDOR'
		WHEN '' THEN 'S/D'
END TIPO,
RIGHT(MLL.TELEFONO,LENGTH(MLL.TELEFONO)-1) TELEFONO,
MLL.DNID,
CONCAT(CPR.IDCUENTA,' - ',CPR.NOMBRE) AS CUENTA,
MLL.EXTENSION,
MLL.ORIGEN,
MLL.IDPERSONA,
CASE(MLL.ORIGEN)
  	    WHEN 'AFILIADO' THEN (SELECT CONCAT(APPATERNO,' ',APMATERNO,' ',NOMBRE) FROM catalogo_afiliado_persona WHERE IDAFILIADO = MLL.IDPERSONA)
		WHEN 'EXPEDIENTE' THEN (SELECT CONCAT(APPATERNO,' ',APMATERNO,' ',NOMBRE) FROM $temporal.expediente_persona	WHERE IDPERSONA = MLL.IDPERSONA)
		WHEN 'CONTACTO' THEN (SELECT CONCAT(APPATERNO,' ',APMATERNO,' ',NOMBRE) FROM catalogo_proveedor_contacto WHERE IDCONTACTO = MLL.IDPERSONA)
		WHEN 'PROVEEDOR' THEN (SELECT NOMBREFISCAL FROM catalogo_proveedor WHERE IDPROVEEDOR = MLL.IDPERSONA)
		WHEN '' THEN 'S/D'
END NOMBRE,
q.call_callid,
MIN(q.call_fecha),
MAX(q.call_fecha),
TIMEDIFF(MAX(q.call_fecha),
MIN(q.call_fecha)) DURACION
	
FROM 
$temporal.monitor_llamada MLL 
INNER JOIN $temporal.monitor_llamada_queue q ON  MLL.UNIQUEID =q.call_uniqueid 
LEFT JOIN catalogo_programa CPR ON CPR.PILOTO = MLL.DNID
WHERE 
MLL.STATUS NOT IN('WAIT','ANSWER','OUTBOUND')
GROUP BY MLL.UNIQUEID ORDER BY MLL.FECHA DESC LIMIT 5";

$result=$con->query($sql);
?>
<table class="tinytable" width="100%">
<thead>
	<tr >
		<th><h3><?=_('ORIGEN')?></h3></th>
		<th><h3><?=_('AFILIADO')?></h3></th>
		<th><h3><?=_('CUENTA/PROGRAMA')?></h3></th>
		<th><h3><?=_('ANI')?></h3></th>
		<th><h3><?=_('DURACION')?></h3></th>
		<th><h3><?=_('FECHA')?></h3></th>
	</tr>
</thead>
<tbody>
<?while($reg = $result->fetch_object()):?>
<tr  class="<?=($linea%2)?'oddrow':''?>">
	<td align='center'><?=$reg->TIPO?></td>
	<td><?=$reg->NOMBRE?></td>
	<td><?=$reg->CUENTA?></td>
	<td align='right'><?=$reg->TELEFONO?>&nbsp;&nbsp;<img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada_pbx('<?=$reg->TELEFONO;?>','<?=$extension;?>','<?=$reg->ID?>')" title="Llamar" /></td>
	<td><?=$reg->DURACION?></td>
	<td><?=$reg->FECHA?></td>
</tr>
<?$linea++;?>
<?endwhile;?>
</tbody>
</table>