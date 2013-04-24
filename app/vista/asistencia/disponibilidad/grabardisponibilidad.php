<script language='javascript'>

function cerrar(){
	parent.win.close();
	return;
}
</script>
<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');



$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->temporal);
	$temporal=$con->temporal;
$idasistencia=$_POST[hid_asistencia];
$fecha = $_POST[date];
$hora=$_POST[cbhora1];
$minuto=$_POST[cbminuto1];
/*
echo $idasistencia;
echo $fecha;
echo $hora;
echo $minuto;
*/

      $regdispo[IDASISTENCIA]=$idasistencia;
      $regdispo[FECHAHORA]=$fecha.' '.$hora.':'.$minuto.':00';
      $con->insert_reg('asistencia_disponibilidad_afiliado',$regdispo);

	  $rows[ARRPRIORIDADATENCION]='PRO';
	  
	//actualiza los datos
 	$resultado=$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
$servprog = $con->lee_parametro('TAREA_SERVICIO_PROGRAMADO');
  $sql_tarea="SELECT SUBDATE('$regdispo[FECHAHORA]', INTERVAL $servprog MINUTE) FECHATAREA,IDEXPEDIENTE from asistencia WHERE IDASISTENCIA = $idasistencia";
//echo $sql_tarea;
 $exec_tarea = $con->query($sql_tarea);


  if($rset_tarea=$exec_tarea->fetch_object()){
    $rowtarea2[IDTAREA]='ASIG_PROV';
    $rowtarea2[FECHATAREA]=$regdispo[FECHAHORA];
    $rowtarea2[IDEXPEDIENTE]=$rset_tarea->IDEXPEDIENTE;
    $rowtarea2[IDASISTENCIA]=$idasistencia;
    $rowtarea2[RECORDATORIO]=1;
    $rowtarea2[STATUSTAREA]='PENDIENTE';
    $rowtarea2[IDUSUARIO]=$_SESSION['user'];
    $rowtarea2[DISPLAY]=0;
    $con->insert_reg('monitor_tarea',$rowtarea2);

}
echo "<script language='javascript'>";

echo "document.location.href = '../../plantillas/etapa2.php?idasistencia=$idasistencia';";
	
	 echo "</script>";	
/*
	$regreclamo[IDCUENTA]=$idcuenta;
	$regreclamo[IDPROGRAMA]=$idprograma;
	$regreclamo[IDAFILIADO]=$idafiliado;
	$regreclamo[MOTIVOLLAMADA]='QUEJASRECLAMOS';
	$regreclamo[IDDETMOTIVOLLAMADA]=$opciones;
	$regreclamo[COMENTARIO]=$comentario;
	$regreclamo[IDUSUARIO]=$_SESSION['user'];
	$regreclamo[STATUS_RETENCION_AFILIADO]='SIN VALIDAR';
	$con->insert_reg('retencion',$regreclamo);*/
	//echo "<script language='javascript'>cerrar();</script>";
	/*echo $regreclamo[IDCUENTA];
	echo $regreclamo[IDPROGRAMA];
	echo $regreclamo[IDAFILIADO];
	echo $regreclamo[IDMOTIVOLLAMADA];*/
	;
	    

?>