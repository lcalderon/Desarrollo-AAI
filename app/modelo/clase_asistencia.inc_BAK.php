<?
class asistencia extends DB_mysqli{
	var $idasistencia;
	var $expediente;
	var $familia;
	var $servicio;
	var $lugardelevento;
	var $arrstatusasistencia; // CM / CANCELADO AL MOMENTO CP / CANCELADO POSTIOR CON / CONCLUIDO PRO / EN PROCESO
	var $arrprioridadatencion; // EME / EMERGENCIA PRO / PROGRAMADO
	var $arrcondicionservicio; 	 // CON / CONEXION STA / STANDAR ADI / ADICIONAL COR / CORTESIA
	var $reembolso;
	var $arrambito;
	var $reportado;
	var $garantia_rel;

	var $etapa;
	var $proveedores;
	var $bitacora;
	var $bitacora2;
	var $fechaminima;
	var $fechamaxima;
	var $fechahora;
	var $evalencuesta;
	var $evalauditoria;
	var $statuscalidad;
	// datos de ubigeo

	var $cvepais;
	var $cveentidad1;
	var $cveentidad2;
	var $cveentidad3;
	var $cveentidad4;
	var $cveentidad5;
	var $cveentidad6;
	var $cveentidad7;
	var $cvetipovia;
	var $descripcion;
	var $direccion;
	var $codpostal;
	var $numero;
	var $latitud;
	var $longitud;
	var $referencia1;
	var $referencia2;
	var $disponibilidad_afiliado;
	var $asistencia_familia;
	var $asistencia_servicio;
	var $costos;
	var $statusautorizaciondesvio ;
	var $statusnegociacionposterior;
	var $idusuarioautorizadesvio;
	var $fechaautorizadesvio;

	var $fechaasignacion;
	var $fechaconcluido;
	var $fechaarribo;
	var $fechacontacto;

	var $monitoreo_demora;
	var $llamadasatisfaccion;
	var $proveedor_ac;
	/*
	metodo Constructor

	*/

	function carga_datos($idasistencia){
		$sql="
		SELECT 
		*
		FROM
			$this->temporal.asistencia a
		WHERE
			a.IDASISTENCIA='$idasistencia'
		";

		$result = $this->query($sql);
		while ($reg = $result->fetch_object()) {
			$this->idasistencia=$reg->IDASISTENCIA;
			$this->expediente =  new expediente();
			$this->expediente->carga_datos($reg->IDEXPEDIENTE);
			$this->familia = new familia();
			$this->familia->carga_datos($reg->IDFAMILIA);
			$this->idprogramaservicio = $reg->IDPROGRAMASERVICIO;
			$this->servicio = new servicio();
			$this->servicio->carga_datos($reg->IDSERVICIO);
			$this->lugardelevento = new ubigeo();
			$this->lugardelevento->leer('ID',$this->temporal,'asistencia_lugardelevento',$reg->IDLUGARDELEVENTO);
			$this->arrstatusasistencia= $reg->ARRSTATUSASISTENCIA;
			$this->arrprioridadatencion= $reg->ARRPRIORIDADATENCION;
			$this->arrcondicionservicio= $reg->ARRCONDICIONSERVICIO;
			$this->evalencuesta= $reg->EVALENCUESTA;
			$this->evalauditoria= $reg->EVALAUDITORIA;
			$this->statuscalidad= $reg->STATUSCALIDAD;
			$this->etapa= new etapa();
			$this->etapa->carga_datos($reg->IDETAPA);
			$this->reembolso = $reg->REEMBOLSO;
			$this->arrambito = $reg->ARRAMBITO;
			$this->reportado = $reg->REPORTADO;
			$this->garantia_rel = $reg->GARANTIA_REL;
			$this->desviacion =$reg->DESVIACION;
			$this->statusautorizaciondesvio = $reg->STATUSAUTORIZACIONDESVIO;
			$this->statusnegociacionposterior = $reg->STATUSNEGOCIACIONPOSTERIOR;
			$this->idusuarioautorizaciondesvio = $reg->IDUSUARIOAUTORIZACIONDESVIO;
			$this->fechaautorizadesvio = $reg->FECHAAUTORIZADESVIO;
			$this->arrstatusencuesta = $reg->ARRSTATUSENCUESTA;
		}

		$sql="
			SELECT 
				* 
			FROM 
				$this->temporal.asistencia_costo 
			WHERE 
				IDASISTENCIA='$idasistencia'
			";
		$result= $this->query($sql);

		while ($reg= $result->fetch_object()){
			$this->costos[$reg->IDMONEDA]=array(
			'AA_TOTALESTIMADO'=>$reg->AA_TOTALESTIMADO,
			'NA_TOTALESTIMADO'=>$reg->NA_TOTALESTIMADO,
			'CC_TOTALESTIMADO'=>$reg->CC_TOTALESTIMADO,
			'IMPORTEESTIMADO'=>$reg->IMPORTEESTIMADO,
			'AA_TOTALREAL'=>$reg->AA_TOTALREAL,
			'NA_TOTALREAL'=>$reg->NA_TOTALREAL,
			'CC_TOTALREAL'=>$reg->CC_TOTALREAL,
			'COSTOFIJO'=>$reg->COSTOFIJO,
			'IMPORTEREAL'=>$reg->IMPORTEREAL,
			'AA_TOTALAUTORIZADO'=>$reg->AA_TOTALAUTORIZADO,
			'NA_TOTALAUTORIZADO'=>$reg->NA_TOTALAUTORIZADO,
			'CC_TOTALAUTORIZADO'=>$reg->CC_TOTALAUTORIZADO,
			'IMPORTEAUTORIZADO'=>$reg->IMPORTEAUTORIZADO
			);
		}

		//  carga de proveedores asignados//
		$sql="
		SELECT
			*
		FROM
			$this->temporal.asistencia_asig_proveedor
		WHERE
			idasistencia='$idasistencia'
		order by STATUSPROVEEDOR ASC, FECHAASIGNACION DESC
			";
		$result = $this->query($sql);

		$i=0;
		$this->proveedor_ac=0;
		while ($reg = $result->fetch_object()) {
			$this->proveedores[$i] = array
			(
			'idasigprov'=>$reg->IDASIGPROV,
			'idproveedor'=>$reg->IDPROVEEDOR,
			'idcontacto'=>$reg->IDCONTACTO,
			'statusproveedor'=>$reg->STATUSPROVEEDOR,
			'fechahora'=>$reg->FECHAASIGNACION,
			'fechaconcluido'=>$reg->FECHACONCLUIDO,
			'fechaarribo'=>$reg->FECHAARRIBO,
			'fechacontacto'=>$reg->FECHACONTACTO,
			'arrprioridadatencion'=>$reg->ARRPRIORIDADATENCION,
			'teat'=>$reg->TEAT,
			'team'=>$reg->TEAM,
			'localforaneo'=>$reg->LOCALFORANEO,
			'aplicanocturno'=>$reg->APLICANOCTURNO,
			'aplicafestivo'=>$reg->APLICAFESTIVO,
			'statusproveedor'=>$reg->STATUSPROVEEDOR,
			'aa_totalestimado'=>$reg->AA_TOTALESTIMADO,
			'na_totalestimado'=>$reg->NA_TOTALESTIMADO,
			'cc_totalestimado'=>$reg->CC_TOTALESTIMADO,
			'aa_totalreal'=>$reg->AA_TOTALREAL,
			'na_totalreal'=>$reg->NA_TOTALREAL,
			'cc_totalreal'=>$reg->CC_TOTALREAL,
			'aa_totalautorizado'=>$reg->AA_TOTALAUTORIZADO,
			'na_totalautorizado'=>$reg->NA_TOTALAUTORIZADO,
			'cc_totalautorizado'=>$reg->CC_TOTALAUTORIZADO,
			'importeestimado'=>$reg->IMPORTEESTIMADO,
			'importereal'=>$reg->IMPORTEREAL,
			'importeautorizado'=>$reg->IMPORTEAUTORIZADO,
			'idmoneda'=>$reg->IDMONEDA,
			'desviacion'=>$reg->DESVIACION,
			'statusautorizaciondesvio'=>$reg->STATUSAUTORIZACIONDESVIO,
			'idusuarioautorizaciondesvio'=>$reg->IDUSUARIOAUTORIZACIONDESVIO,
			'fechaautorizaciondesvio'=>$reg->FECHAAUTORIZACIONDESVIO,
			'statusnegociacionposterior'=>$reg->STATUSNEGOCIACIONPOSTERIOR
			);
			if ($reg->STATUSPROVEEDOR=='AC') $this->proveedor_ac=1;

			$i++;
		}


		/* datos de las asistencias de familia */

		$tabla_familia =strtolower($this->familia->descripcion);
		$sql="select * from $this->temporal.asistencia_".$tabla_familia." where IDASISTENCIA ='$idasistencia'";

		$result = $this->query($sql);
		while ($reg = $result->fetch_object()){
			foreach ($reg as $campo=>$indice)
			{
				$this->asistencia_familia->{$campo} = $reg->{$campo};
			}
		}


		/* datos de las asistencias del servicio */

		$tabla_servicio = $tabla_familia.'_'.substr(strtolower($this->servicio->plantilla->vista),0,-4);
		$sql="select * from $this->temporal.asistencia_".$tabla_servicio." where IDASISTENCIA ='$idasistencia'";

		$result = $this->query($sql);
		while ($reg = $result->fetch_object()){
			foreach ($reg as $campo=>$indice)
			{
				$this->asistencia_servicio->{$campo} = $reg->{$campo};
			}
		}

		/* Disponibilidad del afiliado*/
		$sql="select * from $this->temporal.asistencia_disponibilidad_afiliado where IDASISTENCIA ='$idasistencia' ORDER BY FECHAMOD DESC";
		$result = $this->query($sql);
		$i=0;
		while ($reg = $result->fetch_object()) {
			$this->disponibilidad_afiliado[$i] = array('fechaini'=>$reg->FECHAINI,
			'fechafin'=>$reg->FECHAFIN,
			'id'=>$reg->ID
			);
			$i++;
		}


		/* Llamada de satisfaciion*/
		$sql="
			SELECT 
				ARRCLASIFICACION
			FROM  
				$this->temporal.asistencia_bitacora_etapa8 
			WHERE 
				IDASISTENCIA ='$idasistencia'
				AND ARRCLASIFICACION='LLCNF'
 				LIMIT 1
 			";

		$result = $this->query($sql);
		if ($result->num_rows) $this->llamadasatisfaccion=1;
		else $this->llamadasatisfaccion=0;


		/* monitoreo demora*/
		$sql="
			SELECT 
				ARRCLASIFICACION
			FROM  
				$this->temporal.asistencia_bitacora_etapa2
			WHERE 
				IDASISTENCIA ='$idasistencia'
				AND ARRCLASIFICACION='DEM_ASIG'
 				LIMIT 1
 			";

		$result = $this->query($sql);
		if ($result->num_rows) $this->monitoreo_demora=1;
		else $this->monitoreo_demora=0;


		return;
	}




