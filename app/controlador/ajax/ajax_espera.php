<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../vista/includes/head_prot_win.php');

$con = new DB_mysqli();
$temporal = $con->temporal;
$catalogo = $con->catalogo;
$condicion ='';
$usuario = $_SESSION['user'];
$todocuentas=1;
$linea=0;

/******* DETERMINA LA CONDICION PARA SABER QUE CUENTAS PUEDE VER EL USUARIO*/
/*
$sql ="SELECT cu.TODOCUENTAS FROM $catalogo.catalogo_usuario cu where cu.IDUSUARIO ='$usuario'";
$result=$con->query($sql);
while ($reg=$result->fetch_object()) $todocuentas = $reg->TODOCUENTAS;
if ($todocuentas == 0) $condicion = " AND mill.IDCUENTA  IN (SELECT IDCUENTAS FROM $temporal.seguridad_acceso_cuenta sac where sac.IDUSUARIO='$usuario')";
*/
/*
$sql="
SELECT 
mll.FECHA,
mll.DNID,
mll.EXTENSION,
mll.ORIGEN,
mll.IDPERSONA,
mll.STATUS,
RIGHT(mll.TELEFONO,LENGTH(mll.TELEFONO)-1) TELEFONO,
CASE(mll.ORIGEN)
		WHEN 'AFILIADO' THEN 'AFILIADO'
		WHEN 'EXPEDIENTE' THEN 'EN PROCESO'
		WHEN 'CONTACTO' THEN 'PROVEEDOR'
		WHEN 'PROVEEDOR' THEN 'PROVEEDOR'
		WHEN '' THEN 'S/D'
END TIPO,
CASE(mll.ORIGEN)
	 WHEN 'AFILIADO' THEN (SELECT CONCAT(APPATERNO,' ',APMATERNO,' ',NOMBRE) FROM $catalogo.catalogo_afiliado_persona WHERE IDAFILIADO = mll.IDPERSONA)
   	 WHEN 'EXPEDIENTE' THEN (SELECT CONCAT(APPATERNO,' ',APMATERNO,' ',NOMBRE) FROM $temporal.expediente_persona WHERE IDPERSONA = mll.IDPERSONA)
  	 WHEN 'CONTACTO' THEN (SELECT CONCAT(APPATERNO,' ',APMATERNO,' ',NOMBRE) FROM $catalogo.catalogo_proveedor_contacto	WHERE IDCONTACTO = mll.IDPERSONA)
  	 WHEN 'PROVEEDOR' THEN (SELECT NOMBREFISCAL FROM $catalogo.catalogo_proveedor WHERE IDPROVEEDOR = mll.IDPERSONA)
  	 WHEN '' THEN 'S/D'
END NOMBRE,
IF (cc.IDCUENTA IS NULL, 'S/D',cc.IDCUENTA ) IDCUENTA

FROM 
$temporal.monitor_llamada mll
LEFT JOIN $catalogo.catalogo_cuenta cc ON cc.PILOTO = mll.DNID
WHERE 
mll.STATUS IN ('WAIT')   
$condicion

ORDER BY mll.FECHA DESC LIMIT 5
"
;
*/
$sql="select IDAFILIADO,CALLERID,NOMBRE, CONCAT(IDCUENTA,'/',IDPROGRAMA) AS CUENTA,'' AS EXTENSION,FECHA_REGISTRO from pe1_soaang_temporal.monitor_llamadas_entrantes ORDER BY FECHA_REGISTRO DESC ";

//$SqlPbx="SELECT CALLERID,EXTENSION,FECHA_REGISTRO,busqueda_afiliado_xtelefono(CALLERID) AS RESULTADO FROM pe1_soaang_temporal.llamadas_entrantes WHERE (ESTADO = 'noanswer' AND  FECHA_REGISTRO  >= now() - interval 1 minute) ORDER BY FECHA_REGISTRO DESC ";

$result=$con->query($sql);
?>
<table class="tinytable" width="100%">
<thead>
	<tr>
		<th><h3><?=_('ORIGEN')?></h3></th>
		<th><h3><?=_('AFILIADO')?></h3></th>
		<th><h3><?=_('CUENTA/PROGRAMA')?></h3></th>
		<th><h3><?=_('ANI')?></h3></th>
		<th><h3><?=_('EXTENSION')?>"</h3></th>
		<th><h3><?=_('FECHA')?></h3></th>
	</tr>
</thead>
<tbody>
<?
	while($reg = $result->fetch_object()):
		//$arrayDatos=explode("|",$reg->RESULTADO);
?>
<tr class="<?=($linea%2)?'oddrow':''?>" <? if($reg->IDAFILIADO){?>onclick="window.open('../../lifecare/detalles.php?idafiliado=<?=$reg->IDAFILIADO?>','lifecare','height=450, width=1500,left=100,top=0,resizable=no,scrollbars=yes,toolbar=no,status=yes')" <?}?> >
	<td align='center'><?=$reg->CALLERID?></td>
	<td><?=$reg->NOMBRE?></td>
	<td align="center"><?=$reg->CUENTA?></td>
	<td><a href='index.php?var=CON&id=<?=$reg->UNIQUEID?>' title="<?=_('CONTESTAR')?>"/><?=$reg->TELEFONO?></a></td>
	<td><?=$reg->EXTENSION?></td>
	<td><?=$reg->FECHA_REGISTRO?></td>
</tr>
<?$linea++;?>
<?endwhile;?>
</tbody>