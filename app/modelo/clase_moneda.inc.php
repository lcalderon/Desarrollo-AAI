<?
class moneda extends DB_mysqli{
	var $idmoneda;
	var $descripcion;
	var $simbolo;
	var $activo;


	function __construct() {
		parent::__construct();
		$bd =  $this->catalogo;
		$this->select_db($bd);
		return;
	}

	function carga_datos($idmoneda){
		$sql="
		SELECT 
			*
 		FROM
			catalogo_moneda
		WHERE
			IDMONEDA = '$idmoneda';
		";

		$result= $this->query($sql);
		while($reg = $result->fetch_object()){
			$this->idmoneda = $reg->IDMONEDA;			
			$this->descripcion = $reg->DESCRIPCION;
			$this->simbolo = $reg->SIMBOLO;
			$this->activo = $reg->ACTIVO;
		}

	return;

	}


}


?>