	function graba_asist_asig_prov_costo($datos)
	{
		if ($datos[MODO]=='AUTORIZA')
		{
			foreach ($datos[IDCOSTO] as $indice =>$idcosto){
				if($datos[CHECK][$indice])
				{
					$reg_asistencia[IDASIGPROV]=$datos[IDASIGPROV];
					$reg_asistencia[IDCOSTO]=$datos[IDCOSTO][$indice];
					$reg_asistencia[AA_MONTOAUTORIZADO] =$datos[AA_MONTOAUTORIZADO][$indice];
					$reg_asistencia[NA_MONTOAUTORIZADO] =$datos[NA_MONTOAUTORIZADO][$indice];
					$reg_asistencia[CC_MONTOAUTORIZADO] =$datos[CC_MONTOAUTORIZADO][$indice];
					$reg_asistencia[IDUSUARIOAUTORIZAMONTO] = $datos[IDUSUARIOMOD];
					$reg_asistencia[FECHAAUTORIZACION] = 'CURRENT_TIMESTAMP()';
					$this->insert_update("$this->temporal.asistencia_asig_proveedor_costo",$reg_asistencia)	;
				}
				else{
					$reg_asistencia[IDASIGPROV]=$datos[IDASIGPROV];
					$reg_asistencia[IDCOSTO]=$datos[IDCOSTO][$indice];
					$reg_asistencia[AA_MONTOAUTORIZADO] =0;
					$reg_asistencia[NA_MONTOAUTORIZADO] =0;
					$reg_asistencia[CC_MONTOAUTORIZADO] =0;
					$reg_asistencia[IDUSUARIOAUTORIZAMONTO] = $datos[IDUSUARIOMOD];
					$reg_asistencia[FECHAAUTORIZACION] = 'CURRENT_TIMESTAMP()';
					$this->update("$this->temporal.asistencia_asig_proveedor_costo",$reg_asistencia," WHERE IDASIGPROV='$reg_asistencia[IDASIGPROV]' AND IDCOSTO='$reg_asistencia[IDCOSTO]'")	;
				}
			}

			$reg_asig_prov[IDASIGPROV] = $datos[IDASIGPROV];
			//			$reg_asig_prov[IDMONEDA] =$datos[IDMONEDA];
			$reg_asig_prov[AA_TOTALAUTORIZADO] = $datos[AA_TOTALAUTORIZADO];
			$reg_asig_prov[NA_TOTALAUTORIZADO] = $datos[NA_TOTALAUTORIZADO];
			$reg_asig_prov[CC_TOTALAUTORIZADO] = $datos[CC_TOTALAUTORIZADO];
			$reg_asig_prov[STATUSAUTORIZACIONDESVIO] = 1;
			$reg_asig_prov[IDUSUARIOAUTORIZACIONDESVIO]=$datos[IDUSUARIOMOD];
			$reg_asig_prov[FECHAAUTORIZACIONDESVIO] = 'CURRENT_TIMESTAMP()';
			$reg_asig_prov[IMPORTEAUTORIZADO] = $datos[IMPORTEAUTORIZADO];
			$reg_asig_prov[OBSERVACION] = $datos[OBSERVACION];

			$this->update("$this->temporal.asistencia_asig_proveedor",$reg_asig_prov, "  where IDASIGPROV ='$reg_asig_prov[IDASIGPROV]'");


		}
		else
		{
			$sql="DELETE FROM $this->temporal.asistencia_asig_proveedor_costo WHERE IDASIGPROV= '$datos[IDASIGPROV]'";
			$this->query($sql);
			$reg_asistencia[IDASIGPROV] = $datos[IDASIGPROV];

			foreach ($datos[IDCOSTO] as $indice =>$idcosto){
				if ($datos[CHECK][$indice]){
					$reg_asistencia[IDCOSTO] = $datos[IDCOSTO][$indice];
					$reg_asistencia[UNIDAD] = $datos[UNIDAD][$indice];

					$reg_asistencia[AA_MONTOESTIMADO] =$datos[AA_MONTOESTIMADO][$indice];
					$reg_asistencia[NA_MONTOESTIMADO] =$datos[NA_MONTOESTIMADO][$indice];
					$reg_asistencia[CC_MONTOESTIMADO] =$datos[CC_MONTOESTIMADO][$indice];

					$reg_asistencia[AA_MONTOREAL] =$datos[AA_MONTOREAL][$indice];
					$reg_asistencia[NA_MONTOREAL] =$datos[NA_MONTOREAL][$indice];
					$reg_asistencia[CC_MONTOREAL] =$datos[CC_MONTOREAL][$indice];

					$reg_asistencia[AA_MONTOAUTORIZADO] =$datos[AA_MONTOREAL][$indice];
					$reg_asistencia[NA_MONTOAUTORIZADO] =$datos[NA_MONTOREAL][$indice];
					$reg_asistencia[CC_MONTOAUTORIZADO] =$datos[CC_MONTOREAL][$indice];

					$reg_asistencia[JUSTIFICACION] = $datos[JUSTIFICACION][$indice];
					$reg_asistencia[IDUSUARIOMOD] = $datos[IDUSUARIOMOD];
					$this->insert_reg("$this->temporal.asistencia_asig_proveedor_costo",$reg_asistencia)	;
				}
			}

			$reg_asig_prov[IDASIGPROV] = $datos[IDASIGPROV];
			//			$reg_asig_prov[IDMONEDA] =$datos[IDMONEDA];
			$reg_asig_prov[AA_TOTALESTIMADO] = $datos[AA_TOTALESTIMADO];
			$reg_asig_prov[NA_TOTALESTIMADO] = $datos[NA_TOTALESTIMADO];
			$reg_asig_prov[CC_TOTALESTIMADO] = $datos[CC_TOTALESTIMADO];
			$reg_asig_prov[AA_TOTALREAL] = $datos[AA_TOTALREAL];
			$reg_asig_prov[NA_TOTALREAL] = $datos[NA_TOTALREAL];
			$reg_asig_prov[CC_TOTALREAL] = $datos[CC_TOTALREAL];

			$reg_asig_prov[IMPORTEESTIMADO] = $datos[IMPORTEESTIMADO];
			$reg_asig_prov[COSTOFIJO] = $datos[COSTOFIJO];
			$reg_asig_prov[IMPORTEREAL] = $datos[IMPORTEREAL];
			$reg_asig_prov[IDMONEDA] =$datos[IDMONEDA];
			$reg_asig_prov[OBSERVACION] = $datos[OBSERVACION];
			$reg_asig_prov[DESVIACION] = (($datos[COSTOFIJO]>$datos[IMPORTEESTIMADO]) && ($datos[AA_TOTALREAL] >0)) ?1:0;
			$reg_asig_prov[STATUSAUTORIZACIONDESVIO]= (($datos[COSTOFIJO]>$datos[IMPORTEESTIMADO])&& ($datos[AA_TOTALREAL] >0))?0:1;
			$reg_asig_prov[STATUSNEGOCIACIONPOSTERIOR]=($datos[STATUSNEGOCIACIONPOSTERIOR]=='on')?1:0;
			$this->update("$this->temporal.asistencia_asig_proveedor",$reg_asig_prov, "  where IDASIGPROV ='$reg_asig_prov[IDASIGPROV]'");
		}
		return;
	}

