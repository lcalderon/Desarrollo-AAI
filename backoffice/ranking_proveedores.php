<?php
/*
include_once('../app/modelo/clase_mysqli.inc.php');
include_once('../app/modelo/functions.php');
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');
include_once('../../modelo/clase_ubigeo.inc.php');
include_once('../../modelo/clase_poligono.inc.php');
include_once('../../modelo/clase_circulo.inc.php');
*/
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
?>