<?
session_start();
include_once('../app/modelo/clase_mysqli.inc.php');
include_once('../app/modelo/clase_lang.inc.php');
include_once('../app/modelo/clase_usuario.inc.php');
include_once('../app/modelo/clase_expediente.inc.php');
$con = new DB_mysqli();
$usuario = new usuario();
$expediente  = new expediente($idexpediente);
$extension = $usuario->extension_usada($_SESSION[user]);
$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

//TIEMPO MAXIMO QUE UNA TAREA PASARA DE NO ATENDIDA A ABANDONO
$minutomaximo_tarea=$con->lee_parametro('TIEMPO_MAXIMO_TAREA');

 $intervalo_deficiencia=$con->lee_parametro('TIEMPO_INTERVALO_DEFICIENCIA');

//$con->select_db($con->catalogo);
$con->select_db($con->temporal);
$catalogo = $con->catalogo;
$fechaactual =  date('Y-m-d H:i:s');
//echo $fechaactual;
if ($con->Errno)
{
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}

//TIEMPO MAXIMO DE DESPLIEGUE DE SERVICIOS PROGRAMADOS - DISPLAY = 1
$servprog = $con->lee_parametro('TAREA_DESPLIEGUE');

//ACTUALIZAMOS EL DISPLAY DE LAS TAREAS CUYO TIEMPO DE DESPLIEGUE YA ESTA VENCIDO Y Q AUN ESTEN PENDIENTES
$sql_display="SELECT ID,FECHATAREA,SUBDATE(FECHATAREA, INTERVAL $servprog MINUTE) FECHADISPLAY,DISPLAY FROM monitor_tarea WHERE DISPLAY=0 AND STATUSTAREA='PENDIENTE'";

$exec_display=$con->query($sql_display);
while($rset_display=$exec_display->fetch_object()){
    $fechadisplay=$rset_display->FECHADISPLAY;
    if($fechaactual>=$fechadisplay && $rset_display->DISPLAY==0){
	$tareaupd[DISPLAY] = 1;
	$con->update("monitor_tarea",$tareaupd," WHERE ID=".$rset_display->ID);
    }
}

//CONSULTAMOS TODAS LAS TAREAS PENDIENTES
$sql_result="SELECT MT.ALARMA,CT.ARCHIVO,MT.ID,SUBDATE(MT.FECHATAREA,INTERVAL $intervalo_deficiencia MINUTE) FECHAMIN,ADDDATE(MT.FECHATAREA, INTERVAL $minutomaximo_tarea MINUTE) FECHAMAX,
CONCAT(TRUNCATE(TIMESTAMPDIFF(SECOND,NOW(),MT.FECHATAREA)/60,0),' MIN') TIEMPO,ADDDATE(MT.FECHATAREA, INTERVAL $minutomaximo_tarea + 1 MINUTE) FECHAFINDISPLAY,
NOW() AHORA,MT.FECHATAREA,CT.DESCRIPCION,MT.IDASISTENCIA,MT.ALARMAENVIADO,MT.IDEXPEDIENTE,A.IDSERVICIO,S.DESCRIPCION SERVICIO,MT.IDTAREA,A.IDUSUARIORESPONSABLE,
MT.STATUSTAREA,MT.NUMMON
	FROM  monitor_tarea MT INNER JOIN
	$catalogo.catalogo_tarea CT ON MT.IDTAREA = CT.IDTAREA LEFT JOIN
	asistencia A ON MT.IDASISTENCIA = A.IDASISTENCIA LEFT JOIN
	$catalogo.catalogo_servicio S ON S.IDSERVICIO = A.IDSERVICIO
	WHERE MT.STATUSTAREA in ('PENDIENTE','NO ATENDIDA')  ORDER BY TRUNCATE(TIMESTAMPDIFF(SECOND,NOW(),MT.FECHATAREA)/60,0) ASC";

//echo $sql_result;
$result=$con->query($sql_result);

//echo "<tr ><th>TIEMPO</th><th>TAREA </th><th>ASIST</th><th>TIPO</th><th>AFILIADO</th><th>PROV</th></tr>";


$ult_reg= $result->num_rows;