	function act_asistencia_costo($idasistencia){
		$sql="DELETE FROM $this->temporal.asistencia_costo WHERE IDASISTENCIA='$idasistencia';";
		$this->query($sql);
		$sql="
				
		INSERT INTO $this->temporal.asistencia_costo 
		(IDASISTENCIA,IDMONEDA,
		AA_TOTALESTIMADO,NA_TOTALESTIMADO,CC_TOTALESTIMADO,IMPORTEESTIMADO,
		AA_TOTALREAL,NA_TOTALREAL,CC_TOTALREAL,COSTOFIJO,IMPORTEREAL,
		AA_TOTALAUTORIZADO,NA_TOTALAUTORIZADO,CC_TOTALAUTORIZADO,IMPORTEAUTORIZADO)
 		(SELECT  
 			 IDASISTENCIA,IDMONEDA, 
 			 SUM(AA_TOTALESTIMADO),SUM(NA_TOTALESTIMADO),SUM(CC_TOTALESTIMADO),SUM(IMPORTEESTIMADO), 
 			 SUM(AA_TOTALREAL),SUM(NA_TOTALREAL), SUM(CC_TOTALREAL), SUM(COSTOFIJO), SUM(IMPORTEREAL),
 			 SUM(AA_TOTALAUTORIZADO),SUM(NA_TOTALAUTORIZADO),SUM(CC_TOTALAUTORIZADO), SUM(IMPORTEAUTORIZADO)
 			 FROM $this->temporal.asistencia_asig_proveedor
 			 WHERE idasistencia='$idasistencia' 
 			 GROUP BY IDASISTENCIA,IDMONEDA
 		 )
 		;
		";

		$this->query($sql);
		$this->graba_desvio($idasistencia);
		$this->graba_negociacionposterior($idasistencia);
		return;
	}

	function graba_desvio($idasistencia){
		$sql="
		SELECT 
			IMPORTEESTIMADO,COSTOFIJO,IMPORTEREAL,STATUSNEGOCIACIONPOSTERIOR,AA_TOTALREAL
		FROM  
			$this->temporal.asistencia_asig_proveedor 
		WHERE 
			IDASISTENCIA='$idasistencia'
		";
		$result = $this->query($sql);
		$sw=0;
		while ($reg=$result->fetch_object())
		{
			if ( ($reg->IMPORTEESTIMADO < $reg->COSTOFIJO && $reg->AA_TOTALREAL >0 && $reg->IMPORTEESTIMADO >0 ) OR ($reg->STATUSNEGOCIACIONPOSTERIOR) ) $sw=1;
		}
		$sql="UPDATE $this->temporal.asistencia SET DESVIACION='$sw' where IDASISTENCIA='$idasistencia'";
		$this->query($sql);
		return;
	}

	function graba_negociacionposterior($idasistencia){
		$sql="SELECT STATUSNEGOCIACIONPOSTERIOR FROM  $this->temporal.asistencia_asig_proveedor WHERE IDASISTENCIA='$idasistencia' AND STATUSNEGOCIACIONPOSTERIOR=1";
		$result = $this->query($sql);
		$sw=0;
		if ($result->num_rows) $sw=1;
		$sql="UPDATE $this->temporal.asistencia SET STATUSNEGOCIACIONPOSTERIOR='$sw' where IDASISTENCIA='$idasistencia'";
		$this->query($sql);
		return;
	}


	function disponibilidad($idasistencia){
		$sql = "SELECT MIN(FECHAHORA) FECHAMINIMA,MAX(FECHAHORA) FECHAMAXIMA
			FROM $this->temporal.asistencia_disponibilidad_afiliado WHERE IDASISTENCIA = '$idasistencia'
			  GROUP BY DAY(FECHAHORA)";
		$result = $this->query($sql);
		while ($reg = $result->fetch_object()) {
			$this->fecha[]=array($reg->FECHAMINIMA,$reg->FECHAMAXIMA);
			//$this->fechamaxima=$reg->FECHAMAXIMA;
		}
		return;
	}

