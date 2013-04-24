<?
class servicio extends DB_mysqli {

	var $idservicio;
	var $idplantilla;
	var $descripcion;
	var $duracionestimada;
	var $familia;
	var $idcobertura;
	var $tecs;
	var $margenteat;
	var $margenteam;
	var $validaciontiempo;
	var $concluciontemprana;
	var $conclucionconproveedor;
	
	function carga_datos($idservicio){
		$sql="
		SELECT 
			*
		FROM 
			$this->catalogo.catalogo_servicio
		WHERE
		 	IDSERVICIO ='$idservicio'
		";
		$result=$this->query($sql);
		while ($reg=$result->fetch_object()) {
			$this->idservicio= $reg->IDSERVICIO;
			$this->plantilla = new plantilla();
			$this->plantilla->carga_datos($reg->IDPLANTILLA);
			$this->descripcion = $reg->DESCRIPCION;
			$this->duracionestimada	= $reg->DURACIONESTIMADA;
			$this->familia = new familia();
			$this->familia->carga_datos($reg->IDFAMILIA);
		
			$this->idcobertura = $reg->IDCOBERTURA;
			$this->tecs = $reg->TECS;
			$this->margenteat = $reg->MARGENTEAT;
			$this->margenteam = $reg->MARGENTEAM;
			$this->validaciontiempo = $reg->VALIDACIONTIEMPO;
			$this->concluciontemprana = $reg->CONCLUCIONTEMPRANA;
			$this->conclucionconproveedor = $reg->CONCLUCIONCONPROVEEDOR;
			
		}
		return;
	}
}  // fin de la clase FAMILIA
?>