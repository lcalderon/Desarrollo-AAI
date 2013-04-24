<?
/*
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_poligono.inc.php');
include_once('../../../modelo/clase_circulo.inc.php');*/
//include_once('../../../backoffice/ranking_proveedores.php');
//include_once('../../../modelo/clase_proveedor.inc.php');
/************************************************************************************************
*
*   Ranking( $idservicio,$prov,$idasistencia ) --> array
*        $idservicio : Id del servicio a brindar
*        $prov : Array con los proveedores que ingresaran al calculo de ranking
*		 $idasistencia	: Numero de la asistencia
*        array : Array que contiene los proveedores ordenados por el ranking.
*
*   Desarrollo: Pedro  Quispe sanchez   
*
************************************************************************************************/

function Ranking($idservicio,$prov,$idasistencia,$opcion,$tablatmp){
$con = new DB_mysqli();


$con->select_db($con->temporal);
$db = $con->catalogo;
//echo "<script language='javascript'>alert($idservicio)</script>";
if ($con->Errno) {
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}
// Obtenemos los valores del catalogo de parametros.
$sql_parametro = "SELECT IDPARAMETRO,DATODEFAULT FROM $db.catalogo_parametro WHERE IDPARAMETRO
 IN('RANKING_COSTO','RANKING_CDE','RANKING_INFRAESTRUCTURA','RANKING_FIDELIDAD','RANKING_SATISFACCION')";

	$exec_parametro=$con->query($sql_parametro);
	while ($rset_parametro = $exec_parametro->fetch_object()){
		$idparametro=$rset_parametro->IDPARAMETRO;
		switch($idparametro){
		    case 'RANKING_COSTO' : $rcosto = $rset_parametro->DATODEFAULT;break;
		    case 'RANKING_CDE' : $rcde = $rset_parametro->DATODEFAULT;break;
		    case 'RANKING_INFRAESTRUCTURA' : $rinfra = $rset_parametro->DATODEFAULT;break;
		    case 'RANKING_FIDELIDAD' : $rfidel = $rset_parametro->DATODEFAULT;break;
		    case 'RANKING_SATISFACCION' : $rsatis = $rset_parametro->DATODEFAULT;break;
		}
		
	}


	$hoy = date("Y-m-d");
$totalrankprov=count($prov);
//echo "<script language='javascript'>alert($totalrankprov)</script>";
if($totalrankprov==0){
  
}
else{
	 for($j=0;$j<count($prov);$j++){
	      $idproveedor = $prov[$j][0];
	      $localforaneo = $prov[$j][1];
	      //echo $idproveedor;
if($idservicio==0){
    $costototal=0;
}else{
	     $sql_costo_prov="
SELECT IF((SELECT COUNT(IF('$localforaneo'='LOC',MONTOLOCAL,MONTOFORANEO)) FROM $db.catalogo_proveedor_servicio_costo_negociado 
WHERE  IDCOSTO=1 AND IDPROVEEDOR = $idproveedor AND IDSERVICIO = $idservicio )=0,
(SELECT IF('$localforaneo'='LOC',MONTOLOCAL,MONTOFORANEO) FROM $db.catalogo_servicio_costo
 WHERE IDSERVICIO = $idservicio AND IDCOSTO = 1),(SELECT IF('$localforaneo'='LOC',MONTOLOCAL,MONTOFORANEO) FROM $db.catalogo_proveedor_servicio_costo_negociado  WHERE  IDCOSTO=1
 AND IDPROVEEDOR = $idproveedor AND IDSERVICIO = $idservicio )) 
 AS  COSTO
FROM  $db.catalogo_proveedor_servicio_costo_negociado LIMIT 1";
//echo $sql_costo_prov;
/*
SELECT $idproveedor AS IDPROVEEDOR,P.INTERNO,C.CDE,
		  IF(CN.MONTOLOCAL IS NULL,(SELECT MONTOLOCAL FROM $db.catalogo_servicio_costo WHERE IDSERVICIO = $idservicio AND IDCOSTO = 1),CN.MONTOLOCAL) AS  COSTO,P.EVALINFRAESTRUCTURA,P.EVALFIDELIDAD,P.EVALSATISFACCION 
		   FROM $db1.proceso_carga_cde C INNER JOIN $db.catalogo_proveedor P ON C.IDPROVEEDOR = P.IDPROVEEDOR 
		   LEFT JOIN $db.catalogo_proveedor_servicio_costo_negociado CN ON P.IDPROVEEDOR = CN.IDPROVEEDOR AND IDCOSTO=1
		   WHERE C.IDPROVEEDOR = $idproveedor AND C.IDSERVICIO = $idservicio AND C.FECHAHORA = '$hoy'";*/
//echo "<script language='javascript'>alert($sql_costo_prov)</script>";
	    //echo "<script language='javascript'>alert($sql_costo_prov)</script>";
	      $exec_costo_prov = $con->query($sql_costo_prov);
	      $nreg_costo_prov = $exec_costo_prov->num_rows;
	      if($nreg_costo_prov==0){
	      }else{
		while($rset_costo_prov=$exec_costo_prov->fetch_object()){
		    $costo =$rset_costo_prov->COSTO;
		 // echo $costo;
		}
	      }
	      $costototal = $costototal+$costo;
	     
	  }
 //echo $costototal;
	  $costototal = $costototal/count($prov);

}
}
	  for ($i = 0; $i < count($prov); $i++)	  {
		  $idproveedor = $prov[$i][0];
		  $localforaneo = $prov[$i][1];
		  $distancia = $prov[$i][2];
		  //echo "<script language='javascript'>alert($idservicio)</script>";
		  //Obtenemos el CDE, Costo, Y evaluaciones de proveedore necesarios para el calculo del ranking.
		  $sql_dato_prov="SELECT $idproveedor AS IDPROVEEDOR,'$localforaneo' AS LOCALFORANEO,$distancia AS DISTANCIA,P.INTERNO,
		 COALESCE( IF(P.ARREVALRANKING='SKILL',P.SKILL,IF($idservicio=0,(SELECT CDE FROM $db.catalogo_proveedor WHERE IDPROVEEDOR =$idproveedor),(SELECT IF(ARREVALRANKING='SKILL',SKILL,CDE) CDE FROM $db.catalogo_proveedor_servicio WHERE IDPROVEEDOR =$idproveedor
		  AND IDSERVICIO = $idservicio))),0) AS CDE,
		  IF($idservicio=0,0,IF((SELECT COUNT(IF('$localforaneo'='LOC',MONTOLOCAL,MONTOFORANEO)) FROM $db.catalogo_proveedor_servicio_costo_negociado 
WHERE  IDCOSTO=1 AND IDPROVEEDOR = $idproveedor AND IDSERVICIO = $idservicio )=0,
IF((SELECT IF('$localforaneo'='LOC',MONTOLOCAL,MONTOFORANEO) FROM $db.catalogo_servicio_costo
 WHERE IDSERVICIO = $idservicio AND IDCOSTO = 1) IS NULL,0,(SELECT IF('$localforaneo'='LOC',MONTOLOCAL,MONTOFORANEO) FROM $db.catalogo_servicio_costo
 WHERE IDSERVICIO = $idservicio AND IDCOSTO = 1)),(SELECT IF('$localforaneo'='LOC',MONTOLOCAL,MONTOFORANEO) FROM $db.catalogo_proveedor_servicio_costo_negociado  WHERE  IDCOSTO=1
 AND IDPROVEEDOR = $idproveedor AND IDSERVICIO = $idservicio )) )
 AS  COSTO,
		  P.EVALINFRAESTRUCTURA,P.EVALFIDELIDAD,P.EVALSATISFACCION 
		   FROM  $db.catalogo_proveedor P  
		   WHERE P.IDPROVEEDOR = $idproveedor";
		//echo $sql_dato_prov;	
//echo "<script language='javascript'>alert($sql_dato_prov)</script>";
		  $exec_dato_prov = $con->query($sql_dato_prov);
		  $nreg_dato_prov = $exec_dato_prov->num_rows;
		  if($nreg_dato_prov==0){
		  }else{
		  while($rset_dato_prov=$exec_dato_prov->fetch_object())
		  {

		    $PROV = $rset_dato_prov->IDPROVEEDOR;
		    $INTERNO = $rset_dato_prov->INTERNO;
		    $CDE = $rset_dato_prov->CDE;
		    $COSTO = $rset_dato_prov->COSTO;
		    $INFRAESTRUCTURA = $rset_dato_prov->EVALINFRAESTRUCTURA;
		    $FIDELIDAD = $rset_dato_prov->EVALFIDELIDAD;
		    $SATISFACCION = $rset_dato_prov->EVALSATISFACCION;
		    $LOCALFORANEO = $rset_dato_prov->LOCALFORANEO;
		    $DISTANCIA = $rset_dato_prov->DISTANCIA;
		    
		    //echo $LOCALFORANEO.' '.$DISTANCIA;
		     //echo $INFRAESTRUCTURA.' '.$rinfra;
			 //Calculamos el ranking
		    $sql_ranking = "SELECT $PROV AS XPROVEEDOR,$INTERNO AS INTERNO,'$LOCALFORANEO' AS LOCALFORANEO,$DISTANCIA AS DISTANCIA,($CDE/100)*($rcde/100) AS XCDE,
				      IF(($costototal*($rcosto/100))/$COSTO IS NULL,0,($costototal*($rcosto/100))/$COSTO) AS XCOSTO,
				      ($INFRAESTRUCTURA/10)*($rinfra/10) AS XINFRAESTRUCTURA,
				    ($FIDELIDAD/10)*($rfidel/10) AS XFIDELIDAD,
				      ($SATISFACCION/100)*($rsatis/100) AS XSATISFACCION, 
				if($idservicio=0,((($CDE/100)*($rcde/100)  +
				($SATISFACCION/100)*($rsatis/100)+($INFRAESTRUCTURA/10)
				*($rinfra/10)+($FIDELIDAD/10)*($rfidel/10)))*100,
				if(((($CDE/100)*($rcde/100) +($costototal*($rcosto/100))/$COSTO +
				($SATISFACCION/100)*($rsatis/100)+($INFRAESTRUCTURA/10)
				*($rinfra/10)+($FIDELIDAD/10)*($rfidel/10)))*100 IS NULL,0, ((($CDE/100)*($rcde/100) +($costototal*($rcosto/100))/$COSTO +
				($SATISFACCION/100)*($rsatis/100)+($INFRAESTRUCTURA/10)
				*($rinfra/10)+($FIDELIDAD/10)*($rfidel/10)))*100)   ) AS POR";
		   // echo $sql_ranking;
		//echo "<script language='javascript'>alert($sql_ranking)</script>";
		    $exec_ranking= $con->query($sql_ranking);
		    $nreg_ranking = $exec_ranking->num_rows;
		    while($rset_ranking = $exec_ranking->fetch_object()){
			  
//echo $rset_ranking->XPROVEEDOR.' '.$rset_ranking->XCDE.' '.$rset_ranking->XCOSTO.' '.$rset_ranking->XINFRAESTRUCTURA.' '.$rset_ranking->XFIDELIDAD.' '.$rset_ranking->XSATISFACCION.' '.$rset_ranking->POR.' '.$rset_ranking->LOCALFORANEO.' '.$rset_ranking->DISTANCIA.'<br>';
			  if($opcion=='AUTO'){
			      $sql_insert_ranking="INSERT INTO asistencia_ranking(IDASISTENCIA,IDPROVEEDOR,IDSERVICIO,RANK,INTERNO,LOCALFORANEO,DISTANCIA) 
			      VALUES ($idasistencia,$rset_ranking->XPROVEEDOR,$idservicio,$rset_ranking->POR,$rset_ranking->INTERNO,'$rset_ranking->LOCALFORANEO',$rset_ranking->DISTANCIA)";
			    }
			  elseif($opcion=='MANUAL'){
			      
			      //echo "<script language='javascript'>alert($idservicio)</script>";
			     // if($idservicio==""){ $idservicio =0; }else { $idservicio = $idservicio;}
			      $sql_insert_ranking="INSERT INTO $tablatmp (IDASISTENCIA,IDPROVEEDOR,IDSERVICIO,RANK,INTERNO,LOCALFORANEO,DISTANCIA) 
			      VALUES ($idasistencia,$rset_ranking->XPROVEEDOR,$idservicio,$rset_ranking->POR,$rset_ranking->INTERNO,'$rset_ranking->LOCALFORANEO',$rset_ranking->DISTANCIA)";
			   //echo "<script language='javascript'>alert($sql_insert_ranking)</script>";
			  }
			$exec_insert_ranking=$con->query($sql_insert_ranking);

		     }

		  }
	      }

	  }

}