	function carga_bitacora($idasistencia,$idetapa){

		switch ($idetapa){
			case 1:
			case 3:
			case 5:
			case 8:
				{
					$sql="SELECT
		abe.ARRCLASIFICACION,
		abe.FECHAMOD,
		abe.IDUSUARIOMOD,
		abe.COMENTARIO
		FROM 
			$this->temporal.asistencia_bitacora_etapa$idetapa abe
		WHERE abe.IDASISTENCIA='$idasistencia'  
		ORDER BY abe.FECHAMOD DESC";
					break;
				}

			case 2:
			case 4:
			case 6:
			case 7:
				{
					$sql="SELECT
		abe.ARRCLASIFICACION,
		abe.FECHAMOD,
		abe.IDUSUARIOMOD,
		abe.COMENTARIO,
		cp.NOMBRECOMERCIAL
		FROM 
			$this->temporal.asistencia_bitacora_etapa$idetapa abe
			LEFT JOIN  $this->catalogo.catalogo_proveedor cp ON cp.IDPROVEEDOR = abe.IDPROVEEDOR 
		WHERE abe.IDASISTENCIA='$idasistencia'  
		ORDER BY abe.FECHAMOD DESC";
					break;
				}
		} //fin del switch
		//		echo $sql;
		$result = $this->query($sql);
		$i=0;
		while($reg = $result->fetch_object()){
			$this->bitacora[$i++] = array(
			'arrclasificacion' =>$reg->ARRCLASIFICACION,
			'fechamod'=>$reg->FECHAMOD,
			'fechamanual'=>$reg->FECHAMANUAL,
			'idusuariomod'=>$reg->IDUSUARIOMOD,
			'comentario'=>$reg->COMENTARIO,
			'nom_proveedor'=>$reg->NOMBRECOMERCIAL
			);
		}
		return;
	}

	function carga_bitacora_etapa($idasistencia,$idetapa){
		switch($idetapa){
			case 2: $sql="SELECT AB.FECHAMOD,AB.IDUSUARIOMOD,AB.COMENTARIO,P.NOMBRECOMERCIAL,P.IDPROVEEDOR FROM $this->temporal.asistencia_bitacora_etapa2 AB LEFT JOIN $this->catalogo.catalogo_proveedor P ON AB.IDPROVEEDOR = P.IDPROVEEDOR  WHERE AB.IDASISTENCIA='$idasistencia'   ORDER BY AB.FECHAMOD DESC";break;
			case 3: $sql="SELECT * FROM $this->temporal.asistencia_bitacora_etapa3 WHERE IDASISTENCIA='$idasistencia'   ORDER BY FECHAMOD DESC";break;
			case 4: $sql="SELECT AB.FECHAMOD,AB.IDUSUARIOMOD,AB.COMENTARIO,P.NOMBRECOMERCIAL,P.IDPROVEEDOR FROM $this->temporal.asistencia_bitacora_etapa4 AB LEFT JOIN $this->catalogo.catalogo_proveedor P ON AB.IDPROVEEDOR = P.IDPROVEEDOR  WHERE AB.IDASISTENCIA='$idasistencia'   ORDER BY AB.FECHAMOD DESC";break;
			case 5: $sql="SELECT * FROM $this->temporal.asistencia_bitacora_etapa5 WHERE IDASISTENCIA='$idasistencia'   ORDER BY FECHAMOD DESC";break;
			case 6: $sql="SELECT AB.FECHAMOD,AB.IDUSUARIOMOD,AB.COMENTARIO,P.NOMBRECOMERCIAL,P.IDPROVEEDOR,CASE(AB.STATUSARRCON) WHEN 'ARRENT' THEN 'ARRIBO ENTRANTE' WHEN 'ARRSAL' THEN 'ARRIBO SALIENTE' WHEN 'CONENT' THEN 'CONTACTO ENTRANTE' WHEN 'CONSAL' THEN 'CONTACTO SALIENTE' END STATUSARRCON FROM $this->temporal.asistencia_bitacora_etapa6 AB LEFT JOIN $this->catalogo.catalogo_proveedor P ON AB.IDPROVEEDOR = P.IDPROVEEDOR  WHERE AB.IDASISTENCIA='$idasistencia'   ORDER BY AB.FECHAMOD DESC";break;
			case 7: $sql="SELECT * FROM $this->temporal.asistencia_bitacora_etapa7 WHERE IDASISTENCIA='$idasistencia'  ORDER BY FECHAMOD DESC";break;
			case 8: $sql="SELECT * FROM $this->temporal.asistencia_bitacora_etapa8 WHERE IDASISTENCIA='$idasistencia'   ORDER BY FECHAMOD DESC";break;
		}
		//		echo $sql;
		$result = $this->query($sql);
		while($reg = $result->fetch_object()){
			if($idetapa==6 || $idetapa==2 || $idetapa==4){
				$this->bitacora2[] = array(
				'fechamod'=>$reg->FECHAMOD,
				'status'=>$reg->STATUSARRCON,
				'idusuariomod'=>$reg->IDUSUARIOMOD,
				'proveedor'=>$reg->NOMBRECOMERCIAL,
				'comentario'=>$reg->COMENTARIO
				);
			}else{
				$this->bitacora2[] = array('idetapa'=>$reg->IDETAPA,
				'fechamod'=>$reg->FECHAMOD,
				'idusuariomod'=>$reg->IDUSUARIOMOD,
				'comentario'=>$reg->COMENTARIO
				);
			}
		}
		return;
	}