while($reg = $result->fetch_object())
{
$idasistencia = $reg->IDASISTENCIA;
//echo $idasistencia;
echo $reg->ID.' '.$idasistencia.' '.$reg->IDTAREA.' '.$fechaactual.' '.$reg->FECHATAREA.' '.$reg->FECHAFINDISPLAY.'                                                                                                          ';

	if($reg->STATUSTAREA=='NO ATENDIDA'){
	      if($fechaactual >= $reg->FECHAFINDISPLAY){
		    $tareaup[STATUSTAREA] = 'ABANDONO';
		    $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
	      }
	}


	if($reg->STATUSTAREA=='PENDIENTE'){
	      if($fechaactual >= $reg->FECHATAREA){
		      $tareaup[STATUSTAREA] = 'NO ATENDIDA';
		      $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
	      }
	}

  switch($reg->IDTAREA){
      case 'CON_AFIL':
	 $sql_con_afil ="SELECT FECHAMOD FROM asistencia_bitacora_etapa3 WHERE IDASISTENCIA = $idasistencia";
	$exec_con_afil=$con->query($sql_con_afil);
	while($rset_con_afil=$exec_con_afil->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_con_afil->FECHAMOD >= $reg->FECHAMIN && $rset_con_afil->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}
		if($fechaactual >= $reg->FECHATAREA){
			//$tareaup[STATUSTAREA] = 'NO ATENDIDA';
			//$con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);

			$def[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def[CVEDEFICIENCIA]='CA3';
			$def[IDETAPA]=3;
			$def[ORIGEN]='AUTOMATICA';
			$def[IDASISTENCIA]=$idasistencia;
			$def[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$def);
		    }
	    
	}
    case 'MON_PROV':
  //***************************************************************************************************/////
	$sql_mon_prov ="SELECT FECHAMOD FROM asistencia_bitacora_etapa4 WHERE IDASISTENCIA = $idasistencia";
	$exec_mon_prov=$con->query($sql_mon_prov);
	while($rset_mon_prov=$exec_mon_prov->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_mon_prov->FECHAMOD >= $reg->FECHAMIN && $rset_mon_prov->FECHAMOD <= $reg->FECHATAREA){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
		}
		/*elseif($rset_mon_prov->FECHAMOD >= $reg->FECHATAREA && $rset_mon_prov->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
		}*/
    
	}   
      
		    if($fechaactual >= $reg->FECHATAREA){
			//$tareaup[STATUSTAREA] = 'NO ATENDIDA';
			//$con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);

			$def[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def[CVEDEFICIENCIA]='CP6';
			$def[IDETAPA]=4;
			$def[NUMERO]=$reg->NUMMON;
			$def[ORIGEN]='AUTOMATICA';
			$def[IDASISTENCIA]=$idasistencia;
			$def[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$def);
		    }
		    if($fechaactual >= $reg->FECHAMAX){
			
			 $def1[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def1[CVEDEFICIENCIA]='CP5';
			$def1[IDETAPA]=4;
			$def1[ORIGEN]='AUTOMATICA';
			$def1[NUMERO]=$reg->NUMMON;
			$def1[IDASISTENCIA]=$idasistencia;
			$def1[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$def1);
		     }
      case 'ARR_PROV':
	 $sql_arr ="SELECT FECHAMOD FROM asistencia_bitacora_etapa6 WHERE IDASISTENCIA = $idasistencia AND STATUSARRCON IN ('ARRENT','ARRSAL')";
	$exec_arr=$con->query($sql_arr);
	while($rset_arr=$exec_arr->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_arr->FECHAMOD >= $reg->FECHAMIN && $rset_arr->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}
	
	    
	}
     /* case 'CON_PROV':
	 $sql_con ="SELECT FECHAMOD FROM asistencia_bitacora_etapa5 WHERE IDASISTENCIA = $idasistencia AND STATUSARRCON IN ('CONENT','CONSAL')";
	$exec_con=$con->query($sql_con);
	while($rset_con=$exec_con->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_con->FECHAMOD >= $reg->FECHAMIN && $rset_con->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}*/
		    if($fechaactual > $reg->FECHAMAX){
			
			 $def[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def[CVEDEFICIENCIA]='PA2';
			$def[IDETAPA]=5;
			$def[ORIGEN]='AUTOMATICA';
			$def[IDASISTENCIA]=$idasistencia;
			$def[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$def);
		     }
	
	    
	//}
      case 'MON_AFIL':
	/* $sql_mon_afil ="SELECT FECHAMOD FROM asistencia_bitacora_etapa4 WHERE IDASISTENCIA = $idasistencia";
	$exec_mon_afil=$con->query($sql_mon_afil);
	while($rset_mon_afil=$exec_mon_afil->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_mon_afil->FECHAMOD >= $reg->FECHAMIN && $rset_mon_afil->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}
		if($fechaactual >= $reg->FECHAMAX){
			 //$tareaup[STATUSTAREA] = 'ABANDONO';
			 //$con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
			 $def[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def[CVEDEFICIENCIA]='CA5';
			$def[ORIGEN]='AUTOMATICA';
			$def[IDASISTENCIA]=$idasistencia;
			$def[IDETAPA]=4;
			$def[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$def);
		     }
	    
	}*/
      case 'CALL_CON':
	 $sql_sat_afil ="SELECT FECHAMOD FROM asistencia_bitacora_etapa7 WHERE IDASISTENCIA = $idasistencia";
	$exec_sat_afil=$con->query($sql_sat_afil);
	while($rset_sat_afil=$exec_sat_afil->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_sat_afil->FECHAMOD >= $reg->FECHAMIN && $rset_sat_afil->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}
	
	    
	}
  }

}
?>
