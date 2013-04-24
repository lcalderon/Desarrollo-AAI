<?
session_start();
include_once('../app/modelo/clase_mysqli.inc.php');
include_once('../app/modelo/clase_lang.inc.php');
include_once('../app/modelo/clase_ubigeo.inc.php');
include_once('../app/modelo/clase_moneda.inc.php');
include_once('../app/modelo/clase_plantilla.inc.php');
include_once('../app/modelo/clase_persona.inc.php');
include_once('../app/modelo/clase_telefono.inc.php');
include_once('../app/modelo/clase_cuenta.inc.php');
include_once('../app/modelo/clase_familia.inc.php');
include_once('../app/modelo/clase_servicio.inc.php');
include_once('../app/modelo/clase_programa_servicio.inc.php');
include_once('../app/modelo/clase_programa.inc.php');
include_once('../app/modelo/clase_afiliado.inc.php');
include_once('../app/modelo/clase_etapa.inc.php');
include_once('../app/modelo/clase_contacto.inc.php');
include_once('../app/modelo/clase_proveedor.inc.php');
include_once('../app/modelo/clase_usuario.inc.php');
include_once('../app/modelo/clase_expediente.inc.php');
include_once('../app/modelo/clase_etapa.inc.php');
include_once('../app/modelo/clase_expediente.inc.php');
include_once('../app/modelo/clase_asistencia.inc.php');
$con = new DB_mysqli();
$usuario = new usuario();
$expediente  = new expediente($idexpediente);
$extension = $usuario->extension_usada($_SESSION[user]);
$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

//TIEMPO MAXIMO QUE UNA TAREA PASARA DE NO ATENDIDA A ABANDONO
$minutomaximo_tarea=$con->lee_parametro('TIEMPO_MAXIMO_TAREA');

 $intervalo_deficiencia=$con->lee_parametro('TIEMPO_INTERVALO_DEFICIENCIA');

/*SE VAN CONSULTANDO TODAS LAS TAREAS PENDIENTES, ASU VEZ SE HACE UNA CONSULTA  A LAS TABALS DE BITACORAS Y70 ASIGNACION Y SE VERIFICA SI EXISTE UN REGISTRO 
ASOCIADO DEPENDIENDO DE LA TAREA SELECIONADA SI ENCUENTRA UN REGISTRO EN EL RANGO DE TIEMPOS ESTABLECIDOS LO CONSIDERA COMO TAREA ATENDIDA
SI LA TAREA ES REALIZADA EN EL RANGO DE TOLERANCIA LA CONSIDERA COMO TAREA ATENDIDA CON RETRASO; PARA EL CASO DE ALGUNAS TAREAS QUE YA CUMPLIERON EL RANGO DE TOLERANCIA
SE APLICAN AUTOMATICAMENTE LAS DEFICIENCIAS
*/




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
$sql_display="SELECT ID,FECHATAREA,SUBDATE(FECHATAREA, INTERVAL $servprog MINUTE) FECHADISPLAY,DISPLAY FROM monitor_tarea WHERE DISPLAY=0 AND STATUSTAREA='PENDIENTE' ";

$exec_display=$con->query($sql_display);
while($rset_display=$exec_display->fetch_object()){
    $fechadisplay=$rset_display->FECHADISPLAY;
    if($fechaactual>=$fechadisplay && $rset_display->DISPLAY==0){
	$tareaupd[DISPLAY] = 1;
	$tareaupd[FECHADISPLAY]=$fechaactual;
	$con->update("monitor_tarea",$tareaupd," WHERE ID=".$rset_display->ID);
    }
}

