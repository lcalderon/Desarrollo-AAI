<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

/* DATOS PARA LA TABLA ASISTENCIA */
//
$asis[IDEXPEDIENTE]=$_POST[IDEXPEDIENTE];
$asis[IDFAMILIA]=$_POST[IDFAMILIA];
$asis[IDPROGRAMASERVICIO]=$_POST[IDPROGRAMASERVICIO];
$asis[IDSERVICIO]=$_POST[IDSERVICIO];
$asis[ARRSTATUSASISTENCIA]=$_POST[ARRSTATUSASISTENCIA];
$asis[ARRCONDICIONSERVICIO]= $_POST[ARRCONDICIONSERVICIO];
$asis[IDETAPA]=$_POST[IDETAPA];
$asis[IDCUENTA]=$_POST[IDCUENTA];
$asis[IDPROGRAMA]=$_POST[IDPROGRAMA];
$asis[IDLUGARDELEVENTO]=$_POST[IDLUGARDELEVENTO];
$asis[ARRAMBITO]=$_POST[AMBITO];
$asis[FECHAREGISTRO]=date("Y-m-d H:i:s");
	
if ($_POST[ARRCONDICIONSERVICIO]=='GAR')
$asis[GARANTIA_REL]=$_POST[GARANTIA_REL];
else
$asis[GARANTIA_REL]='';

$asis[REEMBOLSO]=($_POST[REEMBOLSO]=='on')?1:0;
if ($_POST[REEMBOLSO]=='on') $asis[REPORTADO]=$_POST[REPORTADO];
else $asis[REPORTADO]=0;


if ($_POST[IDASISTENCIA]=='')
{
	$asis[IDUSUARIORESPONSABLE] = $_POST[IDUSUARIO];
	$con->insert_reg($con->temporal.'.asistencia',$asis);
	$idasistencia = $con->reg_id();
}
else {
	$asis[IDUSUARIOMOD] = $_POST[IDUSUARIO];
	$con->update($con->temporal.'.asistencia',$asis," where IDASISTENCIA = '$_POST[IDASISTENCIA]' ");
	$idasistencia = $_POST[IDASISTENCIA];
}

if ($_POST[IDLUGARDELEVENTO]!=''){
	$asis_ubigeo[IDASISTENCIA]=$idasistencia;
	$asis_ubigeo[IDEXPEDIENTE]=$_POST[IDEXPEDIENTE];
	$con->update($con->temporal.'.asistencia_lugardelevento',$asis_ubigeo," where ID='$_POST[IDLUGARDELEVENTO]'");
}

$asis_just[IDASISTENCIA] = $idasistencia;
$asis_just[CVEJUSTIFICACION] ='2';
$asis_just[MOTIVO] = $_POST[JUSTIFICACION];
$asis_just[IDUSUARIOMOD] =$_POST[IDUSUARIO];

$con->insert_reg($con->temporal.'.asistencia_justificacion',$asis_just);



/* DATOS PARA LA TABLA ASISTENCIA_USUARIO */
$asis_usuario[IDASISTENCIA]=$idasistencia;
$asis_usuario[IDUSUARIO]=$_POST[IDUSUARIO];
$asis_usuario[IDETAPA]=$_POST[ETAPACULMINADA];
$asis_usuario[OBSERVACION]=$_POST[OBSERVACION];
$con->insert_reg($con->temporal.'.asistencia_usuario',$asis_usuario);

/* DATOS PARA LA TABLA HOGAR */
$garantia_extendida[IDASISTENCIA]= $idasistencia;
//$garantia_extendida[IDCONTACTANTE]= $_POST[IDCONTACTANTE];
$garantia_extendida[IDSERVICIO]=$_POST[IDSERVICIO];
$garantia_extendida[IDCASO]= $_POST[IDCASO];
$garantia_extendida[ORIGENLLAMADA]=$_POST[ORIGENLLAMADA];
$garantia_extendida[IDUSUARIOMOD]=$_POST[IDUSUARIO];

if ($_POST[IDASISTENCIA]=='')
{
	$con->insert_reg($con->temporal.'.asistencia_garantia_extendida',$garantia_extendida);
}
else {
	$con->update($con->temporal.'.asistencia_garantia_extendida',$garantia_extendida," where IDASISTENCIA = '$_POST[IDASISTENCIA]'  ");

}


echo  $idasistencia;
?>