<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once('../../../modelo/functions.php');	
	include_once("../../includes/arreglos.php");

	$con= new DB_mysqli("replica");
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
		
	session_start();
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);

	foreach($_POST['cmbstatusEncuenta'] as $datostatusEnc){            
		$arrstatusEnc[] ="'$datostatusEnc'";
		$statusEnc =implode(',',$arrstatusEnc);
	}
 
	if($_POST["cmbcuenta"])	$ver_cuentas="catalogo_cuenta.IDCUENTA='".$_POST["cmbcuenta"]."' AND ";
	
	 $Sql_encuesta="SELECT
                      /* REPORTE DE ENCUESTA */ 
					  asistencia.IDEXPEDIENTE,
					  asistencia.IDASISTENCIA,
					  asistencia.ARRSTATUSENCUESTA,
					  asistencia.ARRSTATUSASISTENCIA,
					  catalogo_cuenta.NOMBRE,
					   @INTERNO:= (SELECT
						 CONCAT(catalogo_proveedor.INTERNO,'-',catalogo_proveedor.NOMBREFISCAL)
					   FROM $con->temporal.asistencia_asig_proveedor
						 INNER JOIN $con->catalogo.catalogo_proveedor
						   ON catalogo_proveedor.IDPROVEEDOR = asistencia_asig_proveedor.IDPROVEEDOR
					   WHERE asistencia_asig_proveedor.IDASISTENCIA = asistencia.IDASISTENCIA
						   AND asistencia_asig_proveedor.STATUSPROVEEDOR IN('PC','AC') GROUP BY asistencia_asig_proveedor.IDASISTENCIA) AS proveedor,						   
					  (SELECT
						 expediente_usuario.IDUSUARIO
					   FROM $con->temporal.expediente_usuario
					   WHERE expediente_usuario.ARRTIPOMOVEXP='APE' AND expediente_usuario.IDEXPEDIENTE=asistencia.IDEXPEDIENTE ) AS USUARIOAPEEXP,	
					  (SELECT
						 expediente_usuario.FECHAHORA
					   FROM $con->temporal.expediente_usuario
					   WHERE expediente_usuario.ARRTIPOMOVEXP='APE' AND expediente_usuario.IDEXPEDIENTE=asistencia.IDEXPEDIENTE ) AS FECHAAPEEXP,	
						   
					  (SELECT
						 CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,', ',expediente_persona.NOMBRE)
					   FROM $con->temporal.expediente
						 INNER JOIN $con->temporal.expediente_persona
						   ON expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
					   WHERE expediente_persona.ARRTIPOPERSONA = 'TITULAR'
						   AND asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE GROUP BY expediente_persona.ARRTIPOPERSONA) AS titular,
					  (SELECT
						 GROUP_CONCAT(expediente_persona_telefono.NUMEROTELEFONO)
					   FROM $con->temporal.expediente
						 INNER JOIN $con->temporal.expediente_persona
						   ON expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
						 INNER JOIN $con->temporal.expediente_persona_telefono
						   ON expediente_persona_telefono.IDPERSONA = expediente_persona.IDPERSONA
					   WHERE expediente_persona.ARRTIPOPERSONA = 'TITULAR'
						   AND asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE) AS telefonotitular,
					  IF(asistencia.IDPROGRAMASERVICIO,catalogo_programa_servicio.ETIQUETA,catalogo_servicio.DESCRIPCION) AS servicio,
						(SELECT asistencia_usuario_calidad.IDUSUARIO FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA=asistencia.IDASISTENCIA AND PROCESO='ENCUESTA' ORDER BY asistencia_usuario_calidad.FECHAHORA LIMIT 1) AS USUARIOENCUENTA,
						(SELECT LEFT(MAX(asistencia_usuario_calidad.FECHAHORA),10) FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA=asistencia.IDASISTENCIA AND PROCESO='ENCUESTA' ORDER BY asistencia_usuario_calidad.FECHAHORA LIMIT 1) AS FECHAENCUENTA,
						(SELECT RIGHT(MAX(asistencia_usuario_calidad.FECHAHORA),8) FROM $con->temporal.asistencia_usuario_calidad WHERE asistencia_usuario_calidad.IDASISTENCIA=asistencia.IDASISTENCIA AND PROCESO='ENCUESTA' ORDER BY asistencia_usuario_calidad.FECHAHORA LIMIT 1) AS HORAENCUENTA,
						asistencia_encuesta_calidad.EMAILS,
						asistencia_encuesta_calidad.COMENTARIO,
						catalogo_programa.NOMBRE AS plan,
						 @C_1:= IF(asistencia_encuesta_calidad.C1='100','0',IF(asistencia_encuesta_calidad.C1='101','3',IF(asistencia_encuesta_calidad.C1='0',2,asistencia_encuesta_calidad.C1))) AS C1,
						  
						 @C_2:=   IF(asistencia_encuesta_calidad.C2='100','0',IF(asistencia_encuesta_calidad.C2='101','3',IF(asistencia_encuesta_calidad.C2='0',2,asistencia_encuesta_calidad.C2))) AS C2,
						  
						 @C_3:=  IF(asistencia_encuesta_calidad.C3='100','0',IF(asistencia_encuesta_calidad.C3='101','3',IF(asistencia_encuesta_calidad.C3='0',2,asistencia_encuesta_calidad.C3))) AS C3,
						  
						 @C_4:=   IF(asistencia_encuesta_calidad.C4='100','0',IF(asistencia_encuesta_calidad.C4='101','3',IF(asistencia_encuesta_calidad.C4='0',2,asistencia_encuesta_calidad.C4))) AS C4,
						  
						 @C_5:=   IF(asistencia_encuesta_calidad.C5='100','0',IF(asistencia_encuesta_calidad.C5='101','3',IF(asistencia_encuesta_calidad.C5='0',2,asistencia_encuesta_calidad.C5))) AS C5,
						  
						 @T_1:=   IF(asistencia_encuesta_calidad.T1='100','0',IF(asistencia_encuesta_calidad.T1='101','3',IF(asistencia_encuesta_calidad.T1='0',2,asistencia_encuesta_calidad.T1))) AS T1,
						  
						 @T_2:=   IF(asistencia_encuesta_calidad.T2='100','0',IF(asistencia_encuesta_calidad.T2='101','3',IF(asistencia_encuesta_calidad.T2='0',2,asistencia_encuesta_calidad.T2))) AS T2,
						  
						 @T_3:=   IF(asistencia_encuesta_calidad.T3='100','0',IF(asistencia_encuesta_calidad.T3=
						'101','3',IF(asistencia_encuesta_calidad.T3='0',2,asistencia_encuesta_calidad.T3))) AS T3,

						 @T_4:=   IF(asistencia_encuesta_calidad.T4='100','0',IF(asistencia_encuesta_calidad.T4='101','3',IF(asistencia_encuesta_calidad.T4='0',2,asistencia_encuesta_calidad.T4))) AS T4,
						  
						 @T_5:=   IF(asistencia_encuesta_calidad.T5='100','0',IF(asistencia_encuesta_calidad.T5='101','3',IF(asistencia_encuesta_calidad.T5='0',2,asistencia_encuesta_calidad.T5))) AS T5,
						  
						 @G_1:=   IF(asistencia_encuesta_calidad.G1='10','1',IF(asistencia_encuesta_calidad.G1='7.5',2, IF(asistencia_encuesta_calidad.G1='5',3,IF(asistencia_encuesta_calidad.G1='2.5',4,IF(asistencia_encuesta_calidad.G1='0',5,IF(asistencia_encuesta_calidad.G1='100',0,'')))))) AS G1,
						  
						 @G_2:=   IF(asistencia_encuesta_calidad.G2='10','1',IF(asistencia_encuesta_calidad.G2='7.5',2, IF(asistencia_encuesta_calidad.G2='5',3,IF(asistencia_encuesta_calidad.G2='2.5',4,IF(asistencia_encuesta_calidad.G2='0',5,IF(asistencia_encuesta_calidad.G2='100',0,'')))))) AS G2,
						  
						 @G_4:=   IF(asistencia_encuesta_calidad.G4='10','1',IF(asistencia_encuesta_calidad.G4='7.5',2, IF(asistencia_encuesta_calidad.G4='5',3,IF(asistencia_encuesta_calidad.G4='2.5',4,IF(asistencia_encuesta_calidad.G4='0',5,IF(asistencia_encuesta_calidad.G4='100',0,'')))))) AS G4,
						  
						 @G_3:=  IF(asistencia_encuesta_calidad.G3='10','1',IF(asistencia_encuesta_calidad.G3='7',2, IF(asistencia_encuesta_calidad.G3='4','3',IF(asistencia_encuesta_calidad.G3='0','4',IF(asistencia_encuesta_calidad.G3='100',0,''))) )) AS G3,
						  
						 @G_5:=   IF(asistencia_encuesta_calidad.G5='10','1',IF(asistencia_encuesta_calidad.G5='7',2, IF(asistencia_encuesta_calidad.G5='4','3',IF(asistencia_encuesta_calidad.G5='0','4',IF(asistencia_encuesta_calidad.G5='100',0,''))) )) AS G5,
						  
						 @G_6:=   IF(asistencia_encuesta_calidad.G6='1',1,IF(asistencia_encuesta_calidad.G6='0',2, IF(asistencia_encuesta_calidad.G6='101',3,IF(asistencia_encuesta_calidad.G6='100',0,'')))) AS G6,
						 IF((@C_1+@C_2+@C_3+@C_4+@C_5 + @T_1+@T_2+@T_3+@T_4+@T_5+ @G_1+@G_2+@G_3+@G_4+@G_5+@G_6) <> 0,1,'-') AS SUMATOTAL,
						 
						 IF((@C_1+@C_2+@C_3+@C_4+@C_5+@T_1+@T_2+@T_3+@T_4+@T_5+@G_1+@G_2+@G_3+@G_4+@G_5+@G_6) >0,'1','') AS sumtotal,
						 
						 IF(@C_1=1,'1',IF(@C_1=2,'0','-')) AS cabina1,
						 IF(@C_2=1,'1',IF(@C_2=2,'0','-')) AS cabina2,
						 IF(@C_3=1,'1',IF(@C_3=2,'0','-')) AS cabina3,
						 IF(@C_4=1,'1',IF(@C_4=2,'0','-')) AS cabina4,
						 IF(@C_5=1,'1',IF(@C_5=2,'0','-')) AS cabina5,
						 
						 @TEC_1:=IF(@T_1=1,'1',IF(@T_1=2,'0','-')) AS tecnico1,
						 @TEC_2:=IF(@T_2=1,'1',IF(@T_2=2,'0','-')) AS tecnico2,
						 @TEC_3:=IF(@T_3=1,'1',IF(@T_3=2,'0','-')) AS tecnico3,
						 @TEC_4:=IF(@T_4=1,'1',IF(@T_4=2,'0','-')) AS tecnico4, 
						 @TEC_5:=IF(@T_5=1,'1',IF(@T_5=2,'0','-')) AS tecnico5,
						 
						 @GEN_1:=IF(@G_1=1,10,IF(@G_1=2,7.5,IF(@G_1=3,5,IF(@G_1=4,2.5,IF(@G_1=5,0,'-'))))) as general1,
						 @GEN_2:=IF(@G_2=1,10,IF(@G_2=2,7.5,IF(@G_2=3,5,IF(@G_2=4,2.5,IF(@G_2=5,0,'-'))))) as general2,
						 @GEN_3:=IF(@G_3=1,10,IF(@G_3=2,7,IF(@G_3=3,4,IF(@G_3=4,1,'-')))) as general3,
						 @GEN_4:=IF(@G_4=1,10,IF(@G_4=2,7.5,IF(@G_4=3,5,IF(@G_4=4,2.5,IF(@G_4=5,0,'-'))))) as general4,
						 @GEN_5:=IF(@G_5=1,10,IF(@G_5=2,7,IF(@G_5=3,4,IF(@G_5=4,0,'-')))) as general5,
						 @GEN_6:=IF(@G_6=1,10,IF(@G_6=2,0,'-')) as general6,
						 
						 IF(LEFT(@INTERNO,1)=1,@T_1,'-') AS RESTECNICOS_1,
						 IF(LEFT(@INTERNO,1)=1,@T_2,'-') AS RESTECNICOS_2,
						 IF(LEFT(@INTERNO,1)=1,@T_3,'-') AS RESTECNICOS_3,
						 IF(LEFT(@INTERNO,1)=1,@T_4,'-') AS RESTECNICOS_4,
						 IF(LEFT(@INTERNO,1)=1,@T_5,'-') AS RESTECNICOS_5,
						 
						 IF(LEFT(@INTERNO,1)=1,@TEC_1,'-') AS FILTECNICOS_1,
						 IF(LEFT(@INTERNO,1)=1,@TEC_2,'-') AS FILTECNICOS_2,
						 IF(LEFT(@INTERNO,1)=1,@TEC_3,'-') AS FILTECNICOS_3,
						 IF(LEFT(@INTERNO,1)=1,@TEC_4,'-') AS FILTECNICOS_4,
						 IF(LEFT(@INTERNO,1)=1,@TEC_5,'-') AS FILTECNICOS_5,
						 
						 IF(LEFT(@INTERNO,1)=1,@GEN_1,'-') AS GENERAL_1,
						 IF(LEFT(@INTERNO,1)=1,@GEN_2,'-') AS GENERAL_2,
						 IF(LEFT(@INTERNO,1)=1,@GEN_3,'-') AS GENERAL_3,
						 IF(LEFT(@INTERNO,1)=1,@GEN_4,'-') AS GENERAL_4,
						 IF(LEFT(@INTERNO,1)=1,@GEN_5,'-') AS GENERAL_5,
						 IF(LEFT(@INTERNO,1)=1,@GEN_6,'-') AS GENERAL_6					  
											  
					FROM $con->temporal.asistencia
					  INNER JOIN $con->temporal.expediente ON expediente.IDEXPEDIENTE = asistencia.IDEXPEDIENTE 					
					  INNER JOIN $con->temporal.asistencia_encuesta_calidad
						ON asistencia_encuesta_calidad.IDASISTENCIA = asistencia.IDASISTENCIA
					  INNER JOIN $con->catalogo.catalogo_cuenta
						ON catalogo_cuenta.IDCUENTA = asistencia.IDCUENTA
					  INNER JOIN $con->catalogo.catalogo_programa
						ON catalogo_programa.IDPROGRAMA = asistencia.IDPROGRAMA						
					  INNER JOIN $con->catalogo.catalogo_servicio
						ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
					  LEFT JOIN $con->catalogo.catalogo_programa_servicio
						ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO
					  INNER JOIN $con->temporal.asistencia_usuario
						ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA							
					WHERE $ver_cuentas
						asistencia_usuario.IDETAPA=1 AND expediente.ARRSTATUSEXPEDIENTE='CER'						
						AND asistencia.IDPROGRAMA LIKE '".$_POST["cmbplan"]."%'
						AND asistencia.ARRSTATUSENCUESTA IN($statusEnc)
						GROUP BY asistencia.IDASISTENCIA 
						HAVING LEFT(MIN(asistencia_usuario.FECHAHORA),7)='".$_POST["cmbanio"]."-".$_POST["cmbmes"]."' ";						
 
	$resultexp=$con->query($Sql_encuesta);
	if($resultexp->num_rows*1 == 0){	 
		echo "<script>";
		echo "alert('NO EXISTE REGISTROS PARA LA CONSULTA');";
		echo "</script>";
 	
	 } else{
	  
	set_time_limit(990);

	$aleatorio= rand(1000,9999);

//nombre archivo excel	
	$fecha= date("Ymd"); 
	$hora= date("hms"); 
	$nombre= "ENCUESTA_".$fecha."-".$aleatorio ; 

//libreria pear excel 	
	$pear = "./../../../../librerias/pearExcel";
	ini_set("include_path",ini_get("include_path").":$pear");
	require_once("../../../../librerias/pearExcel/Spreadsheet/Excel/Writer.php");

	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$nombre.xls");
	
	$workbook = & new Spreadsheet_Excel_Writer();

	$worksheet = &$workbook->addWorksheet("ENCUESTA");
	
//hidden
	$worksheet->setColumn(16,20,20,0,1);

//formato
	$titulo =& $workbook->addFormat();
	$titulo->setBold();
 
	//$titulo->setBgColor('red');
	
// formato tamano celdas
	$worksheet->setColumn(0,0,5); 
	$worksheet->setColumn(1,1,10); 
	$worksheet->setColumn(2,2,10); 
	$worksheet->setColumn(3,3,20); 
	$worksheet->setColumn(4,13,18); 
	$worksheet->setColumn(4,14,18);
	$worksheet->setColumn(4,15,18);
	$worksheet->setColumn(4,16,12); 
	
	$worksheet->setColumn(0,17,4); 
	$worksheet->setColumn(0,18,4); 
	$worksheet->setColumn(0,19,4); 
	$worksheet->setColumn(0,20,4); 
	$worksheet->setColumn(0,21,18); 
	$worksheet->setColumn(0,22,4); 
	$worksheet->setColumn(0,23,4); 
	$worksheet->setColumn(0,24,4); 
	$worksheet->setColumn(0,25,4); 
	$worksheet->setColumn(0,26,4); 
	$worksheet->setColumn(0,27,4); 
	$worksheet->setColumn(0,28,4); 
	$worksheet->setColumn(0,29,4); 
	$worksheet->setColumn(0,30,4); 
	$worksheet->setColumn(0,31,4); 
	$worksheet->setColumn(0,32,4); 
	
	$worksheet->setColumn(0,34,4); 
	$worksheet->setColumn(0,35,4); 
	$worksheet->setColumn(0,36,4); 
	$worksheet->setColumn(0,37,4); 
	$worksheet->setColumn(0,38,4); 
	$worksheet->setColumn(0,39,4); 
	$worksheet->setColumn(0,40,4); 
	$worksheet->setColumn(0,41,4); 
	$worksheet->setColumn(0,42,4); 
	$worksheet->setColumn(0,43,4); 
	$worksheet->setColumn(0,44,4); 
	$worksheet->setColumn(0,45,4); 
	$worksheet->setColumn(0,46,4); 
	$worksheet->setColumn(0,47,4); 
	$worksheet->setColumn(0,48,4); 
	$worksheet->setColumn(0,49,4); 
	$worksheet->setColumn(0,50,4); 
	$worksheet->setColumn(0,51,4); 
 
	$worksheet->setColumn(0,53,4); 
	$worksheet->setColumn(0,54,4); 
	$worksheet->setColumn(0,55,10); 
	$worksheet->setColumn(0,56,12); 
	$worksheet->setColumn(0,57,4); 
	$worksheet->setColumn(0,58,4); 
	$worksheet->setColumn(0,59,4); 
	$worksheet->setColumn(0,60,4); 
	$worksheet->setColumn(0,61,4); 
	$worksheet->setColumn(0,62,4); 
	$worksheet->setColumn(0,63,4); 
	$worksheet->setColumn(0,64,4); 
	$worksheet->setColumn(0,65,4); 
	$worksheet->setColumn(0,66,4); 
	$worksheet->setColumn(0,67,4);  
	$worksheet->setColumn(0,68,4);  
	$worksheet->setColumn(0,69,4);  
	$worksheet->setColumn(0,70,4);  
	$worksheet->setColumn(0,71,4);  
	$worksheet->setColumn(0,72,4);  
   
//color titulo
	$titulos =& $workbook->addFormat();
	$titulos->setBorder(1);
	$titulos->setHAlign('center');
	$titulos->setFgColor('aqua');
	$titulos->setBold();
	
//color titulo cabina,tecnico y general
	$titulosOtros =& $workbook->addFormat();
	$titulosOtros->setAlign('center');
	$titulosOtros->setBorder(1);
//esconder  columnas



//cabecera
$txtccl=number_format(($_POST["txtccl"])/100,2);
$worksheet->write(1, 0,"DATOS PARA ENCUESTA DE SATISFACCION",$titulo);
$worksheet->write(1, 4,"CCL PREVIO=");

$worksheet->write(1, 6,"OBJETIVO=");
$worksheet->write(1, 7,$_POST["txtobjetivo"]);
 
//$worksheet->write(3, 30,"",$titulosOtros);

//cabecera resultado
	//formato celdas centrado
		$format_centro =& $workbook->addFormat();
		$format_centro->setAlign('center');
		$format_centro->setBorder(1);	

		$format_centroVert =& $workbook->addFormat();
		$format_centroVert->setHAlign('center');
		$format_centroVert->setVAlign('vcenter');
		$format_centroVert->setFgColor('aqua');
		$format_centroVert->setBorder(1);
		$format_centroVert->setBold();
	
$worksheet->write(3, 0,"NRO",$format_centroVert);
$worksheet->write(3, 1,"EXPEDIENTE",$format_centroVert);
$worksheet->write(3, 2,"ASISTENCIA",$format_centroVert);
$worksheet->write(3, 3,"NOMBRECUENTA",$format_centroVert);
$worksheet->write(3, 4,"NOMBREPLAN",$format_centroVert);
$worksheet->write(3, 5,"USUARIOEXP",$format_centroVert);
$worksheet->write(3, 6,"FECHAAPERTURAEXP",$format_centroVert);
$worksheet->write(3, 7,"NOMBREAFILIADO",$format_centroVert);
$worksheet->write(3, 8,"STATUSASISTENCIA",$format_centroVert);
$worksheet->write(3, 9,"SERVICIO",$format_centroVert);
$worksheet->write(3, 10,"USUARIOENCUESTA",$format_centroVert);
$worksheet->write(3, 11,"FECHAENCUESTA",$format_centroVert);
$worksheet->write(3, 12,"HORAENCUESTA",$format_centroVert);
$worksheet->write(3, 13,"OBSERVACION",$format_centroVert);
$worksheet->write(3, 14,"EMAILS",$format_centroVert);
$worksheet->write(3, 15,"PROVEEDOR",$format_centroVert);
$worksheet->write(3, 21,"TEC. PROPIO",$format_centroVert);
$worksheet->write(3, 22,"CABINA",$format_centro);

$worksheet->write(4, 22,"1",$titulosOtros);
$worksheet->write(4, 23,"2",$titulosOtros);
$worksheet->write(4, 24,"3",$titulosOtros);
$worksheet->write(4, 25,"4",$titulosOtros);
$worksheet->write(4, 26,"5",$titulosOtros);


$worksheet->write(3, 27,"TECNICOS",$format_centro);
$worksheet->write(4, 27,"1",$titulosOtros);
$worksheet->write(4, 28,"2",$titulosOtros);
$worksheet->write(4, 29,"3",$titulosOtros);
$worksheet->write(4, 30,"4",$titulosOtros);
$worksheet->write(4, 31,"5",$titulosOtros);

$worksheet->write(3, 32,"GENERAL",$format_centro);
$worksheet->write(4, 32,"1",$titulosOtros);
$worksheet->write(4, 33,"2",$titulosOtros);
$worksheet->write(4, 34,"3",$titulosOtros);
$worksheet->write(4, 35,"4",$titulosOtros);
$worksheet->write(4, 36,"5",$titulosOtros);
$worksheet->write(4, 37,"6",$titulosOtros);

$worksheet->write(3, 39,"CABINA",$format_centro);
$worksheet->write(4, 39,"1",$titulosOtros);
$worksheet->write(4, 40,"2",$titulosOtros);
$worksheet->write(4, 41,"3",$titulosOtros);
$worksheet->write(4, 42,"4",$titulosOtros);
$worksheet->write(4, 43,"5",$titulosOtros);

$worksheet->write(3, 44,"TECNICOS",$format_centro);
$worksheet->write(4, 44,"1",$titulosOtros);
$worksheet->write(4, 45,"2",$titulosOtros);
$worksheet->write(4, 46,"3",$titulosOtros);
$worksheet->write(4, 47,"4",$titulosOtros);
$worksheet->write(4, 48,"5",$titulosOtros);

$worksheet->write(3, 49,"GENERAL",$format_centro);
$worksheet->write(4, 49,"1",$titulosOtros);
$worksheet->write(4, 50,"2",$titulosOtros);
$worksheet->write(4, 51,"3",$titulosOtros);
$worksheet->write(4, 52,"4",$titulosOtros);
$worksheet->write(4, 53,"5",$titulosOtros);
$worksheet->write(4, 54,"6",$titulosOtros);

$worksheet->write(3, 55,"PROMEDIO",$titulosOtros);
$worksheet->write(3, 56,"CALIFICACION",$titulosOtros);
 
$worksheet->write(3, 57,"TECNICOS",$format_centro);
$worksheet->write(4, 57,"1",$titulosOtros);
$worksheet->write(4, 58,"2",$titulosOtros);
$worksheet->write(4, 59,"3",$titulosOtros);
$worksheet->write(4, 60,"4",$titulosOtros);
$worksheet->write(4, 61,"5",$titulosOtros);
$worksheet->write(3, 62,"TECNICOS FILT.",$format_centro);
$worksheet->write(4, 62,"1",$titulosOtros);
$worksheet->write(4, 63,"2",$titulosOtros);
$worksheet->write(4, 64,"3",$titulosOtros);
$worksheet->write(4, 65,"4",$titulosOtros);
$worksheet->write(4, 66,"5",$titulosOtros);
$worksheet->write(3, 67,"GENERAL",$format_centro);
$worksheet->write(4, 67,"1",$titulosOtros);
$worksheet->write(4, 68,"2",$titulosOtros);
$worksheet->write(4, 69,"3",$titulosOtros);
$worksheet->write(4, 70,"4",$titulosOtros);
$worksheet->write(4, 71,"5",$titulosOtros);
$worksheet->write(4, 72,"6",$titulosOtros);  
 
//combinar celdas

	$worksheet->mergeCells(3, 0, 4,0);
	$worksheet->mergeCells(3, 1, 4,1);
	$worksheet->mergeCells(3, 2, 4,2);
	$worksheet->mergeCells(3, 3, 4,3);
	$worksheet->mergeCells(3, 4, 4,4);
	$worksheet->mergeCells(3, 5, 4,5);
	$worksheet->mergeCells(3, 6, 4,6);
	$worksheet->mergeCells(3, 7, 4,7);
	$worksheet->mergeCells(3, 8, 4,8);
	$worksheet->mergeCells(3, 9, 4,9);
	$worksheet->mergeCells(3, 10, 4,10);
	$worksheet->mergeCells(3, 11, 4,11);
	$worksheet->mergeCells(3, 12, 4,12);
	$worksheet->mergeCells(3, 13, 4,13);
	$worksheet->mergeCells(3, 14, 4,14);
	$worksheet->mergeCells(3, 15, 4,15);
	$worksheet->mergeCells(3, 21, 4,21);

	$worksheet->mergeCells(3, 22, 3,26);
	$worksheet->mergeCells(3, 27, 3,31);
	$worksheet->mergeCells(3, 32, 3,37);
	
	$worksheet->mergeCells(3, 38, 4,38);
 
	$worksheet->mergeCells(3, 39, 3,43);
	$worksheet->mergeCells(3, 44, 3,48);
	$worksheet->mergeCells(3, 49, 3,54);
	$worksheet->mergeCells(3, 55, 4,55);
	$worksheet->mergeCells(3, 56, 4,56);
	
	$worksheet->mergeCells(3, 57, 3,61);
	$worksheet->mergeCells(3, 62, 3,66);
	$worksheet->mergeCells(3, 67, 3,72);  

//formatos
	$worksheet->write(4, 0, "",$format_centroVert);
	$worksheet->write(4, 1, "",$format_centroVert);
	$worksheet->write(4, 2, "",$format_centroVert);
	$worksheet->write(4, 3, "",$format_centroVert);
	$worksheet->write(4, 4, "",$format_centroVert);
	$worksheet->write(4, 5, "",$format_centroVert);
	$worksheet->write(4, 6, "",$format_centroVert);
	$worksheet->write(4, 7, "",$format_centroVert);
	$worksheet->write(4, 8, "",$format_centroVert);
	$worksheet->write(4, 9, "",$format_centroVert);
	$worksheet->write(4, 10, "",$format_centroVert);
	$worksheet->write(4, 11, "",$format_centroVert);
	$worksheet->write(4, 12, "",$format_centroVert);
	$worksheet->write(4, 13, "",$format_centroVert);
	$worksheet->write(4, 14, "",$format_centroVert);
	$worksheet->write(4, 15, "",$format_centroVert);
	$worksheet->write(4, 16, "",$format_centroVert); 


	$worksheet->write(3, 18, "",$format_centro);
	$worksheet->write(3, 19, "",$format_centro);
	$worksheet->write(3, 20, "",$format_centro);
	$worksheet->write(4, 21, "",$format_centro);

	$worksheet->write(3, 23, "",$format_centro);
	$worksheet->write(3, 24, "",$format_centro);
	$worksheet->write(3, 25, "",$format_centro);
	$worksheet->write(3, 26, "",$format_centro);
	
	$worksheet->write(3, 28, "",$format_centro);
	$worksheet->write(3, 29, "",$format_centro);
	$worksheet->write(3, 30, "",$format_centro);
	$worksheet->write(3, 31, "",$format_centro);
		
	$worksheet->write(3, 33, "",$format_centro);
 	$worksheet->write(3, 34, "",$format_centro);
 	$worksheet->write(3, 35, "",$format_centro);
 	$worksheet->write(3, 36, "",$format_centro);
 	$worksheet->write(3, 37, "",$format_centro);
	
 	$worksheet->write(3, 38, "",$format_centro);
 	$worksheet->write(4, 38, "",$format_centro);
 
 

	 		 
	$worksheet->write(3, 40, "",$format_centro);
	$worksheet->write(3, 41, "",$format_centro);
	$worksheet->write(3, 42, "",$format_centro);
	$worksheet->write(3, 43, "",$format_centro);
	
	$worksheet->write(3, 45, "",$format_centro);
	$worksheet->write(3, 46, "",$format_centro);
	$worksheet->write(3, 47, "",$format_centro);
	$worksheet->write(3, 48, "",$format_centro);
 
	$worksheet->write(3, 50, "",$format_centro); 
	$worksheet->write(3, 51, "",$format_centro); 
	$worksheet->write(3, 52, "",$format_centro); 
	$worksheet->write(3, 53, "",$format_centro); 
	$worksheet->write(3, 54, "",$format_centro); 
 

	$worksheet->write(4, 55, "",$format_centro);
	$worksheet->write(4, 56, "",$format_centro);
		
	$worksheet->write(3, 58, "",$format_centro);
	$worksheet->write(3, 59, "",$format_centro);
	$worksheet->write(3, 60, "",$format_centro);
	$worksheet->write(3, 61, "",$format_centro);
	$worksheet->write(3, 63, "",$format_centro);
	$worksheet->write(3, 64, "",$format_centro);
	$worksheet->write(3, 65, "",$format_centro);
	$worksheet->write(3, 66, "",$format_centro);
	$worksheet->write(3, 68, "",$format_centro);
	$worksheet->write(3, 69, "",$format_centro);
	$worksheet->write(3, 70, "",$format_centro);
	$worksheet->write(3, 71, "",$format_centro);
	$worksheet->write(3, 72, "",$format_centro);  
 
 
 
  
	$fila=5;
	
	while($row = $resultexp->fetch_object()){
			
		$columna=0;
		$contador++;
		
		$worksheet->write($fila,$columna,$contador);
		$worksheet->write($fila,$columna+1,$row->IDEXPEDIENTE  );
		$worksheet->write($fila,$columna+2,$row->IDASISTENCIA  );
		$worksheet->write($fila,$columna+3,$row->NOMBRE  );
		$worksheet->write($fila,$columna+4,$row->plan  );
		$worksheet->write($fila,$columna+5,$row->USUARIOAPEEXP  );
		$worksheet->write($fila,$columna+6,$row->FECHAAPEEXP  );
		$worksheet->write($fila,$columna+7,$row->titular  );
		$worksheet->write($fila,$columna+8,$desc_status_asistencia[$row->ARRSTATUSASISTENCIA]);
		$worksheet->write($fila,$columna+9,$row->servicio  );		
		$worksheet->write($fila,$columna+10,$row->USUARIOENCUENTA  );
		$worksheet->write($fila,$columna+11,$row->FECHAENCUENTA  );
		//$worksheet->write($fila,$columna+12,$evalencuesta_new[$row->ARRSTATUSENCUESTA]  );		
		$worksheet->write($fila,$columna+12,$row->HORAENCUENTA  );		
		$worksheet->write($fila,$columna+13,$row->COMENTARIO  );
		$worksheet->write($fila,$columna+14,strtolower($row->EMAILS)  );
		$worksheet->write($fila,$columna+15,($row->proveedor)?substr($row->proveedor,2,150):"" );
		$worksheet->write($fila,$columna+21,($row->proveedor)?substr($row->proveedor,0,1):""  );
	  	$worksheet->write($fila,$columna+22,$row->C1  );
		$worksheet->write($fila,$columna+23,$row->C2  );
		$worksheet->write($fila,$columna+24,$row->C3  );
		$worksheet->write($fila,$columna+25,$row->C4  );
		$worksheet->write($fila,$columna+26,$row->C5  );
		$worksheet->write($fila,$columna+27,$row->T1  );
		$worksheet->write($fila,$columna+28,$row->T2  );
		$worksheet->write($fila,$columna+29,$row->T3  );
		$worksheet->write($fila,$columna+30,$row->T4  );
		$worksheet->write($fila,$columna+31,$row->T5  );
		$worksheet->write($fila,$columna+32,$row->G1  );
		$worksheet->write($fila,$columna+33,$row->G2  );
		$worksheet->write($fila,$columna+34,$row->G3  );
		$worksheet->write($fila,$columna+35,$row->G4  );
		$worksheet->write($fila,$columna+36,$row->G5  );
		$worksheet->write($fila,$columna+37,$row->G6  );
		$worksheet->write($fila,$columna+38,'=IF(SUM(W'.($fila+1).':AL'.($fila+1).') <>0,1,"-")'  );	  
		$worksheet->write($fila,$columna+39,$row->cabina1  );
		$worksheet->write($fila,$columna+40,$row->cabina2  );
		$worksheet->write($fila,$columna+41,$row->cabina3  );
		 $worksheet->write($fila,$columna+42,$row->cabina4  );
		$worksheet->write($fila,$columna+43,$row->cabina5  );
		$worksheet->write($fila,$columna+44,$row->tecnico1  );
		$worksheet->write($fila,$columna+45,$row->tecnico2  );
		$worksheet->write($fila,$columna+46,$row->tecnico3  );
		$worksheet->write($fila,$columna+47,$row->tecnico4  );
		$worksheet->write($fila,$columna+48,$row->tecnico5  );
		$worksheet->write($fila,$columna+49,$row->general1  );
		$worksheet->write($fila,$columna+50,$row->general2  );
		$worksheet->write($fila,$columna+51,$row->general3  );
		$worksheet->write($fila,$columna+52,$row->general4  );
		$worksheet->write($fila,$columna+53,$row->general5  );
		$worksheet->write($fila,$columna+54,$row->general6  );
		$worksheet->write($fila,$columna+55,"=AVERAGE(AX".($fila+1).":BB".($fila+1).")" );
		$worksheet->write($fila,$columna+56,'=IF(BD'.($fila+1).' >=9,"E",(IF(AND(BD'.($fila+1).' <9,BD'.($fila+1).' >=7),"MB",IF(AND(BD'.($fila+1).' <7,BD'.($fila+1).' >=5),"B",IF(AND(BD'.($fila+1).' <5,BD'.($fila+1).' >=3),"R","M")))))' );
		$worksheet->write($fila,$columna+57,$row->RESTECNICOS_1  );
		$worksheet->write($fila,$columna+58,$row->RESTECNICOS_2  );
		$worksheet->write($fila,$columna+59,$row->RESTECNICOS_3  );
		$worksheet->write($fila,$columna+60,$row->RESTECNICOS_4  );
		$worksheet->write($fila,$columna+61,$row->RESTECNICOS_5  );
		$worksheet->write($fila,$columna+62,$row->FILTECNICOS_1  );
		$worksheet->write($fila,$columna+63,$row->FILTECNICOS_2  );
		$worksheet->write($fila,$columna+64,$row->FILTECNICOS_3  );
		$worksheet->write($fila,$columna+65,$row->FILTECNICOS_4  );
		$worksheet->write($fila,$columna+66,$row->FILTECNICOS_5  );
		$worksheet->write($fila,$columna+67,$row->GENERAL_1  );
		$worksheet->write($fila,$columna+68,$row->GENERAL_2  );
		$worksheet->write($fila,$columna+69,$row->GENERAL_3  );
		$worksheet->write($fila,$columna+70,$row->GENERAL_4  );
		$worksheet->write($fila,$columna+71,$row->GENERAL_5  );
		$worksheet->write($fila,$columna+72,$row->GENERAL_6  );		 
		$worksheet->write($fila,$columna+73,'=IF(SUM(BK'.($fila+1).':BU'.($fila+1).')<>0,AVERAGE(BP'.($fila+1).':BT'.($fila+1).'),"-")' );

		$fila++;	 
	}  
	
//formato porcentaje
	$porcentajeFormat =& $workbook->addFormat();
	$porcentajeFormat->setNumFormat('0.0%');
	
//formato porcentaje resultados CCL
	$porcentajeFormat =& $workbook->addFormat();
	$porcentajeFormat->setNumFormat('0.0%');
	
	$worksheet->write(1,5,$txtccl,$porcentajeFormat); 
	 

	$worksheet->write($fila+2,0,$fila-5);
	$worksheet->write($fila+2,1,"REGISTROS BARRIDOS");
	$worksheet->write($fila+3,0,"=H2" );
	$worksheet->write($fila+3,1,"OBJETIVO A COMPLETAR ( DE OPTIMIZADOR )");
	$worksheet->write($fila+4,0,"=F2",$porcentajeFormat );
	$worksheet->write($fila+4,1,"CCL ANTERIOR ( DE OPTIMIZADOR )" );
	$worksheet->write($fila+5,0,"=1-F2");
	
	$worksheet->write($fila+7,0,"RESULTADOS CABINA");
	$worksheet->write($fila+8,1,"CCLc" );
	$worksheet->write($fila+8,2,'=2/((1/AVERAGE(W'.($fila+13).':AA'.($fila+13).')+(1/((AI'.($fila+10).')/10))))',$porcentajeFormat );	
	
	$worksheet->write($fila+9,1,"CCL TOTAL");
	$worksheet->write($fila+9,2,'=(C'.($fila+9).'+C'.($fila+12).')/2',$porcentajeFormat );
	
	$worksheet->write($fila+10,0,"RESULTADOS TECNICOS");
	$worksheet->write($fila+11,1,"CCLt" );
	$worksheet->write($fila+11,2,'=2/((1/AVERAGE(AB'.($fila+13).':AF'.($fila+13).')+(1/((AK'.($fila+10).')/10))))',$porcentajeFormat );
	$worksheet->write($fila+12,2,'=2/((1/AVERAGE(BI'.($fila+13).':BM'.($fila+13).')+(1/((BR'.($fila+10).')/10))))',$porcentajeFormat );
	
	$worksheet->write($fila+12,1,"CCLt Internos" );
	$worksheet->write($fila+13,1,"CCL TOTAL" );
	$worksheet->write($fila+13,2,'=(C'.($fila+12).'+C'.($fila+9).')/2',$porcentajeFormat );

//titulos
	$columnaRes=16;
	$worksheet->write($fila+2,$columnaRes+5,"Promedio" );
	$worksheet->write($fila+3,$columnaRes+5,"T inversa" );
	$worksheet->write($fila+4,$columnaRes+5,"Grados lib." );
	$worksheet->write($fila+5,$columnaRes+5,"DesvEst" );
	$worksheet->write($fila+6,$columnaRes+5,"Raiz (n)" );
	$worksheet->write($fila+7,$columnaRes+5,"Maximo" );
	$worksheet->write($fila+8,$columnaRes+5,"Minimo" );
	$worksheet->write($fila+9,$columnaRes+5,"Prom prob falla" );
		
//promedio	
	$worksheet->write($fila+2,$columnaRes+6,"=AVERAGE(AN6:AN".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+7,"=AVERAGE(AO6:AO".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+8,"=AVERAGE(AP6:AP".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+9,"=AVERAGE(AQ6:AQ".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+10,"=AVERAGE(AR6:AR".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+11,"=AVERAGE(AS6:AS".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+12,"=AVERAGE(AT6:AT".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+13,"=AVERAGE(AU6:AU".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+14,"=AVERAGE(AV6:AV".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+15,"=AVERAGE(AW6:AW".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+16,"=AVERAGE(AX6:AX".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+17,"=AVERAGE(AY6:AY".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+18,"=AVERAGE(AZ6:AZ".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+19,"=AVERAGE(BA6:BA".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+20,"=AVERAGE(BB6:BB".($fila).")" );
	$worksheet->write($fila+2,$columnaRes+21,"=AVERAGE(BC6:BC".($fila).")" );

//tinversa
	$worksheet->write($fila+3,$columnaRes+6,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+7,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+8,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+9,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+10,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+11,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+12,"=TINV(A".($fila+6).",H2-1)" );
 	$worksheet->write($fila+3,$columnaRes+13,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+14,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+15,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+16,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+17,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+18,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+19,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+20,"=TINV(A".($fila+6).",H2-1)" );
	$worksheet->write($fila+3,$columnaRes+21,"=TINV(A".($fila+6).",H2-1)" );

	$worksheet->write($fila+4,$columnaRes+6,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+7,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+8,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+9,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+10,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+11,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+12,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+13,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+14,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+15,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+16,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+17,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+18,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+19,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+20,"=H2-1" );
	$worksheet->write($fila+4,$columnaRes+21,"=H2-1" );
 
 //desviacion standart
 	$worksheet->write($fila+5,$columnaRes+6,"=STDEV(AN6:AN".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+7,"=STDEV(AO6:AO".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+8,"=STDEV(AP6:AP".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+9,"=STDEV(AQ6:AQ".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+10,"=STDEV(AR6:AR".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+11,"=STDEV(AS6:AS".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+12,"=STDEV(AT6:AT".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+13,"=STDEV(AU6:AU".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+14,"=STDEV(AV6:AV".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+15,"=STDEV(AW6:AW".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+16,"=STDEV(AX6:AX".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+17,"=STDEV(AY6:AY".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+18,"=STDEV(AZ6:AZ".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+19,"=STDEV(BA6:BA".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+20,"=STDEV(BB6:BB".($fila).")" );
	$worksheet->write($fila+5,$columnaRes+21,"=STDEV(BC6:BC".($fila).")" );

 //raiz
 	$worksheet->write($fila+6,$columnaRes+6,"=SQRT(COUNT(AN6:AN".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+7,"=SQRT(COUNT(AO6:AO".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+8,"=SQRT(COUNT(AP6:AP".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+9,"=SQRT(COUNT(AQ6:AQ".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+10,"=SQRT(COUNT(AR6:AR".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+11,"=SQRT(COUNT(AS6:AS".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+12,"=SQRT(COUNT(AT6:AT".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+13,"=SQRT(COUNT(AU6:AU".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+14,"=SQRT(COUNT(AV6:AV".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+15,"=SQRT(COUNT(AW6:AW".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+16,"=SQRT(COUNT(AX6:AX".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+17,"=SQRT(COUNT(AY6:AY".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+18,"=SQRT(COUNT(AZ6:AZ".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+19,"=SQRT(COUNT(BA6:BA".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+20,"=SQRT(COUNT(BB6:BB".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+21,"=SQRT(COUNT(BC6:BC".($fila)."))" );
//maximo
 	$worksheet->write($fila+7,$columnaRes+6,"=W".($fila+3)."+(W".($fila+4)."*W".($fila+6).")/W".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+7,"=X".($fila+3)."+(X".($fila+4)."*X".($fila+6).")/X".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+8,"=Y".($fila+3)."+(Y".($fila+4)."*Y".($fila+6).")/Y".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+9,"=Z".($fila+3)."+(Z".($fila+4)."*Z".($fila+6).")/Z".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+10,"=AA".($fila+3)."+(AA".($fila+4)."*AA".($fila+6).")/AA".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+11,"=AB".($fila+3)."+(AB".($fila+4)."*AB".($fila+6).")/AB".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+12,"=AC".($fila+3)."+(AC".($fila+4)."*AC".($fila+6).")/AC".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+13,"=AD".($fila+3)."+(AD".($fila+4)."*AD".($fila+6).")/AD".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+14,"=AE".($fila+3)."+(AE".($fila+4)."*AE".($fila+6).")/AE".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+15,"=AF".($fila+3)."+(AF".($fila+4)."*AF".($fila+6).")/AF".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+16,"=AG".($fila+3)."+(AG".($fila+4)."*AG".($fila+6).")/AG".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+17,"=AH".($fila+3)."+(AH".($fila+4)."*AH".($fila+6).")/AH".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+18,"=AI".($fila+3)."+(AI".($fila+4)."*AI".($fila+6).")/AI".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+19,"=AJ".($fila+3)."+(AJ".($fila+4)."*AJ".($fila+6).")/AJ".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+20,"=AK".($fila+3)."+(AK".($fila+4)."*AK".($fila+6).")/AK".($fila+7)."" );	
 	$worksheet->write($fila+7,$columnaRes+21,"=AL".($fila+3)."+(AL".($fila+4)."*AL".($fila+6).")/AL".($fila+7)."" );
	
//minimo
 	$worksheet->write($fila+8,$columnaRes+6,"=W".($fila+3)."-(W".($fila+4)."*W".($fila+6).")/W".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+7,"=X".($fila+3)."-(X".($fila+4)."*X".($fila+6).")/X".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+8,"=Y".($fila+3)."-(Y".($fila+4)."*Y".($fila+6).")/Y".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+9,"=Z".($fila+3)."-(Z".($fila+4)."*Z".($fila+6).")/Z".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+10,"=AA".($fila+3)."-(AA".($fila+4)."*AA".($fila+6).")/AA".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+11,"=AB".($fila+3)."-(AB".($fila+4)."*AB".($fila+6).")/AB".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+12,"=AC".($fila+3)."-(AC".($fila+4)."*AC".($fila+6).")/AC".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+13,"=AD".($fila+3)."-(AD".($fila+4)."*AD".($fila+6).")/AD".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+14,"=AE".($fila+3)."-(AE".($fila+4)."*AE".($fila+6).")/AE".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+15,"=AF".($fila+3)."-(AF".($fila+4)."*AF".($fila+6).")/AF".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+16,"=AG".($fila+3)."-(AG".($fila+4)."*AG".($fila+6).")/AG".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+17,"=AH".($fila+3)."-(AH".($fila+4)."*AH".($fila+6).")/AH".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+18,"=AI".($fila+3)."-(AI".($fila+4)."*AI".($fila+6).")/AI".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+19,"=AJ".($fila+3)."-(AJ".($fila+4)."*AJ".($fila+6).")/AJ".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+20,"=AK".($fila+3)."-(AK".($fila+4)."*AK".($fila+6).")/AK".($fila+7)."" );	
 	$worksheet->write($fila+8,$columnaRes+21,"=AL".($fila+3)."-(AL".($fila+4)."*AL".($fila+6).")/AL".($fila+7)."" );	 

// promedio falla
	$worksheet->write($fila+9,$columnaRes+6,"=W".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+7,"=X".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+8,"=Y".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+9,"=Z".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+10,"=AA".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+11,"=AB".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+12,"=AC".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+13,"=AD".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+14,"=AE".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+15,"=AF".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+16,"=AG".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+17,"=AH".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+18,"=AI".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+19,"=AJ".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+20,"=AK".($fila+3)."" );	 
	$worksheet->write($fila+9,$columnaRes+21,"=AL".($fila+3)."" );	 

//titulo calificacion
	$worksheet->write($fila+10,$columnaRes+6,"Lo escucho atentamente?" );	
	$worksheet->write($fila+10,$columnaRes+7,"Se expresaba en forma clara?" );	
	$worksheet->write($fila+10,$columnaRes+8,"Entendio su problema?" );	
	$worksheet->write($fila+10,$columnaRes+9,"Fue amable?" );	
	$worksheet->write($fila+10,$columnaRes+10,"Brindo la ayuda esperada?" );	
	$worksheet->write($fila+10,$columnaRes+11,"Tenia conocimiento previo del problema?" );	
	$worksheet->write($fila+10,$columnaRes+12,"Dio explicacion de tareas?" );	
	$worksheet->write($fila+10,$columnaRes+13,"Fue amable y respetuoso?" );	
	$worksheet->write($fila+10,$columnaRes+14,"Trabajo con eficiencia y limpieza?" );	
	$worksheet->write($fila+10,$columnaRes+15,"Arribo a tiempo?" );	
	$worksheet->write($fila+10,$columnaRes+16,"Rapidez telefonica" );	
	$worksheet->write($fila+10,$columnaRes+17,"Asignacion rapida de tecnico" );	
	$worksheet->write($fila+10,$columnaRes+18,"Satisfaccion con telefonistas" );	
	$worksheet->write($fila+10,$columnaRes+19,"Calidad tecnica" );	
	$worksheet->write($fila+10,$columnaRes+20,"Satisfaccion con tecnicos" );	
	$worksheet->write($fila+10,$columnaRes+21,"Nos recomendaria?" );	

//resumen calificacion
	//formato porcentaje
	$porcentajeFormat =& $workbook->addFormat();
	$porcentajeFormat->setNumFormat('0.0%');
	$porcentajeFormat->setBorder(1);
	
	$worksheet->write($fila+2,$columnaRes+37,"E",$titulosOtros );
	$worksheet->write($fila+3,$columnaRes+37,"MB",$titulosOtros );
	$worksheet->write($fila+4,$columnaRes+37,"B",$titulosOtros );
	$worksheet->write($fila+5,$columnaRes+37,"R",$titulosOtros );
	$worksheet->write($fila+6,$columnaRes+37,"M",$titulosOtros );
		
	$worksheet->write($fila+2,$columnaRes+38,'=COUNTIF(BE6:BE'.($fila).',"E")',$titulosOtros );
	$worksheet->write($fila+3,$columnaRes+38,'=COUNTIF(BE6:BE'.($fila).',"MB")',$titulosOtros );
	$worksheet->write($fila+4,$columnaRes+38,'=COUNTIF(BE6:BE'.($fila).',"B")',$titulosOtros );
	$worksheet->write($fila+5,$columnaRes+38,'=COUNTIF(BE6:BE'.($fila).',"R")',$titulosOtros );
	$worksheet->write($fila+6,$columnaRes+38,'=COUNTIF(BE6:BE'.($fila).',"M")',$titulosOtros );

	$worksheet->write($fila+2,$columnaRes+39,'=BC'.($fila+3).'/SUM(BC'.($fila+3).':BC'.($fila+7).')',$porcentajeFormat );
	$worksheet->write($fila+3,$columnaRes+39,'=BC'.($fila+4).'/SUM(BC'.($fila+3).':BC'.($fila+7).')',$porcentajeFormat );
	$worksheet->write($fila+4,$columnaRes+39,'=BC'.($fila+5).'/SUM(BC'.($fila+3).':BC'.($fila+7).')',$porcentajeFormat );
	$worksheet->write($fila+5,$columnaRes+39,'=BC'.($fila+6).'/SUM(BC'.($fila+3).':BC'.($fila+7).')',$porcentajeFormat );
	$worksheet->write($fila+6,$columnaRes+39,'=BC'.($fila+7).'/SUM(BC'.($fila+3).':BC'.($fila+7).')',$porcentajeFormat );
 		

//promedio tecnicos
	//titulos
	
	$worksheet->write($fila+2,$columnaRes+41,"Promedio" );
	$worksheet->write($fila+3,$columnaRes+41,"T inversa" );
	$worksheet->write($fila+4,$columnaRes+41,"Grados lib." );
	$worksheet->write($fila+5,$columnaRes+41,"DesvEst" );
	$worksheet->write($fila+6,$columnaRes+41,"Raiz (n)" );
	$worksheet->write($fila+7,$columnaRes+41,"Maximo" );
	$worksheet->write($fila+8,$columnaRes+41,"Minimo" );
	$worksheet->write($fila+9,$columnaRes+41,"Prom prob falla" );
	
	//promedio
	$worksheet->write($fila+2,$columnaRes+44,"=AVERAGE(BK6:BK".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+45,"=AVERAGE(BL6:BL".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+46,"=AVERAGE(BM6:BM".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+47,"=AVERAGE(BN6:BN".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+48,"=AVERAGE(BO6:BO".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+49,"=AVERAGE(BP6:BP".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+50,"=AVERAGE(BQ6:BQ".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+51,"=AVERAGE(BR6:BR".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+52,"=AVERAGE(BS6:BS".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+53,"=AVERAGE(BT6:BT".($fila).")" );	
	$worksheet->write($fila+2,$columnaRes+54,"=AVERAGE(BU6:BU".($fila).")" );	

//tinversa tecnicos
	$worksheet->write($fila+3,$columnaRes+44,"=TINV(A".($fila+6).",BI".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+45,"=TINV(A".($fila+6).",BJ".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+46,"=TINV(A".($fila+6).",BK".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+47,"=TINV(A".($fila+6).",BL".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+48,"=TINV(A".($fila+6).",BM".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+49,"=TINV(A".($fila+6).",BN".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+50,"=TINV(A".($fila+6).",BO".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+51,"=TINV(A".($fila+6).",BP".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+52,"=TINV(A".($fila+6).",BQ".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+53,"=TINV(A".($fila+6).",BR".($fila+5).")" );
	$worksheet->write($fila+3,$columnaRes+54,"=TINV(A".($fila+6).",BS".($fila+5).")" );

//grados lib.
	$worksheet->write($fila+4,$columnaRes+44,"2" );
	$worksheet->write($fila+4,$columnaRes+45,"2" );
	$worksheet->write($fila+4,$columnaRes+46,"2" );
	$worksheet->write($fila+4,$columnaRes+47,"2" );
	$worksheet->write($fila+4,$columnaRes+48,"2" );
	$worksheet->write($fila+4,$columnaRes+49,"4" );
	$worksheet->write($fila+4,$columnaRes+50,"4" );
	$worksheet->write($fila+4,$columnaRes+51,"3" );
	$worksheet->write($fila+4,$columnaRes+52,"4" );
	$worksheet->write($fila+4,$columnaRes+53,"3" );
	$worksheet->write($fila+4,$columnaRes+54,"2" );
 
//desviacion standart tecnicos
 	$worksheet->write($fila+5,$columnaRes+44,"=STDEV(BK6:BK".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+45,"=STDEV(BL6:BL".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+46,"=STDEV(BM6:BM".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+47,"=STDEV(BN6:BN".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+48,"=STDEV(BO6:BO".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+49,"=STDEV(BP6:BP".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+50,"=STDEV(BQ6:BQ".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+51,"=STDEV(BR6:BR".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+52,"=STDEV(BS6:BS".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+53,"=STDEV(BT6:BT".($fila).")" );
 	$worksheet->write($fila+5,$columnaRes+54,"=STDEV(BU6:BU".($fila).")" );

//raiz tecnicos
 	$worksheet->write($fila+6,$columnaRes+44,"=SQRT(COUNT(BK6:BK".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+45,"=SQRT(COUNT(BL6:BL".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+46,"=SQRT(COUNT(BM6:BM".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+47,"=SQRT(COUNT(BN6:BN".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+48,"=SQRT(COUNT(BO6:BO".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+49,"=SQRT(COUNT(BP6:BP".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+50,"=SQRT(COUNT(BQ6:BQ".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+51,"=SQRT(COUNT(BR6:BR".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+52,"=SQRT(COUNT(BS6:BS".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+53,"=SQRT(COUNT(BT6:BT".($fila)."))" );
 	$worksheet->write($fila+6,$columnaRes+54,"=SQRT(COUNT(BU6:BU".($fila)."))" );

//maximo
 	$worksheet->write($fila+7,$columnaRes+44,"=BI".($fila+3)."+(BI".($fila+4)."*BI".($fila+6).")/BI".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+45,"=BJ".($fila+3)."+(BJ".($fila+4)."*BJ".($fila+6).")/BJ".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+46,"=BK".($fila+3)."+(BK".($fila+4)."*BK".($fila+6).")/BK".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+47,"=BL".($fila+3)."+(BL".($fila+4)."*BL".($fila+6).")/BL".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+48,"=BM".($fila+3)."+(BM".($fila+4)."*BM".($fila+6).")/BM".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+49,"=BN".($fila+3)."+(BN".($fila+4)."*BN".($fila+6).")/BN".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+50,"=BO".($fila+3)."+(BO".($fila+4)."*BO".($fila+6).")/BO".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+51,"=BP".($fila+3)."+(BP".($fila+4)."*BP".($fila+6).")/BP".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+52,"=BQ".($fila+3)."+(BQ".($fila+4)."*BQ".($fila+6).")/BQ".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+53,"=BR".($fila+3)."+(BR".($fila+4)."*BR".($fila+6).")/BR".($fila+7)."" );
 	$worksheet->write($fila+7,$columnaRes+54,"=BS".($fila+3)."+(BS".($fila+4)."*BS".($fila+6).")/BS".($fila+7)."" );
	
//minimo
 	$worksheet->write($fila+8,$columnaRes+44,"=BI".($fila+3)."-(BI".($fila+4)."*BI".($fila+6).")/BI".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+45,"=BJ".($fila+3)."-(BJ".($fila+4)."*BJ".($fila+6).")/BJ".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+46,"=BK".($fila+3)."-(BK".($fila+4)."*BK".($fila+6).")/BK".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+47,"=BL".($fila+3)."-(BL".($fila+4)."*BL".($fila+6).")/BL".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+48,"=BM".($fila+3)."-(BM".($fila+4)."*BM".($fila+6).")/BM".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+49,"=BN".($fila+3)."-(BN".($fila+4)."*BN".($fila+6).")/BN".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+50,"=BO".($fila+3)."-(BO".($fila+4)."*BO".($fila+6).")/BO".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+51,"=BP".($fila+3)."-(BP".($fila+4)."*BP".($fila+6).")/BP".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+52,"=BQ".($fila+3)."-(BQ".($fila+4)."*BQ".($fila+6).")/BQ".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+53,"=BR".($fila+3)."-(BR".($fila+4)."*BR".($fila+6).")/BR".($fila+7)."" );
 	$worksheet->write($fila+8,$columnaRes+54,"=BS".($fila+3)."-(BS".($fila+4)."*BS".($fila+6).")/BS".($fila+7)."" );	

//promedio falla
 	$worksheet->write($fila+9,$columnaRes+44,"=BI".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+45,"=BJ".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+46,"=BK".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+47,"=BL".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+48,"=BM".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+49,"=BN".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+50,"=BO".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+51,"=BP".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+52,"=BQ".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+53,"=BR".($fila+3) );
 	$worksheet->write($fila+9,$columnaRes+54,"=BS".($fila+3) );
	
//titulo si/no/ns
	//formato porcentaje
	$porcentajeFormat =& $workbook->addFormat();
	$porcentajeFormat->setNumFormat('0%');
	
 	$worksheet->write($fila+12,$columnaRes+5,"SI");	
 	$worksheet->write($fila+13,$columnaRes+5,"NO");	
 	$worksheet->write($fila+14,$columnaRes+5,"NS/NC");	
	  	
 	$worksheet->write($fila+12,$columnaRes+6,'=COUNTIF(W6:W'.($fila).',1)/(COUNTIF(W6:W'.($fila).',1)+COUNTIF(W6:W'.($fila).',2)+COUNTIF(W6:W'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+7,'=COUNTIF(X6:X'.($fila).',1)/(COUNTIF(X6:X'.($fila).',1)+COUNTIF(X6:X'.($fila).',2)+COUNTIF(X6:X'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+8,'=COUNTIF(Y6:Y'.($fila).',1)/(COUNTIF(Y6:Y'.($fila).',1)+COUNTIF(Y6:Y'.($fila).',2)+COUNTIF(Y6:Y'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+9,'=COUNTIF(Z6:Z'.($fila).',1)/(COUNTIF(Z6:Z'.($fila).',1)+COUNTIF(Z6:Z'.($fila).',2)+COUNTIF(Z6:Z'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+10,'=COUNTIF(AA6:AA'.($fila).',1)/(COUNTIF(AA6:AA'.($fila).',1)+COUNTIF(AA6:AA'.($fila).',2)+COUNTIF(AA6:AA'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+11,'=COUNTIF(AB6:AB'.($fila).',1)/(COUNTIF(AB6:AB'.($fila).',1)+COUNTIF(AB6:AB'.($fila).',2)+COUNTIF(AB6:AB'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+12,'=COUNTIF(AC6:AC'.($fila).',1)/(COUNTIF(AC6:AC'.($fila).',1)+COUNTIF(AC6:AC'.($fila).',2)+COUNTIF(AC6:AC'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+13,'=COUNTIF(AD6:AD'.($fila).',1)/(COUNTIF(AD6:AD'.($fila).',1)+COUNTIF(AD6:AD'.($fila).',2)+COUNTIF(AD6:AD'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+14,'=COUNTIF(AE6:AE'.($fila).',1)/(COUNTIF(AE6:AE'.($fila).',1)+COUNTIF(AE6:AE'.($fila).',2)+COUNTIF(AE6:AE'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+15,'=COUNTIF(AF6:AF'.($fila).',1)/(COUNTIF(AF6:AF'.($fila).',1)+COUNTIF(AF6:AF'.($fila).',2)+COUNTIF(AF6:AF'.($fila).',3))',$porcentajeFormat );	
		  	
 	$worksheet->write($fila+13,$columnaRes+6,'=COUNTIF(W6:W'.($fila).',2)/(COUNTIF(W6:W'.($fila).',1)+COUNTIF(W6:W'.($fila).',2)+COUNTIF(W6:W'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+7,'=COUNTIF(X6:X'.($fila).',2)/(COUNTIF(X6:X'.($fila).',1)+COUNTIF(X6:X'.($fila).',2)+COUNTIF(X6:X'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+8,'=COUNTIF(Y6:Y'.($fila).',2)/(COUNTIF(Y6:Y'.($fila).',1)+COUNTIF(Y6:Y'.($fila).',2)+COUNTIF(Y6:Y'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+9,'=COUNTIF(Z6:Z'.($fila).',2)/(COUNTIF(Z6:Z'.($fila).',1)+COUNTIF(Z6:Z'.($fila).',2)+COUNTIF(Z6:Z'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+10,'=COUNTIF(AA6:AA'.($fila).',2)/(COUNTIF(AA6:AA'.($fila).',1)+COUNTIF(AA6:AA'.($fila).',2)+COUNTIF(AA6:AA'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+11,'=COUNTIF(AB6:AB'.($fila).',2)/(COUNTIF(AB6:AB'.($fila).',1)+COUNTIF(AB6:AB'.($fila).',2)+COUNTIF(AB6:AB'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+12,'=COUNTIF(AC6:AC'.($fila).',2)/(COUNTIF(AC6:AC'.($fila).',1)+COUNTIF(AC6:AC'.($fila).',2)+COUNTIF(AC6:AC'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+13,'=COUNTIF(AD6:AD'.($fila).',2)/(COUNTIF(AD6:AD'.($fila).',1)+COUNTIF(AD6:AD'.($fila).',2)+COUNTIF(AD6:AD'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+14,'=COUNTIF(AE6:AE'.($fila).',2)/(COUNTIF(AE6:AE'.($fila).',1)+COUNTIF(AE6:AE'.($fila).',2)+COUNTIF(AE6:AE'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+15,'=COUNTIF(AF6:AF'.($fila).',2)/(COUNTIF(AF6:AF'.($fila).',1)+COUNTIF(AF6:AF'.($fila).',2)+COUNTIF(AF6:AF'.($fila).',3))',$porcentajeFormat );	
			  	
 	$worksheet->write($fila+14,$columnaRes+6,'=COUNTIF(W6:W'.($fila).',3)/(COUNTIF(W6:W'.($fila).',1)+COUNTIF(W6:W'.($fila).',2)+COUNTIF(W6:W'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+7,'=COUNTIF(X6:X'.($fila).',3)/(COUNTIF(X6:X'.($fila).',1)+COUNTIF(X6:X'.($fila).',2)+COUNTIF(X6:X'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+8,'=COUNTIF(Y6:Y'.($fila).',3)/(COUNTIF(Y6:Y'.($fila).',1)+COUNTIF(Y6:Y'.($fila).',2)+COUNTIF(Y6:Y'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+9,'=COUNTIF(Z6:Z'.($fila).',3)/(COUNTIF(Z6:Z'.($fila).',1)+COUNTIF(Z6:Z'.($fila).',2)+COUNTIF(Z6:Z'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+10,'=COUNTIF(AA6:AA'.($fila).',3)/(COUNTIF(AA6:AA'.($fila).',1)+COUNTIF(AA6:AA'.($fila).',2)+COUNTIF(AA6:AA'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+11,'=COUNTIF(AB6:AB'.($fila).',3)/(COUNTIF(AB6:AB'.($fila).',1)+COUNTIF(AB6:AB'.($fila).',2)+COUNTIF(AB6:AB'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+12,'=COUNTIF(AC6:AC'.($fila).',3)/(COUNTIF(AC6:AC'.($fila).',1)+COUNTIF(AC6:AC'.($fila).',2)+COUNTIF(AC6:AC'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+13,'=COUNTIF(AD6:AD'.($fila).',3)/(COUNTIF(AD6:AD'.($fila).',1)+COUNTIF(AD6:AD'.($fila).',2)+COUNTIF(AD6:AD'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+14,'=COUNTIF(AE6:AE'.($fila).',3)/(COUNTIF(AE6:AE'.($fila).',1)+COUNTIF(AE6:AE'.($fila).',2)+COUNTIF(AE6:AE'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+15,'=COUNTIF(AF6:AF'.($fila).',3)/(COUNTIF(AF6:AF'.($fila).',1)+COUNTIF(AF6:AF'.($fila).',2)+COUNTIF(AF6:AF'.($fila).',3))',$porcentajeFormat );	
	
 	$worksheet->write($fila+12,$columnaRes+21,'=COUNTIF(AL6:AL'.($fila).',1)/(COUNTIF(AL6:AL'.($fila).',1)+COUNTIF(AL6:AL'.($fila).',2)+COUNTIF(AL6:AL'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+21,'=COUNTIF(AL6:AL'.($fila).',2)/(COUNTIF(AL6:AL'.($fila).',1)+COUNTIF(AL6:AL'.($fila).',2)+COUNTIF(AL6:AL'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+21,'=COUNTIF(AL6:AL'.($fila).',3)/(COUNTIF(AL6:AL'.($fila).',1)+COUNTIF(AL6:AL'.($fila).',2)+COUNTIF(AL6:AL'.($fila).',3))',$porcentajeFormat );	

//titulo si/no/ns tecnicos

 	$worksheet->write($fila+12,$columnaRes+43,"SI");	
 	$worksheet->write($fila+13,$columnaRes+43,"NO");	
	$worksheet->write($fila+14,$columnaRes+43,"NS/NC");	

 	$worksheet->write($fila+12,$columnaRes+44,'=COUNTIF(BF6:BF'.($fila).',1)/(COUNTIF(BF6:BF'.($fila).',1)+COUNTIF(BF6:BF'.($fila).',2)+COUNTIF(BF6:BF'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+45,'=COUNTIF(BG6:BG'.($fila).',1)/(COUNTIF(BG6:BG'.($fila).',1)+COUNTIF(BG6:BG'.($fila).',2)+COUNTIF(BG6:BG'.($fila).',3))',$porcentajeFormat ,$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+46,'=COUNTIF(BH6:BH'.($fila).',1)/(COUNTIF(BH6:BH'.($fila).',1)+COUNTIF(BH6:BH'.($fila).',2)+COUNTIF(BH6:BH'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+47,'=COUNTIF(BI6:BI'.($fila).',1)/(COUNTIF(BI6:BI'.($fila).',1)+COUNTIF(BI6:BI'.($fila).',2)+COUNTIF(BI6:BI'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+12,$columnaRes+48,'=COUNTIF(BJ6:BJ'.($fila).',1)/(COUNTIF(BJ6:BJ'.($fila).',1)+COUNTIF(BJ6:BJ'.($fila).',2)+COUNTIF(BJ6:BJ'.($fila).',3))',$porcentajeFormat );	

 	$worksheet->write($fila+13,$columnaRes+44,'=COUNTIF(BF6:BF'.($fila).',2)/(COUNTIF(BF6:BF'.($fila).',1)+COUNTIF(BF6:BF'.($fila).',2)+COUNTIF(BF6:BF'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+45,'=COUNTIF(BG6:BG'.($fila).',2)/(COUNTIF(BG6:BG'.($fila).',1)+COUNTIF(BG6:BG'.($fila).',2)+COUNTIF(BG6:BG'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+46,'=COUNTIF(BH6:BH'.($fila).',2)/(COUNTIF(BH6:BH'.($fila).',1)+COUNTIF(BH6:BH'.($fila).',2)+COUNTIF(BH6:BH'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+47,'=COUNTIF(BI6:BI'.($fila).',2)/(COUNTIF(BI6:BI'.($fila).',1)+COUNTIF(BI6:BI'.($fila).',2)+COUNTIF(BI6:BI'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+13,$columnaRes+48,'=COUNTIF(BJ6:BJ'.($fila).',2)/(COUNTIF(BJ6:BJ'.($fila).',1)+COUNTIF(BJ6:BJ'.($fila).',2)+COUNTIF(BJ6:BJ'.($fila).',3))',$porcentajeFormat );	
  
 	$worksheet->write($fila+14,$columnaRes+44,'=COUNTIF(BF6:BF'.($fila).',3)/(COUNTIF(BF6:BF'.($fila).',1)+COUNTIF(BF6:BF'.($fila).',3)+COUNTIF(BF6:BF'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+45,'=COUNTIF(BG6:BG'.($fila).',3)/(COUNTIF(BG6:BG'.($fila).',1)+COUNTIF(BG6:BG'.($fila).',3)+COUNTIF(BG6:BG'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+46,'=COUNTIF(BH6:BH'.($fila).',3)/(COUNTIF(BH6:BH'.($fila).',1)+COUNTIF(BH6:BH'.($fila).',3)+COUNTIF(BH6:BH'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+47,'=COUNTIF(BI6:BI'.($fila).',3)/(COUNTIF(BI6:BI'.($fila).',1)+COUNTIF(BI6:BI'.($fila).',3)+COUNTIF(BI6:BI'.($fila).',3))',$porcentajeFormat );	
 	$worksheet->write($fila+14,$columnaRes+48,'=COUNTIF(BJ6:BJ'.($fila).',3)/(COUNTIF(BJ6:BJ'.($fila).',1)+COUNTIF(BJ6:BJ'.($fila).',3)+COUNTIF(BJ6:BJ'.($fila).',3))',$porcentajeFormat );	

	//$workbook->send('test.xls');
	$workbook->close();
}
	
?>