	function listar_asistencias($idusuario='',$arrstatusexpediente='PRO',$arrstatusasistencia,$idetapas,$idcuentas,$modo='',$statusautorizaciondesvio='0',$annio,$mes,$dia,$hora,$annio2,$mes2,$dia2,$hora2,$idasistencia,$statusnegociacionposterior,$campo,$dato)
	{
		$condicion = "where a.IDEXPEDIENTE = e.IDEXPEDIENTE AND e.IDCUENTA in ($idcuentas)";
		if ($arrstatusexpediente!='') {
			$condicion.=" AND e.ARRSTATUSEXPEDIENTE = '$arrstatusexpediente' ";
			$condicion2.=" AND e.ARRSTATUSEXPEDIENTE = '$arrstatusexpediente' ";
		}

		switch ($campo){
			case 'a.IDEXPEDIENTE':
			case 'a.IDASISTENCIA':
				{
					if ($dato!=''){
						$condicion.=" AND $campo='$dato'  AND e.ARRSTATUSEXPEDIENTE='$arrstatusexpediente' ";
						$condicion2.=" HAVING IDASISTENCIA <>'S/D'";
					}
					break;
				}
			default:
				{
					if ($arrstatusasistencia!='') $condicion.= " AND a.ARRSTATUSASISTENCIA = '$arrstatusasistencia'";
					if ($idusuario!='') {
						$condicion.= " AND a.IDUSUARIORESPONSABLE in ( $idusuario)";
					    $condicion2.= " AND e.IDUSUARIOMOD in ($idusuario) ";
					}
					if ($statusautorizaciondesvio=='1') $condicion.= " AND a.STATUSAUTORIZACIONDESVIO=1";
					if ($annio2=='' &&  $mes2=='' && $dia2=='' )
					{
						if ($annio!=''){
							$condicion.= " AND YEAR(au.FECHAHORA)=$annio ";
							$condicion2.= " AND YEAR(e.FECHAREGISTRO)=$annio ";
						}
						if ($mes!='') {
							$condicion.= " AND MONTH(au.FECHAHORA)=$mes ";
							$condicion2.= " AND MONTH(e.FECHAREGISTRO)=$mes ";
						}
						if ($dia!='') {
							$condicion.= " AND DAY(au.FECHAHORA)=$dia";
							$condicion2.= " AND DAY(e.FECHAREGISTRO)=$dia";
						}
						if ($hora!='') {
							$condicion.= " AND HOUR(au.FECHAHORA)=$hora ";
							$condicion2.= " AND HOUR(e.FECHAREGISTRO)=$hora ";
						}
					}
					else if ($hora2=='' && $hora=='')
					{
						$condicion.=" AND (au.FECHAHORA BETWEEN '$annio-$mes-$dia $hora:00:00' AND '$annio2-$mes2-$dia2 23:59:59' )";
						$condicion2.=" AND (e.FECHAREGISTRO BETWEEN '$annio-$mes-$dia $hora:00:00' AND '$annio2-$mes2-$dia2 23:59:59' )";
					}
					else
					{
						$condicion.=" AND (au.FECHAHORA BETWEEN '$annio-$mes-$dia $hora:00:00' AND '$annio2-$mes2-$dia2 $hora2:59:59' )";
						$condicion2.=" AND (e.FECHAREGISTRO BETWEEN '$annio-$mes-$dia $hora:00:00' AND '$annio2-$mes2-$dia2 $hora2:59:59' )";
					}


					if ($statusnegociacionposterior!='') $condicion.=" AND a.STATUSNEGOCIACIONPOSTERIOR= '$statusnegociacionposterior'";
					if ($statusautorizaciondesvio==0) $condicion.= " AND a.STATUSAUTORIZACIONDESVIO=0 AND a.IDUSUARIOAUTORIZACIONDESVIO=''";
					else $condicion.= " AND a.STATUSAUTORIZACIONDESVIO=1";

					if ($modo=='DESVIO') $condicion.= " AND a.DESVIACION=1";
					else
					$condicion.= " AND a.IDETAPA in ( $idetapas)";

					if ($campo!='' && $dato!='') 	$having.= " HAVING $campo LIKE '%$dato%'";


				} // FIN DEL DEFAULT
		} // FIN DEL SWITCH
		$sql="
(SELECT 
	a.IDASISTENCIA,
	a.IDEXPEDIENTE,
	e.ARRSTATUSEXPEDIENTE,
	a.IDCUENTA,
	cc.NOMBRE NOM_CUENTA,
	a.IDPROGRAMASERVICIO,
	a.IDSERVICIO,
	cs.DESCRIPCION AA_SERVICIO,
	cps.ETIQUETA NOM_SERVICIO,
	a.IDPROGRAMA,
	cp.NOMBRE NOM_PROGRAMA,
	a.ARRSTATUSASISTENCIA,
	a.ARRPRIORIDADATENCION,
	a.ARRCONDICIONSERVICIO,
	a.IDETAPA,
	ce.DESCRIPCION NOM_ETAPA,
	a.IDUSUARIORESPONSABLE,
	a.DESVIACION,
	a.STATUSAUTORIZACIONDESVIO,
	a.IDUSUARIOAUTORIZACIONDESVIO,
	CONCAT(ep.APPATERNO,' ',ep.APMATERNO,' ',ep.NOMBRE) NOM_AFILIADO,
	MIN(au.FECHAHORA) FECHAHORA,
	aap.IDPROVEEDOR,
	cprov.NOMBRECOMERCIAL NOM_PROVEEDOR
FROM
(
$this->temporal.asistencia a,
$this->temporal.expediente e,
$this->catalogo.catalogo_usuario cu
)
LEFT JOIN $this->catalogo.catalogo_cuenta cc ON cc.IDCUENTA = a.IDCUENTA
LEFT JOIN $this->catalogo.catalogo_programa_servicio cps ON cps.IDPROGRAMASERVICIO = a.IDPROGRAMASERVICIO
LEFT JOIN $this->catalogo.catalogo_servicio cs ON cs.IDSERVICIO = a.IDSERVICIO
LEFT JOIN $this->catalogo.catalogo_programa cp ON cp.IDPROGRAMA = a.IDPROGRAMA
LEFT JOIN $this->catalogo.catalogo_etapa ce ON ce.IDETAPA = a.IDETAPA
LEFT JOIN $this->temporal.expediente_persona ep ON ep.IDEXPEDIENTE = a.IDEXPEDIENTE  AND ep.ARRTIPOPERSONA = 'TITULAR'
LEFT JOIN $this->temporal.asistencia_usuario au ON au.IDASISTENCIA = a.IDASISTENCIA AND au.IDETAPA=1
LEFT JOIN $this->temporal.asistencia_asig_proveedor aap ON aap.IDASISTENCIA = a.IDASISTENCIA  
LEFT JOIN $this->catalogo.catalogo_proveedor cprov ON cprov.IDPROVEEDOR = aap.IDPROVEEDOR
$condicion
 AND ( aap.IDPROVEEDOR =(
 SELECT 
aap1.idproveedor
FROM 
$this->temporal.asistencia_asig_proveedor aap1
 WHERE 
 	aap1.IDASISTENCIA =a.IDASISTENCIA
ORDER BY aap1.IDASIGPROV DESC
LIMIT 1 
) 
OR aap.IDPROVEEDOR IS NULL 

)		

GROUP BY a.IDASISTENCIA
$having
)

UNION ALL 
(
SELECT 
'S/D' IDASISTENCIA,
e.IDEXPEDIENTE,
e.ARRSTATUSEXPEDIENTE,
e.IDCUENTA,
cc.NOMBRE NOM_CUENTA,
'S/D' IDPROGRAMASERVICIO,
'S/D' IDSERVICIO,
'S/D' AA_SERVICIO,
'S/D' NOM_SERVICIO, 
'S/D' IDPROGRAMA,
'S/D' NOM_PROGRAMA,
'S/D' ARRSTATUSASISTENCIA,
'S/D' ARRPRIORIDADATENCION,
'S/D' ARRCONDICIONSERVICIO,
'S/D' IDETAPA,
'S/D' NOM_ETAPA,
e.IDUSUARIOMOD IDUSUARIORESPONSABLE,
'S/D' DESVIACION,
'S/D' STATUSAUTORIZACIONDESVIO,
'S/D' IDUSUARIOAUTORIZACIONDESVIO,
CONCAT(ep.APPATERNO,' ',ep.APMATERNO,' ',ep.NOMBRE) NOM_AFILIADO, 
e.FECHAREGISTRO  FECHAHORA,
'S/D' IDPROVEEDOR,
'S/D' NOM_PROVEEDOR
FROM 
$this->temporal.expediente e
LEFT JOIN $this->catalogo.catalogo_cuenta cc ON cc.IDCUENTA = e.IDCUENTA
LEFT JOIN $this->temporal.expediente_persona ep ON ep.IDEXPEDIENTE = e.IDEXPEDIENTE AND ep.ARRTIPOPERSONA = 'TITULAR' 
LEFT JOIN $this->temporal.asistencia a  ON e.IDEXPEDIENTE = a.IDEXPEDIENTE 
WHERE
a.IDEXPEDIENTE IS NULL
AND e.IDCUENTA in ($idcuentas)
$condicion2
$having
)


ORDER BY FECHAHORA DESC
		";
//		echo $sql;
		$result = $this->query($sql);

		return $result;
	}

