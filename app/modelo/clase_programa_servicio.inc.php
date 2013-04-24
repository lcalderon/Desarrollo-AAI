<?
class programa_servicio extends DB_mysqli {
	var $idprogramaservicio;
	var $eventos;
	var $idtipofrecuencia;
	var $montoxserv;
	var $moneda;
	var $tipocobertura;
	var $etiqueta;
	var $nombreservicio_aa;
	
	function carga_datos($idprogramaservicio){
		$sql="
		SELECT 
			cps.IDPROGRAMASERVICIO,
			cps.EVENTOS,
			cps.IDTIPOFRECUENCIA,
			cps.MONTOXSERV,
			cps.IDMONEDA,
			cps.TIPOCOBERTURA,
			cps.ETIQUETA,
			cs.DESCRIPCION NOMBRESERVICIO_AA			
		FROM 
			$this->catalogo.catalogo_programa_servicio cps,
			$this->catalogo.catalogo_servicio cs
		WHERE
			cps.IDPROGRAMASERVICIO ='$idprogramaservicio'
			AND cps.IDSERVICIO = cs.IDSERVICIO
		";
//		echo $sql;
		$result=$this->query($sql);
		
		while ($reg=$result->fetch_object()) {
			$this->idprogramaservicio=$reg->IDPROGRAMASERVICIO;
			$this->eventos = $reg->EVENTOS;
			$this->idtipofrecuencia = $reg->IDTIPOFRECUENCIA;
			$this->montoxserv = $reg->MONTOXSERV;
			
			$this->moneda = new moneda();
			$this->moneda->carga_datos($reg->IDMONEDA);
			
			$this->tipocobertura = $reg->TIPOCOBERTURA;
			$this->etiqueta = $reg->ETIQUETA;
			$this->nombreservicio_aa = $reg->NOMBRESERVICIO_AA;
		}
		return;
	}
	
}  // fin de la clase FAMILIA
?>