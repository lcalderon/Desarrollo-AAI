<?php

    session_start();
	
	include_once("../../../modelo/clase_lang.inc.php");
    include_once("../../../modelo/clase_mysqli.inc.php");
    include_once("../../../modelo/functions.php");
    include_once("../../includes/arreglos.php");

    $con= new DB_mysqli("replica");

    if($con->Errno){
        printf("Fallo de conexion: %s\n", $con->Error);
        exit();
    }

	if($_POST["cmbzonahoraria"] =="FILIAL"){
		$con->query("SET time_zone = (SELECT IF(DATO='',DATODEFAULT,DATO) FROM $con->catalogo.catalogo_parametro WHERE IDPARAMETRO='ZONA_HORARIA')");
		date_default_timezone_set($con->lee_parametro('ZONA_HORARIA'));
	} else{
		$con->query("SET time_zone = (SELECT IF(DATO='',DATODEFAULT,DATO) FROM $con->catalogo.catalogo_parametro WHERE IDPARAMETRO='ZONA_HORARIA_LOCAL')");
		date_default_timezone_set($con->lee_parametro('ZONA_HORARIA_LOCAL'));
	}
	
	$nroubigeo=$con->lee_parametro("UBIGEO_NIVELES_ENTIDADES");
	
	$entidad1=quitar_caracteresEspeciales(_("ENTIDAD 1"));
	$entidad2=quitar_caracteresEspeciales(_("ENTIDAD 2"));
	$entidad3=quitar_caracteresEspeciales(_("ENTIDAD 3"));
	$entidad4=quitar_caracteresEspeciales(_("ENTIDAD 4"));
	$entidad5=quitar_caracteresEspeciales(_("ENTIDAD 5"));
	$entidad6=quitar_caracteresEspeciales(_("ENTIDAD 6"));
 
    foreach($_REQUEST['cmbcuenta'] as $idcuenta){
        $cuentas[] ="'$idcuenta'";
        $idcuentas =implode(',',$cuentas);
    } 
 
    if($idcuentas and $_REQUEST['ckbtodocuenta'] !=1) $ver_cuentas="catalogo_cuenta.IDCUENTA IN($idcuentas) AND"; else if($_REQUEST['ckbtodocuenta'] ==1) $ver_cuentas=""; 
 
    if($_REQUEST["radio"]==1){
        $fecha=$_REQUEST["cmbanio"]."-".$_REQUEST["cmbmes"];
        $Sql_fecha=" HAVING (MIN(asistencia_usuario.FECHAHORA) LIKE '$fecha%') ";
    } else{
        $Sql_fecha=" HAVING (LEFT(MIN(asistencia_usuario.FECHAHORA),10) BETWEEN '".$_REQUEST["fechaini"]."' AND '".$_REQUEST["fechafin"]."')  ";
    }
	
	if($nroubigeo ==3){
		
			$Sql_ubigeo="
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3 AND CVEENTIDAD4='0' AND CVEENTIDAD5='0') AS $entidad3,";
	} else if($nroubigeo ==4){
		
			$Sql_ubigeo="
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3 AND CVEENTIDAD4='0' AND CVEENTIDAD5='0') AS $entidad3,
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3  AND CVEENTIDAD4=expediente_ubigeo.CVEENTIDAD4 AND CVEENTIDAD5='0' ) AS $entidad4,
				";
	} else if($nroubigeo ==5){
		
			$Sql_ubigeo="
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3 AND CVEENTIDAD4='0' AND CVEENTIDAD5='0') AS $entidad3,
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3  AND CVEENTIDAD4=expediente_ubigeo.CVEENTIDAD4 AND CVEENTIDAD5='0') AS $entidad4,
				(SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3=expediente_ubigeo.CVEENTIDAD3  AND CVEENTIDAD4=expediente_ubigeo.CVEENTIDAD4 AND CVEENTIDAD5=expediente_ubigeo.CVEENTIDAD5 AND CVEENTIDAD6='0' ) AS $entidad5,
				";
	}
 	
    $Sql="SELECT
              /* REPORTE TRANFERENCIA */ 
              expediente.IDEXPEDIENTE,
              IF(expediente.ASIGNARTITULAR =1,'SI','NO') AS ASIG_DIFERIDA,
              asistencia.IDASISTENCIA,
              catalogo_cuenta.IDCUENTA,
              catalogo_cuenta.NOMBRE  AS CUENTA,
              catalogo_programa.IDPROGRAMA AS IDPLAN,
              catalogo_programa.NOMBRE AS PLAN,
			 /* tiempos */
			 @fechaaper:= (SELECT DATE(expediente_usuario.FECHAHORA) FROM $con->temporal.expediente_usuario WHERE expediente_usuario.IDEXPEDIENTE=expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP='APE') AS FECHA_APERTURA_EXP,  
			 (SELECT expediente_usuario.IDUSUARIO FROM $con->temporal.expediente_usuario WHERE expediente_usuario.IDEXPEDIENTE=expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP='APE') AS COORDINADOR_APERTURA_EXP,  
			 
              @horaaper:= (SELECT RIGHT(expediente_usuario.FECHAHORA,8) FROM $con->temporal.expediente_usuario WHERE expediente_usuario.IDEXPEDIENTE=expediente.IDEXPEDIENTE AND expediente_usuario.ARRTIPOMOVEXP='APE') AS HORA_APERTURA_EXP,  
              (SELECT LEFT(MIN(asistencia_usuario.FECHAHORA),10) FROM $con->temporal.asistencia_usuario WHERE asistencia_usuario.IDASISTENCIA=asistencia.IDASISTENCIA AND IDETAPA=1) AS FECHA_REGISTRO_ASIS,
              (SELECT RIGHT(MIN(asistencia_usuario.FECHAHORA),8) FROM $con->temporal.asistencia_usuario WHERE asistencia_usuario.IDASISTENCIA=asistencia.IDASISTENCIA AND IDETAPA=1) AS HORA_REGISTRO_ASIS,
              (SELECT SUBSTR(CONCAT(MIN(asistencia_usuario.FECHAHORA),'-',asistencia_usuario.IDUSUARIO),21,20) FROM $con->temporal.asistencia_usuario WHERE asistencia_usuario.IDASISTENCIA=asistencia.IDASISTENCIA AND IDETAPA=1) AS COORDINADOR_APERTURA_ASIS,
			  
              LEFT(asistencia_asig_proveedor.FECHAARRIBO,10) AS FECHAARRIBO,              
              RIGHT(asistencia_asig_proveedor.FECHAARRIBO,8) AS HORAARRIBO,			  
			  
			   LEFT(asistencia_asig_proveedor.FECHAASIGNACION,10) AS FECHA_ASIGNACION_PROV,
			   RIGHT(asistencia_asig_proveedor.FECHAASIGNACION,8) AS HORA_ASIGNACION_PROV,
				
				TIMEDIFF(asistencia_asig_proveedor.FECHAASIGNACION,(SELECT MIN(asistencia_usuario.FECHAHORA) FROM $con->temporal.asistencia_usuario WHERE asistencia_usuario.IDASISTENCIA=asistencia.IDASISTENCIA AND IDETAPA=1) ) AS TIEMPO_ASIGNACION,
                            
               LEFT(asistencia_asig_proveedor.FECHACONTACTO,10) AS FECHACONTACTO,
              
              RIGHT(asistencia_asig_proveedor.FECHACONTACTO,8) AS HORACONTACTO,
			  IF(asistencia_asig_proveedor.FECHACONTACTO,TIMEDIFF(asistencia_asig_proveedor.FECHACONTACTO,asistencia_asig_proveedor.FECHAASIGNACION ),'') AS TIEMPO_CONTACTO,
                            
              LEFT(asistencia_asig_proveedor.FECHACONCLUIDO,10) AS FECHA_TERMINO_PROV,
              
               RIGHT(asistencia_asig_proveedor.FECHACONCLUIDO,8) AS HORA_TERMINO_PROV,
              
				TIMEDIFF(asistencia_asig_proveedor.FECHACONCLUIDO,asistencia_asig_proveedor.FECHACONTACTO) AS TIEMPO_TERMINO,			  

              (SELECT
                 CONCAT(APPATERNO,' ',APMATERNO,' ,',NOMBRE)
               FROM $con->temporal.expediente_persona
               WHERE expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
                   AND ARRTIPOPERSONA = 'CONTACTO') AS NOMBRECONTACTO,  
               (SELECT
                 CONCAT(APPATERNO,' ',APMATERNO,' ,',NOMBRE)
               FROM $con->temporal.expediente_persona
               WHERE expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
                  AND ARRTIPOPERSONA = 'TITULAR' GROUP BY expediente_persona.ARRTIPOPERSONA) AS NOMBRETITULAR,   
              asistencia.ARRSTATUSASISTENCIA as STATUSASISTENCIA,              
              CONCAT(' ',expediente.CVEAFILIADO) as CVEAFILIADO,
              asistencia.IDSERVICIO,
              asistencia.IDETAPA AS NUMERO_ETAPA,
              catalogo_servicio.DESCRIPCION AS SERVICIO,
              catalogo_programa_servicio.ETIQUETA AS NOMBRECOMERCIALSERV,
              asistencia_vehicular.ARRSERVICIOPOR AS SERVICIOPOR,
              asistencia_vehicular_auxiliovial.ARRTIPOAUXILIO AS TIPOSERVICIOVIAL,  
              (SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2='0' AND CVEENTIDAD3='0') AS $entidad1,
              (SELECT DESCRIPCION FROM $con->catalogo.catalogo_entidad WHERE CVEENTIDAD1=expediente_ubigeo.CVEENTIDAD1  AND CVEENTIDAD2=expediente_ubigeo.CVEENTIDAD2  AND CVEENTIDAD3='0')  AS $entidad2,
              $Sql_ubigeo
				 asistencia_asig_proveedor.IDPROVEEDOR AS IDPROVEEDOR,

                 (SELECT catalogo_proveedor.NOMBRECOMERCIAL  FROM $con->temporal.asistencia_asig_proveedor asip
                 INNER JOIN $con->catalogo.catalogo_proveedor
                  ON catalogo_proveedor.IDPROVEEDOR =asip.IDPROVEEDOR 
                  WHERE asip.IDASISTENCIA=asistencia.IDASISTENCIA 
					AND asip.IDASIGPROV=asistencia_asig_proveedor.IDASIGPROV) AS NOMBREPROVEEDOR,
                 
				 asistencia_asig_proveedor.STATUSPROVEEDOR AS STATUSPROVEEDOR,
				 asistencia_asig_proveedor.LOCALFORANEO,
                 
                 (SELECT catalogo_proveedor.INTERNO FROM $con->temporal.asistencia_asig_proveedor asip
                 INNER JOIN $con->catalogo.catalogo_proveedor
                  ON catalogo_proveedor.IDPROVEEDOR =asip.IDPROVEEDOR 
                  WHERE asip.IDASISTENCIA=asistencia.IDASISTENCIA  
                  AND asip.IDASIGPROV=asistencia_asig_proveedor.IDASIGPROV) AS CONDICIONPROVEEDOR,
     
                (SELECT  SUM(asistencia_asig_proveedor_costo.AA_MONTOREAL) FROM $con->temporal.asistencia_asig_proveedor_costo
                INNER JOIN $con->temporal.asistencia_asig_proveedor asip ON asip.IDASIGPROV=asistencia_asig_proveedor_costo.IDASIGPROV
                WHERE asistencia_asig_proveedor.IDASISTENCIA =asistencia.IDASISTENCIA AND asistencia_asig_proveedor_costo.IDASIGPROV=asistencia_asig_proveedor.IDASIGPROV) AS MONTOAA,     
                
                (SELECT  SUM(asistencia_asig_proveedor_costo.CC_MONTOREAL) FROM $con->temporal.asistencia_asig_proveedor_costo
                INNER JOIN $con->temporal.asistencia_asig_proveedor asip ON asip.IDASIGPROV=asistencia_asig_proveedor_costo.IDASIGPROV
                WHERE asistencia_asig_proveedor.IDASISTENCIA =asistencia.IDASISTENCIA AND asistencia_asig_proveedor_costo.IDASIGPROV=asistencia_asig_proveedor.IDASIGPROV) AS MONTOCC,    
                
                (SELECT  SUM(asistencia_asig_proveedor_costo.NA_MONTOREAL) FROM $con->temporal.asistencia_asig_proveedor_costo
                INNER JOIN $con->temporal.asistencia_asig_proveedor asip ON asip.IDASIGPROV=asistencia_asig_proveedor_costo.IDASIGPROV
                WHERE asistencia_asig_proveedor.IDASISTENCIA =asistencia.IDASISTENCIA AND asistencia_asig_proveedor_costo.IDASIGPROV=asistencia_asig_proveedor.IDASIGPROV) AS MONTONA,
                        
				
    
              if(asistencia_asig_proveedor.ARRPRIORIDADATENCION='',asistencia.ARRPRIORIDADATENCION,asistencia_asig_proveedor.ARRPRIORIDADATENCION) AS PRIORIDAD_ATENCION,
              asistencia.GARANTIA_REL AS EXP_GARANTIA,
              asistencia.ARRSTATUSENCUESTA AS STATUSENCUESTA,
              asistencia.ARRCONDICIONSERVICIO AS CONDICIONSERVICIO,
              if(asistencia.REEMBOLSO=1,'SI','NO') as REEMBOLSO,
             CONCAT(catalogo_usuario.APELLIDOS,', ',catalogo_usuario.NOMBRES) AS COORDINADOR_SEGUIMIENTO   ,
             asistencia.ARRAMBITO AS AMBITO,  
             (SELECT catalogo_familia.DESCRIPCION FROM $con->temporal.asistencia asis
             INNER JOIN $con->catalogo.catalogo_familia
              ON catalogo_familia.IDFAMILIA =asis.IDFAMILIA 
              WHERE asis.IDASISTENCIA=asistencia.IDASISTENCIA) AS FAMILIASERVICIO,
                           
             expediente.NOMAUTORIZACION AS NOMBREAUTORIZACION, 
             expediente.NUMAUTORIZACION AS NUMEROAUTORIZACION,   
             expediente.OBSERVACIONES,
             asistencia_vehicular_datosvehiculo.MARCA,   
             asistencia_vehicular_datosvehiculo.PLACA,   
             asistencia_vehicular_datosvehiculo.SUBMARCA AS MODELO,
             asistencia_vehicular_datosvehiculo.ANIO,
             asistencia_vehicular_datosvehiculo.COLOR AS COLORVEH,
             asistencia_vehicular_datosvehiculo.NUMSERIECHASIS,
             asistencia_vehicular_datosvehiculo.NUMSERIEMOTOR,
             asistencia_vehicular_datosvehiculo.NUMVIN AS VIN,
             asistencia_lugardelevento.DIRECCION AS DIRECCIONASIST,
			 
			(SELECT
				expediente_persona_telefono.NUMEROTELEFONO
			FROM
				$con->temporal.expediente_persona
			INNER JOIN $con->temporal.expediente_persona_telefono ON expediente_persona_telefono.IDPERSONA = expediente_persona.IDPERSONA
			WHERE
				expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
			AND expediente_persona.ARRTIPOPERSONA = 'TITULAR'
			AND expediente_persona_telefono.PRIORIDAD = 0 GROUP BY expediente_persona.IDEXPEDIENTE ) AS EXP_TELEFONO1,			 
			(SELECT
				expediente_persona_telefono.NUMEROTELEFONO
			FROM
				$con->temporal.expediente_persona
			INNER JOIN $con->temporal.expediente_persona_telefono ON expediente_persona_telefono.IDPERSONA = expediente_persona.IDPERSONA
			WHERE
				expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
			AND expediente_persona.ARRTIPOPERSONA = 'TITULAR'
			AND expediente_persona_telefono.PRIORIDAD = 1 GROUP BY expediente_persona.IDEXPEDIENTE) AS EXP_TELEFONO2,
				   
			 LEFT(asistencia_asig_proveedor.TEAT,10) AS FECHATEAT,
			 RIGHT(asistencia_asig_proveedor.TEAT,8) AS HORATEAT,
			 
			 TIMEDIFF(asistencia_asig_proveedor.FECHACONCLUIDO,asistencia_asig_proveedor.FECHAASIGNACION) as TIEMPO_TOTAL,
     
             (SELECT catalogo_justificacion.MOTIVO FROM $con->temporal.asistencia_justificacion INNER JOIN $con->catalogo.catalogo_justificacion ON catalogo_justificacion.IDJUSTIFICACION = asistencia_justificacion.IDJUSTIFICACION WHERE asistencia_justificacion.IDASISTENCIA = asistencia.IDASISTENCIA AND catalogo_justificacion.ARRJUSTIFICACIONMODULO = 'CM' GROUP BY asistencia_justificacion.IDASISTENCIA)  AS CANCELADOMOMENTO,
             (SELECT catalogo_justificacion.MOTIVO FROM $con->temporal.asistencia_justificacion INNER JOIN $con->catalogo.catalogo_justificacion ON catalogo_justificacion.IDJUSTIFICACION = asistencia_justificacion.IDJUSTIFICACION WHERE asistencia_justificacion.IDASISTENCIA = asistencia.IDASISTENCIA AND catalogo_justificacion.ARRJUSTIFICACIONMODULO = 'CP' GROUP BY asistencia_justificacion.IDASISTENCIA)  AS CANCELADOPOSTERIOR,         
             (SELECT catalogo_justificacion.MOTIVO FROM $con->temporal.asistencia_justificacion INNER JOIN $con->catalogo.catalogo_justificacion ON catalogo_justificacion.IDJUSTIFICACION = asistencia_justificacion.IDJUSTIFICACION WHERE asistencia_justificacion.IDASISTENCIA = asistencia.IDASISTENCIA AND catalogo_justificacion.ARRJUSTIFICACIONMODULO = 'RASI' ORDER BY asistencia_justificacion.FECHAMOD DESC  LIMIT 1)  AS REASIGNACIONPROV,
			 
             /* calificacion expediente-asistencia*/ 
             asistencia.STATUSCALIDAD AS STATUSCALIFICAEXP,
			 (IF((SELECT SUBSTR(MAX(CONCAT(FECHAHORA,'-',IDUSUARIO)),21) FROM $con->temporal.asistencia_usuario_calidad WHERE $con->temporal.asistencia_usuario_calidad.IDASISTENCIA=asistencia.IDASISTENCIA AND PROCESO='CONFIRMACION' ) !='', (SELECT SUBSTR(MAX(CONCAT(FECHAHORA,'-',IDUSUARIO)),21) FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA=asistencia.IDASISTENCIA AND PROCESO='CONFIRMACION' ), (SELECT SUBSTR(MAX(CONCAT(FECHAREGISTRO,'-',IDSUPERVISOR)),21) FROM $con->temporal.expediente_deficiencia WHERE IDASISTENCIA=asistencia.IDASISTENCIA AND MOVIMIENTO!='NUEVO') ) ) AS USUARIOCALIFICA,
			 
			 /* encuesta */
             asistencia.ARRSTATUSENCUESTA AS STATUSENCUESTA,
			 asistencia_encuesta_calidad.EVALENCUESTA as CALIFSERV,
			 asistencia_encuesta_calidad.COMENTARIO AS COMENTARIOENCUESTA,
			 (SELECT asistencia_usuario_calidad.IDUSUARIO FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA=asistencia.IDASISTENCIA AND PROCESO='ENCUESTA' ORDER BY asistencia_usuario_calidad.FECHAHORA LIMIT 1) AS SUPERVISOR,
			 
			 /* auditoria */ 
			 asistencia.EVALAUDITORIA AS STATUSAUDITORIA,
			 
			 (SELECT GROUP_CONCAT(catalogo_afiliado_poliza.POLIZA) FROM $con->catalogo.catalogo_afiliado_poliza WHERE catalogo_afiliado_poliza.IDAFILIADO= expediente.IDAFILIADO) as POLIZA,
			 /* vehicular */
			  if(asistencia_vehicular_auxiliovial.NUMERORECLAMO >0,asistencia_vehicular_auxiliovial.NUMERORECLAMO,asistencia_vehicular_asesorialegal.NUMERORECLAMO) AS NUMERORECLAMO,
			  if(asistencia_vehicular_auxiliovial.NOMBREAJUSTADOR !='',asistencia_vehicular_auxiliovial.NOMBREAJUSTADOR,asistencia_vehicular_asesorialegal.NOMBREAJUSTADOR) AS NOMBREAJUSTADOR
			 
            FROM $con->temporal.expediente
              INNER JOIN $con->temporal.asistencia              
                ON asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE                
              LEFT JOIN $con->temporal.asistencia_usuario
                ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA   
			  LEFT JOIN $con->temporal.asistencia_encuesta_calidad
                ON asistencia_encuesta_calidad.IDASISTENCIA = asistencia.IDASISTENCIA  	
			  LEFT JOIN $con->temporal.asistencia_auditoria_calidad
                ON asistencia_auditoria_calidad.IDASISTENCIA = asistencia.IDASISTENCIA                    
              LEFT JOIN $con->temporal.expediente_ubigeo
                ON expediente_ubigeo.IDEXPEDIENTE = expediente.IDEXPEDIENTE   
              LEFT JOIN $con->temporal.asistencia_lugardelevento
                ON asistencia_lugardelevento.IDASISTENCIA = asistencia.IDASISTENCIA
              LEFT JOIN $con->temporal.asistencia_vehicular
                ON asistencia_vehicular.IDASISTENCIA = asistencia.IDASISTENCIA    
              LEFT JOIN $con->temporal.asistencia_vehicular_datosvehiculo
                ON asistencia_vehicular_datosvehiculo.IDASISTENCIA = asistencia_vehicular.IDASISTENCIA   
              LEFT JOIN $con->temporal.asistencia_vehicular_auxiliovial
                ON asistencia_vehicular_auxiliovial.IDASISTENCIA = asistencia.IDASISTENCIA  
			  LEFT JOIN $con->temporal.asistencia_vehicular_asesorialegal
                ON asistencia_vehicular_asesorialegal.IDASISTENCIA = asistencia.IDASISTENCIA    
              LEFT JOIN $con->catalogo.catalogo_usuario
                ON catalogo_usuario.IDUSUARIO = asistencia.IDUSUARIORESPONSABLE        
              LEFT JOIN $con->temporal.asistencia_asig_proveedor
                ON asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA      
              LEFT JOIN $con->temporal.asistencia_asig_proveedor_costo
                ON asistencia_asig_proveedor_costo.IDASIGPROV = asistencia_asig_proveedor.IDASIGPROV      
              LEFT JOIN $con->catalogo.catalogo_proveedor
                ON catalogo_proveedor.IDPROVEEDOR = asistencia_asig_proveedor.IDPROVEEDOR
              LEFT JOIN  $con->catalogo.catalogo_servicio
                ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO                   
              INNER JOIN $con->catalogo.catalogo_cuenta
                ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA
              INNER JOIN $con->catalogo.catalogo_programa
                ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
              LEFT JOIN $con->catalogo.catalogo_programa_servicio
                ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO    
              WHERE $ver_cuentas asistencia_usuario.IDETAPA=1 
                GROUP BY expediente.IDEXPEDIENTE,asistencia.IDASISTENCIA,asistencia_asig_proveedor.IDASIGPROV $Sql_fecha                      
                ORDER BY expediente.FECHAREGISTRO
                /*ORDER BY @fechaaper,@horaaper ASC*/";
				
		// echo $Sql;		
		// die(); 
        $resultexp=$con->query($Sql);        
		
		$aleatorio= rand(10000,99999);
        $fecha= date("Ymd"); 
		
        if($resultexp->num_rows ==0){
            echo "<script>\n";
            echo "alert('"._("NO EXISTE REGISTROS PARA LA CONSULTA")."');\n";
           // echo "document.location.href='transferencia.php';\n";  
            echo "</script>\n";
        } else{
		
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=\"TRANSFERENCIA$fecha(".$_REQUEST["txtnombre"].")_".$aleatorio.".xls\"");
		header('Pragma: no-cache');
		header('Expires: 0"');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>American Assist</title>
    <style type="text/css">
    <!--
    .style3 {color: #FFFFFF; font-weight: bold; }
	-->
    </style>
</head>
<body>
	<table width="200" border="1" cellpadding="1" cellspacing="1" style="border:1px solid #333333" id="Exportar_a_Excel">
        <tr>
            <td bgcolor="#284977"><div align="center"><span class="style3">#</span></div></td>
            <td bgcolor="#284977"><div align="center"><span class="style3"><?=_("#EXPEDIENTE") ;?></span></div></td>
            <td bgcolor="#284977"><div align="center"><span class="style3"><?=_("#ASISTENCIA") ;?></span></div></td>
            <?    
                //creando el nombre del la cabecera                
                foreach($_REQUEST['chkopcion1'] as $indice => $nombre){
            ?> 
                    <td bgcolor="#284977"><span class="style3"><?=$nombre;?></span></td>            
            <?
                }                
                foreach($_REQUEST['chkopcproveedor2'] as $indice => $nombre){ 
            
            ?> 
                    <td bgcolor="#284977"><span class="style3"><?=$nombre;?></span></td>            
            <?
                }                
                 foreach($_REQUEST['chkopcion2'] as $indice => $nombrefec){             
            ?> 
                    <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }
                
                 foreach($_REQUEST['chkopcion3'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }            
            
                 foreach($_REQUEST['chkopcion4'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }            
            
                 foreach($_REQUEST['chkopcion5'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?= utf8_encode($nombrefec);?></span></td>            
            <?
                }        
                
                 foreach($_REQUEST['chkopccalidad'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }            
                         
                 foreach($_REQUEST['chkopcproveedor'] as $indice => $nombrefec){        
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }            
                                              
                 foreach($_REQUEST['chkopccosto'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }            
                             
                 foreach($_REQUEST['chkopcion6'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }            
                     
                 foreach($_REQUEST['chkopcvehicular'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }            
                
                 foreach($_REQUEST['chkopcion7'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }
                
                 foreach($_REQUEST['chkopmotivo'] as $indice => $nombrefec){             
            ?> 
                <td bgcolor="#284977"><span class="style3"><?=$nombrefec;?></span></td>            
            <?
                }

    //creando la data de la consulta segun la cabecera

    while($fila=$resultexp->fetch_object()){
        $incre=$incre+1;
		$descripcion_ocurrido= campoDescripcion($fila->IDASISTENCIA);
    ?> 
        <tr bgcolor="#FFFFFF">
            <td><?=$incre;?></td>
            <td><?=$fila->IDEXPEDIENTE;?></td>
            <td><?=$fila->IDASISTENCIA;?></td>    
        <? 
            foreach($_REQUEST['chkopcion1'] as $indice => $nombre1){            
        ?>                     
            <td><?=$fila->$nombre1;?></td>            
        <?
            }    
            
            foreach($_REQUEST['chkopcproveedor2'] as $indice => $nombrefec){             
        ?>                     
            <td><?=$fila->$nombrefec;?></td>            
        <?
            }    
            
            foreach($_REQUEST['chkopcion2'] as $indice => $nombre2){            
        ?>                     
            <td><?=$fila->$nombre2;?></td>            
        <?
            }
            
            foreach($_REQUEST['chkopcion3'] as $indice => $nombre3){            
        ?>                     
            <td><?
            
                    switch($nombre3) 
                     {
                        case "STATUSASISTENCIA":
                            echo $desc_status_asistencia[$fila->$nombre3];
                        break;    
                        default:
                        echo $fila->$nombre3;
                    }
            ?></td>            
        <?
            }        
            foreach($_REQUEST['chkopcion4'] as $indice => $nombre4){            
        ?>                     
            <td><?=$fila->$nombre4;?></td>            
        <?
            } 
            
            foreach($_REQUEST['chkopcion5'] as $indice => $nombre5){            
        ?>                     
            <td><?=$fila->$nombre5;?></td>            
        <?
            }
            
            foreach($_REQUEST['chkopccalidad'] as $indice => $nombre6){            
        ?>                     
            <td><?
            
                switch($nombre6) 
                 {
                    case "STATUSENCUESTA":
                        echo $evalencuesta_new[$fila->$nombre6];
                    break;
					case "STATUSCALIFICAEXP":
                        echo $evalexped[$fila->$nombre6];
                    break;                   
					case "STATUSAUDITORIA":
                        echo $evalauditoria[$fila->$nombre6];
                    break;
                    default:
                    echo $fila->$nombre6;
                }
            ?></td>            
        <?
            }
             
            foreach($_REQUEST['chkopcproveedor'] as $indice => $nombre7){
        ?>
            <td><?
            
                    switch($nombre7) 
                     {
                        case "STATUSPROVEEDOR":
                            echo $status_asig_prov[$fila->$nombre7];
                        break;                     
						case "LOCALFORANEO":
                            echo $arr_ambito[$fila->$nombre7];
                        break;
                        case "CONDICIONPROVEEDOR":
                            if($fila->$nombre7==1)    echo _('INTERNO');    else if($fila->$nombre7=='0') echo _('EXTERNO');
                        break;    
                        default:
                        echo $fila->$nombre7;
                    }
            ?></td>                
        <?
            }
			
            foreach($_REQUEST['chkopccosto'] as $indice => $nombre8){  
        ?>                     
            <td><?=$fila->$nombre8;?></td>
        <?
            }
			
            foreach($_REQUEST['chkopcion6'] as $indice => $nombre8){            
        ?>                     
            <td><?
                    switch($nombre8){
                        case "PRIORIDAD_ATENCION":
                            echo $desc_prioridadAtencion[$fila->$nombre8];
                        break;        
                        case "CONDICIONSERVICIO":
                            echo $desc_cobertura_servicio[$fila->$nombre8];
                        break;        
                        case "AMBITO":
                            echo $desc_ambito[$fila->$nombre8];
                        break;      
						case "DESCRIPCION_OCURRIDO":
                            echo $descripcion_ocurrido;
                        break;    
                        default:
                        echo $fila->$nombre8;
                    }            
            ?></td>            
        <?
            }
			
            foreach($_REQUEST['chkopcvehicular'] as $indice => $nombre9){           
        ?>                     
            <td><?=$fila->$nombre9;?></td>            
        <?
            }
			
            foreach($_REQUEST['chkopcion7'] as $indice => $nombre9){
        ?>                     
            <td><?=$fila->$nombre9;?></td>            
        <?
            }        
            
            foreach($_REQUEST['chkopmotivo'] as $indice => $nombre9){
        ?>                     
            <td><?=$fila->$nombre9;?></td>            
        <?
            }
			
			$descripcion_ocurrido="";
     }                     
        ?>
	</table>
</body>
</html>
<? } ?>