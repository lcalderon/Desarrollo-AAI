<?php
	
	session_start();

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_moneda.inc.php');
	include_once('../../../modelo/clase_ubigeo.inc.php');
	include_once('../../../modelo/clase_plantilla.inc.php');
	include_once('../../../modelo/clase_persona.inc.php');
	include_once('../../../modelo/clase_telefono.inc.php');
	include_once('../../../modelo/clase_cuenta.inc.php');
	include_once('../../../modelo/clase_familia.inc.php');
	include_once('../../../modelo/clase_servicio.inc.php');
	include_once('../../../modelo/clase_programa_servicio.inc.php');
	include_once('../../../modelo/clase_programa.inc.php');
	include_once('../../../modelo/clase_contacto.inc.php');
	include_once('../../../modelo/clase_proveedor.inc.php');
	include_once('../../../modelo/clase_afiliado.inc.php');
	include_once('../../../modelo/clase_etapa.inc.php');
	include_once('../../../modelo/clase_expediente.inc.php');
	include_once('../../../modelo/clase_asistencia.inc.php');
	include_once("../../../vista/login/Auth.class.php");
		
	
	Auth::required();
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->temporal);
	
	$idasistencia=$_POST["asistencia"];

	$asis = new asistencia();
	$asis->carga_datos($idasistencia);
	$idexpediente= $asis->expediente->idexpediente;

	$exp= new expediente();
	$exp->carga_datos($idexpediente);

	$cuenta = $exp->cuenta->idcuenta;
	$aniomes = date('Ym');
	
    //echo $cuenta;