	function listar_desviaciones($idusuario='',$arrstatusexpediente='PRO',$arrstatusasistencia,$idetapas,$idcuentas,$modo='',$statusautorizaciondesvio='0',$annio,$mes,$dia,$hora,$annio2,$mes2,$dia2,$hora2,$idasistencia,$statusnegociacionposterior)
	{
		$condicion = "where a.IDEXPEDIENTE = e.IDEXPEDIENTE ";
		if ($idasistencia==''){
			if ($arrstatusexpediente!='') $condicion.=" AND e.ARRSTATUSEXPEDIENTE = '$arrstatusexpediente' ";

			if ($arrstatusasistencia!='') $condicion.= " AND a.ARRSTATUSASISTENCIA = '$arrstatusasistencia'";
			if ($idusuario!='') $condicion.= " AND a.IDUSUARIORESPONSABLE in ( $idusuario)";
			if ($statusautorizaciondesvio=='1') $condicion.= " AND a.STATUSAUTORIZACIONDESVIO=1";
			if ($annio2=='' &&  $mes2=='' && $dia2=='' ){
				if ($annio!='')	$condicion.= " AND YEAR(au.FECHAHORA)=$annio ";
				if ($mes!='') 	$condicion.= " AND MONTH(au.FECHAHORA)=$mes ";
				if ($dia!='') 	$condicion.= " AND DAY(au.FECHAHORA)=$dia";
				if ($hora!='') 	$condicion.= " AND HOUR(au.FECHAHORA)=$hora ";

			}
			else if ($hora2=='' && $hora=='')
			$condicion.=" AND (au.FECHAHORA BETWEEN '$annio-$mes-$dia $hora:00:00' AND '$annio2-$mes2-$dia2 23:59:59' )";
			else
			$condicion.=" AND (au.FECHAHORA BETWEEN '$annio-$mes-$dia $hora:00:00' AND '$annio2-$mes2-$dia2 $hora2:59:59' )";

			if ($statusnegociacionposterior!='') $condicion.=" AND a.STATUSNEGOCIACIONPOSTERIOR= '$statusnegociacionposterior'";
			if ($statusautorizaciondesvio==0) $condicion.= " AND a.STATUSAUTORIZACIONDESVIO=0 AND a.IDUSUARIOAUTORIZACIONDESVIO=''";
			else $condicion.= " AND a.STATUSAUTORIZACIONDESVIO=1";

			if ($modo=='DESVIO') $condicion.= " AND a.DESVIACION=1";
			else
			{
				$condicion.= " AND a.IDETAPA in ( $idetapas)";
				$condicion.= " AND e.IDCUENTA in ($idcuentas)";
			}

		}
		else {
			$condicion.=" AND a.IDASISTENCIA='$idasistencia' AND e.IDCUENTA in ($idcuentas) AND e.ARRSTATUSEXPEDIENTE='$arrstatusexpediente' ";

		}
		$sql="
SELECT 
	a.IDASISTENCIA,
	a.IDEXPEDIENTE,
	e.ARRSTATUSEXPEDIENTE,
	a.IDCUENTA,
	cc.NOMBRE NOM_CUENTA,
	a.IDPROGRAMASERVICIO,
	a.IDSERVICIO,
	cs.DESCRIPCION AA_SERVICIO,
	cps.ETIQUETA NOM_SERVICIO,
	a.IDPROGRAMA,
	cp.NOMBRE NOM_PROGRAMA,
	a.ARRSTATUSASISTENCIA,
	a.ARRPRIORIDADATENCION,
	a.ARRCONDICIONSERVICIO,
	a.IDETAPA,
	ce.DESCRIPCION NOM_ETAPA,
	a.IDUSUARIORESPONSABLE,
	a.DESVIACION,
	a.STATUSAUTORIZACIONDESVIO,
	a.IDUSUARIOAUTORIZACIONDESVIO,
	CONCAT(ep.APPATERNO,' ',ep.APMATERNO,' ',ep.NOMBRE) NOM_AFILIADO,
	MIN(au.FECHAHORA) FECHAHORA,
	aap.IDPROVEEDOR
FROM
(
$this->temporal.asistencia a,
$this->temporal.expediente e,
$this->catalogo.catalogo_usuario cu
)
LEFT JOIN $this->catalogo.catalogo_cuenta cc ON cc.IDCUENTA = a.IDCUENTA
LEFT JOIN $this->catalogo.catalogo_programa_servicio cps ON cps.IDPROGRAMASERVICIO = a.IDPROGRAMASERVICIO
LEFT JOIN $this->catalogo.catalogo_servicio cs ON cs.IDSERVICIO = a.IDSERVICIO
LEFT JOIN $this->catalogo.catalogo_programa cp ON cp.IDPROGRAMA = a.IDPROGRAMA
LEFT JOIN $this->catalogo.catalogo_etapa ce ON ce.IDETAPA = a.IDETAPA
LEFT JOIN $this->temporal.expediente_persona ep ON ep.IDEXPEDIENTE = a.IDEXPEDIENTE  AND ep.ARRTIPOPERSONA = 'TITULAR'
LEFT JOIN $this->temporal.asistencia_usuario au ON au.IDASISTENCIA = a.IDASISTENCIA AND au.IDETAPA=1
LEFT JOIN $this->temporal.asistencia_asig_proveedor aap ON aap.IDASISTENCIA = a.IDASISTENCIA  
$condicion
 AND ( aap.IDPROVEEDOR =(
 SELECT 
aap1.idproveedor
FROM 
$this->temporal.asistencia_asig_proveedor aap1
 WHERE 
 	aap1.IDASISTENCIA =a.IDASISTENCIA
ORDER BY aap1.IDASIGPROV DESC
LIMIT 1 
) OR aap.IDPROVEEDOR IS NULL  )		

GROUP BY a.IDASISTENCIA

ORDER BY FECHAHORA DESC
		";
//								echo $sql;
		$result = $this->query($sql);

		return $result;
	}




