<?php

	include_once("../../../modelo/clase_mysqli.inc.php");
	include_once("../../../modelo/clase_ubigeo.inc.php");
	include_once("../../../modelo/functions.php");
	include_once("../../includes/arreglos.php");
	include_once("../../../modelo/funciones_excel_pear.php");

	//$con= new DB_mysqli("replica");
			
	$pais=strtolower($_REQUEST["cmbpais"]);
		
	$con= new DB_mysqli("",$pais);		 
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}	
		
	session_start();
 
	$var_idcuenta= $_POST["cmbcuenta"];
	$var_anio= $_POST["cmbanio"];
	$var_mes= $_POST["cmbmes"];
	
	//Fechas a indicar
	$var_fecha_day= $var_anio."-".$var_mes."-01";
	$v_fecha_ini= date("Y-m", strtotime($var_fecha_day)) . "-01";
	$v_fecha_fin= date("Y-m", strtotime($var_fecha_day)) . "-" . ultimoDia(date("m", strtotime($var_fecha_day)),date("Y", strtotime($var_fecha_day)));
	/**/
	
	$Sql_transfer_veri= "SELECT COUNT(*) FROM $con->global.".$pais."_guardia_indices WHERE IDCUENTAGLOBAL='".$var_idcuenta."' AND FECHA_APERTURA_EXP BETWEEN '".$v_fecha_ini."' AND '".$v_fecha_fin."'";

	$result_trans=$con->query($Sql_transfer_veri);
	$row_trans = $result_trans->fetch_array();
	
	if($row_trans[0] == 0){	 
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

	$Sql_cuenta_datos= "SELECT NOMBRE FROM $con->global.catalogo_cuenta WHERE IDCUENTAGLOBAL='".$var_idcuenta."'";

	$result_cuenta=$con->query($Sql_cuenta_datos);
	$row_trans = $result_cuenta->fetch_array();
	$var_nombre_cuenta= $row_trans[0];

//libreria pear excel 	
	$pear = "./../../../../librerias/pearExcel";
	ini_set("include_path",ini_get("include_path").":$pear");
	require_once("../../../../librerias/pearExcel/Spreadsheet/Excel/Writer.php");
	
	$workbook = & new Spreadsheet_Excel_Writer();
	$workbook->setVersion(8);
		
	/*Definir los colores para el libro*/
	$workbook->setCustomColor(12, 51, 51, 153);
	$workbook->setCustomColor(13, 153, 204, 255);
	$workbook->setCustomColor(14, 204, 255, 255);
	$workbook->setCustomColor(15, 192, 0, 0);
	$workbook->setCustomColor(16, 191, 191, 191);
	
	/*LIBRERIA STYLES EXCEL PEAR*/
	include_once("../../../modelo/func_styles_excel_pear.php");
	/**/
		
	/*** FICHA - INDICES DIARIOS ***/
	$hoja_indices =& $workbook->addWorksheet('INDICES_DIARIOS_ASISTENCIAS');
	
	//Establecer ancho de columnas y filas
	$hoja_indices->setColumn(0,0,0.75);
	$hoja_indices->setColumn(0,1,9);
	$hoja_indices->setColumn(0,2,10.71);
	$hoja_indices->setColumn(0,3,8);
	$hoja_indices->setColumn(0,4,8);
	$hoja_indices->setColumn(0,5,8);
	$hoja_indices->setColumn(0,6,9);
	$hoja_indices->setColumn(0,7,9);
	$hoja_indices->setColumn(0,8,9);
	$hoja_indices->setColumn(0,9,6.14);
	$hoja_indices->setColumn(0,10,6.14);
	$hoja_indices->setColumn(0,11,6.14);
	$hoja_indices->setColumn(0,12,6.14);
	$hoja_indices->setColumn(0,13,6.14);
	$hoja_indices->setColumn(0,14,9.57);
	$hoja_indices->setColumn(0,15,9.57);
	$hoja_indices->setColumn(0,16,10.57);
	$hoja_indices->setColumn(0,17,9.57);
	$hoja_indices->setColumn(0,18,8.14);
	$hoja_indices->setColumn(0,19,6.29);
	$hoja_indices->setColumn(0,20,10.57);
	$hoja_indices->setColumn(0,21,1.43);
	$hoja_indices->setColumn(0,22,1.29);
	
	
	$hoja_indices->setRow(0,12.75);
	$hoja_indices->setRow(1,12.75);
	$hoja_indices->setRow(2,16.5);
	$hoja_indices->setRow(3,16.5);
	$hoja_indices->setRow(4,13.5);
	$hoja_indices->setRow(5,15.75);
	$hoja_indices->setRow(6,19.5);
	$hoja_indices->setRow(7,30.75);
	$hoja_indices->setRow(8,15.75);
	
	$hoja_indices->freezePanes(array(9, 3));
	
	$hoja_indices->protect();
	
	$hoja_indices->insertBitmap(0,0,'../../../../imagenes/logos/img_borde_cabecera.bmp',0,0,0.237,1) ;
	$hoja_indices->insertBitmap(4,1,'../../../../imagenes/logos/img_logo_concentra.bmp',5,9,0.2,1) ;
	/**/
	
	//Convertir la fecha de cadena en un valor numerico
	$var_date= func_fecha_format_numero(date("Y-m-d",strtotime($var_fecha_day)));
	
	//CABECERA FICHA INDICES
	$hoja_indices->write(2, 1, 'MES', $style_mes);
	$hoja_indices->write(3, 1, $var_date, $style_mes_dato);
	$hoja_indices->setMerge(2, 5, 2, 7);
	$hoja_indices->write(2, 5, 'CLIENTE', $style_titulo_1);
	$hoja_indices->write(2, 6, '', $style_titulo_1);
	$hoja_indices->write(2, 7, '', $style_titulo_1);
	$hoja_indices->setMerge(3, 5, 3, 7);
	$hoja_indices->write(3, 5, $var_nombre_cuenta, $style_titulo_2);
	$hoja_indices->write(3, 6, '', $style_titulo_2);
	$hoja_indices->write(3, 7, '', $style_titulo_2);
	$hoja_indices->setMerge(5, 3, 5, 13);
	$hoja_indices->write(5, 3, 'COMPORTAMIENTO DE LAS ASISTENCIAS', $style_titulo_prin1);
	$hoja_indices->write(5, 4, '', $style_titulo_prin1);
	$hoja_indices->write(5, 5, '', $style_titulo_prin1);
	$hoja_indices->write(5, 6, '', $style_titulo_prin1);
	$hoja_indices->write(5, 7, '', $style_titulo_prin1);
	$hoja_indices->write(5, 8, '', $style_titulo_prin1);
	$hoja_indices->write(5, 9, '', $style_titulo_prin1);
	$hoja_indices->write(5, 10, '', $style_titulo_prin1);
	$hoja_indices->write(5, 11, '', $style_titulo_prin1);
	$hoja_indices->write(5, 12, '', $style_titulo_prin1);
	$hoja_indices->write(5, 13, '', $style_titulo_prin1);
	$hoja_indices->setMerge(5, 14, 5, 20);
	$hoja_indices->write(5, 14, 'COSTO PARA AMERICAN ASSIST', $style_titulo_prin1);
	$hoja_indices->write(5, 15, '', $style_titulo_prin1);
	$hoja_indices->write(5, 16, '', $style_titulo_prin1);
	$hoja_indices->write(5, 17, '', $style_titulo_prin1);
	$hoja_indices->write(5, 18, '', $style_titulo_prin1);
	$hoja_indices->write(5, 19, '', $style_titulo_prin1);
	$hoja_indices->write(5, 20, '', $style_titulo_prin1);
	$hoja_indices->setMerge(6, 3, 7, 3);
	$hoja_indices->write(6, 3, 'SOLCTDAS TOTALES', $style_titulo_nivel2_a);
	$hoja_indices->write(7, 3, '', $style_titulo_nivel2_a);
	$hoja_indices->setMerge(6, 4, 7, 4);
	$hoja_indices->write(6, 4, 'CONCLDS TOTALES', $style_titulo_nivel2_b);
	$hoja_indices->write(7, 4, '', $style_titulo_nivel2_b);
	$hoja_indices->setMerge(6, 5, 7, 5);
	$hoja_indices->write(6, 5, 'CONCLDS CONEXIÓN', $style_titulo_nivel2_b);
	$hoja_indices->write(7, 5, '', $style_titulo_nivel2_b);
	$hoja_indices->setMerge(6, 6, 6, 8);
	$hoja_indices->write(6, 6, 'CONCLUIDOS - EN COBERTURA', $style_titulo_nivel2_c);
	$hoja_indices->write(6, 7, '', $style_titulo_nivel2_c);
	$hoja_indices->write(6, 8, '', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 6, 'LOCAL', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 7, 'FORANEO', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 8, 'TOTAL', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 9, 7, 9);
	$hoja_indices->write(6, 9, 'CM', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 9, '', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 10, 7, 10);
	$hoja_indices->write(6, 10, 'CP', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 10, '', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 11, 7, 11);
	$hoja_indices->write(6, 11, 'T', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 11, '', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 12, 7, 12);
	$hoja_indices->write(6, 12, '% CM', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 12, '', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 13, 7, 13);
	$hoja_indices->write(6, 13, '% CP', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 13, '', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 14, 6, 16);
	$hoja_indices->write(6, 14, 'COSTO TOTAL EN CONCLUIDOS', $style_titulo_nivel2_d);
	$hoja_indices->write(6, 15, '', $style_titulo_nivel2_d);
	$hoja_indices->write(6, 16, '', $style_titulo_nivel2_d);
	$hoja_indices->write(7, 14, 'LOCAL', $style_titulo_nivel2_d);
	$hoja_indices->write(7, 15, 'FORANEO', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 16, 'TOTAL', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 17, 7, 17);
	$hoja_indices->write(6, 17, 'COSTO MEDIO TOTAL', $style_titulo_nivel2_e);
	$hoja_indices->write(7, 17, '', $style_titulo_nivel2_e);
	$hoja_indices->setMerge(6, 18, 7, 18);
	$hoja_indices->write(6, 18, 'CP', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 18, '', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 19, 7, 19);
	$hoja_indices->write(6, 19, '% CP', $style_titulo_nivel2_c);
	$hoja_indices->write(7, 19, '', $style_titulo_nivel2_c);
	$hoja_indices->setMerge(6, 20, 7, 20);
	$hoja_indices->write(6, 20, 'TOTAL', $style_titulo_nivel2_f);
	$hoja_indices->write(7, 20, '', $style_titulo_nivel2_f);
	$hoja_indices->write(8, 1, 'Día', $style_nivel3_titulo1);
	$hoja_indices->write(8, 2, 'Sub Total:', $style_nivel3_titulo1);
	
	
	//Mostrar la data
	unset($array_fecha);
	$array_fecha= func_lista_fechas($v_fecha_ini, $v_fecha_fin);
	
	$num_reg_det= 9;
	$num_dias_mes= 0;
	$var_dia_query= "";
	foreach ($array_fecha as $reg_fecha=>$valor) {
		$num_dias_mes++;
		$var_dato_dia= $array_fecha[$reg_fecha]['str_dia'];
		$var_dato_fecha= $array_fecha[$reg_fecha]['fecha'];
		$var_dato_day= $array_fecha[$reg_fecha]['fecha_normal'];
		
		$hoja_indices->setRow($num_reg_det, 15);
		$hoja_indices->write($num_reg_det, 1, $var_dato_dia, $style_det_col_1);
		$hoja_indices->write($num_reg_det, 2, $var_dato_fecha, $style_det_col_2);
		
		//Parte del query por dia
		$var_dia_query.= "COUNT(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' THEN FECHA_APERTURA_EXP ELSE NULL END) AS SOLICITADOS_TOTALES_$reg_fecha,
						  COUNT(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='C' THEN FECHA_APERTURA_EXP ELSE NULL END) AS CONCLUIDOS_TOTALES_$reg_fecha,
						  COUNT(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='C' AND CONDICIONSERVICIO='CONEXION' THEN FECHA_APERTURA_EXP ELSE NULL END) AS CONCLUIDOS_CONEXION_$reg_fecha,
						  COUNT(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='C' AND CONDICIONSERVICIO='EN COBERTURA' AND LOCALFORANEO='LOCAL' THEN FECHA_APERTURA_EXP ELSE NULL END) AS CONCLUIDOS_LOCAL_$reg_fecha,
						  COUNT(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='C' AND CONDICIONSERVICIO='EN COBERTURA' AND LOCALFORANEO='FORANEO' THEN FECHA_APERTURA_EXP ELSE NULL END) AS CONCLUIDOS_FORANEO_$reg_fecha,
						  COUNT(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='CM' THEN FECHA_APERTURA_EXP ELSE NULL END) AS STATUS_CM_$reg_fecha,
						  COUNT(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='CP' THEN FECHA_APERTURA_EXP ELSE NULL END) AS STATUS_CP_$reg_fecha,
						  COUNT(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='T' THEN FECHA_APERTURA_EXP ELSE NULL END) AS STATUS_T_$reg_fecha,
						  IFNULL(SUM(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='C' AND LOCALFORANEO='LOCAL' THEN MONTOAA ELSE NULL END), 0) AS COSTO_LOCAL_$reg_fecha,
						  IFNULL(SUM(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND LOCALFORANEO='LOCAL' THEN MONTOAA ELSE NULL END), 0) AS COSTO_LOCAL_GENERAL_$reg_fecha,
						  IFNULL(SUM(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='C' AND LOCALFORANEO='FORANEO' THEN MONTOAA ELSE NULL END), 0) AS COSTO_FORANEO_$reg_fecha,
						  IFNULL(SUM(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND LOCALFORANEO='FORANEO' THEN MONTOAA ELSE NULL END), 0) AS COSTO_FORANEO_GENERAL_$reg_fecha,
						  IFNULL(SUM(CASE WHEN FECHA_APERTURA_EXP='$var_dato_day' AND STATUSASISTENCIA='CP' THEN MONTOAA ELSE NULL END), 0) AS COSTO_CP_$reg_fecha,";
		
		$num_reg_det++;
	}
	
	//Realizar la Consulta General a Indices Diarios
	$sql_detalle_indices= "SELECT ".$var_dia_query.
						  "COUNT(FECHA_APERTURA_EXP) AS TOTAL
						   FROM $con->global.".$pais."_guardia_indices
						   WHERE IDCUENTAGLOBAL='".$var_idcuenta."' AND 
						   FECHA_APERTURA_EXP BETWEEN '".$v_fecha_ini."' AND '".$v_fecha_fin."'";
	
	$rs_indices= $con->query($sql_detalle_indices);
	$row_indices = $rs_indices->fetch_array();
		
	//Dibujar los datos de Indices Diarios
	$csup_totales_solic_total= 0;
	$csup_totales_conclu_total= 0;
	$csup_totales_conclu_conexion= 0;
	$csup_totales_conclu_local= 0;
	$csup_totales_conclu_foraneo= 0;
	$csup_totales_cobertura_total= 0;
	$csup_totales_status_cm= 0;
	$csup_totales_status_cp= 0;
	$csup_totales_status_t= 0;
	$csup_totales_porcen_cm= 0;
	$csup_totales_porcen_cp= 0;
	$csup_totales_costo_local= 0;
	$csup_totales_costo_local_general= 0;
	$csup_totales_costo_foraneo= 0;
	$csup_totales_costo_foraneo_general= 0;
	$csup_totales_costo_conclu_total= 0;
	$csup_totales_costo_medio_total= 0;
	$csup_totales_costo_cp= 0;
	$csup_totales_costo_porcen_cp= 0;
	$csup_totales_costo_total= 0;
	
	$num_reg_det= 9;
	$num_solic_cero= 0;
	foreach ($array_fecha as $reg_fecha=>$valor) {
		$v_lbl_solic_total= "SOLICITADOS_TOTALES_".$reg_fecha;
		$v_lbl_conclu_total= "CONCLUIDOS_TOTALES_".$reg_fecha;
		$v_lbl_conclu_conexion= "CONCLUIDOS_CONEXION_".$reg_fecha;
		$v_lbl_conclu_local= "CONCLUIDOS_LOCAL_".$reg_fecha;
		$v_lbl_conclu_foraneo= "CONCLUIDOS_FORANEO_".$reg_fecha;
		$v_lbl_status_cm= "STATUS_CM_".$reg_fecha;
		$v_lbl_status_cp= "STATUS_CP_".$reg_fecha;
		$v_lbl_status_t= "STATUS_T_".$reg_fecha;
		$v_lbl_costo_local= "COSTO_LOCAL_".$reg_fecha;
		$v_lbl_costo_local_general= "COSTO_LOCAL_GENERAL_".$reg_fecha;
		$v_lbl_costo_foraneo= "COSTO_FORANEO_".$reg_fecha;
		$v_lbl_costo_foraneo_general= "COSTO_FORANEO_GENERAL_".$reg_fecha;
		$v_lbl_costo_cp= "COSTO_CP_".$reg_fecha;
		
		$var_solic_total= $row_indices[$v_lbl_solic_total];
		$var_conclu_total= $row_indices[$v_lbl_conclu_total];
		$var_conclu_conexion= $row_indices[$v_lbl_conclu_conexion];
		$var_conclu_local= $row_indices[$v_lbl_conclu_local];
		$var_conclu_foraneo= $row_indices[$v_lbl_conclu_foraneo];
		$var_cobertura_total= $var_conclu_local + $var_conclu_foraneo;
		$var_status_cm= $row_indices[$v_lbl_status_cm];
		$var_status_cp= $row_indices[$v_lbl_status_cp];
		$var_status_t= $row_indices[$v_lbl_status_t];
		$var_porcen_cm= $var_status_cm / $var_solic_total;
		$var_porcen_cp= $var_status_cp / $var_solic_total;
		$var_costo_local= $row_indices[$v_lbl_costo_local];
		$var_costo_local_general= $row_indices[$v_lbl_costo_local_general];
		$var_costo_foraneo= $row_indices[$v_lbl_costo_foraneo];
		$var_costo_foraneo_general= $row_indices[$v_lbl_costo_foraneo_general];
		$var_costo_conclu_total= $var_costo_local + $var_costo_foraneo;
		$var_costo_medio_total= $var_costo_conclu_total / $var_cobertura_total;
		$var_costo_cp= $row_indices[$v_lbl_costo_cp];
		$var_costo_porcen_cp= $var_costo_cp / $var_costo_conclu_total;
		$var_costo_total= $var_costo_cp + $var_costo_conclu_total;
			
		if ($var_solic_total == 0) {
			$num_solic_cero++;
			
			$var_solic_total= "";
			$var_conclu_total= "";
			$var_conclu_conexion= "";
			$var_conclu_local= "";
			$var_conclu_foraneo= "";
			$var_cobertura_total= "";
			$var_status_cm= "";
			$var_status_cp= "";
			$var_status_t= "";
			$var_porcen_cm= "";
			$var_porcen_cp= "";
			$var_costo_local= "";
			$var_costo_local_general= "";
			$var_costo_foraneo= "";
			$var_costo_foraneo_general= "";
			$var_costo_conclu_total= "";
			$var_costo_medio_total= "";
			$var_costo_cp= "";
			$var_costo_porcen_cp= "";
			$var_costo_total= "";
		}
		$hoja_indices->write($num_reg_det, 3, $var_solic_total, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 4, $var_conclu_total, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 5, $var_conclu_conexion, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 6, $var_conclu_local, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 7, $var_conclu_foraneo, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 8, $var_cobertura_total, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 9, $var_status_cm, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 10, $var_status_cp, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 11, $var_status_t, $style_det_col_3_num);
		$hoja_indices->write($num_reg_det, 12, $var_porcen_cm, $style_det_col_porcen);
		$hoja_indices->write($num_reg_det, 13, $var_porcen_cp, $style_det_col_porcen_div);
		$hoja_indices->write($num_reg_det, 14, $var_costo_local, $style_det_col_money);
		$hoja_indices->write($num_reg_det, 15, $var_costo_foraneo, $style_det_col_money);
		$hoja_indices->write($num_reg_det, 16, $var_costo_conclu_total, $style_det_col_money);
		$hoja_indices->write($num_reg_det, 17, $var_costo_medio_total, $style_det_col_money_medio);
		$hoja_indices->write($num_reg_det, 18, $var_costo_cp, $style_det_col_money);
		$hoja_indices->write($num_reg_det, 19, $var_costo_porcen_cp, $style_det_col_porcen);
		$hoja_indices->write($num_reg_det, 20, $var_costo_total, $style_det_col_money_total);
		
		//Acumula los valores para la cabecera superior de totales
		$csup_totales_solic_total+= ($var_solic_total)=="" ? 0 : $var_solic_total;
		$csup_totales_conclu_total+= ($var_conclu_total)=="" ? 0 : $var_conclu_total;
		$csup_totales_conclu_conexion+= ($var_conclu_conexion)=="" ? 0 : $var_conclu_conexion;
		$csup_totales_conclu_local+= ($var_conclu_local)=="" ? 0 : $var_conclu_local;
		$csup_totales_conclu_foraneo+= ($var_conclu_foraneo)=="" ? 0 : $var_conclu_foraneo;
		$csup_totales_status_cm+= ($var_status_cm)=="" ? 0 : $var_status_cm;
		$csup_totales_status_cp+= ($var_status_cp)=="" ? 0 : $var_status_cp;
		$csup_totales_status_t+= ($var_status_t)=="" ? 0 : $var_status_t;
		$csup_totales_costo_local+= ($var_costo_local)=="" ? 0 : $var_costo_local;
		$csup_totales_costo_local_general+= ($var_costo_local_general)=="" ? 0 : $var_costo_local_general;
		$csup_totales_costo_foraneo+= ($var_costo_foraneo)=="" ? 0 : $var_costo_foraneo;
		$csup_totales_costo_foraneo_general+= ($var_costo_foraneo_general)=="" ? 0 : $var_costo_foraneo_general;
		$csup_totales_costo_cp+= ($var_costo_cp)=="" ? 0 : $var_costo_cp;
		
		$num_reg_det++;
	}
	
	//Realizar los calculos para los campos subtotales
	$csup_totales_cobertura_total= $csup_totales_conclu_local + $csup_totales_conclu_foraneo;
	$csup_totales_porcen_cm= $csup_totales_status_cm / $csup_totales_solic_total;
	$csup_totales_porcen_cp= $csup_totales_status_cp / $csup_totales_solic_total;
	$csup_totales_costo_conclu_total= $csup_totales_costo_local + $csup_totales_costo_foraneo;
	$csup_totales_costo_medio_total= $csup_totales_costo_conclu_total / $csup_totales_cobertura_total;
	$csup_totales_costo_porcen_cp= $csup_totales_costo_cp / $csup_totales_costo_conclu_total;
	$csup_totales_costo_total= $csup_totales_costo_conclu_total + $csup_totales_costo_cp;
	
	//Dibuja Cabecera Superior de Totales
	$hoja_indices->write(8, 3, $csup_totales_solic_total, $style_nivel3_total_num);
	$hoja_indices->write(8, 4, $csup_totales_conclu_total, $style_nivel3_total_num);
	$hoja_indices->write(8, 5, $csup_totales_conclu_conexion, $style_nivel3_total_num);
	$hoja_indices->write(8, 6, $csup_totales_conclu_local, $style_nivel3_total_num);
	$hoja_indices->write(8, 7, $csup_totales_conclu_foraneo, $style_nivel3_total_num);
	$hoja_indices->write(8, 8, $csup_totales_cobertura_total, $style_nivel3_total_num);
	$hoja_indices->write(8, 9, $csup_totales_status_cm, $style_nivel3_total_num);
	$hoja_indices->write(8, 10, $csup_totales_status_cp, $style_nivel3_total_num);
	$hoja_indices->write(8, 11, $csup_totales_status_t, $style_nivel3_total_num);
	$hoja_indices->write(8, 12, $csup_totales_porcen_cm, $style_nivel3_total_porcen);
	$hoja_indices->write(8, 13, $csup_totales_porcen_cp, $style_nivel3_total_porcen);
	$hoja_indices->write(8, 14, $csup_totales_costo_local, $style_nivel3_total_money);
	$hoja_indices->write(8, 15, $csup_totales_costo_foraneo, $style_nivel3_total_money);
	$hoja_indices->write(8, 16, $csup_totales_costo_conclu_total, $style_nivel3_total_money);
	$hoja_indices->write(8, 17, $csup_totales_costo_medio_total, $style_nivel3_total_money_medio);
	$hoja_indices->write(8, 18, $csup_totales_costo_cp, $style_nivel3_total_money);
	$hoja_indices->write(8, 19, $csup_totales_costo_porcen_cp, $style_nivel3_total_porcen);
	$hoja_indices->write(8, 20, $csup_totales_costo_total, $style_nivel3_total_money_total);
	
	//Dibujar la seccion de Totales en la parte Inferior
	$cinf_totales_solic_total= 0;
	$cinf_totales_conclu_total= 0;
	$cinf_totales_conclu_conexion= 0;
	$cinf_totales_conclu_local= 0;
	$cinf_totales_conclu_foraneo= 0;
	$cinf_totales_cobertura_total= 0;
	$cinf_totales_status_cm= 0;
	$cinf_totales_status_cp= 0;
	$cinf_totales_status_t= 0;
	$cinf_totales_porcen_cm= 0;
	$cinf_totales_porcen_cp= 0;
	$cinf_totales_costo_local= 0;
	$cinf_totales_costo_local_general= 0;
	$cinf_totales_costo_foraneo= 0;
	$cinf_totales_costo_foraneo_general= 0;
	$cinf_totales_costo_conclu_total= 0;
	$cinf_totales_costo_medio_total= 0;
	$cinf_totales_costo_cp= 0;
	$cinf_totales_costo_porcen_cp= 0;
	$cinf_totales_costo_total= 0;
	
	$cinf_totales_solic_total= $csup_totales_solic_total;
	$cinf_totales_conclu_total= $csup_totales_conclu_total;
	$cinf_totales_conclu_conexion= $csup_totales_conclu_conexion;
	$cinf_totales_conclu_local= $csup_totales_conclu_local;
	$cinf_totales_conclu_foraneo= $csup_totales_conclu_foraneo;
	$cinf_totales_cobertura_total= $cinf_totales_conclu_local + $cinf_totales_conclu_foraneo;
	$cinf_totales_status_cm= $csup_totales_status_cm;
	$cinf_totales_status_cp= $csup_totales_status_cp;
	$cinf_totales_status_t= $csup_totales_status_t;
	$cinf_totales_porcen_cm= $csup_totales_porcen_cm;
	$cinf_totales_porcen_cp= $csup_totales_porcen_cp;
	$cinf_totales_costo_local_general= $csup_totales_costo_local_general;
	$cinf_totales_costo_foraneo_general= $csup_totales_costo_foraneo_general;
	$cinf_totales_costo_conclu_total= $csup_totales_costo_conclu_total;
	$cinf_totales_costo_medio_total= $csup_totales_costo_medio_total;
	$cinf_totales_costo_cp= $csup_totales_costo_cp;
	$cinf_totales_costo_porcen_cp= $cinf_totales_costo_cp / $cinf_totales_costo_conclu_total;
	$cinf_totales_costo_total= $cinf_totales_costo_cp + $cinf_totales_costo_conclu_total;
	
	if (!isset($array_fecha)) { $num_reg_det++; }
	$hoja_indices->setRow($num_reg_det, 5);
	
	$hoja_indices->write($num_reg_det, 1, '', $style_nivel_separacion_left);
	$hoja_indices->write($num_reg_det, 2, '', $style_nivel_separacion_right);
	$hoja_indices->write($num_reg_det, 13, '', $style_nivel_separacion_right);
	$hoja_indices->write($num_reg_det, 20, '', $style_nivel_separacion_right);
	
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 15.75);
	
	$hoja_indices->setMerge($num_reg_det, 1, $num_reg_det, 2);
	$hoja_indices->write($num_reg_det, 1, 'Total:', $style_nivel_totales_col_1);
	$hoja_indices->write($num_reg_det, 2, '', $style_nivel_totales_col_1);
	$hoja_indices->write($num_reg_det, 3, $cinf_totales_solic_total, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 4, $cinf_totales_conclu_total, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 5, $cinf_totales_conclu_conexion, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 6, $cinf_totales_conclu_local, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 7, $cinf_totales_conclu_foraneo, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 8, $cinf_totales_cobertura_total, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 9, $cinf_totales_status_cm, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 10, $cinf_totales_status_cp, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 11, $cinf_totales_status_t, $style_nivel_totales_col_num);
	$hoja_indices->write($num_reg_det, 12, $cinf_totales_porcen_cm, $style_nivel_totales_col_porcen);
	$hoja_indices->write($num_reg_det, 13, $cinf_totales_porcen_cp, $style_nivel_totales_col_porcen);
	$hoja_indices->write($num_reg_det, 14, $cinf_totales_costo_local_general, $style_nivel_totales_col_money);
	$hoja_indices->write($num_reg_det, 15, $cinf_totales_costo_foraneo_general, $style_nivel_totales_col_money);
	$hoja_indices->write($num_reg_det, 16, $cinf_totales_costo_conclu_total, $style_nivel_totales_col_money);
	$hoja_indices->write($num_reg_det, 17, $cinf_totales_costo_medio_total, $style_nivel_totales_col_money_medio);
	$hoja_indices->write($num_reg_det, 18, $cinf_totales_costo_cp, $style_nivel_totales_col_money);
	$hoja_indices->write($num_reg_det, 19, $cinf_totales_costo_porcen_cp, $style_nivel_totales_col_porcen_dec);
	$hoja_indices->write($num_reg_det, 20, $cinf_totales_costo_total, $style_nivel_totales_col_money);
	
	//Dibujar los Titulos para los porcentajes
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 15);
	
	$hoja_indices->setMerge($num_reg_det, 1, $num_reg_det, 2);
	$hoja_indices->write($num_reg_det, 1, '% Participación al Total:', $style_nivel_porcen_titulo_1);
	$hoja_indices->write($num_reg_det, 2, '', $style_nivel_porcen_titulo_1);
	
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 15);
	
	$hoja_indices->setMerge($num_reg_det, 1, $num_reg_det, 2);
	$hoja_indices->write($num_reg_det, 1, 'TODOS', $style_nivel_porcen_titulo_2);
	$hoja_indices->write($num_reg_det, 2, '', $style_nivel_porcen_titulo_2);
	
	//Dibujar Porcentajes de Participacion
	$totales_porcen_solic_total= $csup_totales_solic_total / $cinf_totales_solic_total;
	$totales_porcen_conclu_total= $csup_totales_conclu_total / $cinf_totales_conclu_total;
	$totales_porcen_conclu_conexion= $csup_totales_conclu_conexion / $cinf_totales_conclu_conexion;
	$totales_porcen_conclu_local= $csup_totales_conclu_local / $cinf_totales_conclu_local;
	$totales_porcen_conclu_foraneo= $csup_totales_conclu_foraneo / $cinf_totales_conclu_foraneo;
	$totales_porcen_cobertura_total= $csup_totales_cobertura_total / $cinf_totales_cobertura_total;
	$totales_porcen_costo_local= $csup_totales_costo_local / $cinf_totales_costo_local_general;
	$totales_porcen_costo_foraneo= $csup_totales_costo_foraneo / $cinf_totales_costo_foraneo_general;
	$totales_porcen_costo_conclu_total= $csup_totales_costo_conclu_total / $cinf_totales_costo_conclu_total;
	$totales_porcen_costo_medio_total= $csup_totales_costo_medio_total / $cinf_totales_costo_medio_total;
	$totales_porcen_costo_cp= $csup_totales_costo_cp / $cinf_totales_costo_cp;
	$totales_porcen_costo_total= $csup_totales_costo_total / $cinf_totales_costo_total;
	
	$num_reg_det--;
	$hoja_indices->setMerge($num_reg_det, 3, ($num_reg_det + 1), 3);
	$hoja_indices->write($num_reg_det, 3, $totales_porcen_solic_total, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 3, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 4, ($num_reg_det + 1), 4);
	$hoja_indices->write($num_reg_det, 4, $totales_porcen_conclu_total, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 4, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 5, ($num_reg_det + 1), 5);
	$hoja_indices->write($num_reg_det, 5, $totales_porcen_conclu_conexion, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 5, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 6, ($num_reg_det + 1), 6);
	$hoja_indices->write($num_reg_det, 6, $totales_porcen_conclu_local, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 6, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 7, ($num_reg_det + 1), 7);
	$hoja_indices->write($num_reg_det, 7, $totales_porcen_conclu_foraneo, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 7, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 8, ($num_reg_det + 1), 8);
	$hoja_indices->write($num_reg_det, 8, $totales_porcen_cobertura_total, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 8, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 14, ($num_reg_det + 1), 14);
	$hoja_indices->write($num_reg_det, 14, $totales_porcen_costo_local, $style_nivel_porcen_dec_left);
	$hoja_indices->write(($num_reg_det + 1), 14, '', $style_nivel_porcen_dec_left);
	$hoja_indices->setMerge($num_reg_det, 15, ($num_reg_det + 1), 15);
	$hoja_indices->write($num_reg_det, 15, $totales_porcen_costo_foraneo, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 15, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 16, ($num_reg_det + 1), 16);
	$hoja_indices->write($num_reg_det, 16, $totales_porcen_costo_conclu_total, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 16, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 17, ($num_reg_det + 1), 17);
	$hoja_indices->write($num_reg_det, 17, $totales_porcen_costo_medio_total, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 17, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 18, ($num_reg_det + 1), 18);
	$hoja_indices->write($num_reg_det, 18, $totales_porcen_costo_cp, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 18, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 19, ($num_reg_det + 1), 19);
	$hoja_indices->write($num_reg_det, 19, '', $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 19, '', $style_nivel_porcen_dec);
	$hoja_indices->setMerge($num_reg_det, 20, ($num_reg_det + 1), 20);
	$hoja_indices->write($num_reg_det, 20, $totales_porcen_costo_total, $style_nivel_porcen_dec);
	$hoja_indices->write(($num_reg_det + 1), 20, '', $style_nivel_porcen_dec);
	
	//Proyecciones de Cierre de Mes
	$proyec_solic_mes= ceil(($cinf_totales_solic_total / ($num_dias_mes - $num_solic_cero)) * $num_dias_mes);
	$proyec_conclu_mes= ceil(($cinf_totales_cobertura_total / ($num_dias_mes - $num_solic_cero)) * $num_dias_mes);
	$proyec_cp_mes= ceil(($cinf_totales_status_cp / ($num_dias_mes - $num_solic_cero)) * $num_dias_mes);
	$proyec_cm_mes= ceil(($cinf_totales_status_cm / ($num_dias_mes - $num_solic_cero)) * $num_dias_mes);
	$proyec_t_mes= ceil(($cinf_totales_status_t / ($num_dias_mes - $num_solic_cero)) * $num_dias_mes);
	
	$num_reg_det= $num_reg_det + 2;
	
	$hoja_indices->setRow($num_reg_det, 15);
	$num_reg_det++;
	$num_reg_proyec= $num_reg_det;
	$hoja_indices->setRow($num_reg_det, 18.75);
	
	$hoja_indices->setMerge($num_reg_det, 3, $num_reg_det, 7);
	$hoja_indices->write($num_reg_det, 3, 'PROYECCIONES DE CIERRE DE MES', $style_proyec_cab_1);
	$hoja_indices->write($num_reg_det, 4, '', $style_proyec_cab_1);
	$hoja_indices->write($num_reg_det, 5, '', $style_proyec_cab_1);
	$hoja_indices->write($num_reg_det, 6, '', $style_proyec_cab_1);
	$hoja_indices->write($num_reg_det, 7, '', $style_proyec_cab_1);
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 18.75);
	
	$hoja_indices->setMerge($num_reg_det, 3, $num_reg_det, 6);
	$hoja_indices->write($num_reg_det, 3, 'Proyección Solicitadas del Mes:', $style_proyec_titulo_1);
	$hoja_indices->write($num_reg_det, 4, '', $style_proyec_titulo_1);
	$hoja_indices->write($num_reg_det, 5, '', $style_proyec_titulo_1);
	$hoja_indices->write($num_reg_det, 6, '', $style_proyec_titulo_1);
	$hoja_indices->write($num_reg_det, 7, $proyec_solic_mes, $style_proyec_num_bottom);
	
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 5);
	
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 17.25);
	
	$hoja_indices->setMerge($num_reg_det, 3, $num_reg_det, 7);
	$hoja_indices->write($num_reg_det, 3, 'SERVICIOS EN COBERTURA', $style_titulo_prin2);
	$hoja_indices->write($num_reg_det, 4, '', $style_titulo_prin2);
	$hoja_indices->write($num_reg_det, 5, '', $style_titulo_prin2);
	$hoja_indices->write($num_reg_det, 6, '', $style_titulo_prin2);
	$hoja_indices->write($num_reg_det, 7, '', $style_titulo_prin2);
	
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 17.25);
	
	$hoja_indices->setMerge($num_reg_det, 3, $num_reg_det, 6);
	$hoja_indices->write($num_reg_det, 3, 'Proyección de Concluidos del Mes:', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 4, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 5, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 6, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 7, $proyec_conclu_mes, $style_proyec_num_right);
	
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 17.25);
	
	$hoja_indices->setMerge($num_reg_det, 3, $num_reg_det, 6);
	$hoja_indices->write($num_reg_det, 3, 'Proyección de CP del Mes:', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 4, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 5, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 6, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 7, $proyec_cp_mes, $style_proyec_num_right);
	
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 17.25);
	
	$hoja_indices->setMerge($num_reg_det, 3, $num_reg_det, 6);
	$hoja_indices->write($num_reg_det, 3, 'Proyección de CM del Mes:', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 4, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 5, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 6, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 7, $proyec_cm_mes, $style_proyec_num_right);
	
	$num_reg_det++;
	$hoja_indices->setRow($num_reg_det, 17.25);
	
	$hoja_indices->setMerge($num_reg_det, 3, $num_reg_det, 6);
	$hoja_indices->write($num_reg_det, 3, 'Proyección de T del Mes:', $style_proyec_titulo_1);
	$hoja_indices->write($num_reg_det, 4, '', $style_proyec_titulo_1);
	$hoja_indices->write($num_reg_det, 5, '', $style_proyec_titulo_1);
	$hoja_indices->write($num_reg_det, 6, '', $style_proyec_titulo_1);
	$hoja_indices->write($num_reg_det, 7, $proyec_t_mes, $style_proyec_num_bottom);
	
	
	//Dibujar proyecciones de costos al cierre
	$proyec_costo_conclu_mes= ceil(($cinf_totales_costo_conclu_total / ($num_dias_mes - $num_solic_cero)) * $num_dias_mes);
	$proyec_costo_cp_mes= ceil(($cinf_totales_costo_cp / ($num_dias_mes - $num_solic_cero)) * $num_dias_mes);
	$proyec_costo_total= $proyec_costo_conclu_mes + $proyec_costo_cp_mes;
	
	$num_reg_det= $num_reg_proyec;
	
	$hoja_indices->setMerge($num_reg_det, 14, ($num_reg_det + 1), 17);
	$hoja_indices->write($num_reg_det, 14, 'PROYECCIONES DE COSTOS AL CIERRE', $style_proyec_cab_1);
	$hoja_indices->write($num_reg_det, 15, '', $style_proyec_cab_1);
	$hoja_indices->write($num_reg_det, 16, '', $style_proyec_cab_1);
	$hoja_indices->write($num_reg_det, 17, '', $style_proyec_cab_1);
	$hoja_indices->write(($num_reg_det + 1), 14, '', $style_proyec_cab_1);
	$hoja_indices->write(($num_reg_det + 1), 15, '', $style_proyec_cab_1);
	$hoja_indices->write(($num_reg_det + 1), 16, '', $style_proyec_cab_1);
	$hoja_indices->write(($num_reg_det + 1), 17, '', $style_proyec_cab_1);
	
	$num_reg_det= $num_reg_det + 3;
	$hoja_indices->setMerge($num_reg_det, 14, ($num_reg_det + 1), 17);
	$hoja_indices->write($num_reg_det, 14, 'SERVICIOS EN COBERTURA', $style_titulo_prin2);
	$hoja_indices->write($num_reg_det, 15, '', $style_titulo_prin2);
	$hoja_indices->write($num_reg_det, 16, '', $style_titulo_prin2);
	$hoja_indices->write($num_reg_det, 17, '', $style_titulo_prin2);
	$hoja_indices->write(($num_reg_det + 1), 14, '', $style_titulo_prin2);
	$hoja_indices->write(($num_reg_det + 1), 15, '', $style_titulo_prin2);
	$hoja_indices->write(($num_reg_det + 1), 16, '', $style_titulo_prin2);
	$hoja_indices->write(($num_reg_det + 1), 17, '', $style_titulo_prin2);
	
	$num_reg_det= $num_reg_det + 2;
	$hoja_indices->setMerge($num_reg_det, 14, $num_reg_det, 16);
	$hoja_indices->write($num_reg_det, 14, 'Proyección de Costo C del Mes:', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 15, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 16, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 17, $proyec_costo_conclu_mes, $style_proyec_money_right);
	
	$num_reg_det++;
	$hoja_indices->setMerge($num_reg_det, 14, $num_reg_det, 16);
	$hoja_indices->write($num_reg_det, 14, 'Proyección de Costo CP del Mes:', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 15, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 16, '', $style_proyec_titulo_2);
	$hoja_indices->write($num_reg_det, 17, $proyec_costo_cp_mes, $style_proyec_money_right);
	
	$num_reg_det++;
	$hoja_indices->setMerge($num_reg_det, 14, $num_reg_det, 16);
	$hoja_indices->write($num_reg_det, 14, 'Proyecciones del Costo Total:', $style_proyec_titulo_3);
	$hoja_indices->write($num_reg_det, 15, '', $style_proyec_titulo_3);
	$hoja_indices->write($num_reg_det, 16, '', $style_proyec_titulo_3);
	$hoja_indices->write($num_reg_det, 17, $proyec_costo_total, $style_proyec_money_bottom);
	
	/**/
	
	//PIE DE LA HOJA
	$hoja_indices->setZoom(90);
	$hoja_indices->hideScreenGridlines();
	/******/
	
	/* Definimos mediante el método send que el archivo debe enviarse al usuario al ejecutar el código y le damos el nombre que tendrá. En este caso ejemplo.xls */
	$workbook->send('INDICES_DIARIOS_EC_FINAL.xls');
	
	/* Mediante el método close cerramos y enviamos el archivo al usuario */
	$workbook->close();
}
	
?>