$p1 = $_POST[p1];
$p2 = $_POST[p2];
$p3 = $_POST[p3];
$p4 = $_POST[p4];
$p5 = $_POST[p5];
$t1 = $_POST[t1];
$t2 = $_POST[t2];
$t3 = $_POST[t3];
$t4 = $_POST[t4];
$t5 = $_POST[t5];
$s1 = $_POST[s1];
$s2 = $_POST[s2];
$s3 = $_POST[s3];
$s4 = $_POST[s4];
$s5 = $_POST[s5];
$s6 = $_POST[s6];
$interno = $_POST[hid_interno];
	$enc[IDASISTENCIA]=$idasistencia;
	$enc[C1]=$p1;
	$enc[C2]=$p2;
	$enc[C3]=$p3;
	$enc[C4]=$p4;
	$enc[C5]=$p5;
	
	$enc[T1]=$t1;
	$enc[T2]=$t2;
	$enc[T3]=$t3;
	$enc[T4]=$t4;
	$enc[T5]=$t5;
	$enc[G1]=$s1;
	$enc[G2]=$s2;
	$enc[G3]=$s3;
	$enc[G4]=$s4;
	$enc[G5]=$s5;
	$enc[G6]=$s6;
	
	/*echo $enc[IDASISTENCIA];
	echo $enc[C1];
	echo $enc[C2];
	echo $enc[C3];
	echo $enc[C4];
	echo $enc[C5];
	echo $enc[IDUSUARIOMOD];
	echo $enc[T1];
	echo $enc[T2];
	echo $enc[T3];
	echo $enc[T4];
	echo $enc[T5];
	echo $enc[G1];
	echo $enc[G2];
	echo $enc[G3];
	echo $enc[G4];
	echo $enc[G5];
	echo $enc[G6];*/
	//PARA EL PROMEDIO DE LA ENCUESTA. NO SE CONSIDERA LOS VALORES QUE NO HAYAN SIDO INGRESADOS. 
	//ESTOS VALORES CUYOS CAMPO DE SELECCION QUEDO VACIO LOS IDENTIFICAMOS CON 100
	if($s1!='100'){ $var1=1; }
	if($s2!='100'){ $var2=1; } 
	if($s3!='100'){ $var3=1; } 
	if($s4!='100'){ $var4=1; } 
	if($s5!='100'){ $var5=1; }  
	
	//DEFINIMOS EL TOTAL DE CAMPOS COMPLETADOS
	$total = $var1+$var2+$var3+$var4+$var5;

	//SI LOS CAMPOS ESTAN VACIOS = 100 LOS IGUALAMOS A 0 PARA QUE EL 100 NO TENGA EFECTO EN EL CALCULO DEL PROMEDIO
	if($s1=='100'){ $s1 = 0; }
	if($s2=='100'){ $s2 = 0; }
	if($s3=='100'){ $s3 = 0; }
	if($s4=='100'){ $s4 = 0; }
	if($s5=='100'){ $s5 = 0; }
	//CALUCLAMOS EL PROMEDIO DE LA ENCUESTA
	$enc[PROMEDIO] = ($s1+$s2+$s3+$s4+$s5)/$total;
 
	//INDICAMOS LA ETIQUETA DEPENDIENDO DEL PROMEDIO
 
	if($enc[PROMEDIO]>= 9){ 
		$eval[EVALENCUESTA]='E';
	} elseif($enc[PROMEDIO] < 9 && $enc[PROMEDIO] >=7){
		$eval[EVALENCUESTA]='MB'; 
	} elseif($enc[PROMEDIO] < 7 && $enc[PROMEDIO] >=5){ 
		$eval[EVALENCUESTA]='B';
	}elseif($enc[PROMEDIO] < 5 && $enc[PROMEDIO] >=3){ 
		$eval[EVALENCUESTA]='R'; 
	// } else if($enc[PROMEDIO] ==0){
	} else if(is_numeric($enc[PROMEDIO])){	
		$eval[EVALENCUESTA]='M';
	}
	
	if($_POST["cmbevaluacionenc"] =="SLOC" || $_POST["cmbevaluacionenc"] =="NCEN")		$eval[EVALENCUESTA]="";

	//SI EL TECNICO ES INTERNO REGITRAMOS LOS VALORES EN LA ZONA DE RESULTADO DE TECNICOS
	if($interno==1){
		$rt1=$p1;
		$rt2=$p2;
		$rt3=$p3;
		$rt4=$p4;
		$rt5=$p5;
		
		$rf1=$t1;
		$rf2=$t2;
		$rf3=$t3;
		$rf4=$t4;
		$rf5=$t5;
				
		$rg1=$s1;
		$rg2=$s2;
		$rg3=$s3;
		$rg4=$s4;
		$rg5=$s5;
		$rg6=$s6;
	}elseif($interno==0){ // SI NO ES UN TECNICO INTERNO SE INGRESA UN VALOR VACIO = FALSO EN EXCEL
		$rt1=100;
		$rt2=100;
		$rt3=100;
		$rt4=100;
		$rt5=100;
		
		$rf1=100;
		$rf2=100;
		$rf3=100;
		$rf4=100;
		$rf5=100;
				
		$rg1=100;
		$rg2=100;
		$rg3=100;
		$rg4=100;
		$rg5=100;
		$rg6=100;
	}
	
		if($rf1==100){ $xrf1=0; }else{ $xrf1 = $rf1; }
		if($rf2==100){ $xrf2=0; }else{ $xrf2 = $rf2; }
		if($rf3==100){ $xrf3=0; }else{ $xrf3 = $rf3; }
		if($rf4==100){ $xrf4=0; }else{ $xrf4 = $rf4; }
		if($rf5==100){ $xrf5=0; }else{ $xrf5 = $rf5; }
		//CALCULAMOS EL PROMEDIO EN LA ZONA DE RESULTADOS DE TECNICOS
	if(($xrf1+$xrf2+$xrf3+$xrf4+$xrf5+$rg1+$rg2+$rg3+$rg4+$rg5+$rg6)!=0){ $tpromedio= ($rg1+$rg2+$rg3+$rg4+$rg5)/5; }
	$enc[INTERNO]=$interno;
	$enc[RT1]=$rt1;
	$enc[RT2]=$rt2;
	$enc[RT3]=$rt3;
	$enc[RT4]=$rt4;
	$enc[RT5]=$rt5;
	$enc[TF1]=$rf1;
	$enc[TF2]=$rf2;
	$enc[TF3]=$rf3;
	$enc[TF4]=$rf4;
	$enc[TF5]=$rf5;
	$enc[TG1]=$rg1;
	$enc[TG2]=$rg2;
	$enc[TG3]=$rg3;
	$enc[TG4]=$rg4;
	$enc[TG5]=$rg5;
	$enc[TG6]=$rg6;
	$enc[TPROMEDIO]=$tpromedio;
	
	$enc[NOMBREINFORMANTE]=$_POST["txtnombreinfo"];
	$enc[OTROS]=$_POST["txtotros"];
	$enc[EMAILS]=$_POST["txtemail"];
    $enc[EVALENCUESTA]=$eval[EVALENCUESTA];
	$enc[COMENTARIO]=$_POST["txtacomentario"];
	
	//REGISTRAMOS TODOS LOS DATOS DE LA ENCUESTA
	
	$Sql_existe="SELECT COUNT(*) FROM $con->temporal.asistencia_encuesta_calidad WHERE IDASISTENCIA='$idasistencia' GROUP BY IDASISTENCIA ";
	$encuesta_exis=$con->consultation($Sql_existe);
	
	$hist[IDASISTENCIA]=$idasistencia;
	$hist[IDUSUARIO]=$_SESSION[user];
	$hist[STATUSCALIFICA]=$_POST["cmbevaluacionenc"];
		
	if($encuesta_exis[0][0] ==""){
		
		$enc[IDUSUARIOMOD]=$_SESSION[user];
		$con->insert_reg("$con->temporal.asistencia_encuesta_calidad",$enc);
		
// Grabar historico
		$con->insert_reg("$con->temporal.asistencia_encuesta_calidad_historico",$hist); 
		
	//echo $encuesta_exis[0][0]."--";
	} else{
		
		$con->update("$con->temporal.asistencia_encuesta_calidad",$enc,' WHERE IDASISTENCIA='.$idasistencia);
// Grabar historico
		$con->insert_reg("$con->temporal.asistencia_encuesta_calidad_historico",$hist); 
	//echo $encuesta_exis[0][0]."**";
    }
   
 	$eval[ARRSTATUSENCUESTA]=$_POST["cmbevaluacionenc"];
	//ACTUALIZAMOS EL ESTATUS EN LA TABLA ASISTENCIA PARA INDICAR QUE LA ENCUESTA YA SE REALIZO
	$con->update("$con->temporal.asistencia",$eval,' WHERE IDASISTENCIA='.$idasistencia);
	
	//REGISTRAMOS EL USUARIO QUE HIZO LA ENCUESTA
	$sup[IDASISTENCIA]=$idasistencia;
	$sup[IDUSUARIO]=$_SESSION[user];
	$sup[PROCESO]='ENCUESTA';
	$sup[OBSERVACION]=$_POST["cmbevaluacionenc"];
	$con->insert_reg('asistencia_usuario_calidad',$sup);

	echo "<script language='javascript'>cerrar();</script>";