	function listar_asistencias_calidad($idusuario='',$arrstatusexpediente,$arrstatusasistencia,$idetapas,$idcuentas,$modo='',$statusautorizaciondesvio='0',$annio,$mes,$dia,$hora,$buscar,$statusnegociacionposterior,$auditoria,$encuesta,$evalexped,$condiconserv,$fechaini,$fechafin,$buscarpor,$reembolso,$todosCuenta){

		$condicion = "WHERE expediente.ARRSTATUSEXPEDIENTE ='CER' AND asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE ";

		if($reembolso =="1") $condicion.=" AND asistencia.REEMBOLSO = '1' ";
		if($buscarpor =="nombreafi" and $buscar!="") $havingAfiliado="  HAVING NOM_AFILIADO LIKE '%$buscar%' ";
		if ($condiconserv!='') $condicion.=" AND asistencia.ARRCONDICIONSERVICIO = '$condiconserv' ";
		if ($auditoria!='') $condicion.=" AND asistencia.EVALAUDITORIA = '$auditoria' ";
		if($encuesta!='') $condicion.=" AND asistencia.ARRSTATUSENCUESTA = '$encuesta'";
		if($evalexped!='') $condicion.=" AND asistencia.STATUSCALIDAD ='$evalexped' ";
		if ($arrstatusasistencia!='') $condicion.= " AND asistencia.ARRSTATUSASISTENCIA IN('$arrstatusasistencia')";
		if ($idusuario!='') $condicion.= " AND asistencia.IDUSUARIORESPONSABLE in ( $idusuario)";
		if ($fechaini !='' and $fechafin =='') $condicion.= " AND SUBSTR(expediente.FECHAREGISTRO,1,10)='$fechaini' ";
		if ($fechaini !='' and $fechafin !='') $condicion.= " AND SUBSTR(expediente.FECHAREGISTRO,1,10) BETWEEN '$fechaini' AND '$fechafin' ";
		if(!$todosCuenta) $condicion.= " AND expediente.IDCUENTA in ($idcuentas)";
 
		if($buscarpor =="codigoexp" and $buscar !="")	$condicion=" WHERE expediente.ARRSTATUSEXPEDIENTE ='CER' AND asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE AND expediente.IDEXPEDIENTE='$buscar' AND expediente.IDCUENTA in ($idcuentas) ";
		if($buscarpor =="codigoasi" and $buscar !="")	$condicion=" WHERE expediente.ARRSTATUSEXPEDIENTE ='CER' AND asistencia.IDEXPEDIENTE = expediente.IDEXPEDIENTE AND asistencia.IDASISTENCIA='$buscar' AND expediente.IDCUENTA in ($idcuentas) ";
 
		/*$Sql_calidad="SELECT
				  (SELECT
					 COUNT(ED.CVEDEFICIENCIA)
				   FROM $this->temporal.expediente_deficiencia ED
					 INNER JOIN $this->catalogo.catalogo_deficiencia D
					   ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA
				   WHERE ED.IDASISTENCIA = a.IDASISTENCIA
					   AND D.ORIGEN = 'INTERNO'
					   AND D.IMPORTANCIA = 'LEVE'
					   AND ED.MOVIMIENTO NOT IN('RETIRA'))    INTLEVE,
				  (SELECT
					 COUNT(ED.CVEDEFICIENCIA)
				   FROM $this->temporal.expediente_deficiencia ED
					 INNER JOIN $this->catalogo.catalogo_deficiencia D
					   ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA
				   WHERE ED.IDASISTENCIA = a.IDASISTENCIA
					   AND D.ORIGEN = 'EXTERNO'
					   AND D.IMPORTANCIA = 'LEVE'
					   AND ED.MOVIMIENTO NOT IN('RETIRA'))    EXTLEVE,
				  (SELECT
					 COUNT(ED.CVEDEFICIENCIA)
				   FROM $this->temporal.expediente_deficiencia ED
					 INNER JOIN $this->catalogo.catalogo_deficiencia D
					   ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA
				   WHERE ED.IDASISTENCIA = a.IDASISTENCIA
					   AND D.ORIGEN = 'INTERNO'
					   AND D.IMPORTANCIA = 'GRAVE'
					   AND ED.MOVIMIENTO NOT IN('RETIRA'))    INTGRAVE,
				  (SELECT
					 COUNT(ED.CVEDEFICIENCIA)
				   FROM $this->temporal.expediente_deficiencia ED
					 INNER JOIN $this->catalogo.catalogo_deficiencia D
					   ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA
				   WHERE ED.IDASISTENCIA = a.IDASISTENCIA
					   AND D.ORIGEN = 'EXTERNO'
					   AND D.IMPORTANCIA = 'GRAVE'
					   AND ED.MOVIMIENTO NOT IN('RETIRA'))    EXTGRAVE,
				  a.IDASISTENCIA,
				  a.IDEXPEDIENTE,
				  e.ARRSTATUSEXPEDIENTE,
				  a.IDCUENTA,
				  cc.NOMBRE                        NOM_CUENTA,
				  a.IDPROGRAMASERVICIO,
				  a.IDSERVICIO,
				  cs.DESCRIPCION                   AA_SERVICIO,
				  cps.ETIQUETA                     NOM_SERVICIO,
				  a.IDPROGRAMA,
				  cp.NOMBRE                        NOM_PROGRAMA,
				  a.ARRSTATUSASISTENCIA,
				  a.ARRPRIORIDADATENCION,
				  a.ARRCONDICIONSERVICIO,
				  a.IDETAPA,
				  ce.DESCRIPCION                   NOM_ETAPA,
				  a.IDUSUARIORESPONSABLE,
				  a.DESVIACION,
				  a.STATUSAUTORIZACIONDESVIO,
				  a.IDUSUARIOAUTORIZACIONDESVIO,
				  CONCAT(ep.APPATERNO,' ',ep.APMATERNO,' ',ep.NOMBRE)    NOM_AFILIADO,
				  MIN(au.FECHAHORA)                FECHAHORA,
				  aenc.EVALENCUESTA,
				  a.EVALAUDITORIA,
				  a.ARRSTATUSENCUESTA,
				  a.STATUSCALIDAD
				FROM ($this->temporal.asistencia a,
				   $this->temporal.expediente e,
				   $this->catalogo.catalogo_usuario cu)
				  LEFT JOIN $this->catalogo.catalogo_cuenta cc
					ON cc.IDCUENTA = a.IDCUENTA		
    			  LEFT JOIN $this->temporal.asistencia_encuesta_calidad aenc
					ON aenc.IDASISTENCIA = a.IDASISTENCIA
				  LEFT JOIN $this->catalogo.catalogo_programa_servicio cps
					ON cps.IDPROGRAMASERVICIO = a.IDPROGRAMASERVICIO
				  LEFT JOIN $this->catalogo.catalogo_servicio cs
					ON cs.IDSERVICIO = a.IDSERVICIO
				  LEFT JOIN $this->catalogo.catalogo_programa cp
					ON cp.IDPROGRAMA = a.IDPROGRAMA
				  LEFT JOIN $this->catalogo.catalogo_etapa ce
					ON ce.IDETAPA = a.IDETAPA
				  LEFT JOIN $this->temporal.expediente_persona ep
					ON ep.IDEXPEDIENTE = a.IDEXPEDIENTE
					  AND ep.ARRTIPOPERSONA = 'TITULAR'
				  LEFT JOIN $this->temporal.asistencia_usuario au
					ON au.IDASISTENCIA = a.IDASISTENCIA
					  AND au.IDETAPA = 1
				$condicion
				GROUP BY a.IDASISTENCIA
				$havingAfiliado
				ORDER BY a.IDASISTENCIA DESC  LIMIT 100";*/
				
		$Sql_calidad="SELECT 	
                          /* BUSCAR EXPEDIENTE CALIDAD */ 					  
						  asistencia.IDASISTENCIA,
						  asistencia.IDEXPEDIENTE,
						  expediente.ARRSTATUSEXPEDIENTE,
						  asistencia.IDCUENTA,
						  /*cc.NOMBRE    AS  NOM_CUENTA,*/
						  asistencia.IDPROGRAMASERVICIO,
						  asistencia.IDSERVICIO,
						  catalogo_servicio.DESCRIPCION  AS AA_SERVICIO,
						  catalogo_programa_servicio.ETIQUETA NOM_SERVICIO,
						  asistencia.IDPROGRAMA,
						  /*cp.NOMBRE      AS   NOM_PROGRAMA,*/
						  asistencia.ARRSTATUSASISTENCIA,
						  asistencia.ARRPRIORIDADATENCION,
						  asistencia.ARRCONDICIONSERVICIO,
						  asistencia.IDETAPA,
						  catalogo_etapa.DESCRIPCION AS NOM_ETAPA,
						  asistencia.IDUSUARIORESPONSABLE,
						  asistencia.DESVIACION,
						  asistencia.STATUSAUTORIZACIONDESVIO,
						  asistencia.IDUSUARIOAUTORIZACIONDESVIO,
						  CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,' ',expediente_persona.NOMBRE) AS NOM_AFILIADO,
						  MIN(asistencia_usuario.FECHAHORA) AS FECHAHORA,
						  asistencia_encuesta_calidad.EVALENCUESTA,
						  asistencia.EVALAUDITORIA,
						  asistencia.ARRSTATUSENCUESTA,
						  asistencia.STATUSCALIDAD,
						  
