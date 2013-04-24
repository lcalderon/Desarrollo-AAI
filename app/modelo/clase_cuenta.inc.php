<?
class cuenta extends DB_mysqli {

	var $idcuenta;
	var $nombre;
	var $afiliados;
	var $piloto;
	var $activo;
	var $cuentavip;
	

	function carga_datos($idcuenta){
		
		$sql="
		SELECT 
			*
		FROM 
			$this->catalogo.catalogo_cuenta
		WHERE
		 	IDCUENTA ='$idcuenta'
		";
		$result=$this->query($sql);
		while ($reg=$result->fetch_object()) {
			$this->idcuenta= $reg->IDCUENTA;
			$this->nombre = $reg->NOMBRE;
			$this->afiliados = $reg->AFILIADOS;
			$this->piloto = $reg->PILOTO;
			$this->activo = $reg->ACTIVO;
			$this->cuentavip= $reg->CUENTAVIP;
		}
		return;
	}
}  // fin de la clase afilia
?>