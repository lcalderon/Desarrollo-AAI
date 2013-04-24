<?
class familia extends DB_mysqli {

	var $idfamilia;
	var $descripcion;
	var $color;
	var $activo;
	var $importancia;
	var $servicios;

	function carga_datos($idfamilia){
		$sql="
		SELECT 
			*
		FROM 
			$this->catalogo.catalogo_familia
		WHERE
		 	IDFAMILIA ='$idfamilia'
		";
		$result=$this->query($sql);
		while ($reg=$result->fetch_object()) {
			$this->idfamilia= $reg->IDFAMILIA;
			$this->descripcion = $reg->DESCRIPCION;
			$this->color	= $reg->COLOR;
			$this->activo = $reg->ACTIVO;
			$this->importancia = $reg->IMPORTANCIA;

		}
		return;
	}

	function carga_servicios($idfamilia){
		$sql="
		SELECT
			*
		FROM
			$this->catalogo.catalogo_servicio
		WHERE 
			IDFAMILIA='$idfamilia'
		";
		$result=$this->query($sql);
		$i=0;
		while ($reg=$result->fetch_object()) {
			$this->servicios[$i]=$reg->IDSERVICIO;
			$i++;

		}
		return;
	}

}  // fin de la clase FAMILIA
?>