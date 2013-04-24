<?
class programa extends DB_mysqli {

	var $idprograma;
	var $cuenta;
	var $nombre;
	var $eventos;
	var $frecuencia;
	var $cvetipofrecuencia;
	var $piloto;
	var $activo;
	var $programavip;
	var $fechainivigencia;
	var $fechafinvigencia;
	var $enviomail;
	var $servicios;

	function carga_datos($idprograma){
		$sql="
		SELECT 
			*
		FROM 
			$this->catalogo.catalogo_programa
		WHERE
		 	IDPROGRAMA ='$idprograma'
		";
		$result=$this->query($sql);
		while ($reg=$result->fetch_object()) {
			$this->idprograma= $reg->IDPROGRAMA;
			$this->cuenta = new cuenta();
			$this->cuenta->carga_datos($reg->IDCUENTA);
			
			$this->nombre = $reg->NOMBRE;
			$this->eventos = $reg->EVENTOS;
			$this->frecuencia= $reg->FRECUENCIA;
			$this->cvetipofrecuencia = $reg->CVETIPOFRECUENCIA;
			$this->piloto = $reg->PILOTO;
			$this->activo = $reg->ACTIVO;
			$this->fechainivigencia = $reg->FECHAINIVIGENCIA;
			$this->fechafinvigencia = $reg->FECHAFINVIGENCIA;
			$this->programavip = $reg->PROGRAMAVIP;
			
			
			$this->enviomail = $reg->ENVIOMAIL;
		}
//		$sql="
//		SELECT 
//			*
//		FROM 
//			$this->catalogo.catalogo_programa_servicio 
//		WHERE 
//			IDPROGRAMA='$idprograma'
//		";
//		$result=$this->query($sql);
////		$i=0;
//		while ($reg=$result->fetch_object()) {
//			$this->servicios[$reg->IDPROGRAMASERVICIO]=$reg->IDSERVICIO;
//
////			$i++;
//
//		}
		return;
	}
	
	function carga_servicios($idprograma,$idfamilia){
		$sql="
		SELECT 
			cps.IDPROGRAMASERVICIO,
			cps.IDSERVICIO
		FROM 
			$this->catalogo.catalogo_programa_servicio cps,
			$this->catalogo.catalogo_servicio cs
		WHERE 
			cps.IDSERVICIO = cs.IDSERVICIO
			AND cps.IDPROGRAMA='$idprograma'
			AND cs.IDFAMILIA = '$idfamilia'
			
		";
//		echo $sql;
		$result=$this->query($sql);

		while ($reg=$result->fetch_object()) {
			$this->servicios[$reg->IDPROGRAMASERVICIO]=$reg->IDSERVICIO;
		}
		
		return $this->servicios;
	}
}  // fin de la clase afilia
?>