						  (SELECT
							 COUNT(ED.CVEDEFICIENCIA)
						   FROM $this->temporal.expediente_deficiencia ED
							 INNER JOIN $this->catalogo.catalogo_deficiencia D
							   ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA
						   WHERE ED.IDASISTENCIA = asistencia.IDASISTENCIA
							   AND D.ORIGEN = 'INTERNO'
							   AND D.IMPORTANCIA = 'LEVE'
							   AND ED.MOVIMIENTO NOT IN('RETIRA')) AS INTLEVE,
						  (SELECT
							 COUNT(ED.CVEDEFICIENCIA)
						   FROM $this->temporal.expediente_deficiencia ED
							 INNER JOIN $this->catalogo.catalogo_deficiencia D
							   ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA
						   WHERE ED.IDASISTENCIA = asistencia.IDASISTENCIA
							   AND D.ORIGEN = 'EXTERNO'
							   AND D.IMPORTANCIA = 'LEVE'
							   AND ED.MOVIMIENTO NOT IN('RETIRA'))  AS  EXTLEVE,
						  (SELECT
							 COUNT(ED.CVEDEFICIENCIA)
						   FROM $this->temporal.expediente_deficiencia ED
							 INNER JOIN $this->catalogo.catalogo_deficiencia D
							   ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA
						   WHERE ED.IDASISTENCIA = asistencia.IDASISTENCIA
							   AND D.ORIGEN = 'INTERNO'
							   AND D.IMPORTANCIA = 'GRAVE'
							   AND ED.MOVIMIENTO NOT IN('RETIRA'))  AS  INTGRAVE,
						  (SELECT
							 COUNT(ED.CVEDEFICIENCIA)
						   FROM $this->temporal.expediente_deficiencia ED
							 INNER JOIN $this->catalogo.catalogo_deficiencia D
							   ON ED.CVEDEFICIENCIA = D.CVEDEFICIENCIA
						   WHERE ED.IDASISTENCIA = asistencia.IDASISTENCIA
							   AND D.ORIGEN = 'EXTERNO'
							   AND D.IMPORTANCIA = 'GRAVE'
							   AND ED.MOVIMIENTO NOT IN('RETIRA')) AS EXTGRAVE				  
						  
				  
						FROM $this->temporal.asistencia
						  INNER JOIN $this->temporal.expediente
							ON expediente.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
						  INNER JOIN $this->catalogo.catalogo_cuenta
							ON catalogo_cuenta.IDCUENTA = asistencia.IDCUENTA
						  INNER JOIN $this->catalogo.catalogo_servicio
							ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
						  INNER JOIN $this->catalogo.catalogo_programa
							ON catalogo_programa.IDPROGRAMA = asistencia.IDPROGRAMA
						  LEFT JOIN $this->catalogo.catalogo_programa_servicio
							ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO
						  INNER JOIN $this->temporal.expediente_persona
							ON expediente_persona.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
							  AND expediente_persona.ARRTIPOPERSONA = 'TITULAR'
						  INNER JOIN $this->catalogo.catalogo_etapa
							ON catalogo_etapa.IDETAPA = asistencia.IDETAPA
						  LEFT JOIN $this->temporal.asistencia_encuesta_calidad
							ON asistencia_encuesta_calidad.IDASISTENCIA = asistencia.IDASISTENCIA
						  LEFT JOIN $this->temporal.asistencia_usuario
							ON asistencia_usuario.IDASISTENCIA = asistencia.IDASISTENCIA
							  AND asistencia_usuario.IDETAPA = 1
							 $condicion
						GROUP BY asistencia.IDASISTENCIA
						$havingAfiliado
						ORDER BY asistencia.IDASISTENCIA DESC  LIMIT 100 ";
    //echo $Sql_calidad;
		$result = $this->query($Sql_calidad);

		return $result;

	}

	function validar_statusautorizaciondesvio($idasistencia){

		$sql="SELECT * FROM $this->temporal.asistencia_asig_proveedor WHERE IDASISTENCIA='$idasistencia' AND STATUSAUTORIZACIONDESVIO='0'";
		$result = $this->query($sql);
		if ($result->num_rows)  $sw=false;
		else  $sw=true;
		return $sw;
	}


	/*  activa el status de autorizacion de desvio de la asistencia*/
	function activa_statusautorizaciondesvio($idasistencia){
		$sql="UPDATE $this->temporal.asistencia  SET STATUSAUTORIZACIONDESVIO='1',IDUSUARIOAUTORIZACIONDESVIO='$_SESSION[user]', FECHAAUTORIZADESVIO = CURRENT_TIMESTAMP() WHERE IDASISTENCIA='$idasistencia'";
		$this->query($sql);

		return;
	}



	function total_asistencia($idasistencia)
	{
		$sql="
		SELECT 
			IDMONEDA, SUM(AA_TOTALESTIMADO) SUMA_AA_TOTALESTIMADO,SUM(NA_TOTALESTIMADO) SUMA_NA_TOTALESTIMADO,SUM(CC_TOTALESTIMADO) SUMA_CC_TOTALESTIMADO, SUM(IMPORTEESTIMADO) SUMA_IMPORTEESTIMADO,
			SUM(AA_TOTALREAL) SUMA_AA_TOTALREAL,SUM(NA_TOTALREAL) SUMA_NA_TOTALREAL, SUM(CC_TOTALREAL) SUMA_CC_TOTALREAL, SUM(IMPORTEREAL) SUMA_IMPORTEREAL,
			SUM(AA_TOTALAUTORIZADO) SUMA_AA_TOTALAUTORIZADO, SUM(NA_TOTALAUTORIZADO) SUMA_NA_TOTALAUTORIZADO, SUM(CC_TOTALAUTORIZADO) SUMA_CC_TOTALAUTORIZADO, SUM(IMPORTEAUTORIZADO) SUMA_IMPORTEAUTORIZADO
		FROM $this->temporal.asistencia_asig_proveedor WHERE idasistencia='$idasistencia' 
		GROUP BY IDMONEDA
		";
		$result = $this->query($sql);
		return $result;
	}


	/* ESTA FUNCION VERIFICA SI SE COMPLETARON LOS DATOS DE LA PLANTILLA DE LA ASISTENCIA */

	function asistencia_completa($idasistencia)
	{

		/* busca los campos que son obligatorios en este servicio*/
		$sql="
	SELECT 
		cdo.IDSERVICIO,
		cdo.NOMBRETABLA,
		cdo.CAMPOOBLIGATORIO
	FROM
 		$this->temporal.asistencia a,
 		$this->catalogo.catalogo_datosobligatorios cdo 
	WHERE
		a.IDSERVICIO = cdo.IDSERVICIO
		AND a.IDASISTENCIA ='$idasistencia'
		";
		$result = $this->query($sql);
		while($reg=$result->fetch_object())
		{
			$tabla=$reg->NOMBRETABLA;
			$campos[] = $reg->CAMPOOBLIGATORIO;
		}

		foreach ($campos as $valor)
		$condicion.= " AND ".$valor. "<>''";

		/*luego busca si estos campos estan llenos en la tabla respectiva */
		$sql="
			SELECT 
				*
			FROM 
 				$this->temporal.$tabla 
			WHERE 
 				IDASISTENCIA ='$idasistencia'
 				$condicion
 			 ";

		$result = $this->query($sql);
		if ($result->num_rows) return true;
		return false;
	}
}





?>