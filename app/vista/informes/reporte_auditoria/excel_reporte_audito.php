<?php

	include_once('../../../modelo/clase_mysqli.inc.php');

/* NOMBRE DEL ARCHIVO */
$fecha_act=date("Ymd");
$aleatorio= rand(1000,9999);
$nombre_arch = 'AUD'.$fecha_act.'-'.$aleatorio.'.xls';

//ver cuentas

    foreach($_REQUEST['cmbcuenta'] as $idcuenta){            
        $cuentas[] ="'$idcuenta'";
        $idcuentas =implode(',',$cuentas);
     }
         
    if($idcuentas) $ver_cuentas="IDCUENTA IN($idcuentas) AND"; else $ver_cuentas="IDCUENTA IN('') AND";

$ar_statusasistencia=array(
'CON'=>'CONCLUIDO',
'CM'=>'CANCELADO MOMENTO'
);

$ar_preguntas= array(
'P1'=>_('PROTOCOLO TELEFONICO'),
'P2'=>_('ESCUCHA ACTIVA'),
'P3'=>_('MANEJO SPEECH Y PROCESO'),
'P4'=>_('SONRISA TELEFONICA'),
'P5'=>_('MANEJO DE TMO'),
'A1'=>_('MANEJO DE CONEXION'),
'A2'=>_('MANEJO CL CRITICO'),
'A3'=>_('USA FRASES NEGATIVAS'),
);


$ar_valores_p= array(
'1'=>_('CUMPLE'),
'0.5'=>_('REGULAR'),
'0'=>_('NO CUMPLE')
);


$ar_valores_a = array(
'1'=>_('SI'),
'0'=>_('NO'),
'-1'=>_('NO APLICA'),
);

$con= new DB_mysqli("replica");

/* CONDICION DE FECHA*/
switch ($_POST["rdopc"]){
	case 1:
		{
			$condicion_fecha =" AND YEAR(asistencia_usuario.FECHAHORA)=$_POST[cmbanio] ";
			$condicion_fecha.=" AND MONTH(asistencia_usuario.FECHAHORA)=$_POST[cmbmes] ";
			break;
		}
	case 2:
		{
			$condicion_fecha =" AND asistencia_usuario.FECHAHORA BETWEEN '$_POST[fechaini]' AND '$_POST[fechafin] 23:59:59' ";
			break;
		}
}


$Sql_auditoria="SELECT
                  /* REPORTE DE AUDITORIA */ 
				  asistencia.IDASISTENCIA,
				  asistencia.IDEXPEDIENTE,
				  asistencia.ARRSTATUSASISTENCIA,
				  asistencia.EVALAUDITORIA,
				  catalogo_servicio.DESCRIPCION,
				  IF(asistencia.IDPROGRAMASERVICIO='',catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA) AS nombreservicio,
				  asistencia_auditoria_calidad.P1,
				  asistencia_auditoria_calidad.P2,
				  asistencia_auditoria_calidad.P3,
				  asistencia_auditoria_calidad.P4,
				  asistencia_auditoria_calidad.P5,
				  asistencia_auditoria_calidad.A1,
				  asistencia_auditoria_calidad.A2,
				  asistencia_auditoria_calidad.A3,
				  asistencia_auditoria_calidad.GRABACION,
				  asistencia_auditoria_calidad.EVALUACIONAUDITOR,
				  asistencia_auditoria_calidad.IDCOORDINADOR,
				  asistencia_auditoria_calidad.IDAUDITOR,
				  asistencia_auditoria_calidad.FECHAMOD,
				  asistencia_auditoria_calidad.OBSERVACION,
				  (SELECT expediente_usuario.FECHAHORA FROM $con->temporal.expediente_usuario 
				   WHERE expediente_usuario.IDEXPEDIENTE=expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP='APE' ) as fechaexp,
				  (SELECT expediente_usuario.IDUSUARIO FROM $con->temporal.expediente_usuario 
				   WHERE expediente_usuario.IDEXPEDIENTE=expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP='APE' ) as usuarioexp,				  
				  catalogo_cuenta.NOMBRE as cuenta,
				  (SELECT
					GROUP_CONCAT(expediente_persona_telefono.NUMEROTELEFONO)
				   FROM $con->temporal.expediente_persona_telefono
					INNER JOIN $con->temporal.expediente_persona
						ON expediente_persona.IDPERSONA = expediente_persona_telefono.IDPERSONA
					WHERE expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
						AND expediente_persona.ARRTIPOPERSONA = 'TITULAR') as telefonos					 
					
				FROM $con->temporal.asistencia
				  INNER JOIN  $con->temporal.expediente
					ON expediente.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
				  INNER JOIN $con->temporal.asistencia_usuario_calidad
					ON asistencia_usuario_calidad.IDASISTENCIA = asistencia.IDASISTENCIA
				  INNER JOIN $con->catalogo.catalogo_cuenta
					ON catalogo_cuenta.IDCUENTA = asistencia.IDCUENTA
				  INNER JOIN $con->temporal.asistencia_auditoria_calidad
					ON asistencia_auditoria_calidad.IDASISTENCIA = asistencia.IDASISTENCIA
				  INNER JOIN $con->catalogo.catalogo_servicio
					ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
				  LEFT JOIN $con->catalogo.catalogo_programa_servicio
					ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO
				  LEFT JOIN $con->temporal.asistencia_usuario
					ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
					  AND asistencia_usuario.IDETAPA = 1
				  WHERE catalogo_cuenta.$ver_cuentas /*asistencia.EVALAUDITORIA = 'AUDITADO' AND*/
						asistencia.ARRSTATUSASISTENCIA IN('CP','CM','CON')
					$condicion_fecha 
				GROUP BY asistencia.IDASISTENCIA ";
 
 // echo $Sql_auditoria;
