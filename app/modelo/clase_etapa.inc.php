<?
class etapa extends DB_mysqli {

	var $idetapa;
	var $descripcion;
	var $objetivo;
	

	function carga_datos($idetapa){
		$sql="
		SELECT 
			*
		FROM 
			$this->catalogo.catalogo_etapa
		WHERE
		 	IDETAPA ='$idetapa'
		";
		$result=$this->query($sql);
		while ($reg=$result->fetch_object()) {
			$this->idetapa= $reg->IDETAPA;	
			$this->descripcion = $reg->DESCRIPCION;
			$this->objetivo	= $reg->OBJETIVO;
			
		}
		return;
	}
}  // fin de la clase afilia
?>