//OBTENEMOS LOS PARAMETROS DEL CATALOGO_PARAMETROS_INDICADORES_CALIDAD
$sql_parametro="SELECT * from $con->catalogo.catalogo_parametro_indicador_encuesta WHERE PARAMETRO IN ('SI','NO','NSNC') AND ANIOMES='$aniomes'";
$exec_parametro = $con->query($sql_parametro);
while($rset_parametro=$exec_parametro->fetch_object()){

    switch($rset_parametro->PARAMETRO){
		  case 'SI': $valor=$rset_parametro->VALOR;break;
		  case 'NO': $valor=$rset_parametro->VALOR;break;
		  case 'NSNC': $valor=$rset_parametro->VALOR;break;
	  
      }
	
	//OBTENEMOS LOS TOTALES DE LAS 10 PRIMERAS PREGUNTAS DE LA ENCUESTA NECESARIAS PARA LOS GRAFICOS DE PIE Y DE BARRA DEL REPORTE DE CALIDAD, AGRUPADOS POR CUENTA Y POR MES
      $sql_total="SELECT '$rset_parametro->PARAMETRO' PARAMETRO,(COUNT(CASE(AC.C1) WHEN $valor THEN AC.C1 END)/COUNT(CASE WHEN AC.C1<>100 THEN AC.C1 END ))*100 C1,
	      (COUNT(CASE(AC.C2) WHEN $valor THEN AC.C2 END)/COUNT(CASE WHEN AC.C2<>100 THEN AC.C2 END ))*100 C2,
	      (COUNT(CASE(AC.C3) WHEN $valor THEN AC.C3 END)/COUNT(CASE WHEN AC.C3<>100 THEN AC.C3 END ))*100 C3,	
	      (COUNT(CASE(AC.C4) WHEN $valor THEN AC.C4 END)/COUNT(CASE WHEN AC.C4<>100 THEN AC.C4 END ))*100 C4,
	      (COUNT(CASE(AC.C5) WHEN $valor THEN AC.C5 END)/COUNT(CASE WHEN AC.C5<>100 THEN AC.C5 END ))*100 C5,
	      (COUNT(CASE(AC.T1) WHEN $valor THEN AC.T1 END)/COUNT(CASE WHEN AC.T1<>100 THEN AC.T1 END ))*100 T1,
	      (COUNT(CASE(AC.T2) WHEN $valor THEN AC.T2 END)/COUNT(CASE WHEN AC.T2<>100 THEN AC.T2 END ))*100 T2,
	      (COUNT(CASE(AC.T3) WHEN $valor THEN AC.T3 END)/COUNT(CASE WHEN AC.T3<>100 THEN AC.T3 END ))*100 T3,
	      (COUNT(CASE(AC.T4) WHEN $valor THEN AC.T4 END)/COUNT(CASE WHEN AC.T4<>100 THEN AC.T4 END ))*100 T4,
	      (COUNT(CASE(AC.T5) WHEN $valor THEN AC.T5 END)/COUNT(CASE WHEN AC.T5<>100 THEN AC.T5 END ))*100 T5
      FROM asistencia A INNER JOIN asistencia_encuesta_calidad AC
      ON A.IDASISTENCIA = AC.IDASISTENCIA
      WHERE A.IDCUENTA = '$cuenta' AND DATE_FORMAT(AC.FECHAMOD,'%Y%m') = '$aniomes'";
	  
      $exec_total=$con->query($sql_total);
      $nreg_total = $exec_total->num_rows;
	  
      echo "<script language='javascript'>alert($nreg_total)</script>";
      if($rset_total=$exec_total->fetch_object()){

			$row_i[IDPARAMETRO]=$rset_total->PARAMETRO;
			$row_i[IDCUENTA]=$cuenta;
			$row_i[ANIOMES]=$aniomes;
			$row_i[C1]=$rset_total->C1;    
			$row_i[C2]=$rset_total->C2;
			$row_i[C3]=$rset_total->C3;
			$row_i[C4]=$rset_total->C4;
			$row_i[C5]=$rset_total->C5;
			$row_i[T1]=$rset_total->T1;
			$row_i[T2]=$rset_total->T2;
			$row_i[T3]=$rset_total->T3;
			$row_i[T4]=$rset_total->T4;
			$row_i[T5]=$rset_total->T5;
			if($nreg_total!='0')
			{
			//SI YA EXISTEN ENCUESTAS REALIZADAS PARA ESA CUENTA Y EN ESE MES ACTUALIZAMOS LOS RESULTADOS DEL CONSOLIDADO, CASO CONTRARIO INGRESAMOS LOS NUEVOS RESULTADOS
			$con->update("$con->temporal.calidad_resultado",$row_i," WHERE IDPARAMETRO = '$rset_total->PARAMETRO' AND IDCUENTA='$cuenta' AND ANIOMES='$aniomes'"); 
			}
			else
			{
			 
			$con->insert_reg("$con->temporal.calidad_resultado",$row_i);
			}
      }

}
?>
<script>	
	document.location.href='gestionarEncuesta.php?asistencia=<?=$idasistencia?>';	
</script>