$result = $con->query($Sql_auditoria);
$i=1;
 
$color_encabezado='CCFFFF';

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=\"" .$nombre_arch  . "\"" );
header('Pragma: no-cache');
header('Expires: 0"');

?>
 
<table border="1">
	<thead >
	<tr>
		<th colspan="23" bgcolor="#5BADFF">
			<?=_('AUDITO DE CAT: EVALUACION DE LA CALIDAD DE LA COMUNICACION')?>
		</th>
 
		
	</tr>
	<tr>
		<th bgcolor="<?=$color_encabezado?>"><?=_('NRO')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('NROEXP')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('NROASIST')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('CUENTA')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('SERVICIO')?></th>		
		<th bgcolor="<?=$color_encabezado?>"><?=_('ESTADOASIST')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('FECHAHORAEXP')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('USUARIOEXP')?></th>		
		<th bgcolor="<?=$color_encabezado?>"><?=_('TELEFONOTITULAR')?></th>		
		<th bgcolor="<?=$color_encabezado?>"><?=_('AUDITOR')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('STATUSAUDITORIA')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('COORDINADOR')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('FECHAAUDITADO')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?='1.-'.$ar_preguntas[P1]?></th>
		<th bgcolor="<?=$color_encabezado?>"><?='2.-'.$ar_preguntas[P2]?></th>
		<th bgcolor="<?=$color_encabezado?>"><?='3.-'.$ar_preguntas[P3]?></th>
		<th bgcolor="<?=$color_encabezado?>"><?='4.-'.$ar_preguntas[P4]?></th>
		<th bgcolor="<?=$color_encabezado?>"><?='5.-'.$ar_preguntas[P5]?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('EVALUACION')?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=$ar_preguntas[A1]?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=$ar_preguntas[A2]?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=$ar_preguntas[A3]?></th>
		<th bgcolor="<?=$color_encabezado?>"><?=_('OBSERVACION')?></th>		
	</tr>	
		
	</thead>


<? while($reg = $result->fetch_object()): ?>
	<tr>
	<?
	$evaluacion=0;
	if (($reg->A1==0) OR ($reg->A2==0) OR ($reg->A3==1)) $evaluacion=(($reg->P1 + $reg->P2 + $reg->P3 + $reg->P4 + $reg->P5)/5) * 5;
	else if ($reg->DESCRIPCION!='') $evaluacion= (($reg->P1 + $reg->P2 + $reg->P3 + $reg->P4 + $reg->P5)/5)*10;
	else  $evaluacion='';
	?>
		<td><?=$i++;?></td>	
		<td><?=$reg->IDEXPEDIENTE ?></td>
		<td><?=$reg->IDASISTENCIA ?></td>		
		<td><?=$reg->cuenta ?></td>
		<td><?=$reg->nombreservicio ?></td>		
		<td><?=$ar_statusasistencia[$reg->ARRSTATUSASISTENCIA] ?></td>
		<td><?=$reg->fechaexp ?></td>
		<td><?=$reg->usuarioexp ?></td>
		<td><?=$reg->telefonos ?></td>
		<td><?=$reg->IDAUDITOR ?></td>		
		<td><?=$reg->EVALAUDITORIA ?></td>		
		<td><?=$reg->IDCOORDINADOR ?></td>		
		<td><?=$reg->FECHAMOD ?></td>	
		<td><?=$ar_valores_p[$reg->P1] ?></td>
		<td><?=$ar_valores_p[$reg->P2] ?></td>
		<td><?=$ar_valores_p[$reg->P3] ?></td>
		<td><?=$ar_valores_p[$reg->P4] ?></td>
		<td><?=$ar_valores_p[$reg->P5] ?></td>
		<td bgcolor="<?=($evaluacion<5 and $evaluacion!="")?'FF0000':''?>"><?=$evaluacion ?></td>
		<td><?=$ar_valores_a[$reg->A1] ?></td>
		<td><?=$ar_valores_a[$reg->A2] ?></td>
		<td><?=$ar_valores_a[$reg->A3] ?></td>
		<td><?=$reg->OBSERVACION ?></td>
	</tr>
<?endwhile;?>

</table>
