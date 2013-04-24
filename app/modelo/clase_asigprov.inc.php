<?
class asigprov extends DB_mysqli {
	var $idasigprov;
	var $proveedor;
	var $contacto;
	var $asistencia;
	var $statusproveedor;
	var $fechahora;
	var $localforaneo;
	var $aplicanocturno;
	var $aplicafestivo;
	var $observacion;
	var $aa_totalestimado;
	var $na_totalestimado;
	var $cc_totalestimado;
	var $aa_totalreal;
	var $na_totalreal;
	var $cc_totalreal;
	var $statusnegociacionposterior;
	
	
	var $importeestimado;
	var $importereal;
	var $moneda;
	var $costos;
	
	var $fechaarribo;

	function carga_datos($idasigprov){
		$sql="
		SELECT 
			*
		FROM
			$this->temporal.asistencia_asig_proveedor 
		WHERE 
			IDASIGPROV = '$idasigprov'
			";
		$result = $this->query($sql);
		while($reg= $result->fetch_object()){
			$this->idasigprov = $reg->IDASIGPROV;
			$this->proveedor = new proveedor();
			$this->proveedor->carga_datos($reg->IDPROVEEDOR);
			$this->contacto = new contacto();
			$this->contacto->carga_datos($reg->IDCONTACTO);
			$this->asistencia = new asistencia();
			$this->asistencia->carga_datos($reg->IDASISTENCIA);
			$this->statusproveedor =$reg->STATUSPROVEEDOR;
			$this->fechahora =$reg->FECHAHORA;
			$this->localforaneo = $reg->LOCALFORANEO;
			$this->aplicanocturno = $reg->APLICANOCTURNO;
			$this->aplicafestivo = $reg->APLICAFESTIVO;
			$this->observacion = $reg->OBSERVACION;
			$this->aa_totalestimado =$reg->AA_TOTALESTIMADO;
			$this->na_totalestimado =$reg->NA_TOTALESTIMADO;
			$this->cc_totalestimado =$reg->CC_TOTALESTIMADO;
			$this->aa_totalreal =$reg->AA_TOTALREAL;
			$this->na_totalreal =$reg->NA_TOTALREAL;
			$this->cc_totalreal =$reg->CC_TOTALREAL;
			$this->aa_totalautorizado =$reg->AA_TOTALAUTORIZADO;
			$this->na_totalautorizado =$reg->NA_TOTALAUTORIZADO;
			$this->cc_totalautorizado =$reg->CC_TOTALAUTORIZADO;
			
			$this->importeestimado = $reg->IMPORTEESTIMADO;
			$this->importereal = $reg->IMPORTEREAL;
			$this->importeautorizado = $reg->IMPORTEAUTORIZADO;
			
			$this->statusnegociacionposterior = $reg->STATUSNEGOCIACIONPOSTERIOR;
			$this->moneda = new moneda();
			$this->moneda->carga_datos($reg->IDMONEDA);
			
			$this->fechaarribo = $reg->FECHAARRIBO;
			
		}

		$sql ="
		select *
		from
		 $this->temporal.asistencia_asig_proveedor_costo
		where 
		IDASIGPROV='$idasigprov'
		";
//		echo $sql;
		$result = $this->query($sql);
		while($reg= $result->fetch_object()){
			$this->costos[$reg->IDCOSTO]= array('UNIDAD'=>$reg->UNIDAD,
			'AA_MONTOESTIMADO'=>$reg->AA_MONTOESTIMADO,
			'NA_MONTOESTIMADO'=>$reg->NA_MONTOESTIMADO,
			'CC_MONTOESTIMADO'=>$reg->CC_MONTOESTIMADO,
			'AA_MONTOREAL'=>$reg->AA_MONTOREAL,
			'NA_MONTOREAL'=>$reg->NA_MONTOREAL,
			'CC_MONTOREAL'=>$reg->CC_MONTOREAL,
			'AA_MONTOAUTORIZADO'=>$reg->AA_MONTOAUTORIZADO,
			'NA_MONTOAUTORIZADO'=>$reg->NA_MONTOAUTORIZADO,
			'CC_MONTOAUTORIZADO'=>$reg->CC_MONTOAUTORIZADO,
			'JUSTIFICACION'=>$reg->JUSTIFICACION
			);
		}

		return;
	}


     
}


?>