function AlgoritmoProveedores($idasistencia,$opcion,$cveentidad1,$cveentidad2,$cveentidad3,$cveentidad4,$cveentidad5,$cveentidad6,$cveentidad7,$xidservicio,$tablatmp){
if($cveentidad1==''){ $cveentidad1=0; }
if($cveentidad2==''){ $cveentidad2=0; }
  if($cveentidad3==''){ $cveentidad3=0; }

//echo "<script language='javascript'>alert($idasistencia.' '.$opcion.' '.$cveentidad1.' '.$cveentidad2.' '.$cveentidad3.' '.$cveentidad4.' '.$cveentidad5.' '.$cveentidad6.' '.$cveentidad7.' '.$xidservicio.' '.$tablatmp)</script>";
$con = new DB_mysqli();
$prov =  new proveedor();
$ubicacion = new ubigeo();
//$idasistencia=1;
$temporal = $con->temporal;
$catalogo = $con->catalogo;
$trash = $con->trash;
$sql_datos_asistencia = "SELECT IDEXPEDIENTE,IDASISTENCIA, IDSERVICIO FROM $temporal.asistencia
			  WHERE IDASISTENCIA =".$idasistencia;
$exec_datos_asistencia = $con->query($sql_datos_asistencia);

while($rset_datos_asistencia = $exec_datos_asistencia->fetch_object()){
    $idexpediente=$rset_datos_asistencia->IDEXPEDIENTE;
    $idservicio = $rset_datos_asistencia->IDSERVICIO;
}

//MODIFICAR
$sql_ubigeo="SELECT CVEPAIS,CVEENTIDAD1,CVEENTIDAD2,CVEENTIDAD3,CVEENTIDAD4,CVEENTIDAD5,CVEENTIDAD6,LATITUD,LONGITUD FROM $temporal.expediente_ubigeo WHERE IDEXPEDIENTE = $idexpediente";
$exec_ubigeo = $con->query($sql_ubigeo);
if($rset_ubigeo = $exec_ubigeo->fetch_object())
{
  $ubicacion->cvepais=$rset_ubigeo->CVEPAIS;
$ubicacion->cveentidad1=$rset_ubigeo->CVEENTIDAD1;
$ubicacion->cveentidad2=$rset_ubigeo->CVEENTIDAD2;
$ubicacion->cveentidad3=$rset_ubigeo->CVEENTIDAD3;
$ubicacion->cveentidad4=$rset_ubigeo->CVEENTIDAD4;
$ubicacion->cveentidad5=$rset_ubigeo->CVEENTIDAD5;
$ubicacion->cveentidad6=$rset_ubigeo->CVEENTIDAD6;
$ubicacion->cveentidad7=$rset_ubigeo->CVEETNIDAD7;
$ubicacion->latitud=$rset_ubigeo->LATITUD;
$ubicacion->longitud=$rset_ubigeo->LONGITUD;
}


//$idservicio=3;
//echo $idservicio;
//
if($opcion=='AUTO'){
    //$ubicacion->leer($idubigeo);
    $lista_prov = $prov->lista_proveedores($ubicacion,$idservicio);
//echo "<script language='javascript'>alert($idubigeo)</script>";
    $sql_servicio_interno="SELECT PS.IDPROVEEDOR FROM $catalogo.catalogo_proveedor_servicio PS INNER JOIN  $catalogo.catalogo_proveedor P
    ON PS.IDPROVEEDOR = P.IDPROVEEDOR 
    WHERE PS.IDSERVICIO = $idservicio AND P.INTERNO = 1";
}elseif($opcion=='MANUAL'){
//echo "<script language='javascript'>alert($opcion)</script>";
    //$ubicacion->cvepais=$cvepais;

    $ubicacion->cveentidad1=$cveentidad1;
    $ubicacion->cveentidad2=$cveentidad2;
    $ubicacion->cveentidad3=$cveentidad3;
    $ubicacion->cveentidad4=$cveentidad4;
    $ubicacion->cveentidad5=$cveentidad5;
    $ubicacion->cveentidad6=$cveentidad6;
    $ubicacion->cveentidad7=$cveentidad7;
    

    
$sql_servicio_int="SELECT PS.IDPROVEEDOR FROM $catalogo.catalogo_proveedor_servicio PS INNER JOIN  $catalogo.catalogo_proveedor P
ON PS.IDPROVEEDOR = P.IDPROVEEDOR 
WHERE  ";
if($xidservicio==''){
	$xzidservicio=0;
	$where = " PS.IDSERVICIO IN(SELECT IDSERVICIO FROM $catalogo.catalogo_servicio) AND P.INTERNO = 1 GROUP BY PS.IDPROVEEDOR";
    }
    else{
	$xzidservicio = $xidservicio;
	$where = " PS.IDSERVICIO = $xzidservicio AND P.INTERNO = 1";
    }
$sql_servicio_interno = $sql_servicio_int.$where;
$lista_prov = $prov->lista_proveedores($ubicacion,$xzidservicio);
//echo $sql_servicio_interno;

}
 
/*
for($i=0;$i<count($lista_prov);$i++){
  echo "<script language='javascript'>alert($lista_prov[$i])</script>";
}

*/


//echo $sql_servicio_interno;
$exec_servicio_interno = $con->query($sql_servicio_interno);
$nrows_servicio_interno = $exec_servicio_interno->num_rows;
//Si puede ser atendido por tecnicos Internos
//echo "<script language='javascript'>alert($nrows_servicio_interno.$opcion)</script>";
if($nrows_servicio_interno!=0){
    if($opcion=='AUTO'){


	foreach($lista_prov as $idproveedor=>$datos)
	{
	//echo "<script language='javascript'>alert($opcion)</script>";
	    $prov->carga_datos($idproveedor);
	    //
	    //echo $prov->idproveedor;
	      $sql_tecnicos_internos = "SELECT IDPROVEEDOR FROM $catalogo.catalogo_proveedor WHERE IDPROVEEDOR=".$prov->idproveedor." AND INTERNO =1  AND ACTIVO =1";
	      
	      $exec_tecnicos_internos = $con->query($sql_tecnicos_internos);
	      
	      while($rset_tecnicos_internos=$exec_tecnicos_internos->fetch_object()){
		  //$internodisposervicio[] = $rset_tecnicos_internos->IDPROVEEDOR;
		  //  echo "<script language='javascript'>alert($rset_tecnicos_internos->IDPROVEEDOR)</script>";
		  //echo $rset_tecnicos_internos->IDPROVEEDOR.'<BR>';
		  //OBTENEMOS TEAT Y TEC DE LOS PROVEEDORES INTERNOS
		  $sql_dispo_prov="SELECT AP.IDPROVEEDOR,P.NOMBRECOMERCIAL,A.IDASISTENCIA,S.IDSERVICIO,P.INTERNO,S.TECS,MIN(AP.TEAT) TEAT,MAX(IF(AP.TEC='0000-00-00 00:00:00',ADDDATE(AP.TEAT, INTERVAL S.TECS MINUTE),AP.TEC)) TEC FROM
		  $temporal.asistencia_asig_proveedor AP INNER JOIN $catalogo.catalogo_proveedor P
		  ON  AP.IDPROVEEDOR = P.IDPROVEEDOR
		  INNER JOIN $temporal.asistencia A
		  ON AP.IDASISTENCIA = A.IDASISTENCIA
		  INNER JOIN $catalogo.catalogo_servicio S
		  ON A.IDSERVICIO = S.IDSERVICIO WHERE P.IDPROVEEDOR=".$rset_tecnicos_internos->IDPROVEEDOR." AND AP.TEAT >= CURDATE()
		  GROUP BY AP.IDPROVEEDOR";
		  //echo $sql_dispo_prov;
		  $exec_dispo_prov = $con->query($sql_dispo_prov);
		  $nreg_dispo_prov = $exec_dispo_prov->num_rows;
		
		  if($nreg_dispo_prov==0){
		    
		      $arrayProvInt[] = $rset_tecnicos_internos->IDPROVEEDOR;
		      //echo "<script language='javascript'>alert($rset_tecnicos_internos->IDPROVEEDOR)</script>";
		      //echo $rset_tecnicos_internos->IDPROVEEDOR;
		  }else{
		      //echo $rset_tecnicos_internos->IDPROVEEDOR;
		      while($rset_dispo_prov=$exec_dispo_prov->fetch_object()){
		      //VERIFICAMOS SI EXISTEN TECNICOS DISPONIBLES EN LAS HORAS ASIGANDAS POR EL AFILIADO
			    //echo $rset_dispo_prov->IDPROVEEDOR.' '.$rset_dispo_prov->TEAT.' '.$rset_dispo_prov->TEC.'<BR>';
			    //echo $rset_dispo_prov->IDPROVEEDOR;
			    
			    $sql_dispo_afil="SELECT FECHAHORA FROM $temporal.asistencia_disponibilidad_afiliado
					WHERE IDASISTENCIA =".$idasistencia." AND FECHAHORA NOT BETWEEN 
					DATE_FORMAT('".$rset_dispo_prov->TEAT."','%Y-%m-%d %H') AND  DATE_FORMAT('".$rset_dispo_prov->TEC."','%Y-%m-%d %H')
					AND FECHAHORA >= DATE_FORMAT('".$rset_dispo_prov->TEAT."','%Y-%m-%d') 
					-- AND FECHAHORA <= DATE_FORMAT('".$rset_dispo_prov->TEC."','%Y-%m-%d %H')";
			    // echo $sql_dispo_afil.'<BR>';
			    $exec_dispo_afil=$con->query($sql_dispo_afil);
			    $nreg_dispo_afil =$exec_dispo_afil->num_rows;
			    //echo $rset_tecnicos_internos->IDPROVEEDOR.' '.$nreg_dispo_afil;
			      //echo "<script language='javascript'>alert($nreg_dispo_afil)</script>";
			    if($nreg_dispo_afil!=0){
				//echo $rset_tecnicos_internos->IDPROVEEDOR;
				
				$arrayProvInt[] = $rset_tecnicos_internos->IDPROVEEDOR;
			    }else{
			      $arrayProvInt[0]=0;
			      }
			      
			    
		      }
		  
		  }
	    }
	}
    }elseif($opcion=='MANUAL'){

      foreach ($lista_prov as $idproveedor=>$datos)
	{
	$prov->carga_datos($idproveedor);
	    //echo $prov->idproveedor;
	      $sql_tecnicos_internos = "SELECT IDPROVEEDOR FROM $catalogo.catalogo_proveedor WHERE IDPROVEEDOR=".$prov->idproveedor." AND INTERNO =1  AND ACTIVO =1";

	      $exec_tecnicos_internos = $con->query($sql_tecnicos_internos);
	      while($rset_tecnicos_internos=$exec_tecnicos_internos->fetch_object()){
		  $arrayProvInt[] = $rset_tecnicos_internos->IDPROVEEDOR;
	      }
      }
    }
/*
for($i =0;$i<count($arrayProvInt);$i++){
    echo "<script language='javascript'>alert($arrayProvInt[$i])</script>";
  }
*/

      //Para tecnicos Externos
 	 foreach ($lista_prov as $idproveedor=>$datos)
	 {
	    $prov->carga_datos($idproveedor);
	   // echo $prov->idproveedor.' '.$datos[AMBITO];
	    
	    $sql_tecnicos_externos1 = "SELECT IDPROVEEDOR FROM $catalogo.catalogo_proveedor WHERE IDPROVEEDOR=".$prov->idproveedor." AND INTERNO =0 AND ACTIVO = 1";
	    //echo $sql_tecnicos_externos1;
//echo "<script language='javascript'>alert($sql_tecnicos_externos1)</script>";
	    $exec_tecnicos_externos1 = $con->query($sql_tecnicos_externos1);
	    $nreg_tecnicos_externos1 = $exec_tecnicos_externos1->num_rows;
	    if($nreg_tecnicos_externos1==0){
	       $arrayProvExt1[] =0;
	    }
	      else{
		while($rset_tecnicos_externos1 = $exec_tecnicos_externos1->fetch_object()){
		//echo $rset_tecnicos_externos1->IDPROVEEDOR;
		
		      $arrayProvExt1[] = $rset_tecnicos_externos1->IDPROVEEDOR;
		  }
	    }
	    //EJECUTAMOS RANKING
	    
	    
	}
/*
for($ef =0;$ef<count($arrayProvInt);$ef++){
      echo "<script language='javascript'>alert($arrayProvInt[$ef])</script>";
}
for($e =0;$e<count($arrayProvExt1);$e++){
     echo $arrayProvExt1[$e];
}*/
//Unimos los dos arrays Intenos y externos
  if(count($arrayProvExt1)==0){ $arrayProvExt1[]=0; };
  if(count($arrayProvInt)==0){ $arrayProvInt[]=0; };
	    $arrayProvRank1 = array_merge($arrayProvExt1, $arrayProvInt);

	
	    $nregarray = count($arrayProvRank1);
	    //$arrayProvRankunique1 = array_unique($arrayProvRank1);
/*for($e =0;$e<count($arrayProvRank1);$e++){
    echo $arrayProvRank1[$e];
}*/
/*foreach($arrayProvRank1 as $idproveedor=>$datos){
      $prov->carga_datos($idproveedor);
      echo $idprovedor;
}*/

	   for($ex =0;$ex<count($arrayProvRank1);$ex++){
		$prov->carga_datos($arrayProvRank1[$ex]);
		//echo "<script language='javascript'>alert($prov[DISTANCIA])</script>";
		
		  //echo "<script language='javascript'>alert($datos[DISTANCIA])</script>";
		  $arrayProvRank[] = ARRAY($arrayProvRank1[$ex],$datos[AMBITO],$datos[DISTANCIA]);
		
		//echo $arrayProvRank1[$ex].' - '.$prov->idproveedor.' - '.$datos[AMBITO].' '.$datos[DISTANCIA].'<br>';
	    }
	    

      if($opcion=='AUTO'){
	      Ranking($idservicio,$arrayProvRank,$idasistencia,$opcion,$tablatmp);
      }
      elseif($opcion=='MANUAL'){
	    Ranking($xzidservicio,$arrayProvRank,$idasistencia,$opcion,$tablatmp);
	}
	    /*if($nregarray!='null'){
		for($i =0;$i<count($arrayProvInt);$i++){
		    echo $arrayProvInt[$i].'<br>';
		}
	    }
	    else{
		echo 'No hay tecnicos Internos';
	    }*/
}else{
      //echo 'No puede ser atendido por tecnicos Internos'.'<br>';
	 foreach ($lista_prov as $idproveedor=>$datos)
	 {
	    $prov->carga_datos($idproveedor);
	    //echo $prov->idproveedor;
	    $sql_tecnicos_externos = "SELECT IDPROVEEDOR FROM $catalogo.catalogo_proveedor WHERE IDPROVEEDOR=".$prov->idproveedor." AND INTERNO =0 AND ACTIVO = 1";
	   // echo $sql_tecnicos_externos;
	  
	    $exec_tecnicos_externos = $con->query($sql_tecnicos_externos);
	    while($rset_tecnicos_externos = $exec_tecnicos_externos->fetch_object()){
		//echo $rset_tecnicos_externos->IDPROVEEDOR;
		//echo "<script language='javascript'>alert($rset_tecnicos_externos->IDPROVEEDOR)</script>";
		$arrayProvRank[] = ARRAY($rset_tecnicos_externos->IDPROVEEDOR,$datos[AMBITO],$datos[DISTANCIA]);
	    }
	    //EJECUTAMOS RANKING
	    
	    
      }
      /*for($ex =0;$ex<count($arrayProvRank);$ex++){
		$prov->carga_datos($arrayProvRank[$ex]);
		echo $arrayProvRank[$ex].' - '.$prov->idproveedor.' - '.$datos[$ex][AMBITO].' '.$datos[$ex][DISTANCIA].'<br>';
	}*/
      if($opcion=='AUTO'){
	Ranking($idservicio,$arrayProvRank,$idasistencia,$opcion,$tablatmp);
      }
      elseif($opcion=='MANUAL'){
	    Ranking($xzidservicio,$arrayProvRank,$idasistencia,$opcion,$tablatmp);
	}
}
	  /*FOR($r=0;$r<count($arrayProvExt);$r++){
		  echo $arrayProvExt[$r];
	     }*/
/*for($h=0;$h<count($arrayProvExtranking);$h++){
  echo 'prov '.$arrayProvExtranking[$h];
}*/

 if($opcion=='AUTO'){
	
//	while($rset_consulta_ranking = $exec_consulta_ranking->fetch_object())
//	{
	    foreach ($lista_prov as $idproveedor=>$datos)
	    {
	      $consulta_ranking="SELECT IDPROVEEDOR FROM $temporal.asistencia_ranking WHERE IDASISTENCIA = $idasistencia
	      AND IDPROVEEDOR = $idproveedor";
	//echo "<script language='javascript'>alert($consulta_ranking)</script>";
	      $exec_consulta_ranking = $con->query($consulta_ranking);
	      if($rset_consulta_ranking= $exec_consulta_ranking->fetch_object()){
		$prov->carga_datos($rset_consulta_ranking->IDPROVEEDOR);
		$sql_update_ranking="UPDATE $temporal.asistencia_ranking SET LOCALFORANEO = '$datos[AMBITO]', DISTANCIA = '$datos[DISTANCIA]'
		WHERE IDASISTENCIA = $idasistencia AND IDPROVEEDOR = $idproveedor";
		 $exec_update_ranking=$con->query($sql_update_ranking);
		//echo "<script language='javascript'>alert($rset_consulta_ranking->IDPROVEEDOR.$datos[DISTANCIA])</script>";
	      }
		//echo "<script language='javascript'>alert($rset_consulta_ranking->IDPROVEEDOR.$datos[DISTANCIA])</script>";
	    }
}
elseif($opcion=='MANUAL'){
      foreach ($lista_prov as $idproveedor=>$datos)
	    {
	      $consulta_ranking="SELECT IDPROVEEDOR FROM $temporal.$tablatmp WHERE IDASISTENCIA = $idasistencia
	      AND IDPROVEEDOR = $idproveedor";
	//echo "<script language='javascript'>alert($consulta_ranking)</script>";
	      $exec_consulta_ranking = $con->query($consulta_ranking);
	      if($rset_consulta_ranking= $exec_consulta_ranking->fetch_object()){
		$prov->carga_datos($rset_consulta_ranking->IDPROVEEDOR);
		$sql_update_ranking="UPDATE $temporal.$tablatmp SET LOCALFORANEO = '$datos[AMBITO]', DISTANCIA = '$datos[DISTANCIA]'
		WHERE IDASISTENCIA = $idasistencia AND IDPROVEEDOR = $idproveedor";
		 $exec_update_ranking=$con->query($sql_update_ranking);
		//echo "<script language='javascript'>alert($rset_consulta_ranking->IDPROVEEDOR.$datos[DISTANCIA])</script>";
	      }
		//echo "<script language='javascript'>alert($rset_consulta_ranking->IDPROVEEDOR.$datos[DISTANCIA])</script>";
	    }
}
//	}
	
				    



}
?>