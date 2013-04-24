<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/functions.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_usuario.inc.php');
include_once('../../modelo/clase_expediente.inc.php');
$con = new DB_mysqli();
$usuario = new usuario();
$expediente  = new expediente($idexpediente);
$extension = $usuario->extension_usada($_SESSION[user]);
$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

//$con->select_db($con->catalogo);
$con->select_db($con->temporal);
$catalogo = $con->catalogo;


echo "<link href='../../../estilos/tablas/styletable.css' rel='stylesheet' type='text/css' />";

$minutomaximo_tarea=$con->lee_parametro('TIEMPO_MAXIMO_TAREA');

$sql_result="SELECT  ADDDATE(MT.FECHATAREA, INTERVAL $minutomaximo_tarea MINUTE) FECHAFINDISPLAY,MT.ID,MT.ALARMA,CT.ARCHIVO,MT.ID,
IF(TIMESTAMPDIFF(SECOND,NOW(),MT.FECHATAREA)>60,
CONCAT(TRUNCATE(TIMESTAMPDIFF(SECOND,NOW(),MT.FECHATAREA),0),' seg'),
CONCAT(TRUNCATE(TIMESTAMPDIFF(SECOND,NOW(),MT.FECHATAREA)/60,0),' min') ) TIEMPO,MT.IDTAREA,
NOW() AHORA,MT.FECHATAREA,CT.DESCRIPCION,MT.IDASISTENCIA,MT.ALARMAENVIADO,MT.IDEXPEDIENTE,A.IDSERVICIO,S.DESCRIPCION SERVICIO
	FROM  monitor_tarea MT INNER JOIN
	$catalogo.catalogo_tarea CT ON MT.IDTAREA = CT.IDTAREA LEFT JOIN
	asistencia A ON MT.IDASISTENCIA = A.IDASISTENCIA LEFT JOIN
	$catalogo.catalogo_servicio S ON S.IDSERVICIO = A.IDSERVICIO
	WHERE MT.STATUSTAREA ='NO ATENDIDA' AND MT.IDUSUARIO='".$_SESSION['user']."' AND MT.DISPLAY=1";

//echo $sql_result;
$result=$con->query($sql_result);
$fechaactual =  date('Y-m-d H:i:s');
echo "<tr ><th>TIEMPO</th><th>TAREA </th><th>ASIST</th><th>TIPO</th><th>AFILIADO</th><th>PROV</th></tr>";


$ult_reg= $result->num_rows;
$ii=0;
while($reg = $result->fetch_object())
{
   // echo $fechaactual.' '.$reg->FECHAFINDISPLAY;

      	/*if($fechaactual > $reg->FECHAFINDISPLAY){
	    $tareaup[STATUSTAREA] = 'ABANDONO';
	    $con->update("monitor_tarea",$tareaup,"WHERE ID=".$reg->ID);
	}*/
	
	if($reg->IDEXPEDIENTE<>0){
	    $sql_telefono_afil="SELECT EPT.NUMEROTELEFONO
FROM expediente_persona_telefono EPT
INNER JOIN expediente_persona EP
ON EPT.IDPERSONA = EP.IDPERSONA
WHERE EP.IDEXPEDIENTE = ".$reg->IDEXPEDIENTE." AND EP.ARRTIPOPERSONA = 'CONTACTO'";
	      //echo $sql_telefono_afil;
	      $exec_telefono_afil = $con->query($sql_telefono_afil);
	      if($rset_telefono_afil=$exec_telefono_afil->fetch_object()){
		  $telefonoafil=$rset_telefono_afil->CODIGOAREA.$rset_telefono_afil->NUMEROTELEFONO;
		//echo $telefonoafil;
	      }

	}

      if($reg->IDASISTENCIA<>0){
	     $sql_telefono_prov="SELECT IF(AP.IDCONTACTO=0,PT.NUMEROTELEFONO,CT.NUMEROTELEFONO) NUMEROTELEFONO
FROM asistencia_asig_proveedor AP
LEFT JOIN $catalogo.catalogo_proveedor_contacto_telefono CT ON AP.IDCONTACTO = CT.IDCONTACTO 
LEFT JOIN $catalogo.catalogo_proveedor_telefono PT ON AP.IDPROVEEDOR = PT.IDPROVEEDOR
WHERE AP.IDASISTENCIA = ".$reg->IDASISTENCIA." AND AP.STATUSPROVEEDOR in ('AC','PC') AND IF(AP.IDCONTACTO=0,PT.PRIORIDAD,CT.PRIORIDAD)=1
GROUP BY 1" ;
	      $exec_telefono_prov = $con->query($sql_telefono_prov);
	      if($rset_telefono_prov=$exec_telefono_prov->fetch_object()){
		  $telefonoprov=$rset_telefono_prov->CODIGOAREA.$rset_telefono_prov->NUMEROTELEFONO;
		//ECHO $telefonoprov;
	      }

	}
	//echo 'A'.$telefonoafil;
	//echo 'P'.$telefonoprov;

 if($ii%2==0) $clase='trdir1'; else $clase='trarq0';

$linea=$linea+1;

	
?>
<style type="text/css">
<!--
.Estilo3 {color: #FFFFFF}
-->
</style>

<tr  class='<?=$clase;?>' >
<?php

   switch($reg_v->IDTAREA){
	case 'ASIG_PROV': $idetapa=2;break;
	case 'CON_AFIL': $idetapa=3;break;
	case 'MON_PROV': $idetapa=4;break;
	case 'ARR_PROV': $idetapa=6;break;
	case 'CON_PROV': $idetapa=6;break;
	case 'CALL_CON': $idetapa=8;break;
	case 'MON_AFIL': $idetapa=5;break;
     }

echo "<td align='right'>".$reg->TIEMPO.'</td>';
echo "<td><a href='/app/vista/plantillas/etapa$idetapa.php?idasistencia=$reg->IDASISTENCIA' target='_blank'>".$reg->DESCRIPCION.'</a></td>';
echo '<td>'.$reg->IDASISTENCIA.'</td>';
echo '<td>'.$reg->SERVICIO.'</td>';?>
<td align='right'> <?PHP if($telefonoafil==''){ }else { echo $telefonoafil;?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$extension;?>','<?=$prefijo;?>','<?=$telefonoafil;?>')" title="Llamar" /><?PHP }?></td>
<td align='right'><?PHP if($telefonoprov==''){ }else { echo $telefonoprov;?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$extension;?>','<?=$prefijo;?>','<?=$telefonoprov;?>')" title="Llamar" /><?PHP }?></td>
</tr>
<?php
$ii=$ii+1;


}
?>