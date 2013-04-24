<?
include_once('../app/modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$con->select_db($con->temporal);
$catalogo = $con->catalogo;
$fechaini = $argv[1];
$fechafin = $argv[2];
//$fechaini='2009-12-01';
//$fechafin=date('Y-m-d');

echo $fechaini.' '.$fechafin;
//creamos una tabla temporal
$sql_temporal="CREATE TEMPORARY TABLE tmp_prueba(IDASISTENCIA INT NOT NULL, IDUSUARIO CHAR(20), IDETAPA INT,TOTAL INT,TOTALDEF INT, LEVE INT, GRAVE INT)";
//echo $sql_temporal;
$exec_temporal=$con->query($sql_temporal);


/*insertamos en la tabla temporal las deficiencias las deficiecias agrupadas por etapa para cada cordinador,
diferenciando si son leves y graves - detallado*/
$sql=" INSERT tmp_prueba (IDASISTENCIA,IDUSUARIO,IDETAPA,TOTAL,TOTALDEF,LEVE,GRAVE)
SELECT AU.IDASISTENCIA,AU.IDUSUARIO,AU.IDETAPA,COUNT(DISTINCT AU.IDETAPA),
COUNT(CASE(IF(ED.ORIGEN<>'RETIRA','TRUE','FALSE')) WHEN 'TRUE' THEN ED.CVEDEFICIENCIA END)TOTALDEF,
COUNT(CASE(IF(D.ORIGEN='INTERNO' AND D.IMPORTANCIA='LEVE','TRUE','FALSE')) WHEN 'TRUE' THEN ED.CVEDEFICIENCIA END) LEVE,
COUNT(CASE(IF(D.ORIGEN='INTERNO' AND D.IMPORTANCIA='GRAVE','TRUE','FALSE')) WHEN 'TRUE' THEN ED.CVEDEFICIENCIA END) GRAVE
FROM asistencia_usuario AU LEFT JOIN expediente_deficiencia ED
ON AU.IDASISTENCIA=ED.IDASISTENCIA AND AU.IDETAPA=ED.IDETAPA 
LEFT JOIN $catalogo.catalogo_deficiencia D
ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA AND ED.ORIGEN NOT IN('RETIRA')
LEFT JOIN asistencia A ON AU.IDASISTENCIA=A.IDASISTENCIA
WHERE AU.FECHAHORA BETWEEN '$fechaini' AND '$fechafin' AND A.ARRSTATUSASISTENCIA IN('CON','CP')
GROUP BY AU.IDASISTENCIA,AU.IDUSUARIO,AU.IDETAPA
UNION
SELECT 0,IDUSUARIO,0,0,0,0,0 FROM $catalogo.catalogo_usuario 
WHERE IDUSUARIO NOT IN(SELECT DISTINCT IDUSUARIO FROM asistencia_usuario)
AND ACTIVO = 1
";
//echo $sql;
$exec_sql = $con->query($sql);
//insertamos en la tabla el total de deficiencias leves y graves agrpadas por cordinador y etapa
$sql_data="INSERT proceso_carga_cdi(IDCOORDINADOR,IDETAPA,TOTAL,DEFLEVE,DEFGRAVE) SELECT IDUSUARIO, IDETAPA,SUM(TOTAL) TOTAL,SUM(LEVE) LEVE,SUM(GRAVE) GRAVE FROM tmp_prueba GROUP BY IDUSUARIO,IDETAPA";
$exec_data =$con->query($sql_data);

$PONDLEVE=$con->lee_parametro('LEVE_CDI');
$PONDGRAVE=$con->lee_parametro('GRAVE_CDI');

//calculamos los campos de porleve y porgrave Y cdi
$sql_cdi_grupo="SELECT ID,IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100) PORLEVE,
	IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100) PORGRAVE,
	IF((100-((IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100)*$PONDLEVE)+(IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100)*$PONDGRAVE)))>0,
	(100-((IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100)*$PONDLEVE)+(IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100)*$PONDGRAVE))),0)
	CDI,DATE_FORMAT(FECHAHORA,'%Y-%m-%d') FECHAHORA
	FROM proceso_carga_cdi where DATE_FORMAT(FECHAHORA,'%Y-%m-%d')='$fechafin'";
	//echo $sql_cdi_grupo;
$exec_cdi_grupo=$con->query($sql_cdi_grupo);
while($rset_cdi_grupo=$exec_cdi_grupo->fetch_object()){
	$cdi[PORLEVE]=$rset_cdi_grupo->PORLEVE;
	$cdi[PORGRAVE]=$rset_cdi_grupo->PORGRAVE;
	$cdi[CDI]=$rset_cdi_grupo->CDI;
	$id=$rset_cdi_grupo->ID;
	$fecha=$rset_cdi_grupo->FECHAHORA;
	//ACTUALIZAMOS LOS CAMPOS PORLEVE,PORGRAVE Y CDI
	$con->update("proceso_carga_cdi",$cdi,"  WHERE ID =".$id);
}

