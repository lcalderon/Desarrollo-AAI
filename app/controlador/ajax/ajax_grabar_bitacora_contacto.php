<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];
$asis_bitacora[COMENTARIO]=$_POST[COMENTARIO];
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[IDPROVEEDOR]=$_GET[proveedor_act];
$asis_bitacora[IDASIGPROV]=$_GET[idasigprov];

/* GRABA LA BITACORA*/
$con->insert_reg("$con->temporal.asistencia_bitacora_etapa6",$asis_bitacora);



/*  GRABA LA FECHA DE CONTACTO EN LA TABLA asistencia_asig_proveedor */
$sql=
"
UPDATE
	$con->temporal.asistencia_asig_proveedor
SET
	FECHACONTACTO = NOW()
WHERE
	IDASIGPROV = '$_GET[idasigprov]'

";

//echo $sql;
$con->query($sql);


/* GRABA REG EN ASISTENCIA USUARIO*/
$asis_usuario[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_usuario[IDUSUARIO]=$_POST[IDUSUARIOMOD];
$asis_usuario[IDETAPA]=6;
$con->insert_reg("$con->temporal.asistencia_usuario",$asis_usuario);


//
///* GRABA EL CAMBIO DE ETAPA Y CAMBIA A ETAPA 7 */
$asis[IDETAPA]=7;
$con->update("$con->temporal.asistencia",$asis," WHERE IDASISTENCIA='$_POST[IDASISTENCIA]'");







return;
?>