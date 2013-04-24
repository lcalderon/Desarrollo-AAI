<?php

    session_start();

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once('../../../modelo/functions.php');	
	include_once("../../includes/arreglos.php");

    //$con= new DB_mysqli("replica");
    $con= new DB_mysqli();

    if($con->Errno){
        printf("Fallo de conexion: %s\n", $con->Error);
        exit();
    }

    foreach($_REQUEST['cmbstatusasistencia'] as $statusasistencia){
        $arrstatusasistencia[] ="$statusasistencia";
        $resp_statusasistencia =implode(',',$arrstatusasistencia);
    }
	
    foreach($_REQUEST['cmbcondicionservicio'] as $condicionservicio){
        $arrcondicionservicio[] ="$condicionservicio";
        $resp_arrcondicionservicio =implode(',',$arrcondicionservicio);
    }
 
    if($_REQUEST["radio"]==1){
        $fecha=($_REQUEST["cmbmes"])?$_REQUEST["cmbanio"]."-".$_REQUEST["cmbmes"]:$_REQUEST["cmbanio"];
        //$Sql_fecha=" (MIN(asistencia_usuario.FECHAHORA) LIKE '$fecha%')  ";
        $Sql_fecha="e.FECHAREGISTRO LIKE '$fecha%' AND";
    } else{
        //$Sql_fecha=" (LEFT(MIN(asistencia_usuario.FECHAHORA),10) BETWEEN '".$_REQUEST["fechaini"]."' AND '".$_REQUEST["fechafin"]."')  ";
        $Sql_fecha="e.FECHAREGISTRO BETWEEN '".$_REQUEST["fechaini"]."' AND '".$_REQUEST["fechafin"]."' AND";
    }
 
    $Sql="SELECT
			/* CONSULTA REINCIDENCIAS*/
			COUNT(*) AS CANTIDAD,
			e.idafiliado,
			ca.CVEAFILIADO,
			IF(cap.NOMBRE ='',cap.APPATERNO,IF(cap.APPATERNO ='',cap.NOMBRE,CONCAT(cap.APPATERNO,' ',cap.APMATERNO,', ',cap.NOMBRE))) AS NOMBRES,
			cs.descripcion AS SERVICIOS
		FROM
			(
				$con->temporal.asistencia a,
				$con->temporal.expediente e,
				$con->catalogo.catalogo_servicio cs
			)
		LEFT JOIN $con->catalogo.catalogo_afiliado ca ON ca.idafiliado = e.IDAFILIADO
		LEFT JOIN $con->catalogo.catalogo_afiliado_persona cap ON cap.idafiliado = e.IDAFILIADO
		WHERE $Sql_fecha 
			a.idexpediente = e.idexpediente
		AND e.idcuenta ='".$_REQUEST["cmbcuenta"]."'
		AND a.idservicio = cs.idservicio
		AND a.arrstatusasistencia IN($resp_statusasistencia)
		AND a.ARRCONDICIONSERVICIO IN($resp_arrcondicionservicio)
		GROUP BY	2,	5 HAVING COUNT(*) > 1 ORDER BY	1 DESC";
				
		// echo $Sql;		
		// die(); 
        $resultexp=$con->query($Sql);     
		
		$aleatorio= rand(10000,99999);
        $fecha= date("Ymd"); 
		
        if($resultexp->num_rows ==0){
            echo "<script>\n";
            echo "alert('"._("NO EXISTE REGISTROS PARA LA CONSULTA")."');\n";
            echo "</script>\n";
        } else{		
	  
		set_time_limit(990);

		$aleatorio= rand(1000,9999);

	//nombre archivo excel	
		$fecha= date("Ymd"); 
		$hora= date("hms"); 
		$nombre= "REINCIDENCIAS".$fecha."-".$aleatorio ; 

	//libreria pear excel 	
		$pear = "./../../../../librerias/pearExcel";
		ini_set("include_path",ini_get("include_path").":$pear");
		require_once("../../../../librerias/pearExcel/Spreadsheet/Excel/Writer.php");

		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$nombre.xls");
		
		$workbook = & new Spreadsheet_Excel_Writer();
		$workbook->setVersion(8);
		$worksheet = &$workbook->addWorksheet("DATA-REINCIDENCIAS");

	//formato
		$titulo =& $workbook->addFormat();
		$titulo->setBold();		
		
	// FORMATO: tamano celdas
		$worksheet->setColumn(0,0,5); 
		$worksheet->setColumn(0,1,15); 
		$worksheet->setColumn(0,2,35); 
		$worksheet->setColumn(0,3,45); 
		$worksheet->setColumn(0,4,10); 
		
	// FORMATO:contenido dataa
		$format_contenido =& $workbook->addFormat();
		$format_contenido->setBorder(1);

		$format_contenidoCentrado =& $workbook->addFormat();
		$format_contenidoCentrado->setAlign('center');
		$format_contenidoCentrado->setBorder(1);

	//FORMATO: TITULO

		$format_titulo =& $workbook->addFormat();
		$format_titulo->setHAlign('center');
		$format_titulo->setVAlign('vcenter');
		$format_titulo->setFgColor('aqua');
		$format_titulo->setBorder(1);
		$format_titulo->setBold();	
	
	// NOMBRE COLUMNA
		$worksheet->write(0, 0,"#",$format_titulo);
		$worksheet->write(0, 1,"CVEAFILIADO",$format_titulo);
		$worksheet->write(0, 2,"NOMBREAFILIADO",$format_titulo);
		$worksheet->write(0, 3,"SERVICIO",$format_titulo);
		$worksheet->write(0, 4,"CANTIDAD",$format_titulo);	
		
		$fila=1;
		while($row = $resultexp->fetch_object()){
				
			$columna=0;
			$contador++;
			
			$worksheet->write($fila,$columna,$contador,$format_titulo);
			$worksheet->writeString($fila,$columna+1,$row->CVEAFILIADO,$format_contenidoCentrado);
			$worksheet->write($fila,$columna+2,$row->NOMBRES,$format_contenido);
			$worksheet->write($fila,$columna+3,$row->SERVICIOS,$format_contenido);
			$worksheet->write($fila,$columna+4,$row->CANTIDAD,$format_contenidoCentrado);
			
			$fila++;
		}		
		
		$workbook->close();	
	}
?>