$sql_consolidado="INSERT proceso_carga_cdi_consolidado(IDCOORDINADOR,IDETAPA,TOTAL,DEFLEVE,DEFGRAVE,FLG) SELECT IDCOORDINADOR,IF(IDETAPA=0,0,COUNT(IDETAPA)) ,SUM(TOTAL)/IF(IDETAPA=0,0,COUNT(IDETAPA)),SUM(DEFLEVE),SUM(DEFGRAVE),'USUARIO'
FROM proceso_carga_cdi WHERE DATE_FORMAT(FECHAHORA,'%Y-%m-%d') = '$fechafin'
GROUP BY IDCOORDINADOR";
//echo $sql_consolidado;
$exec_consolidado=$con->query($sql_consolidado);

$sql_cdi_consolidado="SELECT ID,IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100) PORLEVE,
	IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100) PORGRAVE,
	IF((100-((IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100)*$PONDLEVE)+(IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100)*$PONDGRAVE)))>0,
	(100-((IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100)*$PONDLEVE)+(IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100)*$PONDGRAVE))),0)
	CDI,DATE_FORMAT(FECHAHORA,'%Y-%m-%d') FECHAHORA
	FROM proceso_carga_cdi_consolidado WHERE DATE_FORMAT(FECHAHORA,'%Y-%m-%d')='$fechafin'";
	//echo $sql_cdi_grupo;
$exec_cdi_consolidado=$con->query($sql_cdi_consolidado);
while($rset_cdi_consolidado=$exec_cdi_consolidado->fetch_object()){
	$cdic[PORLEVE]=$rset_cdi_consolidado->PORLEVE;
	$cdic[PORGRAVE]=$rset_cdi_consolidado->PORGRAVE;
	$cdic[CDI]=$rset_cdi_consolidado->CDI;
	$idc=$rset_cdi_consolidado->ID;
	$fecha=$rset_cdi_consolidado->FECHAHORA;
	
	$con->update("proceso_carga_cdi_consolidado",$cdic,"  WHERE ID =".$idc);
}

//INSERTAMOS EN EL CONSOLIDADO LOS TOTALES AGRUPADOS SOLO POR CORDINADOR YA NO POR ETEAP.
$sql_cdi_consolidado_total="INSERT proceso_carga_cdi_consolidado(TOTAL,DEFLEVE,PORLEVE,FLG) SELECT SUM(TOTAL),SUM(DEFLEVE),SUM(DEFGRAVE),'TOTAL'
	FROM proceso_carga_cdi_consolidado where DATE_FORMAT(FECHAHORA,'%Y-%m-%d')='$fechafin'";
	//echo $sql_cdi_grupo;
$exec_cdi_consolidado_total=$con->query($sql_cdi_consolidado_total);

//CALCULAMOS EL CDI TOTAL DEL MES
$sql_cdi_consolidado_total_up="SELECT ID,IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100) PORLEVE,
	IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100) PORGRAVE,
	IF((100-((IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100)*$PONDLEVE)+(IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100)*$PONDGRAVE)))>0,
	(100-((IF(TOTAL=0,0,(DEFLEVE/TOTAL)*100)*$PONDLEVE)+(IF(TOTAL=0,0,(DEFGRAVE/TOTAL)*100)*$PONDGRAVE))),0)
	CDI,DATE_FORMAT(FECHAHORA,'%Y-%m-%d') FECHAHORA
	FROM proceso_carga_cdi_consolidado WHERE DATE_FORMAT(FECHAHORA,'%Y-%m-%d')='$fechafin' AND FLG='TOTAL'";
	//echo $sql_cdi_grupo;
$exec_cdi_consolidado_total_up=$con->query($sql_cdi_consolidado_total_up);
while($rset_cdi_consolidado_up=$exec_cdi_consolidado_total_up->fetch_object()){
	$cdicup[PORLEVE]=$rset_cdi_consolidado_up->PORLEVE;
	$cdicup[PORGRAVE]=$rset_cdi_consolidado_up->PORGRAVE;
	$cdicup[CDI]=$rset_cdi_consolidado_up->CDI;
	$idcup=$rset_cdi_consolidado_up->ID;
	// INSERTAMOS UN REGISTRO MAS EN LA TABLA CONSOLIDADO IDENTIFICADO CON LA ETIQUETA TOTAL 
	$con->update("proceso_carga_cdi_consolidado",$cdicup,"  WHERE ID =".$idcup);
}
?>
