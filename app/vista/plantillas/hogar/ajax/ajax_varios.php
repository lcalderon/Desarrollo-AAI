<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];

//if($_POST[UBICACIONDANIOTEXT]==''){
//	$reg[UBICACIONDANIO]='';
	$reg[ARRALCANCEDANIO]=$_POST[UBICACIONDANIO];
//	 if($_POST[UBICACIONDANIO]=="PPAR" || $_POST[UBICACIONDANIO]=="PAUX") {
	if($reg[ARRALCANCEDANIO]=='PAR'){
	 	$reg[UBICACIONDANIOPARCIAL]=$_POST[UBICACIONDANIOPARCIAL];
		if($reg[UBICACIONDANIOPARCIAL]=='OTRO'){
			$reg[OTROS]=$_POST[txtotro];
		 }else{
			$reg[OTROS]='';
		 }
	 }elseif($reg[ARRALCANCEDANIO]=='TOT'){
		    $reg[UBICACIONDANIOPARCIAL]='';
		    $reg[OTROS]='';
	}
//}else{
	$reg[UBICACIONDANIO]=$_POST[UBICACIONDANIOTEXT];
//	$reg[ARRALCANCEDANIO]='';
//}

$reg[DESCRIPCIONSERVICIO]=$_POST[DESCRIPCIONSERVICIO];

//if($_POST[DETALLEPIEZADANIO]==''){
//	$reg[PIEZADANIO]='';
	$reg[ARRPIEZADANIO] = $_POST[DETALLEPIEZADANIO];
//}else{
	$reg[PIEZADANIO]=$_POST[DETALLEPIEZADANIOTEXT];
//	$reg[ARRPIEZADANIO] = '';
//}

$reg[SUBSERVICIO] = $_POST[SUBSERVICIO];
if($_POST[SUBSERVICIO]=='TECV'){
    $reg[SUBSERVICIODETALLE] = $_POST[SUBSERVICIODETALLETEC];
}elseif($_POST[SUBSERVICIO]=='SERV'){
    $reg[SUBSERVICIODETALLE] = $_POST[SUBSERVICIODETALLEVAR];
}

$reg[DIAGNOSTICO] = $_POST[DIAGNOSTICO];
$reg[REPARACION] = $_POST[REPARACION];
$reg[RECOMENDACION] = $_POST[RECOMENDACIONES];
if($_POST[GARANTIA]=='on'){
$reg[INCLUYEGARANTIA] = 1; }
else {
$reg[INCLUYEGARANTIA] = 0;
}
$reg[MATERIAL]=$_POST[MATERIAL];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];



echo 'asis'.$reg[IDASISTENCIA];
if ($con->exist($con->temporal.'.asistencia_hogar_varios','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_hogar_varios',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_hogar_varios',$reg);	
}

//print_r($_POST);

?>