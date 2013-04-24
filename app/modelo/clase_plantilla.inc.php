<?
class plantilla extends DB_mysqli {

	var $idplantilla;
	var $descripcion;
	var $vista;
	var $etiquetaapresentar;
	var $campoapresentar;
	
	
	function carga_datos($idplantilla){
		$sql="
		SELECT 
			*
		FROM 
			$this->catalogo.catalogo_plantilla
		WHERE
		 	IDPLANTILLA ='$idplantilla'
		";
		$result=$this->query($sql);
		while ($reg=$result->fetch_object()) {
			$this->idplantilla = $reg->IDPLANTILLA;
			$this->descripcion = $reg->DESCRIPCION;
			$this->vista = $reg->VISTA;
			$this->etiquetaapresentar = $reg->ETIQUETAAPRESENTAR;
			$this->campoapresentar = $reg->CAMPOAPRESENTAR;
			
			
		}
		return;
	}
}  // fin de la clase FAMILIA
?>