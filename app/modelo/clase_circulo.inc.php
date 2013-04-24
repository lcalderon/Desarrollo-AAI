<?
class circulo{
	var $idcirculo;
	var $latitud;
	var $longitud;
	var $radio;
	var $idmedida;
	var $zoommap;


	var $con;

	function __construct(){
		$this->con = new DB_mysqli;
		$bd=  $this->con->catalogo;
		$this->con->select_db($bd);
		return;

	}


	function leer($idcirculo){
		$sql="
		SELECT
			IDCIRCULO,LATITUD,LONGITUD,RADIO,IDMEDIDA,ZOOMMAP
		FROM
		 catalogo_circulo
		WHERE
		 IDCIRCULO = '$idcirculo'
		";
		$result= $this->con->query($sql);
		while($reg= $result->fetch_object()){
			$this->latitud =$reg->LATITUD;
			$this->longitud =$reg->LONGITUD;
			$this->radio =$reg->RADIO;
			$this->idmedida =$reg->IDMEDIDA;
			$this->zoommap = $reg->ZOOMMAP;
			$this->idcirculo = $reg->IDCIRCULO;
		}

		return;
	}

	function grabar($form){
		$circulo[IDCIRCULO]= $form[IDCIRCULO];
		$circulo[LATITUD]= $form[LATITUD];
		$circulo[LONGITUD]= $form[LONGITUD];
		$circulo[RADIO]= $form[RADIO];
		$circulo[IDMEDIDA]= $form[IDMEDIDA];
		$circulo[ZOOMMAP] =$form[ZOOMMAP];

		if ($this->con->exist('catalogo_circulo','IDCIRCULO'," IDCIRCULO ='$circulo[IDCIRCULO]'"))
		$this->con->update('catalogo_circulo',$circulo," where IDCIRCULO ='$circulo[IDCIRCULO]'");
		else
		$this->con->insert_reg('catalogo_circulo',$circulo);
		return;
	}

	function borrar($idcirculo){
		$sql="
		DELETE
		FROM
		catalogo_circulo
		WHERE
		IDCIRCULO='$idcirculo'
		";
		$this->con->query($sql);
		return;
	}


	function verticesCircle($lat,$lng,$radius,$idmedida)
	{
		$points = array();
		$x = $lat;
		$y = $lng;
				if ($idmedida=='KM')	$r = $radius/100;
		if ($idmedida=='MIL')	$r = $radius/60;
		for ($i = 0; $i < 37; $i++) {
			$x1 = $x+$r*cos(2*pi()*$i/36);
			$y1 = $y+$r*sin(2*pi()*$i/36);
			$points[]=array('lat'=>$x1,'lng'=>$y1);
		}
		return $points;
	}





}


?>