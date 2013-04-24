<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");	
	
	$con = new DB_mysqli();
	
 	session_start();	
	Auth::required($_GET["urlvalido"]);
	
    if ($con->Errno)
     {
        printf("Fallo de conexion: %s\n", $con->Error);
        exit();
     }
                                              
    //Eliminar registro
    
    $datoServ=$con->consultation("SELECT COUNT(*) FROM $con->temporal.asistencia WHERE IDSERVICIO='".$_GET['codigo']."'");
    
    if($datoServ[0][0] ==0){
        $respuesta=$con->query("DELETE FROM $con->catalogo.catalogo_servicio where IDSERVICIO='".$_GET['codigo']."'");
     }
     
    echo "<script>";
    if(!$respuesta)    echo "alert('*** NO SE PUEDE ELIMINAR EL REGISTRO, YA EXISTE ASISTENCIAS O EXPEDIENTES RELACIONADOS A DICHO SERVICIO.');";
    echo "document.location.href='general.php'";
    echo "</script>";

?>