//CONSULTAMOS TODAS LAS TAREAS PENDIENTES
$sql_result="SELECT MT.FECHADISPLAY FECHADESPLIEGUE,MT.ALARMA,CT.ARCHIVO,MT.ID,SUBDATE(MT.FECHATAREA,INTERVAL $intervalo_deficiencia MINUTE) FECHAMIN,ADDDATE(MT.FECHATAREA,INTERVAL $intervalo_deficiencia MINUTE) FECHAMAXDEF,ADDDATE(MT.FECHATAREA, INTERVAL $minutomaximo_tarea MINUTE) FECHAMAX,
CONCAT(TRUNCATE(TIMESTAMPDIFF(SECOND,NOW(),MT.FECHATAREA)/60,0),' MIN') TIEMPO,ADDDATE(MT.FECHATAREA, INTERVAL $minutomaximo_tarea + 1 MINUTE) FECHAFINDISPLAY,
NOW() AHORA,MT.FECHATAREA,CT.DESCRIPCION,MT.IDASISTENCIA,MT.ALARMAENVIADO,MT.IDEXPEDIENTE,A.IDSERVICIO,S.DESCRIPCION SERVICIO,MT.IDTAREA,A.IDUSUARIORESPONSABLE,
MT.STATUSTAREA,MT.NUMMON,S.TIEMPOTOLERANCIA
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
echo 'xx'.$idasistencia;
$asis = new asistencia();
$asis->carga_datos($idasistencia);
foreach ($asis->proveedores as $prov)
{
	
	if ($prov[statusproveedor]=='AC') $proveedor_act= $prov[idproveedor];

}
echo $reg->ID.' '.$idasistencia.' '.$reg->IDTAREA.' '.$fechaactual.' '.$reg->FECHATAREA.' '.$reg->FECHAFINDISPLAY.'                                                                                                          ';
// SIS EL STATUS ES NO ATENDIDA Y SE CUMPLE EL TIEMPO MAXIMO DE TOLERANCIA PASA A ABANDONO
	if($reg->STATUSTAREA=='NO ATENDIDA'){
	      if($fechaactual >= $reg->FECHAFINDISPLAY){
		    $tareaup[STATUSTAREA] = 'ABANDONO';
		    $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
	      }
	}

//SI ESTA PENDIENTE Y SE CUMPLE EL TIEMPO MAXIMO PASA A NO ATENDIDA
	if($reg->STATUSTAREA=='PENDIENTE'){
	      if($fechaactual >= $reg->FECHATAREA){
		      $tareaup[STATUSTAREA] = 'NO ATENDIDA';
		      $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
	      }
	}

  switch($reg->IDTAREA){
      case 'MON_AFIL_24':{ //MONITOREO DE AFILIADO PARA SERVICIOS PROGRAMADOS MAYORES A 48 HORAS

	 $sql_con_afil ="SELECT FECHAMOD FROM asistencia_bitacora_etapa5 WHERE IDASISTENCIA = $idasistencia";
	$exec_con_afil=$con->query($sql_con_afil);
	while($rset_con_afil=$exec_con_afil->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		//IF FECHA DE MONITOREO >= FECHATREA-5 Y <= FECHATAREA => ATENDIDA
		if($rset_con_afil->FECHAMOD >= $reg->FECHAMIN && $rset_con_afil->FECHAMOD <= $reg->FECHATAREA){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}//CASO CONTRARIO SI FECHAMONITOREO >= FECHATAREA Y <= FECHAMAXDEF => ATENDIDA RETRASO
		elseif($rset_con_afil->FECHAMOD >= $reg->FECHATAREA  && $rset_con_afil->FECHAMOD <= $reg->FECHAMAXDEF){
		    $tareaup[STATUSTAREA] = 'ATENDIDA RETRASO';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
			
		}
		
	    
	}
;
	 //echo $fechaactual.'xxxxxxxxxxxxxxxxxxxxxxx'.$reg->FECHATAREA;
		if($fechaactual>=$reg->FECHATAREA){
			$def[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def[CVEDEFICIENCIA]='CA5';
			$def[IDETAPA]=3;
			$def[ORIGEN]='AUTOMATICA';
			$def[NUMERO]=0;
			$def[IDASISTENCIA]=$idasistencia;
			$def[FECHATAREA]=$reg->FECHATAREA;
			$def[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$def[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			$def[IDPROVEEDOR]=0;
			/*echo $def[IDEXPEDIENTE].'<br>';
			echo $def[CVEDEFICIENCIA].'<br>';
			echo $def[IDETAPA].'<br>';
			echo $def[ORIGEN].'<br>';
			echo $def[NUMERO].'<br>';
			echo $def[IDASISTENCIA].'<br>';
			echo $def[IDCOORDINADOR];*/
			$con->insert_reg('expediente_deficiencia',$def);
		}
      }break;
 case 'ARR_AFIL':{
	 	$sql_con_afil ="SELECT FECHAMOD FROM asistencia_bitacora_etapa6 WHERE IDASISTENCIA = $idasistencia AND STATUSARRCON IN ('CONENT','CONSAL')";
	$exec_con_afil=$con->query($sql_con_afil);
	while($rset_con_afil=$exec_con_afil->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		//IF FECHA DE MONITOREO >= FECHATREA-5 Y <= FECHATAREA => ATENDIDA
		if($rset_con_afil->FECHAMOD >= $reg->FECHAMIN && $rset_con_afil->FECHAMOD <= $reg->FECHATAREA){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}//CASO CONTRARIO SI FECHAMONITOREO >= FECHATAREA Y <= FECHAMAXDEF => ATENDIDA RETRASO
		elseif($rset_con_afil->FECHAMOD >= $reg->FECHATAREA  && $rset_con_afil->FECHAMOD <= $reg->FECHAMAXDEF){
		    $tareaup[STATUSTAREA] = 'ATENDIDA RETRASO';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
			
		}
		
	    
	}
;
	 //echo $fechaactual.'xxxxxxxxxxxxxxxxxxxxxxx'.$reg->FECHATAREA;
		if($fechaactual>=$reg->FECHATAREA){
			$def[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def[CVEDEFICIENCIA]='CA5';
			$def[IDETAPA]=3;
			$def[ORIGEN]='AUTOMATICA';
			$def[NUMERO]=0;
			$def[IDASISTENCIA]=$idasistencia;
			$def[FECHATAREA]=$reg->FECHATAREA;
			$def[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$def[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			$def[IDPROVEEDOR]=0;
			/*echo $def[IDEXPEDIENTE].'<br>';
			echo $def[CVEDEFICIENCIA].'<br>';
			echo $def[IDETAPA].'<br>';
			echo $def[ORIGEN].'<br>';
			echo $def[NUMERO].'<br>';
			echo $def[IDASISTENCIA].'<br>';
			echo $def[IDCOORDINADOR];*/
			$con->insert_reg('expediente_deficiencia',$def);
		}
      }break;
      case 'MON_AFIL':{
	 $sql_con_afil ="SELECT FECHAMOD FROM asistencia_bitacora_etapa5 WHERE IDASISTENCIA = $idasistencia";
	$exec_con_afil=$con->query($sql_con_afil);
	while($rset_con_afil=$exec_con_afil->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		//IF FECHA DE MONITOREO >= FECHATREA-5 Y <= FECHATAREA => ATENDIDA
		if($rset_con_afil->FECHAMOD >= $reg->FECHAMIN && $rset_con_afil->FECHAMOD <= $reg->FECHATAREA){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}//CASO CONTRARIO SI FECHAMONITOREO >= FECHATAREA Y <= FECHAMAXDEF => ATENDIDA RETRASO
		elseif($rset_con_afil->FECHAMOD >= $reg->FECHATAREA  && $rset_con_afil->FECHAMOD <= $reg->FECHAMAXDEF){
		    $tareaup[STATUSTAREA] = 'ATENDIDA RETRASO';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
			
		}
		
	    
	}
;
	 //echo $fechaactual.'xxxxxxxxxxxxxxxxxxxxxxx'.$reg->FECHATAREA;
		if($fechaactual>=$reg->FECHATAREA){
			$def[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def[CVEDEFICIENCIA]='CA5';
			$def[IDETAPA]=3;
			$def[ORIGEN]='AUTOMATICA';
			$def[NUMERO]=0;
			$def[IDASISTENCIA]=$idasistencia;
			$def[FECHATAREA]=$reg->FECHATAREA;
			$def[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$def[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			$def[IDPROVEEDOR]=0;
			/*echo $def[IDEXPEDIENTE].'<br>';
			echo $def[CVEDEFICIENCIA].'<br>';
			echo $def[IDETAPA].'<br>';
			echo $def[ORIGEN].'<br>';
			echo $def[NUMERO].'<br>';
			echo $def[IDASISTENCIA].'<br>';
			echo $def[IDCOORDINADOR];*/
			$con->insert_reg('expediente_deficiencia',$def);
		}
      }break;
      case 'CON_AFIL'://MONITOREO DE CONFIRMACION DE SERVICIO
	{
	 $sql_con_afil ="SELECT FECHAMOD FROM asistencia_bitacora_etapa3 WHERE IDASISTENCIA = $idasistencia";
	$exec_con_afil=$con->query($sql_con_afil);
	while($rset_con_afil=$exec_con_afil->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		//IF FECHA DE MONITOREO >= FECHATREA-5 Y <= FECHATAREA => ATENDIDA
		if($rset_con_afil->FECHAMOD >= $reg->FECHAMIN && $rset_con_afil->FECHAMOD <= $reg->FECHATAREA){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}//CASO CONTRARIO SI FECHAMONITOREO >= FECHATAREA Y <= FECHAMAXDEF => ATENDIDA RETRASO
		elseif($rset_con_afil->FECHAMOD >= $reg->FECHATAREA  && $rset_con_afil->FECHAMOD <= $reg->FECHAMAXDEF){
		    $tareaup[STATUSTAREA] = 'ATENDIDA RETRASO';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
			
		}
		
	    
	}
;
	 //echo $fechaactual.'xxxxxxxxxxxxxxxxxxxxxxx'.$reg->FECHATAREA;
		if($fechaactual>=$reg->FECHATAREA){
			$def[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def[CVEDEFICIENCIA]='CA5';
			$def[IDETAPA]=3;
			$def[ORIGEN]='AUTOMATICA';
			$def[NUMERO]=0;
			$def[IDASISTENCIA]=$idasistencia;
			$def[FECHATAREA]=$reg->FECHATAREA;
			$def[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$def[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			$def[IDPROVEEDOR]=0;
			/*echo $def[IDEXPEDIENTE].'<br>';
			echo $def[CVEDEFICIENCIA].'<br>';
			echo $def[IDETAPA].'<br>';
			echo $def[ORIGEN].'<br>';
			echo $def[NUMERO].'<br>';
			echo $def[IDASISTENCIA].'<br>';
			echo $def[IDCOORDINADOR];*/
			$con->insert_reg('expediente_deficiencia',$def);
		}
      }break;
    case 'MON_PROV': //MONITOREO DE PROVEEDOR
    {
  //***************************************************************************************************/////
	$sql_mon_prov ="SELECT FECHAMOD FROM asistencia_bitacora_etapa4 WHERE IDASISTENCIA = $idasistencia";
	$exec_mon_prov=$con->query($sql_mon_prov);
	while($rset_mon_prov=$exec_mon_prov->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_mon_prov->FECHAMOD >= $reg->FECHAMIN && $rset_mon_prov->FECHAMOD <= $reg->FECHATAREA){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
		}elseif($rset_mon_prov->FECHAMOD >= $reg->FECHATAREA  && $rset_mon_prov->FECHAMOD <= $reg->FECHAMAXDEF){
		    $tareaup[STATUSTAREA] = 'ATENDIDA RETRASO';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
			
		}
		/*elseif($rset_mon_prov->FECHAMOD >= $reg->FECHATAREA && $rset_mon_prov->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
		}*/
    
	}   
     //  echo $fechaactual.'xxxxxxxxxxxxxxxxxxxxxxx'.$reg->FECHATAREA;
	if($reg->TIEMPOTOLERANCIA==0){
		    if($fechaactual >= $reg->FECHATAREA){
			//$tareaup[STATUSTAREA] = 'NO ATENDIDA';
			//$con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
			if($proveedor_act!=0){
			$defX[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$defX[CVEDEFICIENCIA]='CP6';
			$defX[IDETAPA]=4;
			$defX[NUMERO]=$reg->NUMMON;
			$defX[ORIGEN]='AUTOMATICA';
			$defX[IDASISTENCIA]=$idasistencia;
			$defX[FECHATAREA]=$reg->FECHATAREA;
			$defX[IDPROVEEDOR]=$proveedor_act;
			$defX[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$defX[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$defX);
			}
		    }
	 }else{	  
		    if($fechaactual >= $reg->FECHAMAXDEF){
			//$tareaup[STATUSTAREA] = 'NO ATENDIDA';
			//$con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
			if($proveedor_act!=0){
			$defZ[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$defZ[CVEDEFICIENCIA]='CP6';
			$defZ[IDETAPA]=4;
			$defZ[NUMERO]=$reg->NUMMON;
			$defZ[ORIGEN]='AUTOMATICA';
			$defZ[FECHATAREA]=$reg->FECHATAREA;
			$defZ[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$defZ[IDPROVEEDOR]=$proveedor_act;
			$defZ[IDASISTENCIA]=$idasistencia;
			$defZ[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$defZ);
			}	
		    }
         }         
		    if($fechaactual >= $reg->FECHAMAX){
			if($proveedor_act!=0){
			 $def1[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def1[CVEDEFICIENCIA]='CP5';
			$def1[IDETAPA]=4;
			$def1[ORIGEN]='AUTOMATICA';
			$def1[NUMERO]=$reg->NUMMON;
			$def1[FECHATAREA]=$reg->FECHATAREA;
			$def1[IDPROVEEDOR]=$proveedor_act;
			$def1[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$def1[IDASISTENCIA]=$idasistencia;
			$def1[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$def1);
			}
		     }
		
    }break;
    case 'MON_PROV_24': // MONITOREO DE PROVEEDOR CUANDO EL SERVICIO PROGRAMADO ES MAYOR A 48 HORAS - TAREA ->  24 HORAS ANTES
    {
  //***************************************************************************************************/////
	$sql_mon_prov ="SELECT FECHAMOD FROM asistencia_bitacora_etapa4 WHERE IDASISTENCIA = $idasistencia";
	$exec_mon_prov=$con->query($sql_mon_prov);
	while($rset_mon_prov=$exec_mon_prov->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_mon_prov->FECHAMOD >= $reg->FECHAMIN && $rset_mon_prov->FECHAMOD <= $reg->FECHATAREA){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
		}elseif($rset_mon_prov->FECHAMOD >= $reg->FECHATAREA  && $rset_mon_prov->FECHAMOD <= $reg->FECHAMAXDEF){
		    $tareaup[STATUSTAREA] = 'ATENDIDA RETRASO';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
			
		}
		/*elseif($rset_mon_prov->FECHAMOD >= $reg->FECHATAREA && $rset_mon_prov->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
		}*/
    
	}   
     //  echo $fechaactual.'xxxxxxxxxxxxxxxxxxxxxxx'.$reg->FECHATAREA;
	if($reg->TIEMPOTOLERANCIA==0){
		    if($fechaactual >= $reg->FECHATAREA){
			//$tareaup[STATUSTAREA] = 'NO ATENDIDA';
			//$con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
			if($proveedor_act!=0){
			$defX[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$defX[CVEDEFICIENCIA]='CP6';
			$defX[IDETAPA]=4;
			$defX[NUMERO]=$reg->NUMMON;
			$defX[ORIGEN]='AUTOMATICA';
			$defX[IDASISTENCIA]=$idasistencia;
			$defX[FECHATAREA]=$reg->FECHATAREA;
			$defX[IDPROVEEDOR]=$proveedor_act;
			$defX[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$defX[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$defX);
			}
		    }
	 }else{	  
		    if($fechaactual >= $reg->FECHAMAXDEF){
			//$tareaup[STATUSTAREA] = 'NO ATENDIDA';
			//$con->update("monitor_tarea",$tareaup," WHERE ID=".$reg->ID);
			if($proveedor_act!=0){
			$defZ[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$defZ[CVEDEFICIENCIA]='CP6';
			$defZ[IDETAPA]=4;
			$defZ[NUMERO]=$reg->NUMMON;
			$defZ[ORIGEN]='AUTOMATICA';
			$defZ[FECHATAREA]=$reg->FECHATAREA;
			$defZ[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$defZ[IDPROVEEDOR]=$proveedor_act;
			$defZ[IDASISTENCIA]=$idasistencia;
			$defZ[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$defZ);
			}	
		    }
         }         
		    if($fechaactual >= $reg->FECHAMAX){
			if($proveedor_act!=0){
			 $def1[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$def1[CVEDEFICIENCIA]='CP5';
			$def1[IDETAPA]=4;
			$def1[ORIGEN]='AUTOMATICA';
			$def1[NUMERO]=$reg->NUMMON;
			$def1[FECHATAREA]=$reg->FECHATAREA;
			$def1[IDPROVEEDOR]=$proveedor_act;
			$def1[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$def1[IDASISTENCIA]=$idasistencia;
			$def1[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$def1);
			}
		     }
		
    }break;
      case 'ARR_PROV':
	{  
	$sql_arr ="SELECT FECHAMOD FROM asistencia_bitacora_etapa6 WHERE IDASISTENCIA = $idasistencia AND STATUSARRCON IN ('ARRENT','ARRSAL')";
	$exec_arr=$con->query($sql_arr);
	while($rset_arr=$exec_arr->fetch_object()){
	   
	   
		//echo $rset_mon_prov->FECHAMOD.' '.$reg->FECHAMIN.' '.$reg->FECHAMAX;
		if($rset_arr->FECHAMOD >= $reg->FECHAMIN && $rset_arr->FECHAMOD <= $reg->FECHAMAX){
		    //ECHO 'Esta en el rango';
		    $tareaup[STATUSTAREA] = 'ATENDIDA';
		    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
		}elseif($rset_arr->FECHAMOD >= $reg->FECHATAREA  && $rset_arr->FECHAMOD <= $reg->FECHAMAXDEF){
		    $tareaup[STATUSTAREA] = 'ATENDIDA RETRASO';
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
		    if($fechaactual > $reg->FECHAMAXDEF){
			if($proveedor_act!=0){
			 $defH[IDEXPEDIENTE]=$reg->IDEXPEDIENTE;
			$defH[CVEDEFICIENCIA]='PA2';
			$defH[IDETAPA]=6;
			$defH[NUMERO]=0;
			$defH[ORIGEN]='AUTOMATICA';
			$defH[IDASISTENCIA]=$idasistencia;
			$defH[FECHATAREA]=$reg->FECHATAREA;
			$defH[IDPROVEEDOR]=$proveedor_act;
			$defH[FECHADESPLIEGUE]=$reg->FECHADESPLIEGUE;
			$defH[IDCOORDINADOR]=$reg->IDUSUARIORESPONSABLE;
			 $con->insert_reg('expediente_deficiencia',$defH);
			}
		     }
	
	    
	//}
}break;

      case 'CALL_CON':{
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
    }break;
  }

}
?>

	  
